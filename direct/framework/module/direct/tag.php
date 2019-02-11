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

final class Tag extends \FrameWork\Common {
	
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
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['tag'], $PARAM);
		}
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		$table=$this->Framework->direct->model->config->TABLE['tag'];
		
		if (!empty($PARAM['id'])) {
			if (!is_array($PARAM['id']))
				$PARAM['id']=array($PARAM['id']);
			foreach ($PARAM['id'] as $value) {
				if (!is_numeric($value)) {
					$ID=$this->Framework->library->model->get($table, array('key'=>$value));
					if (!empty($ID[0]['id']))
						$value=$ID[0]['id'];
				}
				$WHERE[]="`t1`.`id`='".intval($value)."'";
			}
		}
		
		if (!empty($PARAM['company']))
			$WHERE[]="`t1`.`company`='".(int)$PARAM['company']."'";
		
		if (!empty($PARAM['key']))
			$WHERE[]="`t1`.`key`='".$this->Framework->db->quote($PARAM['key'])."'";
			
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
			
		if (!empty($PARAM['search']))
			$WHERE[]="`t1`.`name` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%'";			
			
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