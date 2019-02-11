<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс работы с базой данных Mysql               ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Requirements: PHP >= 5.2.0                            ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\DataBase;

include_once(dirname(__FILE__).'/db.php');

final class MySQL implements db {
	// переменные
	private $Framework=false;
	private $DB = array('hostname'=>'', 'port'=>'', 'username'=>'', 'password'=>'', 'database'=>'', 'socket'=>''); // hostname, port, username, password, db
	private $connect=null;
	private $result=null;
	
	private $time=0;//Время выполнения всех запросов в базу данных

	private $FIELD=array();
	private $COMMENT=array();
	
	// методы
	/**
	 * Конструктор класса
	 *
	 * @param Принимает в качестве параметра массив $DB = array(hostname, username, password, db)
	 * @return Не возвращает значения
	 */
	public function __construct($DB){
		$this->Framework=\FrameWork\Framework::singleton();
		$this->DB=array('hostname'=>$DB['host'], 'port'=>$DB['port'], 'username'=>$DB['user'], 'password'=>$DB['password'], 'database'=>$DB['name'], 'names'=>$DB['charset']); 
		$this->connect = false;
		//$this->Connect();
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
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	private function __clone()
	{
	}	
		
	public function set($sql){
		for ($index=0; $index<2; $index++) {
			if (!$this->connect) {
				$this->connect();
			}
			if (!$this->connect) 
				return false;
			
			$time=microtime(true);
			$result=$this->connect->query($sql);
			$time=microtime(true)-$time;
			$this->time+=$time;
			if ((int)$this->Framework->CONFIG['debug']>0 && !empty($this->Framework->CONFIG['DEBUG']['sql']) && (float)$this->Framework->CONFIG['DEBUG']['sql']>0 && (empty($this->connect->errno) || $this->connect->errno!=2006))
				if ($time>(float)$this->Framework->CONFIG['DEBUG']['sql'])
					$this->Framework->library->error->set('Время выполнения запроса в базу "'.$time.' сек.": "'.$sql, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__, true);
			
			if (!$result) {
				$result=false;
				if (empty($this->connect->errno) || $this->connect->errno!=2006 || !empty($this->Framework->CONFIG['DEBUG']['sql']))
					$this->Framework->library->error->set('Не прошел запрос в базу данных: "'.$sql.'". '.$this->connect->errno.': '.$this->connect->error, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
			
			if (!empty($this->connect->errno) && $this->connect->errno==2006)
				$this->connect=null;
			else
				break;
		}

		$this->result=$result;

		return $result;			 
	}
	
	public function multi($sql){
		if (!$this->connect) {
			$this->Connect();
		}
		if (is_array($sql)) {
			$sql = $sql[0];
		}
		
	$data=array();
	$count=0;	
	if ($this->connect->multi_query($sql)) 
	{
	    do {
	        /* store first result set */
	        if ($result = $this->connect->store_result()) {
	            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	                $data[$count][]=$row;
	            }
	            $result->free();
	        }
	        /* print divider */
	        if ($this->connect->more_results()) {
	
	        }
	    $count++;
	    } while ($this->connect->next_result());
	}
		
		if (($err = $this->connect->error) != '') {
		 	$this->Framework->library->error->set('Не прошел мульти запрос в базу данных! '.$this->connect->errno.': '.$this->connect->error, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		 }
		 
		 return $data;	
	}	
	
	public function id(){
		if (is_object($this->connect)) 
			return $this->connect->insert_id;
		return 0;
	}
	
	public function field($table='') {
		if ($table && is_string($table)) {
			if (empty($this->FIELD[$table])) {
				$FIELD=array();
				$result=$this->set('SHOW COLUMNS FROM `'.$this->quote($table).'`');
				while ($ROW=$this->get($result))
					$FIELD[]=$ROW['Field'];
				$this->FIELD[$table]=$FIELD;
				return $FIELD;	
			} else
				return $this->FIELD[$table];
		}
		return array();
	}
	
	public function comment($table='') {
		if ($table && is_string($table)) {
			if (empty($this->COMMENT[$table])) {
				$COMMENT=array();
				$result=$this->set('SHOW FULL COLUMNS FROM `'.$this->quote($table).'`');
				while ($ROW=$this->get($result))
					$COMMENT[$ROW['Field']]=$ROW['Comment'];
				$this->COMMENT[$table]=$COMMENT;
				return $COMMENT;	
			} else
				return $this->COMMENT[$table];
		}
		return array();
	}	
	
	public function table($db='') {
		$DATA=array();
		$sql='SHOW TABLE STATUS'.($db?' FROM `'.$this->quote($db).'`':'');
		$this->set($sql);
		while ($ROW=$this->get()) {
			$ROW['name']=$ROW['Name'];
			$ROW['count']=$ROW['Rows'];
			$ROW['size']=$ROW['Data_length'] + $ROW['Index_length'];
			$ROW['optimize']=$ROW['Data_free'];
			$ROW['kb']=round($ROW['size']/1024,1); 
			$ROW['mb']=round($ROW['size']/1024000,1); 
			$ROW['time']=$ROW['Update_time'];
			$DATA[]=$ROW;
		}
		return $DATA;
	}
	
	public function check($table='', $fast=false) {
		if (!empty($fast))
			$sql='CHECK TABLE `'.$this->quote($table).'` FAST QUICK';// FAST QUICK
		else
			$sql='CHECK TABLE `'.$this->quote($table).'` CHANGED QUICK';// FAST QUICK
		$this->set($sql);
		while ($ROW=$this->get()) 
			if ($ROW['Msg_type']=='status' && ($ROW['Msg_text']=='OK' || $ROW['Msg_text']=='Table is already up to date'))
				return true;
		return false;
	}
	
	public function repair($table='') {
		$sql='REPAIR TABLE `'.$this->quote($table).'`';
		$this->set($sql);
		while ($ROW=$this->get()) 
			if ($ROW['Msg_type']=='status' && $ROW['Msg_text']=='OK')
				return true;
		return false;
	} 
	
	public function truncate($table='') {
		$sql='TRUNCATE TABLE `'.$this->quote($table).'`';
		$this->set($sql);
		return true;
	}

	public function optimize($table='') {
		$sql='OPTIMIZE TABLE `'.$this->quote($table).'`';
		$this->set($sql);
		while ($ROW=$this->get()) 
			if ($ROW['Msg_type']=='status' && $ROW['Msg_text']=='OK')
				return true;
		return false;
	}
	
	public function number() {
		if (is_object($this->connect)) 
			return $this->connect->affected_rows;
		return 0;
	}
	
	public function version() {
		if (is_object($this->connect)) 
			if (!empty($this->connect->server_version)) 
				return substr($this->connect->server_version, 0, 1).'.'.substr($this->connect->server_version, 2, 1).'.'.substr($this->connect->server_version, -2);
		return null;
	}
	
	public function get($result=false, $both=false){
		if (empty($result) || !is_object($result))
			$result=$this->result;
		if (is_object($result)) {
			$ROW=$result->fetch_array( ($both?MYSQLI_BOTH:MYSQLI_ASSOC) );
			if (!empty($ROW) && is_array($ROW))
				return $ROW;	
		}
		return array();
	}
	
	public function count($result=false) {
		if (!empty($result) && is_object($result))
			return $result->num_rows;
		return 0;		
	}
	
	/**
	 * Экранируем строки
	 *
	 * @param строка 
	 * @return Возвращаем экранированную строку
	 */
	public function quote($string='') {
		if ($this->connect)
			if ($string!='value')
				$string = $this->connect->real_escape_string((string)$string); 
		else
			$string = str_replace("'", "\'", (string)$string);
  		return $string;
	}
	
	public function privilege($string='') {
		$DATA=array();
		if (!$this->connect) {
			$this->Connect();
		}
		if ($this->connect) {
			$this->set('SHOW PRIVILEGES');
			while ($ROW=$this->get()) 
				if (!empty($ROW['Privilege']))
					$DATA[]=$ROW['Privilege'];
			return $DATA;
		}
		return $DATA;
	}
	
	public function connection() {
		if (!empty($this->connect) && is_object($this->connect) && empty($this->connect->connect_errno)) 
			return true;
		else
			return false;
	}
	
	public function connect(){
		if ($this->connect && empty($this->connect->connect_errno))
			return $this->connect;
		$this->connect = new \mysqli($this->DB['hostname'], 
		                              $this->DB['username'], 
		                              $this->DB['password'],
		                              $this->DB['database'],
		                              (!empty ($this->DB['port'])?$this->DB['port']:0),
									  (!empty ($this->DB['socket'])?$this->DB['socket']:'')
		                              );

        if (!is_object($this->connect) || (is_object($this->connect) && !empty($this->connect->connect_errno))) {
			$error=!empty($this->connect->connect_errno)&&!empty($this->connect->connect_error)?$this->connect->connect_errno.': '.$this->connect->connect_error:'';
			$this->connect=false;
			$this->Framework->library->error->set('Не удалось установить соединение с базой данных MySQL: '.(!empty($error)?$error:'неправильные параметры подключения').'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
        
		
           
        if ($this->connect && !empty($this->DB['names'])) {
        	$this->connect->query('SET NAMES '.$this->DB['names']);//Кодировка
        }
		
		if ($this->connect) {
			$this->connect->query('SET GLOBAL max_allowed_packet = 16777215');//Увеличиваем размер запроса
			$this->connect->query('SET GLOBAL wait_timeout = 28800');//Увеличиваем время ожидания между запросами
			$this->connect->query('SET interactive_timeout = 28800');//Увеличиваем время ожидания между запросами
		}
			
        return $this->connect;
	}//\function 

	public function transaction(){
		$this->set('START TRANSACTION');
	}
	
	public function commit(){
		$this->set('COMMIT');
	}

	public function rollback(){
		$this->set('ROLLBACK');
	}
	
	public function install() {
		
	}//\function	
	
	public function time() {
		return round($this->time, 4);
	}
	
	
}//\class
?>