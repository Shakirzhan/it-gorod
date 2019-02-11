<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с HTTP                  ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

class Browser extends \FrameWork\Common {
	public $cookie='cookie.txt';
	public $cookie_dir='cookie';
	 
	public $HEADERS = array (
			'BROWSER' => array (
					'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
					// 'Accept-Encoding'=>'gzip, deflate',
					'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
					'Connection' => 'close',
					'Cookie' => '',
					'Referer' => '',
					'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0' 
			),
			'AJAX' => array (
					'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
					//'Accept-Encoding'=>'gzip, deflate',
					'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
					'Cache-Control' => 'no-cache',
					'Connection' => 'close',
					'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',					
					'Cookie' => '',
					'Pragma' => 'no-cache',
					'Referer' => '',
					'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0',
					'X-Requested-With' => 'XMLHttpRequest' 
			) 
	);
	
	public $COOKIE= array();
	
	// Accept application/xml, text/xml, */*
	// Accept-Encoding gzip, deflate
	// Accept-Language ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3
	// Cache-Control no-cache
	// Connection keep-alive
	// Content-Length 46
	// Content-Type application/x-www-form-urlencoded; charset=UTF-8
	// Cookie fblock=0; sblock=0;
	// __utma=197224235.1675209777.1352379326.1375185985.1375188098.7;
	// __utmz=197224235.1369207977.3.2.utmcsr=datafactory.narod.ru|utmccn=(referral)|utmcmd=referral|utmcct=/;
	// ucvid=dlPAl0icki; UZRef=http://ukoz.ru/;
	// uSID=%5E3LyZ7set%5Ea1TyfShLYBtiAf5eC4a3sK%5EdIVHZ4sMcNMIxAf1td61Uoo;
	// __utmc=197224235;
	// uReg2=1PK2lmS9EqIjFVW%218kzJIE8ptZHADjm6ERCk5nghDtpJ8Vc0CQy%21BHOfOh6u9nuSC5Levi%5EgLgoo;
	// __utmb=197224235.17.9.1375191737916; _ym_visorc=b
	// Host www.ucoz.ru
	// Pragma no-cache
	// Referer http://www.ucoz.ru/register
	// User-Agent Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20100101
	// Firefox/22.0
	// X-Requested-With XMLHttpRequest
	
	// POST запрос
	// Accept text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
	// Accept-Encoding gzip, deflate
	// Accept-Language ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3
	// Connection keep-alive
	// Cookie fblock=0; sblock=0;
	// __utma=197224235.1675209777.1352379326.1375188098.1375268026.8;
	// __utmz=197224235.1369207977.3.2.utmcsr=datafactory.narod.ru|utmccn=(referral)|utmcmd=referral|utmcct=/;
	// ucvid=dlPAl0icki; UZRef=http://ukoz.ru/;
	// uSID=E7zYZZkgHpjLUAFBw4ylOqbeNpnL%5EOFUC2ITcbXj%5EdnfL8pBiUm%5EHsio;
	// __utmc=197224235;
	// uReg2=1PK2lmS9EqIjFVW%218kzJIE8ptZHADjm6ERCk5nghDtpJ8Vc0CQy%21BHOfOh6u9nuSC5Levi%5EgLgoo;
	// __utmb=197224235.3.9.1375268026; _ym_visorc=w
	// Host www.ucoz.ru
	// Referer http://www.ucoz.ru/register
	// User-Agent Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20100101
	// Firefox/22.0
	
	public function __construct() {
		parent::__construct();
		
		$this->cookie_dir=$this->Framework->CONFIG['files_dir'].$this->cookie_dir.'/';
		$this->cookie_get();
	}
	
	public function cookie_set() {
		if (!empty($this->COOKIE)) {
			if (!is_dir($this->cookie_dir)) 
				if (!mkdir($this->cookie_dir, 0777, true)) 
					$this->Framework->library->error->set('Не удалось создать папку: "'.$this->cookie_dir.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			@file_put_contents($this->cookie_dir.$this->cookie, serialize($this->COOKIE));
		}
	}
	
	public function cookie_get() {
		if (file_exists($this->cookie_dir.$this->cookie)) {
			$cookie=@file_get_contents($this->cookie_dir.$this->cookie);
			if ($cookie)
				$this->COOKIE=unserialize($cookie);
		}
		return $this->COOKIE;
	}
	
	public function cookie_delete() {
		if (file_exists($this->cookie_dir.$this->cookie))
			@unlink($this->cookie_dir.$this->cookie);
		$this->COOKIE=array(0=>array());
	}
	
	
	public function ajax() {
	}
	
	public function get($PARAM = array()) {
		$RESULT = array (
				'text' => '',
				'headers' => '',
				'status' => 0,
				'time'=>0
		);
		$time_start=microtime(true);
		$this->cookie_get();
		
		$PARAM ['url'] = isset ( $PARAM ['url'] ) && $PARAM ['url'] ? strtolower ( trim ( $PARAM ['url'] ) ) : '';
		
		if (preg_match ( '/^https:\/\//i', $PARAM ['url'] ))
			$PARAM ['ssl'] = 'ssl://';
		else
			$PARAM ['ssl'] = '';
		$PARAM ['url'] = str_replace ( 'https://', '', $PARAM ['url'] );
		$PARAM ['url'] = str_replace ( 'http://', '', $PARAM ['url'] );
		
		$PARAM ['method'] = isset ( $PARAM ['method'] ) && $PARAM ['method'] ? strtoupper ( trim ( $PARAM ['method'] ) ) : 'GET';
		$PARAM ['data'] = isset ( $PARAM ['data'] ) && $PARAM ['data'] ? preg_replace ( '/^\&/', '', preg_replace ( '/^\?/', '', trim ( $PARAM ['data'] ) ) ) : '';
		
		if (isset ( $PARAM ['DATA'] ) && is_array ( $PARAM ['DATA'] ))
			$PARAM ['data'] = $this->arrayToQueryString ( $PARAM ['DATA'] );
		
		$EXPLODE = explode ( '/', $PARAM ['url'] );
		if (isset ( $EXPLODE [0] ))
			$PARAM ['host'] = $EXPLODE [0];
		if (isset ( $EXPLODE [1] )) {
			array_shift($EXPLODE);
			$PARAM ['path'] = implode('/',$EXPLODE);
		}
		
		if (isset ( $PARAM ['path'] ))
			$PARAM ['path'] = '/' . $PARAM ['path'];
		else
			$PARAM ['path'] = '/';
		
		if ($PARAM ['method'] == 'GET' && $PARAM ['data'])
			$PARAM ['path'] .= '?' . $PARAM ['data'];
		
		$PARAM ['port'] = isset ( $PARAM ['port'] ) && $PARAM ['port'] ? inval ( $PARAM ['port'] ) : ($PARAM ['ssl']?443:80);
		$PARAM ['protocol'] = isset ( $PARAM ['protocol'] ) && $PARAM ['protocol'] ? trim ( $PARAM ['protocol'] ) : 'HTTP/1.1';
		
		if (! $PARAM ['url'])
			return false;
		
		//echo $PARAM['ssl'].$PARAM['host'];
		$connect = @fsockopen($PARAM['ssl'].$PARAM['host'], $PARAM['port'], $errno, $errstr);
		
		if ($connect) {
			$header = '';
			$header .= "{$PARAM['method']} {$PARAM['path']} {$PARAM['protocol']}\r\n";
			$header .= "Host: {$PARAM['host']}\r\n";
			if ($PARAM ['data'])
				$header .= "Content-length: " . strlen ( $PARAM ['data'] ) . "\r\n";

			if (!isset($PARAM['cookie']) || !$PARAM['cookie'])
				$PARAM['cookie']=0;
			$cookie='';
			if (isset($this->COOKIE[$PARAM['cookie']]))
				foreach ($this->COOKIE[$PARAM['cookie']] AS $key=>$value)
					$cookie.=($cookie?'; ':'').$key.'='.$value;
			if ($cookie)
				$PARAM ['HEADER']['Cookie']=$cookie;
			
			if (isset ( $PARAM ['HEADER'] ) && is_array ( $PARAM ['HEADER'] ))
				foreach ( $PARAM ['HEADER'] as $key => $value )
					if (! is_array ( $value ) && $value && $key && $key != 'Content-length' && $key != 'Host')
						$header .= $key . ': ' . $value . "\r\n";
			

			
			$header .= "\r\n";
			echo '<pre>Отправленные заголовки: '.$header.'</pre>'; 
			fputs ( $connect, $header );
									
			if ($PARAM ['method'] == 'POST' && $PARAM ['data'])
				fputs ( $connect, $PARAM ['data'] );
			//echo 'data=<pre>'.$PARAM ['data'].'</pre>end of data';
			$buf = '';
			
			//echo ini_get("memory_limit").'<br>';			
			$buf=stream_get_contents($connect);
			//while (!feof($connect))
			//	$buf .= fread($connect, 100000000);
						
			fclose ( $connect );
			
			if ($buf) {
				$RESULT ['status']=1;
				$RESULT ['headers'] = substr ( $buf, 0, strpos ( $buf, "\r\n\r\n" , 0) );
				$RESULT ['HEADER'] = $this->get_headers_from_string( $RESULT ['headers'] );				
				$RESULT ['COOKIE'] = $this->get_cookie($RESULT ['HEADER']);
				if (count($RESULT['COOKIE'])>0)
					if (!isset($PARAM['cookie']) || !$PARAM['cookie'])
						$PARAM['cookie']=0;
					if (!empty($RESULT['COOKIE'])) {
						if (isset($this->COOKIE[$PARAM['cookie']]) && is_array($this->COOKIE[$PARAM['cookie']]))
							$this->COOKIE[$PARAM['cookie']]=array_merge($this->COOKIE[$PARAM['cookie']], $RESULT['COOKIE']);
						else
							$this->COOKIE[$PARAM['cookie']]=$RESULT['COOKIE'];
					}
	
				$RESULT ['text'] = substr ( $buf, strpos ( $buf, "\r\n\r\n", 0 ) + 4 );
				if ( ($pos=strpos ( $RESULT ['text'], "\r\n0\r\n", 0 ))!==false)
					$RESULT ['text']=substr($RESULT ['text'], 0, $pos);
				unset ( $buf );
				if (isset($RESULT['HEADER']['Content-Encoding']) && $RESULT['HEADER']['Content-Encoding']) 
					if ($RESULT['HEADER']['Content-Encoding']=='gzip')
						$RESULT ['text']=gzdecode($RESULT ['text']);
					elseif ($RESULT['HEADER']['Content-Encoding']=='deflate')
						$RESULT ['text']=gzinflate($RESULT ['text']);
						
				
				$first=substr($RESULT ['text'], 0, 1);
				if ($first && $first!='<')
					$RESULT ['text']=substr($RESULT ['text'], strpos($RESULT ['text'], "\n")+1);	
			} 
			else 
				$this->Framework->library->error->set('Пустой ответ от сервера', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		else 
			$this->Framework->library->error->set('Ошибка №'.$errno.':'.$errstr, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		
		$time_end=microtime(true);
		$RESULT['time']=($time_end-$time_start);
		$this->cookie_set();
		return $RESULT;
	}
	
	public function arrayToQueryString($ARRAY = array()) {
		$data = '';
		foreach ( $ARRAY as $key => $value ) {
			if (! is_array ( $value ) && $key)
				$data .= ($data ? '&' : '') . rawurlencode ( $key ) . '=' . rawurlencode ( $value );
		}
		return $data;
	}
	
	public function get_cookie($HEADERS=array()) {
		$COOKIE=array();
		foreach ($HEADERS as $key=>$VALUE) {
			if ($key && $key=='Set-Cookie') {
				if (!is_array($VALUE))
					$VALUE=array($VALUE); 
				foreach($VALUE as $value) { 
					list($cookie)=explode(';', $value);
					list($name, $value)=explode('=', $cookie);
					$COOKIE[$name]=$value;
				}
			}	
					
		}
		return $COOKIE;
	}
	
	public function get_headers_from_string($string) {
		$HEADERS = array ();
		$data = explode ( "\n", $string );
		$len = count ( $data );
		$count = 0;
		for($i = 0; $i < $len; $i ++) {
			$header = str_replace ( "\r", "", $data [$i] );
			if ($header) {
				
				$pos = strpos ( $header, ":" );
				if ($pos > 0) {
					$key = substr ( $header, 0, $pos );
					$value = trim ( substr ( $header, $pos + 1, strlen ( $header ) ) );
					if ($key)
						if (isset($HEADERS [$key]) && !is_array($HEADERS [$key])) 
							$HEADERS [$key] = array($HEADERS[$key], $value);
						elseif (isset($HEADERS [$key]) && is_array($HEADERS [$key]))
							$HEADERS [$key][] = $value;
						else
							$HEADERS [$key] = $value;
				} else {
					$HEADERS [$count] = $header;
					$count ++;
				}
			} // if header
		} // for
		
		return $HEADERS;
	}
	
} // \class
?>