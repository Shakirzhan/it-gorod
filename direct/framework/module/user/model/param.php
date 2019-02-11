<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\user\model;

final class Param extends \FrameWork\Common {
	private $TABLE=array(
			'param'=>'user_param',		
		);
		
	public function __construct () {
		parent::__construct();
		$this->Framework->library->model->table($this->TABLE);
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function set($PARAM=array()) {			
		//$this->Framework->library->lib->array_empty($PARAM);
		$this->Framework->library->lib->array_trim($PARAM);
		$id=$this->Framework->library->model->set($this->TABLE[0], $PARAM, 'user');
		if ($id) {
			if (empty($PARAM['user']) && !empty($id))
				$PARAM['user']=$id;
		}
		if (!empty($PARAM['user']) && $id)
			return $PARAM['user'];
		return false;
	}
	
	public function get($PARAM=array(), $ORDER=array('user'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0)) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAM['USER']) && is_array($PARAM['USER']))
			$WHERE[]="(`t1`.`user`='".implode("' OR `t1`.`user`='", $PARAM['ID'])."')";
		if (!empty($PARAM['user']))
			$WHERE[]="`t1`.`user`='".intval($PARAM['user'])."'";
		if (!empty($PARAM['token']))
			$WHERE[]="`t1`.`token`='".$this->Framework->db->quote($PARAM['token'])."'";

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
					FROM `".$this->TABLE[0]."` `t1`
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
					FROM `".$this->TABLE[0]."` `t1`
					{$where}
					{$order}
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			$DATA['ELEMENT'][]=$ROW;
		}
		return $DATA;
	}
	
	public function delete($PARAM=array()) {
		if (!empty($PARAM))
			$this->Framework->library->model->delete($this->TABLE[0], $PARAM);
	}
	
	public function count($PARAM=array()) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAM['ID']) && is_array($PARAM['ID']))
			$WHERE[]="(`t1`.`id`='".implode("' OR `t1`.`id`='", $PARAM['ID'])."')";
		if (!empty($PARAM['PARENT']) && is_array($PARAM['PARENT']))
			$WHERE[]="(`t1`.`parent`='".implode("' OR `t1`.`parent`='", $PARAM['PARENT'])."')";
		$where=(count($WHERE)>0)?' WHERE '.implode(' AND ',$WHERE).' ':'';
		
		$sql="SELECT COUNT(`t1`.`id`) as `count`
					FROM `".$this->TABLE[0]."` `t1`
					{$where}
		";
		$result=$this->Framework->db->set($sql);
		
		$ROW=$this->Framework->db->get($result);
		if ($ROW)
			return $ROW;

		return $DATA;
	}
	
}//\class
?>