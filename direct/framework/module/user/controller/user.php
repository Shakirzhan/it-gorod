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

final class User extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header->get('http');
	}
	
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} 
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}		
	
	private function token($PARAM=array()) {
		//Сохранение токена//
		if (!empty($PARAM['user']) && !empty($PARAM['token'])) {
			$TOKEN=$this->Framework->user->model->param->get(array('token'=>$PARAM['token']));
			if (empty($TOKEN['ELEMENT']) || (count($TOKEN['ELEMENT'])==1 && $TOKEN['ELEMENT'][0]['user']==$PARAM['user'])) {
				$this->Framework->user->model->model->set(array('id'=>$PARAM['user'], 'status'=>1));
				$this->Framework->user->model->param->set(array('user'=>$PARAM['user'], 'token'=>$PARAM['token']));
				return 'Токен получен';
			} elseif (!empty($TOKEN['ELEMENT'])) 
				return 'Токен уже получен для другого аккаунта ID: '.$TOKEN['ELEMENT'][0]['user'].'. Убедитесь что вы залогинены под обновляемым аккаунтом в Яндекс.Директ в этом же браузере.';
		}
		//\Сохранение токена//
		return null;
	}
	
	private function refresh($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		//Обновление статуса и пересинхронизация//
		if (!empty($PARAM['id'])) {
			$this->Framework->user->model->model->set(array('id'=>$PARAM['id'], 'status'=>1));
			$USER=$this->Framework->user->model->model->get(array('id'=>$PARAM['id']));
			if (!empty($USER['ELEMENT'][0]['parent'])) {
				$this->Framework->db->set("UPDATE `".$this->Framework->user->model->model->TABLE[0]."` SET `timestamp`=NULL WHERE `id`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`=NULL WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `time`=NULL WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET `time`=NULL WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `datetime`=NULL WHERE `user`='".$PARAM['id']."'");
			} else {
				$this->Framework->db->set("UPDATE `".$this->Framework->user->model->model->TABLE[0]."` SET `timestamp`=NULL WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`=NULL WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `time`=NULL WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET `time`=NULL WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `datetime`=NULL WHERE `account`='".$PARAM['id']."'");
			}
			return 'Пересинхронизация поставлена в очередь на исполнение. Приблизительное время 10 минут.';
		}
		//\Обновление статуса и пересинхронизация//
	}
	
	private function reset($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		//Сброс стратегий и настроек стратегий//
		if (!empty($PARAM['id'])) {
			$USER=$this->Framework->user->model->model->get(array('id'=>$PARAM['id']));
			if (!empty($USER['ELEMENT'][0]['parent'])) {
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0, `stop`=0, `datetime`=NULL WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0 WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0, `plan`=0 WHERE `user`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['retargeting']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0 WHERE `user`='".$PARAM['id']."'");
			} else {
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0, `stop`=0, `datetime`=NULL WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0 WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0, `plan`=0 WHERE `account`='".$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['retargeting']."` SET `strategy`=0, `percent`=0, `add`=0, `maximum`=0, `fixed`=0, `budget`=0, `type`=0, `context`=0, `context_percent`=0, `context_type`=0, `context_maximum`=0, `context_fixed`=0, `context_minimum`=0 WHERE `account`='".$PARAM['id']."'");
			}
			return 'Настройки стратегий сброшены в значения по умолчанию';
		}
		//\Сброс стратегий и настроек стратегий//
	}
	
	private function delete($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		$DATA=array('status'=>0);
		if (!empty($PARAM['id'])) {
			$this->Framework->user->model->model->delete(array('id'=>$PARAM['id']));
			$USER=$this->Framework->user->model->model->get(array('parent'=>$PARAM['id']));
			if (!empty($USER['ELEMENT']))
				foreach($USER['ELEMENT'] as $VALUE)
					$this->Framework->user->model->model->delete(array('parent'=>$VALUE['id']));
			$this->Framework->user->model->model->delete(array('parent'=>$PARAM['id']));
			$this->Framework->user->model->param->delete(array('user'=>$PARAM['id']));
			$DATA['status']=1;
			return 'Аккаунт удален';
		}

	}
	
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			if (!empty($PARAM['refresh']))
				$message=$this->refresh(!empty($PARAM['id'])?$PARAM['id']:0);
			if (!empty($PARAM['reset']))
				$message=$this->reset(!empty($PARAM['id'])?$PARAM['id']:0);
			if (!empty($PARAM['delete']))
				$message=$this->delete(!empty($PARAM['id'])?$PARAM['id']:0);
			if (!empty($PARAM['token']))
				$message=$this->token(array('user'=>!empty($PARAM['user'])?(is_array($PARAM['user'])?$PARAM['user'][0]:$PARAM['user']):0, 'token'=>!empty($PARAM['token'])?(is_array($PARAM['token'])?$PARAM['token'][0]:$PARAM['token']):''));
			if ($this->Framework->user->controller->controller->USER['group']==3)
				$PARAM['parent']=$this->Framework->user->controller->controller->USER['id'];
			if (!empty($PARAM) && is_array($PARAM)) {		
				if (isset($PARAM['id']))
					unset($PARAM['id']);
				$DATA['USER']=$this->Framework->user->model->model->get($PARAM, array('login'), array(), array('page'=>(!empty($PARAM['page'])?(int)$PARAM['page']:0), 'number'=>500));
				$DATA['USER']['PARAM']=$PARAM;
				$DATA['PARAM']=$PARAM;
			}
			if ($message)
				$_SESSION['framework_message']['message']=$message;
			
			if (!$message) {
				$DATA['USER']['message']=$_SESSION['framework_message']['message'];
				unset($_SESSION['framework_message']['message']);
			}
		}

		if ($message) {
			$this->Framework->library->header->set(array('Location'=>(!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$this->Framework->CONFIG['http_dir'].'/user/user/get/param/group/account')));
			$this->Framework->library->header->get();
		} else {
			$this->Framework->template->set('DATA', $DATA);
			echo $this->Framework->template->get('user.html');
		}
	}
	
	public function edit($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['USER']['message']=$this->token(array('user'=>!empty($PARAM['user'])?(is_array($PARAM['user'])?$PARAM['user'][0]:$PARAM['user']):0, 'token'=>!empty($PARAM['token'])?(is_array($PARAM['token'])?$PARAM['token'][0]:$PARAM['token']):''));
			if (!empty($PARAM['id'])) 
				$DATA['USER']=$this->Framework->user->model->model->get(array('id'=>$PARAM['id']));
			$DATA['USER']['PARAM']=$PARAM;
			
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('user.html');
	}	

	public function save($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && ($this->Framework->user->controller->controller->USER['group']==1 || $this->Framework->user->controller->controller->USER['group']==3 || (($this->Framework->user->controller->controller->USER['group']==4 || $this->Framework->user->controller->controller->USER['group']==2) && !empty($PARAM['id']) && $PARAM['id']==$this->Framework->user->controller->controller->USER['id']))) {
			if ($this->Framework->user->controller->controller->USER['group']>1 && !$this->Framework->user->controller->controller->USER['right']>0)
				unset($PARAM['right']);
			$PARAM['status']=1; 
			if (!empty($PARAM['login']))
				$PARAM['login']=trim(preg_replace('/@.+/', '', $PARAM['login']));
			$DATA['id']=$this->Framework->user->model->model->set($PARAM);
			if (!empty($DATA['id'])) {
				$PARAM['user']=$DATA['id'];
				$this->Framework->user->model->param->set($PARAM);
				$DATA['status']=1;
			} else 
				$DATA['ERROR']=array('Такой пользователь уже существует!');
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
}//\class
?>