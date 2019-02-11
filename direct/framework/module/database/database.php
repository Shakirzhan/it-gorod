<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс работы с базой данных                     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Requirements: PHP >= 5.2.0                            ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\DataBase;

final class Database extends \Framework\Common {
	private $OBJECT=array();//Массив объектов соединений с базой данных

	
	
	public function __construct() {
		parent::__construct();
		
		//Подключаемся к базе данных//
		foreach ($this->Framework->CONFIG['DATABASE'] as $key=>$VALUE) {
			if (!$key)
				$key=0;
			if (isset($this->OBJECT[$key])) {
				$this->Framework->library->error()->set('Соединение с базой данных уже установлено с именем: "'.$key.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				return true;
			} 
						
			if (isset($VALUE['class']) && $VALUE['class'] && file_exists(dirname(__FILE__).'/database/'.strtolower($VALUE['class']).'.php')) {
				include_once(dirname(__FILE__).'/database/'.strtolower($VALUE['class']).'.php');
				$class='FrameWork\\module\\DataBase\\'.$VALUE['class'];
				if (class_exists($class)) {
					$this->OBJECT[$key]=new $class($VALUE);
					return true;
				}
				else							
					$this->Framework->library->error()->set('Нет класса с таким именем: "'.$class.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);				
				
				unset($class, $key, $VALUE);
			} 
			else {
				$this->Framework->library->error()->set('Нет такого драйвера для соединения с базой данных: "'.$key.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
		}
		//\Подключаемся к базе данных//
	}
	
	public function __call ($name, $ARGUMENT=array()) {
		if (method_exists($this->OBJECT[0], $name))
			return call_user_func_array(array($this->OBJECT[0], $name), $ARGUMENT);
		elseif (isset($this->OBJECT[$name]))
			return $this->OBJECT[$name];
		else
			$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}
	
	public function __get($name) {
		if (method_exists($this->OBJECT[0], $name))
			return $this->OBJECT[0]->$name();
		elseif (isset($this->OBJECT[$name]))
			return $this->OBJECT[$name];
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;
	}
	
}//\class
?>