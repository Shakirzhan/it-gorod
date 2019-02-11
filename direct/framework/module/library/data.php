<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс для работы с массивами                    ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Data {
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
	
	public function delete($ARRAY=array(), $EXEPTION=array(), $invert=false) {
		if (!empty($ARRAY) && is_array($ARRAY) && !empty($EXEPTION)) {
			if (!is_array($EXEPTION))
				$EXEPTION=array($EXEPTION);
			foreach ($ARRAY as $key=>&$value) {
				if ((!in_array($key, $EXEPTION) && !$invert) || (in_array($key, $EXEPTION) && $invert))
					unset($ARRAY[$key]);
			}
		}
		return $ARRAY;
	}
	
	public function &delete_reference(&$ARRAY , $EXEPTION=array(), $invert=false) {
		if (!empty($ARRAY) && is_array($ARRAY) && !empty($EXEPTION)) {
			if (!is_array($EXEPTION))
				$EXEPTION=array($EXEPTION);
			foreach ($ARRAY as $key=>&$value) {
				if ((!in_array($key, $EXEPTION) && !$invert) || (in_array($key, $EXEPTION) && $invert))
					unset($ARRAY[$key]);
			}
		}
		return $ARRAY;
	}
	
	//Принудительное преобразование объекта в массив//
	public function &get(&$ARRAY) {
		if (!empty($ARRAY) && (is_array($ARRAY) || is_object($ARRAY))) {
			$ARRAY=(array)$ARRAY;
			foreach ($ARRAY as &$value) {
				if (is_object($value)) 
					$value=(array)$value;
				if (is_array($value))
					$this->get($value);
			}
		}
		return $ARRAY;
	}
	//\Принудительное преобразование объекта в массив//
	
	public function in_array($search='', $ARRAY=array()) {
		if (!empty($search))
			if (is_array($search) || is_object($search)) {
				foreach ($search as &$value) {
					if (!in_array($value, $ARRAY))
						return false;
				}
				return true;
			} else
				return in_array($search, $ARRAY);
		return false;
	}

}//\class
?>