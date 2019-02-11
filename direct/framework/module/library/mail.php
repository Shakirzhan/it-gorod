<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин работа с данными                   ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;
include_once(dirname(__FILE__) . '/mail/class.phpmailer.php');
final class Mail extends \PHPMailer {
	private $Framework=false;
	private $dir='';
	
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
		parent::__construct();
		$this->dir=dirname(__FILE__) . '/' . $this->Framework->library->lib()->filename(__FILE__, true) . '/';
	}
	
	public function __call ($name, $ARGUMENTS=array()) {		
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}//\function
	
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
		
	public function __get($name) {
		$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;		
	}
	
	private function __clone()
	{
	}
	
	public function get($PARAMS=array()) {
		// Посылка письма//
		// Устанавливаем от кого письмо
		$this->FromName = $PARAMS['from_name'];
		$this->From = $PARAMS['from'];
		$this->AddReplyTo ( $this->From, $this->FromName );
		$this->Sender = $this->From;
		
		// Устанавливаем тему письма
		$this->Subject = $PARAMS['subject'];
		
		// Задаем тело письма
		$this->Body = $PARAMS['body'];
		unset ( $PARAMS ['body'] );
		$this->isHTML ( true );
		$this->AltBody = strip_tags ( $this->Body );
		
		// Добавляем адрес в список получателей
		$this->AddAddress ( $PARAMS['to'], (isset ( $PARAMS['to_name'] ) ? $PARAMS['to_name'] : '') );
		
		$this->CharSet = "UTF-8";

		if (isset($PARAMS['headers']) && is_array($PARAMS['headers'])) {
			foreach($PARAMS['headers'] AS $value) 
				if (!is_array($value) && $value)
					$this->AddCustomHeader($value);
		}
		
		if (! $this->Send ()) {
			$return=false;
		} else {
			$return=true;
		}
		$this->ClearAddresses ();
		$this->ClearReplyTos ();
		$this->ClearAttachments ();
		$this->ClearCustomHeaders();
		// \Посылка письма//
	return $return;
	}

}//\class
?>