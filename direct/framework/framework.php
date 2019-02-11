<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс ядра фреймворка                           ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork;

include_once(dirname(__FILE__).'/none.php');
include_once(dirname(__FILE__).'/common.php');

final class Framework {
	//Переменные//
	private $MODULE=array();//Путь к подгружаемому классу
	private $OBJECT=array();//Массив объектов
	
	private $FRAMEWORK=array(
		'id'=>null,//Уникальный идентификатор пользователя
		'version'=>'1.0.0',//Версия системы
		'charset'=>'UTF-8',//Кодировка системы
		'session'=>null,//Сессия пользователя
		'http'=>false,//Работа по HTTP протоколу(true) или из консоли (false)
		'ip'=>'',//IP адрес
		'address'=>'',//IP адрес сервера
		'port'=>'',//порт сервера
		'time'=>0,//Время старта программы в секундах
		'microtime'=>0,//Время старта программы в микросекундах
		'dir'=>'',//Текущая директория
	);
	
	private $CONFIG=array(
		//Общие настройки//		
		'name'=>'FrameWork',//Название системы
		'timezone'=>'Europe/Moscow',//Временная зона
		'session_name'=>'framework_session',//Название сессии
		'cookie_name'=>'framework_id',//Название переменной куки содержащей уникальный идентификатор пользователя
		'cookie_expire'=>15552000,//Время хранения куки
		'param'=>'param',//Разделитель для параметров в строке url
		'debug'=>true,//Включить отладку 1 - простая отладка, 2 - мониторинг запросов в базу
		'DEBUG'=>array(
			'debug'=>true,//Состояние отладки: 1 - вкл., 0 - выкл.
			'all'=>0,//Все ошибки: 1 - все, 0 - только критические
			'trace'=>0,//Трассировка пути до метода ошибки: 1 - вкл., 0 - выкл.
			'sql'=>0,//Трассировка медленных запросов в базу данных в секундах
		),
		//\Общие настройки//
	
		//Настройки баз данных (будет установлено столько соединений к базам данных, сколько элементов в массиве)//
		'DATABASE'=>array(
						'class'=>'mysql',//Класс подключения к базе данных (поддерживаются: mysql, sqlite)
						'host'=>'localhost',//Хост
						'port'=>'3306',//Порт
						'user'=>'root',//Имя пользователя
						'password'=>'',//Пароль
						'name'=>'framework',//Название базы данных
						'charset'=>'UTF8',//Кодировка базы данных
						'prefix'=>'framework_',//Префикс таблиц
			),
		
		//\Настройки баз данных//
	
		//Настройки шаблонизатора//
		'TEMPLATE'=>array(
						'name'=>'smarty',//Класс шаблонизатора (поддерживаются: smarty, php, json)
						'dir'=>'../../files/templates/',//Путь к папке с шаблонами
						'compile'=>'../../files/templates_c/',//Путь к папке с откомпилированными шаблонами
						'cache'=>'../../files/templates_cache/',//Путь к папке с закешированными шаблонами
						'config'=>'../../files/templates_config/',//Путь к папке с конфигом для шаблонов
			),
		
		//\Настройки шаблонизатора//	
	
		//Настройка путей относительно текущей папки//
		'document_root'=>'../../',//Путь к корню сайта
		'dir'=>'../',//Путь к папке с фреймворком
		'files_dir'=>'../../files/',//Путь к папке с медиа файлами
		'cache_dir'=>'../../files/cache/',//Путь к папке с кешем
		//\Настройка путей относительно текущей папки//	
		
	);
	//\Переменные//

	
	//Синглтон//
	static private $singleton = NULL;
	
	static function singleton($CONFIG=array())
	{
		if (self::$singleton == NULL)
		{
			self::$singleton = new Framework($CONFIG);
		}
		return self::$singleton;
	}
	//\Синглтон//
	
	private function __construct($CONFIG=array())
	{
		
		//Инициализируем переменные//
		$this->FRAMEWORK['http']=!empty($_SERVER['HTTP_HOST'])?true:false;	
		$this->FRAMEWORK['dir']=dirname(__FILE__).'/';
		$this->FRAMEWORK['ip']=!empty($_SERVER['HTTP_X_REAL_IP'])&&$_SERVER['HTTP_X_REAL_IP']!='127.0.0.1'?$_SERVER['HTTP_X_REAL_IP']:(!empty($_SERVER ['REMOTE_ADDR'])?$_SERVER ['REMOTE_ADDR']:'');
		$this->FRAMEWORK['address']=!empty($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'127.0.0.1';
		$this->FRAMEWORK['port']=!empty($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:0;
		$this->FRAMEWORK['php']=phpversion();
		$allow_url_fopen=ini_get('allow_url_fopen');
		$this->FRAMEWORK['fopen']=!empty($allow_url_fopen)?true:false;
		$this->FRAMEWORK['curl']=function_exists('curl_init')?true:false;
		//\Инициализируем переменные//
		
		//Увеличиваем объем памяти и времени//
		@ini_set ( 'allow_call_time_pass_reference', '1' );
		@ini_set ( 'memory_limit', '16048M' );
		if ($this->FRAMEWORK['http'])
			@ini_set ('max_execution_time', '3600');
		else
			@ini_set ('max_execution_time', '0');
		@ini_set ('default_socket_timeout', '900');
		@ini_set('upload_max_filesize', '256M');
		@ini_set('post_max_size', '256M');
		@ini_set('max_input_vars', '1000000');
		//\Увеличиваем объем памяти и времени//
		
		//Настройки PHP//
		if (function_exists('magic_quotes_runtime'))
			if (ini_get('magic_quotes_runtime'))
				magic_quotes_runtime(false);
		if (ini_get('magic_quotes_gpc'))
			@ini_set('magic_quotes_gpc', 0);
		if (ini_get('magic_quotes_runtime'))
			@ini_set('magic_quotes_runtime', 0);
		if (ini_get('magic_quotes_sybase'))
			@ini_set('magic_quotes_sybase', 0);
		//\Настройки PHP//
		
		//Подключаем конфиг//
		$dir=dirname(__FILE__).'/';
		
		if (!empty($CONFIG['DATABASE']) && is_array($CONFIG['DATABASE'])) {
			if (!isset($CONFIG['DATABASE'][0]))
				$CONFIG['DATABASE']=array($CONFIG['DATABASE']);
			$this->CONFIG['DATABASE']=$CONFIG['DATABASE'];
		}
		if (isset($CONFIG['DATABASE']))
			unset($CONFIG['DATABASE']);
		
		if (!empty($CONFIG['TEMPLATE']) && is_array($CONFIG['TEMPLATE'])) {
			$this->CONFIG['TEMPLATE']=$CONFIG['TEMPLATE'];
			unset($CONFIG['TEMPLATE']);
		}
		if (isset($CONFIG['TEMPLATE']))
			unset($CONFIG['TEMPLATE']);		
			
		$this->CONFIG['document_root']=$dir.$this->CONFIG['document_root'];
			
		$this->CONFIG['dir']=$dir.$this->CONFIG['dir'];
			
		$this->CONFIG['files_dir']=$dir.$this->CONFIG['files_dir'];
				
		$this->CONFIG['cache_dir']=$dir.$this->CONFIG['cache_dir'];
		
		if (!empty($CONFIG['http_dir'])) 
			if (substr($CONFIG['http_dir'], -1)=='/')
				$CONFIG['http_dir']=substr($CONFIG['http_dir'], 0, -1);
		
		if (!empty($CONFIG) && is_array($CONFIG))
			$this->CONFIG=array_merge($this->CONFIG, $CONFIG);
		$this->CONFIG['debug']=$this->CONFIG['DEBUG']['debug'];
		//\Подключаем конфиг//
		
		//Устанавливаем временную зону и время//
		date_default_timezone_set($this->CONFIG['timezone']);
		$this->FRAMEWORK['time']=time();
		$this->FRAMEWORK['microtime']=microtime(true);
		//\Устанавливаем временную зону и время//

		//Устанавливаем кодировку мультибайтовых функций//
		if (function_exists('mb_internal_encoding'))
			if (mb_internal_encoding()!=$this->FRAMEWORK['charset'])
				mb_internal_encoding($this->FRAMEWORK['charset']);
		@setlocale(LC_ALL, "C");
		@ini_set("mbstring.language", "neutral");
		@ini_set("mbstring.regex_encoding", $this->FRAMEWORK['charset']);
		@ini_set("mbstring.encoding_translation", true);
		@ini_set("mbstring.http_input", "pass");
		@ini_set("mbstring.http_output", "pass");
		@ini_set("mbstring.detect_order", "auto");
		@ini_set("mbstring.substitute_character", "none");
		//\Устанавливаем кодировку мультибайтовых функций//
		
		//Инициализируем генератор случайных чисел//
		srand ( intval(microtime (true) * 10000) );
		//\Инициализируем генератор случайных чисел//
		
		//Стартуем сессию//
		if ($this->FRAMEWORK['http']) {
			if (@session_id()=='') {
				if ((!empty($_GET[$this->CONFIG['session_name']]) && is_string($_GET[$this->CONFIG['session_name']]))|| (!empty($_POST[$this->CONFIG['session_name']]) && is_string($_POST[$this->CONFIG['session_name']])))
					@session_id((!empty($_GET[$this->CONFIG['session_name']])?$_GET[$this->CONFIG['session_name']]:$_POST[$this->CONFIG['session_name']]));
				@session_name($this->CONFIG['session_name']);
				@session_set_cookie_params(0, '/');
				@session_start();
			}
			$this->FRAMEWORK['session']=@session_id();
		}
		//\Стартуем сессию//
	
		//Устанавливаем идентификатор пользователя//
		if (!empty($_COOKIE[$this->CONFIG['cookie_name']]))
			$this->FRAMEWORK['id']=$_COOKIE[$this->CONFIG['cookie_name']];
		else	
			$this->FRAMEWORK['id']=$this->FRAMEWORK['session'];
		if (!$this->FRAMEWORK['id']) 
			$this->FRAMEWORK['id']=uniqid(rand());
		@setcookie ($this->CONFIG['cookie_name'], $this->FRAMEWORK['id'], $this->FRAMEWORK['time'] + $this->CONFIG['cookie_expire'], '/');
		//\Устанавливаем идентификатор пользователя//
		
		//Добавляем служебные переменные в конфиг//
		$this->CONFIG=array_merge($this->CONFIG, $this->FRAMEWORK);
		//\Добавляем служебные переменные в конфиг//
		
		//Устанавливаем обработчик ошибок//
		error_reporting(E_ALL);
		ini_set('display_errors','On');
		set_error_handler(function ($errno, $errstr, $errfile, $errline) {
			switch ($errno) {
				case E_NOTICE:
				case E_USER_NOTICE:
					$error = 'Notice';
					break;
				case E_WARNING:
				case E_USER_WARNING:
					$error = 'Warning';
					break;
				case E_ERROR:
				case E_USER_ERROR:
					$error = 'Fatal Error';
					break;
				default:
					$error = 'Unknown';
					break;
			}

			Framework::singleton()->library->error->set($error.' ('.$errno.')'.': '.$errstr.'.', $errfile, '', '', '', $errline, true);
			return true;
		});
		//\Устанавливаем обработчик ошибок//
	}
	
	public function __invoke() {
		return self::singleton();
	}
	
	private function __clone()
	{
	}
	
	public function __destruct()
	{
	
	}	
	
	public function __set($name, $value=null) {
		$name=strtolower($name);
		if ($name=='config')
			$this->CONFIG=$value;
		else
			$this->library->error()->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
	}
	
	public function __get($name) {
		$name=strtolower($name);
		if ($name=='config' && empty($this->MODULE))
			return $this->CONFIG;
		elseif ($name=='template' && empty($this->MODULE)) {
			$this->MODULE=array($name);
			return $this->__caller($name);
		} elseif ($name=='db' && empty($this->MODULE)) {
			$name='database';
			$this->MODULE=array($name);
			return $this->__caller($name);
		} else
			return $this->__caller ($name);	
	}	
	
	public function __call ($name, $ARGUMENT=array()) {
		if ($name=='template' && empty($this->MODULE)) {
			$this->MODULE=array($name);
			return $this->__caller($name, $ARGUMENT);
		} elseif ($name=='db' && empty($this->MODULE)) {
			$name='database';
			$this->MODULE=array($name);
			return $this->__caller($name, $ARGUMENT);
		} else
			return $this->__caller($name, $ARGUMENT);
	}
	
	
	public static function __callStatic ($name, $ARGUMENT=array()) {	
		return None::singleton();
	}
	
	private function __caller ($name, $ARGUMENT=array()) {
		if ($name) {
			$name=strtolower($name);
			$this->MODULE[]=$name;
			$dir=$this->CONFIG['module_dir'].implode('/', $this->MODULE);
			$file=$dir.'.php';
			if (file_exists($file)) {
				$class='\\framework\\module\\'.implode('\\', $this->MODULE);
				$this->MODULE=array();
				if (!isset($this->OBJECT[$class]) || !is_object($this->OBJECT[$class])) {
						include_once($file);
						if (class_exists($class)) {
							$this->OBJECT[$class]=new $class((isset($ARGUMENT[0])?$ARGUMENT[0]:0));
							return $this->OBJECT[$class];
						} 
						else 
							$this->library->error()->set('Нет объекта с таким именем: '.$class.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						
				} 
				elseif (isset($this->OBJECT[$class]) && is_object($this->OBJECT[$class])) 
					return $this->OBJECT[$class];
				else 
					$this->library->error()->set('Нет такого объекта: "'.$class.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			} elseif (is_dir($dir)) {
				return $this;
			} else {
				$this->MODULE=array();
				$this->library->error()->set('Нет файла с таким именем: '.$file, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				return None::singleton();
			}
		}
		else {
			$this->MODULE=array();
			$this->library->error()->set('Не задано имя модуля.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);	
		}
		return None::singleton();
	}
	
	public function __execute($name='', $PARAM=array()) {
		if (!empty($name) && strpos((string)$name, '/')!==false) {
			$MODULE=explode('/', (string)$name);
			$this->MODULE=array();
			foreach($MODULE as &$value)
				if (!empty($value))
					$this->MODULE[]=$value;
			if (!empty($this->MODULE)) {
				$method=array_pop($this->MODULE);
				if (!empty($this->MODULE)) {
					$name=array_pop($this->MODULE);
					$Object=$this->__caller($name);
					return $Object->{$method}($PARAM);
				}
			}
		}
		return null;
	}
	
}//\class
?>