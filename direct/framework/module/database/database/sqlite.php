<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс работы с базой данных SQLLite             ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Requirements: PHP >= 5.2.0                            ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\DataBase;

include_once(dirname(__FILE__).'/db.php');

final class SQLite implements db {
	// переменные	
	private $Framework=false;
	private $connect=false;
	private $file='sqlite/database.txt';
	

	// методы
	/**
	 * Конструктор класса
	 *
	 * @param Принимает в качестве параметра массив $dns = array(hostname, username, password, db)
	 * @return Не возвращает значения
	 */
	public function __construct($DB=array()){
		$this->Framework=\FrameWork\Framework::singleton();
		
		if (isset($DB['name']) && $DB['name'])
			$this->file=$DB['name'];
		else		
			$this->file=dirname(__FILE__).'/'.$this->file;
		
		try {
			$this->connect = new \PDO( 'sqlite:'.$this->file );
		}
		catch (\PDOException $e) {
			$this->Framework->library->error()->set('Не удалось установить соединение с базой данных sqlite: '.$e->getMessage().'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}		
					
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
	
	public function Connect(){		
		return $this->connect;
	}//\function
	
	public function set($sql){
		if (is_object($this->connect)) {
			$result=$this->connect->query($sql);
			if (!$result)
				$this->Framework->library->error->set('Не прошел запрос в базу данных: "'.$sql.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			return $result;
		} 
		else 
			return false;			
	}
	
	
	public function Id(){
		if (is_object($this->connect))
			return $this->connect->lastInsertId();
		else 
			return false;	
	}
		
	public function get($result=false, $both=false){
		if (is_object($result)) {
			$return=$result->fetch( ($both?\PDO::FETCH_BOTH:\PDO::FETCH_ASSOC) );
			return $return;
		}
		else
			return false;
	}
	
	public function Count($result=false){
		if (is_object($result)) {
			$count=$result->rowCount();
			if (!$count>0) {		
				if (isset($result->queryString) && $result->queryString) {
					$sql=preg_replace('/SELECT .+ FROM/isU', 'SELECT COUNT(*) FROM' , $result->queryString);
					if ($sql) {
						$result=$this->query($sql);
						if (is_object($result)) { 
							$ROW=$this->fetch($result, true);
							if (isset($ROW[0]) && $ROW[0])
								$count=$ROW[0];
						}
					}
				}
			}
			
			return intval($count);
		}
		else
			return false;
	}
	
	/**
	 * Экранируем строки
	 *
	 * @param строки или массив строк $el
	 * @param $flag устанавливает необходимость экранирования специ.символов % и _
	 * @param уровень $level
	 * @return Возвращаем строку или массив экранированых строк
	 */
	public function quote($el) { 
		if (is_object($this->connect)) 
  			$el = $this->connect->quote($el);		     		 
  		return $el;
	}

	public function install() {
		$sql="SELECT `name` FROM `sqlite_master` WHERE `type`='table' AND `name`='".$this->Framework->CONFIG['TABLES']['tree']."' LIMIT 1";		
		$result=$this->set($sql);		
		$ROW=$this->get($result, true);		
		if (!isset($ROW['name']) || !$ROW['name']) { 
			$sql="CREATE TABLE IF NOT EXISTS `".$this->Framework->CONFIG['TABLES']['tree']."` (
			  `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `uin` varchar(255) DEFAULT NULL,
			  `data` int(11) NOT NULL DEFAULT '0'
			)";
			$this->set($sql);
		}
		
		$sql="SELECT `name` FROM `sqlite_master` WHERE `type`='table' AND `name`='".$this->Framework->CONFIG['TABLES']['data']."' LIMIT 1";
		$ROW=$this->get($this->set($sql), true);
		if (!isset($ROW['name']) || !$ROW['name']) {				
			$sql="CREATE TABLE IF NOT EXISTS `".$this->Framework->CONFIG['TABLES']['data']."` (
			  `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `string` varchar(1024) DEFAULT NULL,
			  `int` int(11) DEFAULT NULL,
			  `double` double DEFAULT NULL,
			  `text` mediumtext,
			  `file` varchar(1024) DEFAULT NULL,
			  `status` tinyint(1) NOT NULL DEFAULT '1'
			)";
			$this->set($sql);			
		};
	}//\function	
	
}//\class
?>