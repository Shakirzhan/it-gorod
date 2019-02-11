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

final class Excel {
	private $Framework=false;
	private $dir='';
	
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
		$this->dir=dirname(__FILE__) . '/' . $this->Framework->library->lib()->filename(__FILE__, true) . '/PHPExcel/';
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
	
	public function get($file='', $index=null) {
		$DATA=array();
		if (file_exists($this->dir.'IOFactory.php')) {
			include_once($this->dir.'IOFactory.php');
			try {
				$Excel = \PHPExcel_IOFactory::load($file);
			} 
			catch(ErrorException $e) {
				$this->Framework->library->error->set('Ошибка PHPExcel: "'.$e->getMessage().'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
			catch (Exception $e) {
				$this->Framework->library->error->set('Ошибка PHPExcel: "'.$e->getMessage().'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
			if (!empty($Excel)) {
				if (is_numeric($index))
					$Excel->setActiveSheetIndex($index);
				$DATA = $Excel->getActiveSheet()->toArray(null,true,true,true);
			}
		}
		else 
			$this->Framework->library->error->set('Нет такого файла: "'.$this->dir.'IOFactory.php'.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return $DATA;
	}

}//\class
?>