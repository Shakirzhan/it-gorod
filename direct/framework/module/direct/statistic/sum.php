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

final class Sum extends \FrameWork\Common {
	
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
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic'], $PARAM);
		}
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		$table=$this->Framework->direct->model->config->TABLE['statistic'];
		
		if (!empty($PARAM['id'])) {
			if (!is_array($PARAM['id']))
				$PARAM['id']=array($PARAM['id']);
			foreach ($PARAM['id'] as $value) {
				$WHERE[]="`t1`.`id`='".intval($value)."'";
			}
		}
		
		if (!empty($PARAM['account']))
			$WHERE[]="`t1`.`account`='".(int)$PARAM['account']."'";
		
		if (!empty($PARAM['user']))
			$WHERE[]="`t1`.`user`='".(int)$PARAM['user']."'";
			
		if (!empty($PARAM['company']))
			$WHERE[]="`t1`.`company`='".(int)$PARAM['company']."'";
		
		if (isset($PARAM['banner']))
			$WHERE[]="`t1`.`banner`='".(int)$PARAM['banner']."'";
		
		if (isset($PARAM['phrase']))
			$WHERE[]="`t1`.`phrase`='".$this->Framework->db->quote($PARAM['phrase'])."'";
		
		if (!empty($PARAM['key']))
			$WHERE[]="`t1`.`key`='".$this->Framework->db->quote($PARAM['key'])."'";
			
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
			
		if (!empty($PARAM['date']))
			$WHERE[]="(`t1`.`date`='".date('Y-m-d', strtotime($PARAM['date']))."'";
		
		if (!empty($PARAM['date_start']))
			$WHERE[]="`t1`.`date`>='".date('Y-m-d', strtotime($PARAM['date_start']))."'";
		
		if (!empty($PARAM['date_end']))
			$WHERE[]="`t1`.`date`<='".date('Y-m-d', strtotime($PARAM['date_end']))."'";
			
		$where=(count($WHERE)>0)?' WHERE '.implode(' AND ',$WHERE).' ':'';
		
		//Поля//
		$fields='';
		if (!empty($FIELDS) && is_array($FIELDS)) {
			foreach ($FIELDS as $value) {
				$fields.=($fields?',':'').'`t1`.`'.$this->Framework->db->quote($value).'`';			
			}
		}
		if (!$fields)
			$fields='`t1`.*';
		//\Поля//
		
		//Сортировка//
		$order='';
		if (!empty($ORDER) && is_array($ORDER)) {
			foreach($ORDER as $key=>$value)	{
				if (is_numeric($key))
					$order.=($order?',':'')."`t1`.`".$value."` ASC";
				else
					$order.=($order?',':'')."`t1`.`".$key."` ".($value?$value:'ASC')."";
			}
		}
		if ($order)
			$order=' ORDER BY '.$order;
		//\Сортировка//
		
		//Лимит//
		if (!empty($LIMIT['number'])) {
		$sql="SELECT COUNT(*) as `count`
					FROM `".$table."` `t1`
					{$where}
		";
		$result=$this->Framework->db->set($sql);
		$ROW=$this->Framework->db->get($result);
		if (!empty($ROW))
			$LIMIT['count']=$ROW['count'];
		}
		$DATA['PAGE']=$this->Framework->library->page->get($LIMIT);
		//\Лимит//
		
		$sql="SELECT {$fields}  
					FROM `".$table."` `t1`
					{$where}
					{$order}
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			if (!empty($GROUP) && is_array($GROUP)) {
				$ELEMENT=&$DATA['ELEMENT'];
				foreach($GROUP as $value) {
					$value=(string)$value;
					if (!empty($value) && isset($ROW[$value])) {
						if (empty($ROW[$value]))
							$ROW[$value]=0;		
						if (!isset($ELEMENT[$ROW[$value]])) 
							$ELEMENT[$ROW[$value]]=null;
						$ELEMENT=&$ELEMENT[$ROW[$value]];
					}
				}
				$ELEMENT=$ROW;
			} else
				$DATA['ELEMENT'][]=$ROW;
		}
		return $DATA;
	}
	
	public function delete($PARAM=array()) {
	
	}
	
}//\class
?>