<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct;

final class Group extends \FrameWork\Common {
	
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
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], $PARAM);
		}
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		$table=$this->Framework->direct->model->config->TABLE['group'];
		$join='';
		
		if (!empty($PARAM['id'])) {
			if (!is_array($PARAM['id']))
				$PARAM['id']=array($PARAM['id']);
			$where_id='';
			foreach ($PARAM['id'] as $value) {
				$where_id.=($where_id?' OR ':'')."`t1`.`id`='".intval($value)."'";
			}
			$WHERE[]='('.$where_id.')';
		}
		
		if (!empty($PARAM['company'])) {
			if (!is_array($PARAM['company']))
				$PARAM['company']=array($PARAM['company']);
			$where_company='';
			foreach ($PARAM['company'] as $value) {
				$where_company.=($where_company?' OR ':'')."`t1`.`company`='".intval($value)."'";
			}
			$WHERE[]='('.$where_company.')';
		}
		
		if (!empty($PARAM['account'])) {
			if (!is_array($PARAM['account']))
				$PARAM['account']=array($PARAM['account']);
			$where_account='';
			foreach ($PARAM['account'] as $value) {
				$where_account.=($where_account?' OR ':'')."`t1`.`account`='".intval($value)."'";
			}
			$WHERE[]='('.$where_account.')';
		}
		
		if (!empty($PARAM['user'])) {
			if (!is_array($PARAM['user']))
				$PARAM['user']=array($PARAM['user']);
			$where_user='';
			foreach ($PARAM['user'] as $value) {
				$where_user.=($where_user?' OR ':'')."`t1`.`user`='".intval($value)."'";
			}
			$WHERE[]='('.$where_user.')';
		}
		
		if (!empty($PARAM['tag'])) {
			$join.=" LEFT JOIN `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t4` ON (`t4`.`group`=`t1`.`id`) ";
			$WHERE[]="`t4`.`id`='".(int)$PARAM['tag']."'";
		}
			
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
		
		if (!empty($PARAM['strategy'])) {
			if ($PARAM['strategy']==-3)
				$PARAM['strategy']=0;
			$WHERE[]="`t1`.`strategy`='".intval($PARAM['strategy'])."'";
		}		
		
		if (!empty($PARAM['search'])) {
			$join.=" INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) ";
			$WHERE[]="(`t1`.`id` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%' OR `t2`.`id` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%' OR `t1`.`name` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%' OR `t2`.`name` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%' OR `t2`.`body` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%'  OR `t2`.`name` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%')";
		}
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
					if (substr($value, 0, 1)=='-')
						$order.=($order?',':'')."`t1`.`".substr($value, 1)."` DESC";
					else
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
		$sql="SELECT COUNT(DISTINCT `t1`.`id`) as `count`
					FROM `".$table."` `t1`
					{$join}
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
					{$join}
					{$where}
					GROUP BY `t1`.`id`
					{$order}
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			if (isset($ROW['id']))
				$DATA['ID'][]=$ROW['id'];
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