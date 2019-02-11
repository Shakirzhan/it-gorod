<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин обработка ошибок                   ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Error {
	private $Framework=null;
	private $error=0;
	private $ERROR=array();
	
	public function __construct() {
		$this->Framework=\FrameWork\Framework::singleton();
		
	}//\function

	public function __call ($name, $ARGUMENTS=array()) {		
		return $this->error;
	}//\function
	
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
		
	public function __get($name) {
		return $this->error;	
	}
	
	private function __clone()
	{
	}	
	
	public function trace($error='', $file='', $namespace='', $class='', $method='', $line='') {
		$error=(string)$error.((string)$file?' Файл: '.(string)$file:'').((string)$method?', метод: '.(string)$method:'').((string)$line?', строка: '.(string)$line:'').'.';
		if ($this->Framework->CONFIG['DEBUG']['trace'] && function_exists('debug_backtrace')) {
			$TRACE=debug_backtrace(0);
		}
		if (!empty($TRACE) && is_array($TRACE)) {
			$error.=' Стек вызовов:';
			foreach($TRACE as $VALUE) {
				$error.=' '.(!empty($VALUE['file'])?$VALUE['file']:'').(!empty($VALUE['line'])?' ('.$VALUE['line'].'): ':'').(!empty($VALUE['class'])?$VALUE['class']:'').(!empty($VALUE['type'])?$VALUE['type']:'').(!empty($VALUE['function'])?$VALUE['function']:'').'.';
			}
		}
		return $error;
	}
	
	public function set($error='', $file='', $namespace='', $class='', $method='', $line='', $all=false) {
		if (!empty($this->Framework->CONFIG['debug']) && $this->Framework->CONFIG['debug'] && (empty($all) || ($this->Framework->CONFIG['DEBUG']['all'] && $all))) {
			$error=$this->trace($error, $file, $namespace, $class, $method, $line);
			if ($this->Framework->db->connection() && empty($this->Framework->CONFIG['DEBUG']['sql'])) {
				$this->Framework->model->error->set(array(
					'key'=>$method,
					'file'=>$file,
					'line'=>$line,
					'name'=>'',
					'value'=>$error,
					'session'=>$this->Framework->CONFIG['microtime']*10000,
					'datetime'=>date('Y-m-d H:i:s'),
				));
			} else
				$this->ERROR[]=array(
					'key'=>$method,
					'file'=>$file,
					'line'=>$line,
					'name'=>'',
					'value'=>$error,
					'session'=>$this->Framework->CONFIG['microtime']*10000,
					'datetime'=>date('Y-m-d H:i:s'),
				);
		}
		if (empty($all))
			$this->error++;		
		return $error;
	}
	
	public function get($print=false, $console=false) {
		if ($this->Framework->CONFIG['debug']) {
			if ($this->error>0 && $this->Framework->CONFIG['http']) {
				$content='';
				$content.= ($this->Framework->CONFIG['http']?'<br><hr><br>':'')."\r\n".'Отчет о работе программы: '.$this->Framework->CONFIG['name'].'/'.$this->Framework->CONFIG['version'].($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
				$content.= 'Время запуска: '.date('Y-m-d H:i:s', $this->Framework->CONFIG['time']).'. Время работы: '.(round(microtime(true)-$this->Framework->CONFIG['microtime'], 4)).' секунд. Память: '.memory_get_peak_usage(true).' байт'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";

				$ERROR=$this->Framework->model->error->get(array('session'=>$this->Framework->CONFIG['microtime']*10000));
				if (!empty($ERROR['ELEMENT']))
					$ERROR['ELEMENT']=array_merge($this->ERROR, $ERROR['ELEMENT']);
				else
					$ERROR['ELEMENT']=$this->ERROR;
				
				if ($this->error>0)
					$content.= 'Ошибки: '.$this->error.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";	
				
				if (!empty($ERROR['ELEMENT']))
					foreach ($ERROR['ELEMENT'] AS $key=>&$VALUE)
						$content.= (intval($key)+1).'. '.$VALUE['value'].((string)$VALUE['file']?' Файл: '.(string)$VALUE['file']:'').((string)$VALUE['key']?', метод: '.(string)$VALUE['key']:'').((string)$VALUE['line']?', строка: '.(string)$VALUE['line']:'').'.'.($this->Framework->CONFIG['http']?'<br><br>':'')."\r\n";
						
				//Удаляем старые ошибки//
				$this->Framework->model->error->delete(array('datetime'=>array('<='=>$this->Framework->library->time->day(!empty($this->Framework->model->config->CONFIG['error_day'])?-$this->Framework->model->config->CONFIG['error_day']:-1))));
				//\Удаляем старые ошибки//
				
				if ($console)
					$content=str_replace("\r\n", '\n', addslashes(strip_tags($content)));
				if ($print)
					echo $content;
				else 
					return $content;
			}
			
		}	
		return null;
	}	
	
	public function count() {
		return $this->error;
	}
	
	private function write($content='') {
		$error='';
		if ($content) {
			$dir=$this->Framework->CONFIG['files_dir'].'log/';
			$file=$dir.'error.txt';
			if (!is_dir($dir)) 
				if (!mkdir($dir, 0777, true)) 
					$error=($this->Framework->CONFIG['http']?'<br>':'')."\r\nНе удалось создать папку: ".$dir;
			
			if (file_exists($file))
				if (filesize ( $file ) > 1000000)
					unlink($file);
			
			$f=fopen($file, 'a');
			fwrite($f, $content);
			fclose($f);
		}
		return $error;
	}
	
	public function log($error='', $file='', $namespace='', $class='', $method='', $line='') {
		$content=$this->trace($error, $file, $namespace, $class, $method, $line);
		return $this->write(date('Y-m-d H:i:s')."\r\n".$content."\r\n");
	}

}//\class
?>