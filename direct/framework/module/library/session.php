<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с сессиями              ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Session {
	private $Framework=null;

	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
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
	
	public function delete($full=false) {
		//Завершаем сессию//
		session_unset();
		session_destroy();
		unset($_SESSION, $_COOKIE[$this->Framework->CONFIG['session_name']]);
		setcookie ($this->Framework->CONFIG['session_name'], '', 0, '/');
		$this->Framework->CONFIG['session']=null;
		//\Завершаем сессию//
		if ($full) {
			//Удаляем уникальный id//
			setcookie ($this->Framework->CONFIG['name'].'['.$this->Framework->CONFIG['cookie_name'].']', '', 0, '/');
			unset($_COOKIE[$this->Framework->CONFIG['name']][$this->Framework->CONFIG['cookie_name']]);
			$this->Framework->CONFIG['id']=null;
			//\Удаляем уникальный id//
		}
	}	

}//\class
?>