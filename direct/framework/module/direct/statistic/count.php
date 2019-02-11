<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\statistic;

final class Count extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
		if (!empty($PARAM) && is_array($PARAM)) {
			if (empty($PARAM['time']))
				$PARAM['time']=date('Y-m-d H:i:s');
			if (empty($PARAM['id']) && !isset($PARAM['status']))
				$PARAM['status']=0;
			elseif (!empty($PARAM['status']))
				$PARAM['status']=1;
			elseif (isset($PARAM['status']))
				$PARAM['status']=0;
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic_price'], $PARAM);
		}
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		$table=$this->Framework->direct->model->config->TABLE['statistic_price'];
		
		$this->Framework->db->set("SELECT COUNT('id') as `count` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."`");
		$DATA['PHRASE']=$this->Framework->db->get();
		$this->Framework->db->set("SELECT COUNT('id') as `plan`, MIN(`time`) as `time_min`, MAX(`time`) as `time` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `plan`>0");		
		$AUCTION=$this->Framework->db->get();	
		$DATA['PHRASE']=array_merge($DATA['PHRASE'], $AUCTION);
		$this->Framework->db->set("SELECT COUNT('id') as `auction` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `plan`>0 AND `time`>='".date('Y-m-d')." 00:00:00'");
		$PHRASE=$this->Framework->db->get();
		$DATA['PHRASE']['auction']=$PHRASE['auction'];
		$DATA['PHRASE']['progress']=!empty($DATA['PHRASE']['plan'])?round(100*$DATA['PHRASE']['auction']/$DATA['PHRASE']['plan']):0;
		
		$this->Framework->db->set("SELECT 100*SUM(IF(`report` IS NOT NULL, 1, 0))/COUNT('id') as `report` FROM `".$this->Framework->direct->model->config->TABLE['company']."`");
		$COMPANY=$this->Framework->db->get();
		$DATA['PHRASE']['report']=$COMPANY['report'];
	
		$DATA['PHRASE']['interval']=$DATA['PHRASE']['auction']>0?max(1, ceil((strtotime($DATA['PHRASE']['time'])-strtotime($DATA['PHRASE']['time_min']))/60)):0;
		$DATA['PHRASE']['expire']=(!empty($DATA['PHRASE']['time']) && strtotime($DATA['PHRASE']['time'])+3600<time())?1:0;
		$DATA['PHRASE']['time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $DATA['PHRASE']['time']);
		$DATA['PHRASE']['timer']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone']);
		$DATA['PHRASE']['week']=date('w', $DATA['PHRASE']['timer']);
		$DATA['PHRASE']['week_name']=$this->Framework->library->time->week($DATA['PHRASE']['week']);

		return $DATA;
	}
	
	public function delete($PARAM=array()) {
	
	}
	
}//\class
?>