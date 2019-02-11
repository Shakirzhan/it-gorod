<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс для обработки несуществующих методов и    /// 
/// свойств                                               ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@business-automate.ru                      ///
/// Url: http://business-automate.ru                      ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork;

final class None {

	//Переменные//
	private $Framework=false;
	//\Переменные//
		
	//Синглтон//
	static private $singleton = NULL;
	
	static function singleton()
	{
		if (self::$singleton == NULL)
		{
			self::$singleton = new None();
		}
		return self::$singleton;
	}
	//\Синглтон//
	
	protected function __construct()
	{		
		$this->Framework=\FrameWork\Framework::singleton();
	}
	
	private function __clone()
	{
	}
	
	public function __destruct()
	{
	
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
	
	
}//\class
?>