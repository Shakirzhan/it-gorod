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

final class Mail {
	private $Framework=null;
	
	public function __construct() {
		$this->Framework=\FrameWork\Framework::singleton();
		
		$this->Framework->library->header->get('http');
		
	}
	
	public function __call ($name, $ARGUMENTS=array()) {
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}//\function
	
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}
	
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} 
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}		
	
	public function register($PARAMS=array()) {
		$PARAMS['host']=str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		$this->Framework->template->set('DATA', $PARAMS);
		$html=$this->Framework->template->get('register_mail.html');
		$PARAMS['subject']='Регистрация на сайте http://'.$PARAMS['host'].'/ прошла успешно.';
		$PARAMS['body']=$html;
		$PARAMS['from_name']=$PARAMS['host'];
		$PARAMS['from']='info@'.$PARAMS['host'];
		$PARAMS['to']=$PARAMS['email'];
		$PARAMS['to_name']=$PARAMS['name'];
		$this->Framework->library->mail->get($PARAMS);
	}
	
	public function remember($PARAMS=array()) {
		$PARAMS['host']=str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		$this->Framework->template->set('DATA', $PARAMS);
		$html=$this->Framework->template->get('remember_mail.html');
		$PARAMS['subject']='Напоминание пароля http://'.$PARAMS['host'];
		$PARAMS['body']=$html;
		$PARAMS['from_name']=$PARAMS['host'];
		$PARAMS['from']='no-reply@'.(preg_match("/^[0-9\.]+$/", $PARAMS['host'])?'direct-automate.ru':$PARAMS['host']);
		$PARAMS['to']=$PARAMS['email'];
		$PARAMS['to_name']=$PARAMS['name'];
		$this->Framework->library->mail->get($PARAMS);
	}
	
	public function contact($PARAMS=array()) {
		$DATA=array('status'=>0, 'ERRORS'=>array());
		if (empty($PARAMS))
			$PARAMS=$_REQUEST;
		if (!empty($PARAMS)) {
			$PARAMS['ip']=$this->Framework->CONFIG['ip'];
			$PARAMS['id']=$this->Framework->CONFIG['id'];
			$this->Framework->template->set('DATA', $PARAMS);
			$html=$this->Framework->template->get('contact_mail.html');
			$PARAMS['subject']='Письмо с сайта http://miraltyalliance.com/';
			$PARAMS['body']=$html;
			$PARAMS['from_name']=$PARAMS['name'];
			$PARAMS['from']=$PARAMS['email'];
			$PARAMS['to']='support@miraltyalliance.com';
			$PARAMS['to_name']='Miraltyalliance.com';
			if ($this->Framework->library->mail->get($PARAMS)) {
				$DATA['status']=1;
			}
		}
		else
			$DATA['ERRORS'][]='Пустые поля';
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}	
	
	public function otzivy($PARAMS=array()) {
		$DATA=array('status'=>0, 'ERRORS'=>array());
		if (empty($PARAMS))
			$PARAMS=$_REQUEST;
		if (!empty($PARAMS)) {
			$PARAMS['ip']=$this->Framework->CONFIG['ip'];
			$PARAMS['id']=$this->Framework->CONFIG['id'];
			$this->Framework->template->set('DATA', $PARAMS);
			$html=$this->Framework->template->get('otzivy_mail.html');
			$PARAMS['subject']='Отзыв с сайта http://miraltyalliance.com/';
			$PARAMS['body']=$html;
			$PARAMS['from_name']=$PARAMS['name'];
			$PARAMS['from']='support@miraltyalliance.com';
			$PARAMS['to']='support@miraltyalliance.com';
			$PARAMS['to_name']='Miraltyalliance.com';
			if ($this->Framework->library->mail->get($PARAMS)) {
				$DATA['status']=1;
			}
		}
		else
			$DATA['ERRORS'][]='Пустые поля';
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}		

}//\class
?>