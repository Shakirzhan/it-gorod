<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс наследуемый модулями                      ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@business-automate.ru                      ///
/// Url: http://business-automate.ru                      ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork;

class Common {
	protected $Framework=null;
	
	protected function __construct() {
		$this->Framework=Framework::singleton();
	}
	
	public function __call ($name, $ARGUMENTS=array()) {
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
	public function __get($name) {
		$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
	public static function __callStatic ($object, $ARGUMENTS=array()) {	
		return None::singleton();
	}//\function	
	
	protected function __clone() {
	
	}	
}
?>