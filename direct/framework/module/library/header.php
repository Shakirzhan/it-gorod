<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с заголовками           ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Header {
	private $Framework=null;
	private $HEADERS=array();
	private $DEFAULT=array(
							'http'=>array(
								'Content-Type'=>'text/html; charset=utf-8',
								'Cache-Control'=>'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
								)
							);
	
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
	
	public function set($HEADERS=array()) {
		if (is_array($HEADERS)) {
			foreach ($HEADERS as $key=>$value)
				if ($value)
					$this->HEADERS[$key]=$value;
		}
		elseif (!empty($HEADERS) && is_string($HEADERS)) 
			$this->HEADERS[]=$HEADERS;
	
	}
	
	public function get($name='') {
		if (!empty($name) && is_array($this->DEFAULT[$name])) {
			if (method_exists($this, $name)) {
				$HEADERS=$this->$name();
				$HEADERS=array_merge($HEADERS, $this->DEFAULT[$name], $this->HEADERS);
			}
			else
				$HEADERS=array_merge($this->DEFAULT[$name], $this->HEADERS);
		}
		else
			$HEADERS=&$this->HEADERS;
			
		if (is_array($HEADERS)) 
			foreach ($HEADERS as $key=>$value)
				if ($key && !is_numeric($key) && $value)
					header($key.': '.$value);
				elseif (is_numeric($key) && $value)
					header($value);
	
	}	
	
	public function delete($name='') {
		if (!empty($name) && isset($this->HEADERS[$name]))
			unset($this->HEADERS[$name]);
		else
			$this->HEADERS=array();
	}
	
	private function http() {
		$HEADERS['Date']=gmdate ( "D, d M Y H:i:s", $this->Framework->CONFIG['time'] ) . " GMT" ;
		$HEADERS['Last-Modified']=gmdate ( "D, d M Y H:i:s", $this->Framework->CONFIG['time'] ) . " GMT" ;
		$HEADERS['Expires']=gmdate ( "D, d M Y H:i:s", $this->Framework->CONFIG['time'] ) . " GMT" ;
		return $HEADERS;
	}

}//\class
?>