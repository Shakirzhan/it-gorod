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

class Http extends \FrameWork\Common {
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
	
	public function get ($url='', $PARAM=array(), $method='GET') {
		$method=strtoupper($method);
		$allow_url_fopen=ini_get('allow_url_fopen');
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if ($method=='POST') {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAM);
			}
			$result = curl_exec($ch);
			$error_number=curl_errno($ch);
			$error=curl_error($ch);
			curl_close($ch);
		} elseif (!empty($allow_url_fopen)) {
			
			if ($method=='POST') {
				$opts = array(
					'http'=>array(
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
						"User-Agent:FrameWork/1.0.0\r\n",
						'method'=>"POST",
						'content'=>$PARAM,
					)
				); 
				# создание контекста потока
				$context = stream_context_create($opts); 
			} else 
				$context=null;
			# отправляем запрос и получаем ответ от сервера
			try {
				$result = file_get_contents($url, 0, $context);
			} catch (Exception $e) {
				$error=$e->getMessage();
				$error_number=1;
			}
			if (empty($result)) {
				if (empty($error))
					$error='Пустой ответ';
				$error_number=1;
			}
		} else 
			$this->Framework->library->error->set("Не установлена библиотека PHP cURL для доступа к: ".$url.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		if (! $result) {
				$this->Framework->library->error->set("Не удается открыть адрес: ".$url.". (".$error_number.': '.$error .')'.' Время:'.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		} 
		return $result;
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
	
	public function send($PARAMS = array()) {
		$RESULT = array (
				'text' => '',
				'headers' => '',
				'status' => 0,
				'time'=>0
		);
		$time_start=microtime(true);
		$this->cookie_get();
		
		$PARAMS ['url'] = isset ( $PARAMS ['url'] ) && $PARAMS ['url'] ? strtolower ( trim ( $PARAMS ['url'] ) ) : '';
		
		if (preg_match ( '/^https:\/\//i', $PARAMS ['url'] ))
			$PARAMS ['ssl'] = 'ssl://';
		else
			$PARAMS ['ssl'] = '';
		$PARAMS ['url'] = str_replace ( 'https://', '', $PARAMS ['url'] );
		$PARAMS ['url'] = str_replace ( 'http://', '', $PARAMS ['url'] );
		
		$PARAMS ['method'] = isset ( $PARAMS ['method'] ) && $PARAMS ['method'] ? strtoupper ( trim ( $PARAMS ['method'] ) ) : 'GET';
		$PARAMS ['data'] = isset ( $PARAMS ['data'] ) && $PARAMS ['data'] ? preg_replace ( '/^\&/', '', preg_replace ( '/^\?/', '', trim ( $PARAMS ['data'] ) ) ) : '';
		
		if (isset ( $PARAMS ['DATA'] ) && is_array ( $PARAMS ['DATA'] ))
			$PARAMS ['data'] = $this->arrayToQueryString ( $PARAMS ['DATA'] );
		
		$EXPLODE = explode ( '/', $PARAMS ['url'] );
		if (isset ( $EXPLODE [0] ))
			$PARAMS ['host'] = $EXPLODE [0];
		if (isset ( $EXPLODE [1] )) {
			array_shift($EXPLODE);
			$PARAMS ['path'] = implode('/',$EXPLODE);
		}
		
		if (isset ( $PARAMS ['path'] ))
			$PARAMS ['path'] = '/' . $PARAMS ['path'];
		else
			$PARAMS ['path'] = '/';
		
		if ($PARAMS ['method'] == 'GET' && $PARAMS ['data'])
			$PARAMS ['path'] .= '?' . $PARAMS ['data'];
		
		$PARAMS ['port'] = isset ( $PARAMS ['port'] ) && $PARAMS ['port'] ? inval ( $PARAMS ['port'] ) : ($PARAMS ['ssl']?443:80);
		$PARAMS ['protocol'] = isset ( $PARAMS ['protocol'] ) && $PARAMS ['protocol'] ? trim ( $PARAMS ['protocol'] ) : 'HTTP/1.1';
		
		if (! $PARAMS ['url'])
			return false;
		
		//echo $PARAMS['ssl'].$PARAMS['host'];
		$connect = @fsockopen($PARAMS['ssl'].$PARAMS['host'], $PARAMS['port'], $errno, $errstr);
		
		if ($connect) {
			$header = '';
			$header .= "{$PARAMS['method']} {$PARAMS['path']} {$PARAMS['protocol']}\r\n";
			$header .= "Host: {$PARAMS['host']}\r\n";
			if ($PARAMS ['data'])
				$header .= "Content-length: " . strlen ( $PARAMS ['data'] ) . "\r\n";

			if (!isset($PARAMS['cookie']) || !$PARAMS['cookie'])
				$PARAMS['cookie']=0;
			$cookie='';
			if (isset($this->COOKIE[$PARAMS['cookie']]))
				foreach ($this->COOKIE[$PARAMS['cookie']] AS $key=>$value)
					$cookie.=($cookie?'; ':'').$key.'='.$value;
			if ($cookie)
				$PARAMS ['HEADERS']['Cookie']=$cookie;
			
			if (isset ( $PARAMS ['HEADERS'] ) && is_array ( $PARAMS ['HEADERS'] ))
				foreach ( $PARAMS ['HEADERS'] as $key => $value )
					if (! is_array ( $value ) && $value && $key && $key != 'Content-length' && $key != 'Host')
						$header .= $key . ': ' . $value . "\r\n";
			

			
			$header .= "\r\n";
			echo '<pre>Отправленные заголовки: '.$header.'</pre>'; 
			fputs ( $connect, $header );
									
			if ($PARAMS ['method'] == 'POST' && $PARAMS ['data'])
				fputs ( $connect, $PARAMS ['data'] );
			//echo 'data=<pre>'.$PARAMS ['data'].'</pre>end of data';
			$buf = '';
			
			//echo ini_get("memory_limit").'<br>';			
			$buf=stream_get_contents($connect);
			//while (!feof($connect))
			//	$buf .= fread($connect, 100000000);
						
			fclose ( $connect );
			
			if ($buf) {
				$RESULT ['status']=1;
				$RESULT ['headers'] = substr ( $buf, 0, strpos ( $buf, "\r\n\r\n" , 0) );
				$RESULT ['HEADERS'] = $this->get_headers_from_string( $RESULT ['headers'] );				
				$RESULT ['COOKIE'] = $this->get_cookie($RESULT ['HEADERS']);
				if (count($RESULT['COOKIE'])>0)
					if (!isset($PARAMS['cookie']) || !$PARAMS['cookie'])
						$PARAMS['cookie']=0;
					if (!empty($RESULT['COOKIE'])) {
						if (isset($this->COOKIE[$PARAMS['cookie']]) && is_array($this->COOKIE[$PARAMS['cookie']]))
							$this->COOKIE[$PARAMS['cookie']]=array_merge($this->COOKIE[$PARAMS['cookie']], $RESULT['COOKIE']);
						else
							$this->COOKIE[$PARAMS['cookie']]=$RESULT['COOKIE'];
					}
	
				$RESULT ['text'] = substr ( $buf, strpos ( $buf, "\r\n\r\n", 0 ) + 4 );
				if ( ($pos=strpos ( $RESULT ['text'], "\r\n0\r\n", 0 ))!==false)
					$RESULT ['text']=substr($RESULT ['text'], 0, $pos);
				unset ( $buf );
				if (isset($RESULT['HEADERS']['Content-Encoding']) && $RESULT['HEADERS']['Content-Encoding']) 
					if ($RESULT['HEADERS']['Content-Encoding']=='gzip')
						$RESULT ['text']=gzdecode($RESULT ['text']);
					elseif ($RESULT['HEADERS']['Content-Encoding']=='deflate')
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