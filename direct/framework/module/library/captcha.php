<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с каптчей               ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

include_once dirname(__FILE__).'/captcha/securimage.php';

final class Captcha extends \Securimage {
	private $Framework=null;
	private $dir='captcha/';
	
	public function __construct ($CONFIG=array()) {
		$this->Framework=\FrameWork\Framework::singleton();
		parent::__construct($CONFIG);
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
	
	private function __clone() {
	}
	
	
	public function get() {
		$this->show();
	}
	
	public function set($string='') {
		return $this->check($string);
	}
	
	public function play() {
		$this->outputAudioFile();
	}


}//\class
?>