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

final class Execute extends \FrameWork\Common {
	
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
		if (!$this->Framework->db->check($this->Framework->cron->config->TABLE[0]))
			$this->Framework->db->repair($this->Framework->cron->config->TABLE[0]);		
		if (!$this->Framework->db->check($this->Framework->model->config->TABLE['error']))
			$this->Framework->db->repair($this->Framework->model->config->TABLE['error']);

		if (empty($PARAM['time']))
			$PARAM['time']=72*3600;
		$DATA=$this->Framework->cron->cron->get(array(), array('interval', 'time'));
		if (!empty($DATA)) {
			foreach ($DATA as &$VALUE) {
				if (strtotime($VALUE['time'])+60*(int)$VALUE['interval']<=$this->Framework->CONFIG['time']) {
					$CRON=$this->Framework->cron->cron->get(array('id'=>$VALUE['id']));
					if (!empty($CRON)) {
						$CRON=array_shift($CRON);
						if ($CRON['status']==1 && !empty($CRON['time']) && (strtotime($CRON['time'])+(int)$CRON['time_difference']+(!empty($CRON['time_limit'])?(int)$CRON['time_limit']:(int)$PARAM['time']))<$this->Framework->CONFIG['time']) {
							$CRON['status']=0;
							if (empty($CRON['time_difference']))
								$this->Framework->library->model->set($this->Framework->cron->config->TABLE[0], array('id'=>$CRON['id'], 'time_difference'=>$PARAM['time_limit']));
							$kill=exec('kill -9 '.$CRON['pid']);							
							$this->Framework->library->error->set('Завис процесс "'.$CRON['name'].' ('.$CRON['id'].')": " перезапуск '.$kill.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
						if (empty($CRON['status'])) {
							$time=time();
							$this->Framework->library->model->set($this->Framework->cron->config->TABLE[0], array('id'=>$VALUE['id'], 'pid'=>getmypid(), 'time'=>date('Y-m-d H:i:s', $time), 'status'=>1));
							if (!empty($VALUE['param']))
								if (!is_numeric($VALUE['param']))
									$VALUE['param']=unserialize($VALUE['param']);
							$max_execution_time=empty($max_execution_time)?(!empty($CRON['time_limit'])?(int)$CRON['time_limit']:(int)$PARAM['time']):$max_execution_time+(!empty($CRON['time_limit'])?(int)$CRON['time_limit']:(int)$PARAM['time']);
							@ini_set ('max_execution_time', $max_execution_time );
							$this->Framework->__execute($VALUE['value'], $VALUE['param']);
							$this->Framework->library->model->set($this->Framework->cron->config->TABLE[0], array('id'=>$VALUE['id'], 'memory'=>memory_get_peak_usage(true), 'time_end'=>date('Y-m-d H:i:s'), 'time_difference'=>(time()-$time), 'status'=>0));
						}
					}
				}
				
			}
		}
		return false;
	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('value'=>$PARAM);
		if (!empty($PARAM) && is_array($PARAM))
			$DATA=$this->Framework->library->model->get($this->Framework->cron->config->TABLE[0], $PARAM);
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