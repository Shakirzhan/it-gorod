<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\model\api;

final class Model extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($table='', $PARAM=array(), $primary_key='id') {
		
		if (!empty($table) && !empty($PARAM) && is_array($PARAM)) {
			if (empty($PARAM['time']))
				$PARAM['time']=date('Y-m-d H:i:s');
			if (empty($PARAM['id']) && !isset($PARAM['status']))
				$PARAM['status']=0;
			elseif (!empty($PARAM['status']))
				$PARAM['status']=1;
			elseif (isset($PARAM['status']))
				$PARAM['status']=0;
			return $this->Framework->library->model->set($table, $PARAM, $primary_key);
		}
		return 0;
	}
	
	public function get($table='', $PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		if (!empty($table)) {
			if (!empty($PARAM['id'])) {
				if (!is_array($PARAM['id']))
					$PARAM['id']=array($PARAM['id']);
				foreach ($PARAM['id'] as &$value) {
					if (!is_numeric($value)) {
						$ID=$this->Framework->library->model->get($table, array('key'=>$value));
						if (!empty($ID[0]['id']))
							$value=$ID[0]['id'];
					}
					$WHERE[]="`t1`.`id`='".intval($value)."'";
				}
			unset($PARAM['id']);
			}
			
			if (!empty($PARAM) && is_array($PARAM)) {
				foreach ($PARAM as $key=>&$VALUE) {
					if (!empty($key)) {
						$sql='';
						if (!is_array($VALUE)) 
							$VALUE=array($VALUE);
						foreach($VALUE as &$value) 
							$sql.=($sql?' OR ':'')."`t1`.`".$key."`='".$this->Framework->db->quote($value)."'";
						
						$WHERE[]='('.$sql.')';
					}
				}
			}
				
			$where=(count($WHERE)>0)?' WHERE '.implode(' AND ',$WHERE).' ':'';
			
			//Поля//
			$fields='';

			if (!empty($FIELDS) && is_array($FIELDS) && !empty($GROUP) && is_array($GROUP)) 
				$FIELDS=array_merge($FIELDS, $GROUP);
			
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
				if (isset($ROW['id']))
					$DATA['ID'][]=$ROW['id'];
				if (isset($ROW['key']))
					$DATA['KEY'][]=$ROW['key'];
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
		}
		if (!empty($DATA['ELEMENT'])) {
			foreach($DATA['ELEMENT'] as &$value) {
				$DATA['FIRST']=&$value;
				break;
			}
		}
				
		return $DATA;
	}
	
	public function delete ($table='', $PARAM=array(), $ORDER=array(), $LIMIT=array()) {
		$WHERE=array();
		if (!empty($PARAM) && is_array($PARAM)) {
			foreach($PARAM as $key=>&$value) {
				if (is_array($value)) {
					$where_key='';
					foreach($value as $key1=>&$value1)
						$where_key=($where_key?' OR ':'')."`".$key."`".(in_array($key1, array('>','<','>=','<='))?$key1:'=')."'".$this->Framework->db->quote($value1)."'";
					$WHERE[]='('.$where_key.')';
				} else
					$WHERE[]="`".$key."`='".$this->Framework->db->quote($value)."'";
			}
		}
		
		$where=count($WHERE)>0?' WHERE '.implode(' AND ',$WHERE).' ':'';
	
		//Сортировка//
		$order='';
		if (!empty($ORDER) && is_array($ORDER)) {
			foreach($ORDER as $key=>$value)	{
				if (is_numeric($key))
					$order.=($order?',':'')."`".$value."` ASC";
				else
					$order.=($order?',':'')."`".$key."` ".($value?$value:'ASC')."";
			}
		}
		if ($order)
			$order=' ORDER BY '.$order;
		//\Сортировка//
		
		//Лимит//
		$limit='';
		if (!empty($LIMIT) && !is_array($LIMIT)) {
			if ($LIMIT<0) {
				$this->Framework->db->set("SELECT COUNT(*) as `count` FROM `".$this->Framework->db->quote($table)."`");
				$LIMIT_ROW=$this->Framework->db->get();
				if (!empty($LIMIT_ROW['count']))
					$limit=$LIMIT_ROW['count']+$LIMIT;
					if ($limit<=0)
						$limit=0;
			} else { 
				$LIMIT=(int)$LIMIT;
				if ($LIMIT>0)
					$limit=$LIMIT;
				else
					$limit=0;
			}
			if ($limit)
				$limit=' LIMIT '.$limit;
		}
		
		//\Лимит//
		if (!empty($limit) || (empty($limit) && !is_numeric($limit))) {
			$sql = "DELETE FROM
					`".$this->Framework->db->quote($table)."`
					{$where}
					{$order}
					{$limit}
				";
			$result = $this->Framework->db->set($sql);
		}
		
		return null;
	}
	
}//\class
?>