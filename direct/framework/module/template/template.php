<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс шаблонизатора использующего               ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\Template;

final class Template extends \Framework\Common {
		private $Template=null;
		private $template=null;
		private $DATA=array();
		
		public function __construct($template='') {
		parent::__construct();
		
		$this->template=!empty($template)?(string)$template:(!empty($this->Framework->CONFIG['TEMPLATE']['name'])?$this->Framework->CONFIG['TEMPLATE']['name']:$this->Framework->CONFIG['TEMPLATE']['class']);

		if ($this->template!='json') {
			$this->DATA['FRAMEWORK']=array(
				'url'=>$this->Framework->CONFIG['http_dir'],
				'script'=> $this->Framework->library->controller()->path(),
				'controller'=> $this->Framework->library->controller()->controller(),
				'argument'=>$this->Framework->library->controller()->argument(),
			);
			$this->DATA['FRAMEWORK']=array_merge($this->Framework->CONFIG, $this->DATA['FRAMEWORK']);
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
		if (isset($this->DATA[$name]))
			return true;
		else
			return false;
	}
	
	private function smarty($template='') {
		$file=dirname(__FILE__) . '/template/smarty/Smarty.class.php';
		if (file_exists($file))
			include_once($file);
		else 
			$this->Framework->library->error->set('Не найден файл \Smarty: "'.$file.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		if (class_exists('\Smarty')) {
			$this->Template=new \Smarty();
			$this->Template->setTemplateDir($this->Framework->CONFIG['TEMPLATE']['dir']);
			$this->Template->setCompileDir($this->Framework->CONFIG['TEMPLATE']['compile']);
			$this->Template->setConfigDir($this->Framework->CONFIG['TEMPLATE']['config']);
			//$this->Template->setCacheDir($this->Framework->CONFIG['TEMPLATE']['cache']);
			$this->Template->caching = false;
			
		}
		else {
			$this->Template=null;
			$this->Framework->library->error->set('Не найден класс \Smarty: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		
		if (is_object($this->Template) && $template) {
			foreach ($this->DATA as $key=>&$VALUE)
				if (!empty($key))
					$this->Template->assign($key, $VALUE);
					
			$content=null;
			try {
				$content=$this->Template->fetch($template);
			}
			catch (\Exception $e) {
				$this->Framework->library->error->set('Ошибка шаблонизатора \Smarty: '.$e->getMessage().'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
			return $content;
		}
		return null;
	}
	
	private function json () {
		return json_encode($this->DATA);
	}
	
	private function php ($template='') {
		if ($template && file_exists($this->Framework->CONFIG['TEMPLATE']['dir'].$template)) {
			set_include_path($this->Framework->CONFIG['TEMPLATE']['dir']);
			ob_start();
			extract($this->DATA);
			include($this->Framework->CONFIG['TEMPLATE']['dir'].$template);
			$content=ob_get_contents();
			ob_end_clean();
			return $content;
		}
		return null;
	}
	
	public function set($key='', $value=NULL) {
		if ($key) {
			$this->DATA[$key]=$value;
			return true;
		}
		return false;
	}
	
	public function get($template='') {
		if (method_exists($this, $this->template)) {
			return $this->{$this->template}($template);//call_user_func_array(array($this, $this->template), array($template));
		}
		return null;
	}
	
	public function delete($key='') {
		if ($key && isset($this->DATA[$key])) {
			unset($this->DATA[$key]);
		} 
		elseif (empty($key)) {
			$this->DATA=array();
		}
		return true;
	}

}//\class
?>