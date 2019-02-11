<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс контроллер                                ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\user\controller;

final class Controller extends \FrameWork\Common {
	private $USER=false;
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header->get('http');
		
		$this->USER=!empty($_SESSION['USER'])?$_SESSION['USER']:array();
		if (empty($this->USER))
			$this->auth(0, array(), false);
	}
	
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} 
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}		
	
	public function auth($id=0, $GROUP=array(), $captcha=true) {

		if (!empty($this->USER['id']) && (empty($GROUP) || in_array($this->USER['group'], $GROUP)))
			return $this->USER['id'];
		elseif (!empty($_SESSION['USER']['id']) && (empty($GROUP) || in_array($_SESSION['USER']['group'], $GROUP))) {
			$this->USER=$_SESSION['USER'];
			return $this->USER['id'];
		} elseif (!empty($id) && is_numeric($id)) {
			$DATA=$this->Framework->user->model->model->get(array('id'=>$id));
			if (!empty($DATA['ELEMENT']) && is_array($DATA['ELEMENT'])) {
				$this->USER=array_shift($DATA['ELEMENT']);
				$_SESSION['USER']=$this->USER;
				return $this->USER['id'];
			} else 
				$this->USER=null;
		}
		if ($this->Framework->CONFIG['id']) {
			$DATA=$this->Framework->user->model->model->get(array('session'=>$this->Framework->CONFIG['id']));
			if (!empty($DATA['ELEMENT']) && is_array($DATA)) {
				$this->USER=array_shift($DATA['ELEMENT']);
				$_SESSION['USER']=$this->USER;
				return $this->USER['id'];
			}
		}
		
		$PARAMS=$_REQUEST;
		if (!empty($PARAMS['login'])) {
			$PARAMS['captcha_check']=$captcha;
			$this->USER=$this->Framework->user->model->model()->login($PARAMS);
			if (!empty($this->USER) && (empty($GROUP) || in_array($this->USER['group'], $GROUP))) {
				$_SESSION['USER']=$this->USER;
				return $this->USER['id'];
			}
		}
		return false;
	}
	
	public function authorize() {
		$auth=$this->auth(0, array(), false);
		$this->Framework->template('json')->set('status', ($auth?1:0));
		echo $this->Framework->template('json')->get();
	}
	
	public function quit($redirect='') {
		if (!empty($this->USER))
			$this->Framework->user->model->model->set(array('id'=>$this->USER['id'], 'session'=>''));
		unset($_SESSION['USER'], $this->USER);
		$this->Framework->library->session->delete(true);
		if (!$redirect)
			$redirect=$this->Framework->CONFIG['http_dir'].'/user/controller/login/';
		$this->Framework->library->header->set(array('Location'=>$redirect));
		$this->Framework->library->header->get();
		return true;
	}
	
	private function registration($PARAMS=array()) {
		$RETURN=array('status'=>0, 'ERRORS'=>array());
		
		/*if (empty($PARAMS['login'])) 
			$RETURN['ERRORS']['login']='Please, fill form Login';
		else {
			$USER=$this->Framework->user->model->model->get(array('login'=>$PARAMS['login']));
			if (!empty($USER['ELEMENT']))
				$RETURN['ERRORS']['login']='Login already exists';
		}*/

		if (empty($PARAMS['email']) || !($PARAMS['email']=$this->Framework->library->lib->email($PARAMS['email']))) 
			$RETURN['ERRORS']['email']='Please, fill form Email';
		else {
			$USER=$this->Framework->user->model->model->get(array('email'=>$PARAMS['email']));
			if (!empty($USER['ELEMENT']))
				$RETURN['ERRORS']['email']='Email already exists';
		}
		$PARAMS['login']=$PARAMS['email'];
		/*if (empty($PARAMS['parent'])) 
			$RETURN['ERRORS']['parent']='Не заполнено поле "Номер пригласившего участника"';
		else {
			$USER=$this->Framework->user->model->model->get(array('id'=>$PARAMS['parent']));
			if (empty($USER['ELEMENT'][0]['id']) || $USER['ELEMENT'][0]['status']==3)
				$RETURN['ERRORS']['parent']='Неправильный "Номер пригласившего участника"';
		}*/

		if (empty($PARAMS['password'])) 
			$RETURN['ERRORS']['password']='Please, fill form Password';
		if (empty($PARAMS['password1'])) 
			$RETURN['ERRORS']['password1']='Please, fill form Repeat password';
		if (!empty($PARAMS['password']) && !empty($PARAMS['password1']) && $PARAMS['password']!=$PARAMS['password1']) {
			$RETURN['ERRORS']['password']='Passwords do not match';
			$RETURN['ERRORS']['password1']='Passwords do not match';
		}
		if (!empty($PARAMS['password']) && !empty($PARAMS['password1']) && $PARAMS['password']==$PARAMS['password1']) {
			if (!preg_match('/^[a-z0-9]+$/i', $PARAMS['password']) || !preg_match('/[A-Z]+/', $PARAMS['password']) || !preg_match('/[0-9]+/', $PARAMS['password']) || strlen($PARAMS['password'])<8 )
				$RETURN['ERRORS']['password']='The password must contain at least one capital letter and one number, contain the characters are letters or numbers, and contain no less than 8 characters';
		}
		//if (empty($PARAMS['captcha']) || (!empty($PARAMS['captcha']) && !$this->Framework->library->captcha->set($PARAMS['captcha'])))
			//$RETURN['ERRORS']['captcha']='Неправильный код каптчи';

		$PARAMS['group']=2;
		$PARAMS['status']=229;
		$PARAMS['ip']=$this->Framework->CONFIG['ip'];
		$PARAMS['session']=$this->Framework->CONFIG['id'];
		$PARAMS['time']=DATE('Y-m-d H:i:s');
		if (count($RETURN['ERRORS'])==0) {
			$id=$this->Framework->user->model->model->set($PARAMS);
			if ($id) {
				$PARAMS['user']=$id;
				$this->Framework->user->model->param->set($PARAMS);
				$DICTIONARY[$id]=$_REQUEST['dictionary'];
				$this->Framework->user->model->dictionary->set($DICTIONARY);
				$RETURN['status']=1;
				$this->auth($id);
				$this->Framework->user->controller->mail->register($PARAMS);
			}
			else 
				$RETURN['ERRORS'][0]='Неизвестная ошибка';
		}

		return $RETURN;
	}	

	public function profile_save() {
		$RETURN=array('status'=>0, 'ERRORS'=>array());
		if (!empty($this->USER)) {
			$PARAMS=$_REQUEST;
			$this->Framework->library->lib->array_unset($PARAMS, array('group', 'status'), true);
			$PARAMS['id']=$this->USER['id'];
			if (empty($PARAMS['name'])) 
				$RETURN['ERRORS']['name']='Не заполнено поле "Имя"';
			if (empty($PARAMS['lastname'])) 
				$RETURN['ERRORS']['lastname']='Не заполнено поле "Фамилия"';
			if (empty($PARAMS['email'])) 
				$RETURN['ERRORS']['email']='Не заполнено поле "Email"';
			if (empty($PARAMS['phone'])) 
				$RETURN['ERRORS']['phone']='Не заполнено поле "Телефон"';
			if (empty($PARAMS['country'])) 
				$RETURN['ERRORS']['country']='Не заполнено поле "Страна"';
			if (empty($PARAMS['city'])) 
				$RETURN['ERRORS']['city']='Не заполнено поле "Город"';
			if (!empty($PARAMS['password']) && (empty($PARAMS['password1']) || $PARAMS['password']!=$PARAMS['password1'])) 
				$RETURN['ERRORS']['password1']='Пароли не совпадают';
			if (!empty($PARAMS['password']) && !empty($PARAMS['password1']) && $PARAMS['password']==$PARAMS['password1']) {
				if (!preg_match('/^[a-z0-9]+$/i', $PARAMS['password']) || !preg_match('/[A-Z]+/', $PARAMS['password']) || !preg_match('/[0-9]+/', $PARAMS['password']) || strlen($PARAMS['password'])<8 )
					$RETURN['ERRORS']['password']='Пароль должен состоять из хотя бы одной заглавной буквы и одной цифры, содержать символы латинские буквы или цифры, и быть длинной не меньше 8 символов';
			}
			
			if (count($RETURN['ERRORS'])==0) {
				$id=$this->Framework->user->model->model->set($PARAMS);
				if ($id) {
					$RETURN['status']=1;
				}
				else 
					$RETURN['ERRORS'][]='Неизвестная ошибка';
			}
		}
		else 
			$RETURN['ERRORS'][]='Вы не авторизованы';
		$this->Framework->tpl('json')->set('DATA', $RETURN);
		echo $this->Framework->tpl('json')->get();
	}
	
	public function bank_save() {
		$RETURN=array('status'=>0, 'ERRORS'=>array());
		if (!empty($this->USER)) {
			$PARAMS=$_REQUEST;
			$this->Framework->library->lib->array_unset($PARAMS, array('okpay', 'bank') );
			$PARAMS['id']=$this->USER['id'];
			if (empty($PARAMS['okpay']) && empty($PARAMS['bank'])) {
				$RETURN['ERRORS']['okpay']='Не заполнено поле "OKPAY"';
				$RETURN['ERRORS']['bank']='Не заполнено поле "Банковские реквизиты"';
			}
				
			if (count($RETURN['ERRORS'])==0) {
				$id=$this->Framework->user->model->model->set($PARAMS);
				if ($id) {
					$RETURN['status']=1;
				}
				else 
					$RETURN['ERRORS'][]='Неизвестная ошибка';
			}
		}
		else 
			$RETURN['ERRORS'][]='Вы не авторизованы';
		$this->Framework->tpl('json')->set('DATA', $RETURN);
		echo $this->Framework->tpl('json')->get();
	}
	
	private function groups_get($PARAMS=array()) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAMS['id']))
			$WHERE[]="`t1`.`id`='".intval($PARAMS['id'])."'";
		if (!empty($PARAMS['status']))
			$WHERE[]="`t1`.`status`='".($PARAMS['status']?1:0)."'";
		$where=(count($WHERE)>0)?' WHERE '.implode(' AND ',$WHERE).' ':'';
		
		$sql="SELECT `t1`.* 
					FROM `".$this->TABLES['groups']."` `t1`
					{$where}
					ORDER BY `t1`.`name` ASC
		";
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			$DATA[]=$ROW;
		}
		return $DATA;
	}		

	
	public function login($PARAM=array()) {
		$DATA=array();
		$DATA=array_merge($DATA, $this->Framework->page->controller->controller->menu());
		if ($this->USER && $this->USER['group']>=1) {
			$this->Framework->direct->controller->index->index($PARAM);
		} else {
			$this->Framework->template->set('USER', $this->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$this->Framework->template->set('DATA', $DATA);
			echo $this->Framework->template->get('user.html');
		}
	}
	
	public function remember() {
		$this->Framework->template->set('USER', $this->USER);
		echo $this->Framework->template->get('user.html');
	}
	
	public function remember_mail() {
		$RETURN=array('status'=>0, 'ERRORS'=>array());

			$PARAMS=$_REQUEST;
			
			//if (empty($PARAMS['captcha']) || (!empty($PARAMS['captcha']) && !$this->Framework->library->captcha->set($PARAMS['captcha'])))
				//$RETURN['ERRORS']['captcha']='Неправильный код каптчи';
			if (empty($PARAMS['login']) && empty($PARAMS['email'])) 
				$RETURN['ERRORS']['login']='Введите Логин или Email';
			elseif (!empty($PARAMS['login'])) {
				$USER=$this->Framework->user->model->model->get(array('login'=>$PARAMS['login']));
				if (empty($USER['ELEMENT'][0]['id']))
					$RETURN['ERRORS']['login']='Пользователя с таким логином не существует';
				$PARAMS['email']=$USER['ELEMENT'][0]['email'];
			} 
			elseif (!empty($PARAMS['email'])) {
				$USER=$this->Framework->user->model->model->get(array('email'=>$PARAMS['email']));
				if (empty($USER['ELEMENT'][0]['id']))
					$RETURN['ERRORS']['email']='Пользователя с таким Email не существует';
			}
				
			if (count($RETURN['ERRORS'])==0 && !empty($USER['ELEMENT'][0]['id']) && !empty($USER['ELEMENT'][0]['email'])) {
				$PARAMS=$USER['ELEMENT'][0];
				$PARAMS['password']=$this->Framework->library->lib->password(8);
				$id=$this->Framework->user->model->model->set(array('id'=>$USER['ELEMENT'][0]['id'], 'password'=>$PARAMS['password']));
				if ($id) {
					$this->Framework->user->controller->mail->remember($PARAMS);
					$RETURN['status']=1;
				}
				else 
					$RETURN['ERRORS'][]='Неизвестная ошибка';
			} else 
				$RETURN['ERRORS'][]='Пользователь не указал Email';
		
		echo json_encode(array('DATA'=>$RETURN));
	}
	
	public function reg() {
		$DATA=$this->registration($_REQUEST);
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function register($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM)) 
			$DATA['parent']=$PARAM;
		else {
			$DATA['PARAM']=$PARAM;
		}	
		
		
		$DATA=array_merge($DATA, $this->Framework->page->controller->controller->menu());
		$DATA['DICTIONARY']=$this->Framework->dictionary->model->model->get();
		$this->Framework->template->set('USER', $this->USER);
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('user.html');
		
	}
	
	public function index() {
		if (!empty($this->USER)) {
			$DATA=$this->Framework->user->model->model->get(array('id'=>$this->USER['id']));
			$DATA=array_shift($DATA['ELEMENT']);
			$DATA['sum']=$this->Framework->pay->model->model->sum(array('user'=>$this->USER['id']));
			$this->Framework->template->set('USER', $this->USER);
			$this->Framework->template->set('DATA', $DATA);
		}
		echo $this->Framework->template->get('user.html');
	}	

	public function bank() {
		if (!empty($this->USER)) {
			$DATA=$this->Framework->user->model->model->get(array('id'=>$this->USER['id']));
			$DATA=array_shift($DATA['ELEMENT']);
			$this->Framework->template->set('USER', $this->USER);
			$this->Framework->template->set('DATA', $DATA);
		}
		echo $this->Framework->template->get('user.html');
	}	

	public function profile() {
		$DATA=array();
		if (!empty($this->USER)) {
			$DATA=array_merge($DATA, $this->Framework->page->controller->controller->menu());
			$USER=$this->Framework->user->model->model->get(array('id'=>$this->USER['id']));
			$USER=array_shift($USER['ELEMENT']);
			$USER_PARAM=$this->Framework->user->model->param->get(array('user'=>$this->USER['id']));
			$USER_PARAM=array_shift($USER_PARAM['ELEMENT']);
			$DICTIONARY=$this->Framework->user->model->dictionary->get(array('user'=>$this->USER['id']));
			$DATA['USER']=array_merge($USER, $USER_PARAM);
			$DATA['USER']['DICTIONARY']=$DICTIONARY;
			$this->Framework->template->set('USER', $this->USER);
			$this->Framework->template->set('DATA', $DATA);
		}
		echo $this->Framework->template->get('user.html');
	}	
	
	public function captcha() {
		$this->Framework->library->captcha->get();
	}	

	public function users() {
		if (!empty($this->USER)) {
			$this->Framework->template->set('USER', $this->USER);
			$DATA=array();
			$action=!empty($_REQUEST['action'])?$_REQUEST['action']:'';
			$this->Framework->template->set('action', $action);
			
			switch($action) {
				case 'save':
				if ($this->Framework->user->model->set($_REQUEST)) {
					$this->Framework->library->header->set(array('Location'=>$_SERVER['SCRIPT_NAME']));
					$this->Framework->library->header->get();
					break;
				}
				else {
					$this->Framework->template->set('error', 'Неправильно заполнены поля формы!');
					$this->Framework->template->set('action', 'edit');
				}
				case 'edit':
					if (!empty($_REQUEST['id'])) {
						$DATA['USERS']=$this->Framework->user->model->model->get(array('id'=>$_REQUEST['id']));
						$DATA['USERS']=!empty($DATA['USERS']['ELEMENT'])?$DATA['USERS']['ELEMENT']:array();
					}
					$DATA['GROUPS']=$this->groups_get(array('status'=>1));
				break;
			
				default: 
					$DATA=$this->Framework->user->model->model->get();
					$DATA=!empty($DATA['ELEMENT'])?$DATA['ELEMENT']:array();
				break;
			}
			$this->Framework->template->set('DATA', $DATA);	
		}
		
		echo $this->Framework->template->get('index.html');
	}
	
	public function add() {
		if (!empty($this->USER)) {
			$DATA=array();
			$action=!empty($_REQUEST['action'])?$_REQUEST['action']:'';
			$this->Framework->template->set('action', $action);
			
			switch($action) {
				case 'save':
				break;
	
			
				default: 
					$DATA['file1']=$this->Framework->library->lib->uniqid();
					$DATA['file2']=$DATA['file1'].'1';
				break;
			}
			$this->Framework->template->set('USER', $this->USER);
			$this->Framework->template->set('DATA', $DATA);	
		}
		
		echo $this->Framework->template->get('index.html');
	}

}//\class
?>