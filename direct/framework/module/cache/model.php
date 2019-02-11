<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\cache;

final class Model extends \FrameWork\Common {
	private $TABLE=array(
				'cache'=>'cache',
			);
	private $CONFIG=array(
				'time'=>3600,
			);
	
	public function __construct () {
		parent::__construct();
		$this->Framework->library->model()->table($this->TABLE);
		$CONFIG=$this->get();
		foreach ($CONFIG as $VALUE)
			if (!empty($VALUE['key']))
				$this->CONFIG[$VALUE['key']]=$VALUE['value'];
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function __set($name, $value=null) {
		if (isset($this->CONFIG[$name]))
			$this->CONFIG[$name]=$value;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
	}	
	
	public function set($PARAM=array()) {
		$return=0;
		if (!empty($PARAM['key']) && !empty($PARAM['value'])) {
			$PARAM['key']=$this->key($PARAM['key']);
			$DATA=$this->Framework->library->model->get($this->TABLE[0], array('key'=>(string)$PARAM['key']));
			if (!empty($DATA[0]['id'])) 
				$PARAM['id']=$DATA[0]['id'];
			if (empty($PARAM['time'])) 
				$PARAM['time']=date('Y-m-d H:i:s');
			$PARAM['value']=serialize($PARAM['value']);
			$return=$this->Framework->library->model->set($this->TABLE[0], $PARAM);
		}
		return $return;
	}
	
	public function get($PARAM=array()) {
		if (!empty($PARAM) && empty($PARAM['key']))
			$PARAM=array('key'=>$PARAM);
		if (!empty($PARAM['key'])) {
			$PARAM['key']=$this->key($PARAM['key']);
			$DATA=$this->Framework->library->model->get($this->TABLE[0], array('key'=>$PARAM['key']));
			$ROW=array_shift($DATA);
			if (!empty($ROW)) {
				$time=strtotime($ROW['time']);
				if (!empty($PARAM['second']))
					if ($time<time()-(int)$PARAM['second'])
						return null;
						
				if (!empty($ROW['value']))
					return unserialize($ROW['value']);
			}
		}
		return null;
	}
	
	private function key($PARAM=array()) {
		if (!empty($PARAM) && is_array($PARAM))
			return md5(http_build_query($PARAM));
		elseif (!empty($PARAM))
			return (string)$PARAM;
		return '';
	}
	
}//\class
?>