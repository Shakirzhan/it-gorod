<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с url                   ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Controller extends \Framework\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($request_uri='') {
		$DATA=$this->get($request_uri);
		$return=false;
		if (!empty($DATA['CONTROLLER'][0]) && !empty($DATA['CONTROLLER'][1]) && !empty($DATA['CONTROLLER'][2])) {
			$Object=$this->Framework->{$DATA['CONTROLLER'][0]}->controller->{$DATA['CONTROLLER'][1]};
			if (is_object($Object)) {
				$return=call_user_func_array(array($Object, $DATA['CONTROLLER'][2]), (!empty($DATA['ARGUMENTS'])?array($DATA['ARGUMENTS']):$DATA['ARGUMENT']));
			}
		}
		if ($return===false && $this->Framework->CONFIG['controller']) {
			$DATA=$this->get($this->Framework->CONFIG['controller']);
			$return=false;
			if (!empty($DATA['CONTROLLER'][0]) && !empty($DATA['CONTROLLER'][1]) && !empty($DATA['CONTROLLER'][2])) {
				$Object=$this->Framework->{$DATA['CONTROLLER'][0]}->controller->{$DATA['CONTROLLER'][1]};
				if (is_object($Object)) {
					$return=call_user_func_array(array($Object, $DATA['CONTROLLER'][2]), (!empty($DATA['ARGUMENTS'])?array($DATA['ARGUMENTS']):$DATA['ARGUMENT']));
				}
			}
		}
		return $return;
	}
	
	public function get($request_uri='') {
		$DATA=array('ELEMENT'=>array(), 'CONTROLLER'=>array(), 'ARGUMENT'=>array(), 'ARGUMENTS'=>array());

		if (empty($request_uri) || !is_string($request_uri))
			$request_uri=!empty($_REQUEST['controller'])?$_REQUEST['controller']:urldecode($_SERVER['REQUEST_URI']);
		if ($this->Framework->CONFIG['http_dir']) {
			$request_uri=preg_replace("/^".str_replace('/', '\/', $this->Framework->CONFIG['http_dir'])."/i", '', $request_uri);
		}
		if (!empty($request_uri) && $request_uri!='/') {
			$REQUEST_URI = explode ( '?', $request_uri );
			$request_uri=$REQUEST_URI[0];
			$request_uri = preg_replace ( '/[^A-Za-z0-9\/\.\_\-]/', '', $request_uri );
			$request_uri=$REQUEST_URI[0];
			if ($request_uri[0]=='/')
				$request_uri=substr($request_uri, 1);
			if ($request_uri[strlen($request_uri)-1]=='/')
				$request_uri=substr($request_uri, 0, -1);
			if ($request_uri)
				$DATA['ELEMENT']=explode('/', $request_uri);
		}
		if (count($DATA['ELEMENT'])>0) {
			 $DATA['CONTROLLER']=array_slice($DATA['ELEMENT'], 0, 3);
			 $DATA['ARGUMENT']=array_slice($DATA['ELEMENT'], 3);
			 if (!empty($DATA['ARGUMENT']) && is_array($DATA['ARGUMENT'])) {
				if (!empty($DATA['ARGUMENT'][0]) && $DATA['ARGUMENT'][0]==$this->Framework->CONFIG['param']) {
					for ($i=1; $i<count($DATA['ARGUMENT']); $i=$i+2) {
						$value=isset($DATA['ARGUMENT'][$i+1])?$DATA['ARGUMENT'][$i+1]:null;
						if (empty($DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]]))
							$DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]]=$value;
						elseif (!empty($DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]]) && !is_array($DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]]))
							$DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]]=array($DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]], $value);
						else
							$DATA['ARGUMENTS'][$DATA['ARGUMENT'][$i]][]=$value;
					}
				}
			 }
		}
		//Заплатка для неправильной настройки NGINX//
		if (!empty($REQUEST_URI[1]) && empty($_GET)) {
			parse_str($REQUEST_URI[1], $_GET);
		}
		//\Заплатка для неправильной настройки NGINX//

		if (!empty($_GET)) {
			//Заплатка для неправильной настройки NGINX//
			if (isset($_GET['q']) && $_GET['q']==$_SERVER['REQUEST_URI']) 
				unset($_GET['q']);
			if (isset($_GET['rt']) && $_GET['rt']==$_SERVER['REQUEST_URI']) 
				unset($_GET['rt']);
			if (isset($_GET['_url']) && $_GET['_url']==$_SERVER['REQUEST_URI']) 
				unset($_GET['_url']);
			if (isset($_GET['url']) && $_GET['url']==$_SERVER['REQUEST_URI']) 
				unset($_GET['url']);
			//\Заплатка для неправильной настройки NGINX//
			if (!empty($_GET)) 
				$DATA['ARGUMENTS']=array_merge($DATA['ARGUMENT'], $DATA['ARGUMENTS'], $_GET);
		}
		if (!empty($_POST)) 
			$DATA['ARGUMENTS']=array_merge($DATA['ARGUMENT'], $DATA['ARGUMENTS'], $_POST);
		
		unset($DATA['ELEMENT']);
		return $DATA;
	}	
	
	public function path($request_uri='') {
		$DATA=$this->get($request_uri);
		$controller=implode('/', $DATA['CONTROLLER']);
		$argument=implode('/', $DATA['ARGUMENT']);
		return '/'.$controller.($controller && $argument?'/'.implode('/', $DATA['ARGUMENT']):'').($controller?'/':'');
	}
	
	public function uri($request_uri='') {
		if (empty($request_uri))
			$request_uri=$_SERVER['REQUEST_URI'];
		if (substr($request_uri, 0, 1)=='/')
			$request_uri=substr($request_uri, 1);
		list($request_uri)=explode('?', $request_uri);
		if (substr($request_uri, -1)=='/')
			$request_uri=substr($request_uri, 0, -1);
		return $request_uri;
	}	
	
	public function controller($request_uri='') {
		$DATA=$this->get($request_uri);
		$controller=implode('/', $DATA['CONTROLLER']);
		return $controller;
	}
	
	public function argument($request_uri='') {
		$DATA=$this->get($request_uri);
		$argument=implode('/', $DATA['ARGUMENT']);
		return $argument;
	}	
	
	public function ssl($no=false) {
		if ($no) {
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
				$redirect='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$this->Framework->library->header->delete();
				$this->Framework->library->header->set(array('Location'=>$redirect));
				$this->Framework->library->header->get('http');
				die('Сайт можно просматривать только в не зашифрованном режиме без SSL. Пожалуйста перейдите по ссылке: <a href="'.$redirect.'">'.$redirect.'</a>');
			}
		} else {
			if (empty($_SERVER['HTTPS']) || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='on')) {
				$redirect='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$this->Framework->library->header->delete();
				$this->Framework->library->header->set(array('Location'=>$redirect));
				$this->Framework->library->header->get('http');
				die('Сайт можно просматривать только в зашифрованном режиме SSL. Пожалуйста перейдите по ссылке: <a href="'.$redirect.'">'.$redirect.'</a>');
			}
		}
	}
	
	/*
	Метод формирования url
	Параметры: 
		(string) $controller (Передавая первым параметром константу __METHOD__ - url будет автоматически указывать на текущий контроллер)
		(array) $PARAM
	*/
	public function url ($controller=__METHOD__, $PARAM=array()) {
		$url=$controller;
		if (strpos($controller, '\\')!==false) {
			$CONTROLLER=explode('\\', $controller);
			$CONTROLLER=array_slice($CONTROLLER, 2);
			list(, $CONTROLLER[2])=explode('::', $CONTROLLER[2]);
			$url='/'.implode('/', $CONTROLLER).'/';
		}
		if (substr($url, 0, 1)!='/')
			$url='/'.$url;
		if (substr($url, -1)!='/')
			$url.='/';
		if (!empty($PARAM) && is_array($PARAM)) {
			$count=0;
			foreach ($PARAM as $key=>$value) {
				if (is_numeric($key)) {
					$url.=$value;
					break;
				} else
					$url.=($count==0?$this->Framework->CONFIG['param'].'/':'').$key.'/'.$value;
			}
			$count++;
		}
		if (substr($url, -1)!='/')
			$url.='/';
		return $url;
	}
	
	public function domain($url='') {
		if (empty($url)) 
			$url=$_SERVER['HTTP_HOST'];
		$url=trim($url);
		$url=preg_replace('/^([^\/]*\/\/)?([^\/]+)(\/.*|$)/i', '$2', $url);
		$url=strtolower($url);
		$url=preg_replace('/^www\./', '', $url);
		return $url;
	}
	
}//\class
?>