<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\page\model;

final class Page extends \FrameWork\Common {
	private $TABLE=array(
			'page'=>'page',
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
	
	public function set($PARAMS=array()) {
		if (empty($PARAMS['time']))
			$PARAMS['time']=DATE('Y-m-d H:i:s');
		$this->Framework->library->lib->array_empty($PARAMS);
		
		$id=$this->Framework->library->model->set($this->TABLE[0], $PARAMS);
		if ($id) {
			if (empty($PARAMS['id']) && !empty($id))
				$PARAMS['id']=$id;
		}
		if (!empty($PARAMS['id']) && $id)
			return $PARAMS['id'];
		return false;
	}
	
	public function get($PARAMS=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0)) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAMS['ID']) && is_array($PARAMS['ID']))
			$WHERE[]="(`t1`.`id`='".implode("' OR `t1`.`id`='", $PARAMS['ID'])."')";
		if (!empty($PARAMS['PARENT']) && is_array($PARAMS['PARENT']))
			$WHERE[]="(`t1`.`parent`='".implode("' OR `t1`.`parent`='", $PARAMS['PARENT'])."')";
		if (!empty($PARAMS['id'])) {
			if (!is_numeric($PARAMS['id'])) {
				$ID=$this->Framework->library->model->get($this->TABLE[0], array('key'=>$PARAMS['id']));
				if (!empty($ID[0]['id']))
					$PARAMS['id']=$ID[0]['id'];
			}
			$WHERE[]="`t1`.`id`='".intval($PARAMS['id'])."'";
		}
		if (isset($PARAMS['parent'])) {
			if (!is_numeric($PARAMS['parent'])) {
				$PARENT=$this->Framework->library->model->get($this->TABLE[0], array('key'=>$PARAMS['parent']));
				if (!empty($PARENT[0]['id']))
					$PARAMS['parent']=$PARENT[0]['id'];
			}
			$WHERE[]="`t1`.`parent`='".intval($PARAMS['parent'])."'";
		}
		if (!empty($PARAMS['key']))
			$WHERE[]="`t1`.`key`='".$this->Framework->db->quote($PARAMS['key'])."'";
		if (!empty($PARAMS['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAMS['status'])."'";
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
	
}//\class
?>