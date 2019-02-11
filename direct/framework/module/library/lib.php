<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс библиотека методов                        ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class lib 
{
	private $Framework=false;
			
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
	}
	
	public function __call ($name, $ARGUMENTS=array()) {		
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}//\public function
	
	
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
	
	/**
	 * Метод подключения к базе данных MySQL
	 * @param string	$DB['host']				- хост
	 * @param int		$DB['port']				- порт (по умолчанию: 3306)
	 * @param string	$DB['user']				- пользователь
	 * @param string	$DB['password']				- пароль
	 * @param string	$DB['db']				- база данных
	 * @param string	$DB['charset']				- кодировка (например: UTF8)  
	 *
	 * @return resource
	 */
	public function mysql_connect($DB = array()) {
		$link = @mysql_connect ( $DB ["host"] . ":" . $DB ["port"], $DB ["user"], $DB ["password"] );
		@mysql_select_db ( $DB ["db"], $link );
		if (isset ( $DB ['charset'] ))
			if ($DB ['charset'])
			@mysql_query ( "SET NAMES '{$DB['charset']}' ", $link );
		return $link;
	}

	/**
	 * Метод вычисляющий из байтов гигобайты
	 * @param int	$size				- число
	 * @return int
	 */
	public function gb($size=0) {
		return ceil($size/1073741824);
	}

	/**
	 * Метод вычисляющий из байтов мегабайты
	 * @param int	$size				- число
	 * @return int
	 */
	public function mb($size=0) {
		return ceil($size/1048576);
	}	
	
	/**
	 * Метод вычисляющий из байтов килобайты
	 * @param int	$size				- число
	 * @return int
	 */
	public function kb($size=0) {
		return ceil($size/1024);
	}	
	
	/**
	 * Метод возвращающий день недели по русски
	 * @param int	$week				- день недели 0 или 7 - Воскресенье, 1-6 дни недели начиная с понедельника.
	 *
	 * @return string
	 */
	 public function week($week=NULL, $full=false) {
		if (!is_numeric($week))
			$week=DATE('w');
		switch ($week) {
			case 1:
				$name=$full?'Понедельник':'Пн';
			break;
			case 2:
				$name=$full?'Вторник':'Вт';
			break;
			case 3:
				$name=$full?'Среда':'Ср';
			break;
			case 4:
				$name=$full?'Четверг':'Чт';
			break;
			case 5:
				$name=$full?'Пятница':'Пт';
			break;
			case 6:
				$name=$full?'Суббота':'Сб';
			break;			
			case 0:
			default:
				$name=$full?'Воскресенье':'Вс';
			break;
		}
		return $name;
	 }
	
	/**
	 * Метод возвращающий уникальный идентификатор
	 * @param void
	 *
	 * @return string
	 */
	public function uniqid() {
		return uniqid(rand());
	}
	
	public function phpversion() {
		$php=phpversion();
		return explode('.', $php);
	}
	
	/**
	 * Метод возвращающий путь к PHP (только для *nix систем)
	 * @param void
	 *
	 * @return string
	 */	
	public function php() {
		$php=exec('whereis php');
		
		$php=substr($php, 5);

		$pos=strpos($php, ' ');
		if ($pos !== false) {
			$php=substr($php, 0, $pos);
		} 
		if (empty($php))
			$php='/usr/bin/php';
		return $php;
	}
	
	//Функция возвращающая символ BOM кодировки UTF-8//
	function bom() {
		return pack('CCC', 0xef, 0xbb, 0xbf);
	}
	
	public function email($mail='') {
	  $mail=trim((string)$mail);

	  if (strlen($mail)==0) 
		return null;
	  if (!preg_match("/^[a-z0-9_-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|".
	  "edu|gov|arpa|info|biz|inc|name|[a-z]{2,4})|[0-9]{1,3}\.[0-9]{1,3}\.[0-".
	  "9]{1,3}\.[0-9]{1,3})$/is",$mail))
		return null;
	  return $mail;
	}
	
	public function load($url='') {
		$result='';
		$url=trim((string)$url);
		if (!empty($url)) {
			if (strtolower(substr($url, 0, 7))=='http://' || strtolower(substr($url, 0, 8))=='https://') {
				$resource = curl_init();
				curl_setopt($resource, CURLOPT_URL, $url);
				curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($resource, CURLOPT_HEADER, 0);
				
				$result = curl_exec($resource);
				
				curl_close($resource);
			} else
				$result=file_get_contents($url);
		}
		return $result;
	}	

	public function utf8($struct) {
		foreach ($struct as $key => $value) {
			if (is_array($value)) {
				$struct[$key] = $this->utf8($value);
			}
			elseif (is_string($value)) {
				$struct[$key] = utf8_encode($value);
			}
		}
		return $struct;
	}
	
	/**
	 * Метод возвращающий имя файла из пути
	 * @param string	$string				- путь к файлу например из константы __FILE__
	 *
	 * @return string
	 */
	public function filename($string='', $body=false, $extension=false) {
		if ($string) {
			$string=str_replace('\\', '/', $string);
			$STRING=explode('/', $string);
			$string=array_pop($STRING);
			unset($STRING);
			if ($body) {
				$STRING=explode('.', $string);
				if ($extension) {
					if (count($STRING)>1)
						$string=array_pop($STRING);
					else
						$string='';
				} 
				else {
					if (count($STRING)>0) {
						$STRING=array_slice($STRING, 0, count($STRING)-1);
						$string=implode('.', $STRING);
					}
				}
			}
		}
		return $string;
	}
	 
	/**
	 * Метод генерирования паролей
	 * @param int	$num				- длина пароля
	 *
	 * @return string
	 */
	public function password($number = 8) {
		return substr ( md5 ( uniqid ( microtime () ) ), 0, $number );
	}	
	
	/**
	 * Метод формирования SQL строки поиска
	 * @param string	$PARAMS['string']				- строка поиска
	 * @param string	$PARAMS['field']				- название поля в базе данных
	 * @param int	$PARAMS['operand']				- логическая операция: 0 - AND, 1 - OR
	 * @param string	$PARAMS['charset']				- выходная кодировка символов
	 * @param string	$PARAMS['morphy']				- поиск по всем словоформам
	 * @param string	$PARAMS['fulltext']				- полнотекстовый поиск MATCH, если не задан, то поиск оператором LIKE      	
	 *  
	 * @return string
	 */	
	public function sql_search($PARAMS=array())
	{
		$string=isset($PARAMS['string'])?$PARAMS['string']:'';
		$field=isset($PARAMS['field'])?mb_ereg_replace("/[^a-z0-9_\-]/i", '', $PARAMS['field']):'';
		$operand=isset($PARAMS['operand'])?mb_ereg_replace("/[^a-z0-9]/i", '', $PARAMS['operand']):0;
		$charset=isset($PARAMS['charset'])?$PARAMS['charset']:'';
		$morphy=isset($PARAMS['morphy'])?$PARAMS['morphy']:'';
		$fulltext=isset($PARAMS['fulltext'])?$PARAMS['fulltext']:'';
		
		$sql='';
		if ($string)
		{
			$string=mb_ereg_replace("/[^a-zа-я]/i", ' ', $string);
					
			$string=$this->chopper($string);
			$ARRAY=explode(' ', $string);
			
			if ($morphy)
				$Morphy = $this->Framework->morphy;
	
			if (count($ARRAY)>0)
			{
				$ARRAY=$this->strtoupper($ARRAY);
				$ARRAY=$this->duplicate($ARRAY);
				foreach($ARRAY AS $val) {
					$WORDS=array();					

					if ($morphy)
						$WORDS = $Morphy->get( $val );
					
					if (!isset($WORDS[$val]) || !$WORDS[$val]) 
						$WORDS[$val]=$val;

					$sql1='';
					foreach ($WORDS AS $VALUE) {
						if (!is_array($VALUE))
							$VALUE=array($VALUE);
						foreach ($VALUE AS $value) {
							if ($fulltext)
								$sql1.=($sql1?' OR ':'')."MATCH (".$field.") AGAINST ('".$value."')";								
							else 
								$sql1.=($sql1?' OR ':'').$field." LIKE '%".$value."%'";
						}
					}
					$sql.=($sql?($operand?' OR ':' AND '):'').' ('.$sql1.' ) ';
				}
				$sql=' ('.$sql.') ';
			}
		}

		if ($charset && $this->Framework->CONFIG['charset']!=$charset)
			$sql=iconv($this->Framework->CONFIG['charset'], $charset, $sql);
				
		return $sql;
	}

	/**
	 * Метод получающий ссылку на элемент массива по списку ключей
	 * @param pointer array	$ARRAY				- массив
	 * @param array	$KEYS				- ключи
	 * @return pointer mixed
	 */	
	public function &get_array_element_by_keys(&$ARRAY,$KEYS=array(),$counter=0)
	{
		$ELEMENT=false;
		
		if (is_array($ARRAY))
		{
			if (is_array($KEYS)&&count($KEYS)>0) 
			{
					if ($counter+1<count($KEYS)&&isset($KEYS[$counter]))
					{
					if (isset($ARRAY[$KEYS[$counter]]))	$ELEMENT=&$this->get_array_element_by_keys($ARRAY[$KEYS[$counter]],$KEYS,$counter+1);
					} elseif (isset($KEYS[$counter])) $ELEMENT=&$ARRAY[$KEYS[$counter]];
					else $ELEMENT=&$ARRAY;
			} else $ELEMENT=&$ARRAY;
		}//if
		
	return $ELEMENT;
	}
	
	/**
	 * Метод удаляющий лишние пробелы из строки
	 * @param string	$string				- строка
	 * @return string
	 */	
	public function chopper($val)
	{
		$mem="";
		while ($mem!=$val)
		{
			$mem=$val;
			$val=str_replace("  "," ",$val);
		}
		$val=trim($val);
		return $val;
	}

	/**
	 * Метод возвращающий расширение файла
	 * @param string	$string				- строка
	 * @return string
	 */	
	public function FileExtension($string='')
	{
		if ((string)$string) {
			$STRING=explode('.', (string)$string);
			if (count($STRING)>1)
				return array_pop($STRING);
			else
				return '';
		}
		return false;
	}	
	
	/**
	 * Метод удаляющий дубли в массиве
	 * @param array	$ARRAY				- массив
	 * @return array
	 */
	public function duplicate($ARRAY=array())
	{
		$RETURN=array();
		if (is_array($ARRAY)) {
			$ARRAY=array_flip($ARRAY);			
			foreach($ARRAY AS $key=>$value)
				if (!is_array($value))
					$RETURN[$value]=$key;
		}
		unset($ARRAY);
		return $RETURN;
	}	
	
	/**
	 * Метод переводящий массив в верхний регистр
	 * @param array	$ARRAY				- массив
	 * @return array
	 */
	public function strtoupper($ARRAY=array())
	{
		$RETURN='';
		
		if (!isset($ARRAY) || !$ARRAY || is_object($ARRAY))
			return $RETURN;
		
		if (!is_array($ARRAY)) 
			$RETURN=mb_strtoupper($ARRAY);
		else								
			foreach($ARRAY AS $key=>$value) {
				if (!is_array($value))
					$RETURN[$key]=mb_strtoupper($value);
			}
		unset($ARRAY);
		return $RETURN;
	}

	/**
	 * Метод превращающий объект в ассоциативный массив
	 * @param object или array	$Object				- объект или массив
	 * @return array
	 */	
	public function objectToArray($Object=false) {
		if (isset($Object) && (is_object ($Object) || is_array($Object))) {
			$DATA=array();
			foreach ($Object as $key=>$value) {
				if (is_array($value) || is_object($value))
					$DATA[$key]=$this->objectToArray($value);
				else
					$DATA[$key]=$value;
			}
			return $DATA;
		}
		elseif (isset($Object))
			return $Object;
		else
			return false;
	}

	/**
	 * Метод удаляющий пустые записи в массиве
	 * @param array	- массив
	 * @return void
	 */	
	public function array_empty(&$ARRAY) {
		if (!empty($ARRAY) && is_array($ARRAY)) {
			foreach ($ARRAY as $key=>&$value) {
				if (is_array($value))
					$this->array_empty($value);
				elseif (empty($value) && !is_numeric($value)) 
					unset($ARRAY[$key]);

			}			
		}
		return null;
	}
	
	/**
	 * Метод удаляющий концевые пробелы в массиве
	 * @param array	- массив
	 * @return void
	 */	
	public function array_trim(&$ARRAY) {
		if (!empty($ARRAY) && is_array($ARRAY)) {
			foreach ($ARRAY as $key=>&$value) {
				if (is_array($value))
					$this->array_trim($value);
				else
					$value=trim($value);

			}			
		}
		return null;
	}	

	/**
	 * Метод удаляющий пустые записи в массиве
	 * @param array	- массив
	 * @return void
	 */	
	public function &array_unset(&$ARRAY , $EXEPTION=array(), $invert=false) {
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
	
	/**
	 * Метод превращающий массив в query string
	 * @param array	$ARRAY				- массив
	 * @return string
	 */
	public function arrayToQueryString($ARRAY=array()) {
		$data='';
		foreach($ARRAY as $key=>$value) {
			if (!is_array($value) && $value)
				$data.=($data?'&':'').rawurlencode($key).'='.rawurlencode($value);
		}
		return $data;
	}

	/**
	 * Метод генерирующий текст на основе случайных синонимов
	 * @param string	$string				- строка
	 * @param string	$open				- открывающийся тег
	 * @param string	$close				- закрывающийся тег
	 * @param string	$div				- разделитель синонимов
	 * 
	 * @return string
	 */
	public function sinonim($string='', $open='{', $close='}', $div='|') {
		
	
		while (($start=mb_strpos($string, $open))!==false) {
			$end=mb_strpos($string, $close, $start);
			if ($end===false)
				break;
			$sinonims=mb_substr($string, $start+1, $end-$start-1);
			$SINONIMS=explode($div, $sinonims);
			$string=mb_substr($string, 0, $start).$SINONIMS[rand(0, count($SINONIMS)-1)].mb_substr($string, $end+1);
		}//\while
	
		return $string;
	}

	
	/**
	 * Метод генерирующий текст на основе случайных синонимов
	 * @param string	$string				- строка
	 * @param string	$open				- открывающийся тег
	 * @param string	$close				- закрывающийся тег
	 * @param string	$div				- разделитель синонимов
	 *
	 * @return string
	 */
	public function sinonims($STRING='', $open='{', $close='}', $div='|') {		
		
		if (!is_array($STRING)) 
			$STRING=array($STRING);
		foreach ($STRING as $key=>$value) {
			$start=mb_strpos($value, $open);
			if ($start!==false) {
				$end=mb_strpos($value, $close, $start);
				if ($end!==false) {
					$sinonims=mb_substr($value, $start+1, $end-$start-1);
					$SINONIMS=explode($div, $sinonims);
					$STRING1=array();
					for ($i=0; $i<count($SINONIMS); $i++) {					
						$STRING1[]=mb_substr($value, 0, $start).$SINONIMS[$i].mb_substr($value, $end+1);					
					}
					$STRING[$key]='';					
					$STRING=array_merge($STRING, $this->sinonims($STRING1, $open, $close, $div));
				}
			}
		}
		
		$RETURN=array();
		foreach ($STRING as $value)
			if ($value)
				$RETURN[]=$value;	
		return $RETURN;
	}

	
	/**
	 * Метод разрезающий строку на две части по разделителю
	 * @param string	$string				- строка
	 * @param string	$div				- разделитель синонимов
	 *
	 * @return array
	 */	
	public function half ($string='', $div=',') {
		
		
		$left=$string;
		$right='';
		if (($position=mb_strpos($string, $div))!==false) {
			$left=mb_substr($string, 0, $position);
			$right=mb_substr($string, $position+mb_strlen($div));
		}
			
		return array($left, $right);
	}
	
	/**
	 * Метод делающий первую букву заглавной остальные с маленькой
	 * @param string	$string				- строка
	 *
	 * @return string
	 */
	public function firstupper($string='', $lower=false) {
		
		if ($string) {
			if ($lower)
				$string=mb_strtolower($string);
			$string=mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
		}
		return $string;
	}
	
	/**
	 * Метод обрезающий строку
	 * @param string	$string				- строка
	 *
	 * @return string
	 */
	public function truncate($string='', $length=80, $add='...', $space=true) {
		
		
		if (!$string || !$length>0)
			return trim($string).$add;
		
		if (mb_strlen($string)<=$length)
			$str=trim($string);
		else {
			$str=mb_substr($string, 0, $length);
			if ($space) {
				if (mb_substr($str, -1, 1)==' ' || mb_substr($str, -1, 1)=='.' || mb_substr($str, -1, 1)=='!' || mb_substr($str, -1, 1)=='?' || mb_substr($str, -1, 1)==',' || mb_substr($str, -1, 1)==';' || mb_substr($str, -1, 1)==':' || mb_substr($str, -1, 1)=='-')
					$str=mb_substr($str, 0, -1);
				else 
					for ($i=mb_strlen($str)-1; $i>=0; $i--)  
						if (mb_substr($str, $i, 1)==' ' || mb_substr($str, $i, 1)=='.' || mb_substr($str, $i, 1)=='!' || mb_substr($str, $i, 1)=='?' || mb_substr($str, $i, 1)==',' || mb_substr($str, $i, 1)==';' || mb_substr($str, $i, 1)==':' || mb_substr($str, $i, 1)=='-') {
							$str=mb_substr($str, 0, $i);
							break;
						}
			}
		}
		
		$str=trim($str);
		if (mb_substr($str, -1, 1)==' ' || mb_substr($str, -1, 1)=='.' || mb_substr($str, -1, 1)=='!' || mb_substr($str, -1, 1)=='?' || mb_substr($str, -1, 1)==',' || mb_substr($str, -1, 1)==';' || mb_substr($str, -1, 1)==':' || mb_substr($str, -1, 1)=='-')
			$str=mb_substr($str, 0, -1);
		return trim($str).$add;
	}
	
	/**
	 * Метод обрезающий строку по количеству слов
	 * @param string	$string				- строка
	 *
	 * @return string
	 */
	public function words($string='', $length=7) {
		
		
		if (!$string || !$length>0)
			return trim($string);
		
		$string=trim($this->chopper(mb_ereg_replace ('[^А-Яа-яA-Za-z0-9]', ' ', $string)));
		$STRING=explode(' ', $string);
		$string='';
		for ($i=0; $i<$length; $i++)
			if (isset($STRING[$i]))
				$string.=($string?' ':'').$STRING[$i];
			else
				break;
		unset($STRING);
		return $string;
	}
	
	/**
	 * Метод транслитерирующий строку
	 * @param string	$string				- строка
	 * 
	 * @return string
	 */
	public function translit($string='') {
		
		$len = mb_strlen ( $string );
		$result = "";
		for($i = 0; $i < $len; $i ++) {
			$letter = mb_substr ( $string, $i, 1 );
			switch ($letter) {
				case 'а' :
					{
						$result .= 'a';
						break;
					}
				case 'А' :
					{
						$result .= 'A';
						break;
					}
				case 'б' :
					{
						$result .= 'b';
						break;
					}
				case 'Б' :
					{
						$result .= 'B';
						break;
					}
				case 'в' :
					{
						$result .= 'v';
						break;
					}
				case 'В' :
					{
						$result .= 'V';
						break;
					}
				case 'г' :
					{
						$result .= 'g';
						break;
					}
				case 'Г' :
					{
						$result .= 'G';
						break;
					}
				case 'д' :
					{
						$result .= 'd';
						break;
					}
				case 'Д' :
					{
						$result .= 'D';
						break;
					}
				case 'е' :
					{
						$result .= 'e';
						break;
					}
				case 'Е' :
					{
						$result .= 'E';
						break;
					}
				case 'ё' :
					{
						$result .= 'e';
						break;
					}
				case 'Ё' :
					{
						$result .= 'E';
						break;
					}
				case 'ж' :
					{
						$result .= 'zh';
						break;
					}
				case 'Ж' :
					{
						$result .= 'ZH';
						break;
					}
				case 'з' :
					{
						$result .= 'z';
						break;
					}
				case 'З' :
					{
						$result .= 'Z';
						break;
					}
				case 'и' :
					{
						$result .= 'i';
						break;
					}
				case 'И' :
					{
						$result .= 'I';
						break;
					}
				case 'й' :
					{
						$result .= 'I';
						break;
					}
				case 'Й' :
					{
						$result .= 'i';
						break;
					}
				case 'к' :
					{
						$result .= 'k';
						break;
					}
				case 'К' :
					{
						$result .= 'K';
						break;
					}
				case 'л' :
					{
						$result .= 'l';
						break;
					}
				case 'Л' :
					{
						$result .= 'L';
						break;
					}
				case 'м' :
					{
						$result .= 'm';
						break;
					}
				case 'М' :
					{
						$result .= 'M';
						break;
					}
				case 'н' :
					{
						$result .= 'n';
						break;
					}
				case 'Н' :
					{
						$result .= 'N';
						break;
					}
				case 'о' :
					{
						$result .= 'o';
						break;
					}
				case 'О' :
					{
						$result .= 'O';
						break;
					}
				case 'п' :
					{
						$result .= 'p';
						break;
					}
				case 'П' :
					{
						$result .= 'P';
						break;
					}
				case 'р' :
					{
						$result .= 'r';
						break;
					}
				case 'Р' :
					{
						$result .= 'R';
						break;
					}
				case 'с' :
					{
						$result .= 's';
						break;
					}
				case 'С' :
					{
						$result .= 'S';
						break;
					}
				case 'т' :
					{
						$result .= 't';
						break;
					}
				case 'Т' :
					{
						$result .= 'T';
						break;
					}
				case 'у' :
					{
						$result .= 'u';
						break;
					}
				case 'У' :
					{
						$result .= 'U';
						break;
					}
				case 'ф' :
					{
						$result .= 'f';
						break;
					}
				case 'Ф' :
					{
						$result .= 'F';
						break;
					}
				case 'х' :
					{
						$result .= 'h';
						break;
					}
				case 'Х' :
					{
						$result .= 'H';
						break;
					}
				case 'ц' :
					{
						$result .= 'ts';
						break;
					}
				case 'Ц' :
					{
						$result .= 'TS';
						break;
					}
				case 'ч' :
					{
						$result .= 'ch';
						break;
					}
				case 'Ч' :
					{
						$result .= 'CH';
						break;
					}
				case 'ш' :
					{
						$result .= 'sh';
						break;
					}
				case 'Ш' :
					{
						$result .= 'SH';
						break;
					}
				case 'щ' :
					{
						$result .= 'sch';
						break;
					}
				case 'Щ' :
					{
						$result .= 'SCH';
						break;
					}
				case 'ъ' :
				case 'Ъ' :
					{
						$result .= '';
						break;
					}
				case 'ы' :
					{
						$result .= 'y';
						break;
					}
				case 'Ы' :
					{
						$result .= 'Y';
						break;
					}
				case 'ь' :
				case 'Ь' :
					{
						$result .= '';
						break;
					}
				case 'э' :
					{
						$result .= 'e';
						break;
					}
				case 'Э' :
					{
						$result .= 'E';
						break;
					}
				case 'ю' :
					{
						$result .= 'yu';
						break;
					}
				case 'Ю' :
					{
						$result .= 'YU';
						break;
					}
				case 'я' :
					{
						$result .= 'ya';
						break;
					}
				case 'Я' :
					{
						$result .= 'YA';
						break;
					}
				default :
					{
						$result .= $letter;
						break;
					}
			} // switch
		} // for
	
		return $result;
	}

}
?>