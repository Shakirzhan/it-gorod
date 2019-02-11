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

final class Phrase extends \FrameWork\Common {
	
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
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], $PARAM);
		}
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		$table=$this->Framework->direct->model->config->TABLE['phrase'];
		
		if (!empty($PARAM['id'])) {
			if (!is_array($PARAM['id']))
				$PARAM['id']=array($PARAM['id']);
			foreach ($PARAM['id'] as $value) {
				if (!is_numeric($value)) {
					$ID=$this->Framework->library->model->get($table, array('key'=>$value));
					if (!empty($ID[0]['id']))
						$value=$ID[0]['id'];
				}
				$WHERE[]="`t1`.`id`='".$this->Framework->db->quote($value)."'";
			}
		}
		
		if (!empty($PARAM['price'])) {
			if (!is_array($PARAM['price']))
				$PARAM['price']=array($PARAM['price']);
			$where_sql='';
			$where_logic='OR';
			$where_sign='=';
			if (!empty($PARAM['pricelist']) && (int)$PARAM['pricelist']>=1 && (int)$PARAM['pricelist']<=7)
				if ((int)$PARAM['pricelist']==1)
					$where_field='`t1`.`premium_max`';
				elseif ((int)$PARAM['pricelist']==3)
					$where_field='`t1`.`premium_min`';
				elseif ((int)$PARAM['pricelist']==4)
					$where_field='`t1`.`max`';
				elseif ((int)$PARAM['pricelist']==7)
					$where_field='`t1`.`min`';
				else	
					$where_field='`t1`.`position'.(int)$PARAM['pricelist'].'`';
			else
				$where_field='`t1`.`price`';
			foreach ($PARAM['price'] as $value) {
				$value=trim((string)$value);
				if (substr((string)$value, 0, 1)=='&' || substr((string)$value, 1, 1)=='&') 
					$where_logic='AND';
				if (substr($value, 0, 1)=='>' || substr($value, 1, 1)=='>') 
					$where_sign='>=';
				if (substr($value, 0, 1)=='<' || substr($value, 1, 1)=='<') 
					$where_sign='<=';
				$value=round((float)preg_replace('/[^0-9\.]/', '', str_replace(',', '.', $value)), 2);
				$where_sql.=($where_sql?' '.$where_logic.' ':'').$where_field.$where_sign."'".$value."'";
			}
			if ($where_sql)
				$WHERE[]='('.$where_sql.')';
		}
		
		if (!empty($PARAM['real_price'])) {
			if (!is_array($PARAM['real_price']))
				$PARAM['real_price']=array($PARAM['real_price']);
			$where_sql='';
			$where_logic='OR';
			$where_sign='=';
			if (!empty($PARAM['pricelist']) && (int)$PARAM['pricelist']>=1 && (int)$PARAM['pricelist']<=7)
				$where_field='`t1`.`price'.(int)$PARAM['pricelist'].'`';
			else
				$where_field='`t1`.`real_price`';
			foreach ($PARAM['real_price'] as $value) {
				$value=trim((string)$value);
				if (substr((string)$value, 0, 1)=='&' || substr((string)$value, 1, 1)=='&') 
					$where_logic='AND';
				if (substr($value, 0, 1)=='>' || substr($value, 1, 1)=='>') 
					$where_sign='>=';
				if (substr($value, 0, 1)=='<' || substr($value, 1, 1)=='<') 
					$where_sign='<=';
				$value=round((float)preg_replace('/[^0-9\.]/', '', str_replace(',', '.', $value)), 2);
				$where_sql.=($where_sql?' '.$where_logic.' ':'').$where_field.$where_sign."'".$value."'";
			}
			if ($where_sql)
				$WHERE[]='('.$where_sql.')';
		}	

		if (!empty($PARAM['account']))
			$WHERE[]="`t1`.`account`='".(int)$PARAM['account']."'";
		
		if (!empty($PARAM['user']))
			$WHERE[]="`t1`.`user`='".(int)$PARAM['user']."'";
		
		if (!empty($PARAM['company']))
			$WHERE[]="`t1`.`company`='".(int)$PARAM['company']."'";
		
		if (!empty($PARAM['banner']))
			$WHERE[]="`t1`.`banner`='".$this->Framework->db->quote($PARAM['banner'])."'";

		if (!empty($PARAM['group']))
			$WHERE[]="`t1`.`group`='".$this->Framework->db->quote($PARAM['group'])."'";		
		
		if (!empty($PARAM['tag']))
			$WHERE[]="`t3`.`id`='".(int)$PARAM['tag']."'";
		
		if (!empty($PARAM['key']))
			$WHERE[]="`t1`.`key`='".$this->Framework->db->quote($PARAM['key'])."'";
			
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
		
		if (!empty($PARAM['strategy'])) {
			if ($PARAM['strategy']==-3)
				$PARAM['strategy']=0;
			$WHERE[]="`t1`.`strategy`='".intval($PARAM['strategy'])."'";
		}	
		
		if (!empty($PARAM['place']))
			if ((int)$PARAM['place']>0)
				$WHERE[]="`t1`.`place`='".intval($PARAM['place'])."'";	
			else
				$WHERE[]="`t1`.`place`=0";		
			
		if (!empty($PARAM['search']))
			$WHERE[]="(`t1`.`id` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%' OR `t1`.`name` LIKE '%".$this->Framework->db->quote($PARAM['search'])."%')";
			
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
					LEFT JOIN `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t3` ON (`t3`.`banner`=`t1`.`banner`)
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
					LEFT JOIN `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t3` ON (`t3`.`banner`=`t1`.`banner`)
					{$where}
					GROUP BY `t1`.`id`
					{$order}
					
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			if (!empty($ROW['name'])) {
				$ROW['MINUS']=explode(' -', $ROW['name']);
				if (!empty($ROW['MINUS']))
					$ROW['phrase']=array_shift($ROW['MINUS']);
				if (!empty($ROW['MINUS']))
					$ROW['minus']='-'.implode(' -', $ROW['MINUS']);
				$ROW['search']=mb_ereg_replace('[^а-яА-Яa-zA-Z0-9 ]', '', $ROW['name']);
			}
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
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `id`='".$PARAM['id']."' LIMIT 1");
		}
	}
	
}//\class
?>