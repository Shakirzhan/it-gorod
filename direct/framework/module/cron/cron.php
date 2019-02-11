<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\cron;

final class Cron extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function set($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('value'=>$PARAM);
		if (empty($PARAM['pid']))
			$PARAM['pid']=getmypid();
		if (isset($PARAM['status']))
			$PARAM['status']=!empty($PARAM['status'])?1:0;
		if (empty($PARAM['time']) && empty($PARAM['id']))
			$PARAM['time']=DATE('Y-m-d H:i:s');
		elseif (!empty($PARAM['time']))
			$PARAM['time']=DATE('Y-m-d H:i:s', strtotime($PARAM['time']));
		
		$id=$this->Framework->library->model->set($this->Framework->cron->config->TABLE[0], $PARAM);
		if ($id) {
			if (empty($PARAM['id']) && !empty($id))
				$PARAM['id']=$id;
		}
		if (!empty($PARAM['id']) && $id)
			return $PARAM['id'];
		return false;
	}
	
	public function get($PARAM=array(), $ORDER=array()) {
		$DATA=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('value'=>$PARAM);
		$DATA=$this->Framework->library->model->get($this->Framework->cron->config->TABLE[0], $PARAM, $ORDER);
		foreach ($DATA as &$VALUE) {
			$VALUE['expire']=0;
			if ($VALUE['status']==1 AND strtotime($VALUE['time_end']) < strtotime($VALUE['time']) AND $VALUE['time_difference']>0 AND 3*$VALUE['time_difference']<(time() - strtotime($VALUE['time'])))
				$VALUE['expire']=1;
		}	
		return $DATA;
	}
	
	public function delete($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('value'=>$PARAM);
		if (!empty($PARAM) && is_array($PARAM))
			$DATA=$this->Framework->library->model->delete($this->Framework->cron->config->TABLE[0], $PARAM);
		return $DATA;
	}	
	
}//\class
?>