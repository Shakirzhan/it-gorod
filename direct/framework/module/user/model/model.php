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

final class Model extends \FrameWork\Common {
	private $TABLE=array(
			'user'=>'user',
			'group'=>'user_group',		
		);
		
	public function __construct () {
		parent::__construct();
		$this->Framework->library->model()->table($this->TABLE);
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function set($PARAM=array()) {
		
		if (!empty($PARAM['id']) && (!is_numeric($PARAM['id']) || (is_numeric($PARAM['id']) && preg_match('/^[78][0-9]{10}$/i', $PARAM['id'])))) {
			$USER=$this->get(array('login'=>$PARAM['id']));
			if (!empty($USER['ELEMENT'][0]))
				$PARAM['id']=$USER['ELEMENT'][0]['id'];
		}
		
		if (isset($PARAM['password']))
			if (!empty($PARAM['password']))
				$PARAM['password']=md5($PARAM['password']);
			else
				unset($PARAM['password']);
		if (!empty($PARAM['birthdate']))
			$PARAM['birthdate']=date('Y-m-d', strtotime($PARAM['birthdate']));	
		if (empty($PARAM['time']))
			$PARAM['time']=date('Y-m-d H:i:s');	
		if (empty($PARAM['date']))
			$PARAM['date']=date('Y-m-d H:i:s');			
		//$this->Framework->library->lib->array_empty($PARAM);
		$this->Framework->library->lib->array_trim($PARAM);
		
		$id=$this->Framework->library->model->set($this->TABLE[0], $PARAM);
		if ($id) {
			if (empty($PARAM['id']) && !empty($id) && empty($PARAM['login'])) {
				$PARAM['login']=$id;
				$sql="UPDATE `".$this->TABLE[0]."`
					SET
					`login`='".$this->Framework->db->quote($PARAM['login'])."'
					WHERE `id`='".intval($id)."'
				";
				$result=$this->Framework->db->set($sql);
			}
			if (empty($PARAM['id']) && !empty($id))
				$PARAM['id']=$id;
		}
		if (!empty($PARAM['id']) && $id)
			return $PARAM['id'];
		return false;
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0)) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAM['ID']) && is_array($PARAM['ID']))
			$WHERE[]="(`t1`.`id`='".implode("' OR `t1`.`id`='", $PARAM['ID'])."')";
		if (!empty($PARAM['PARENT']) && is_array($PARAM['PARENT']))
			$WHERE[]="(`t1`.`parent`='".implode("' OR `t1`.`parent`='", $PARAM['PARENT'])."')";
		if (!empty($PARAM['id'])) {
			if (!is_numeric($PARAM['id'])) {
				$PARENT=$this->Framework->library->model->get($this->TABLE['id'], array('login'=>$PARAM['id']));
				if (!empty($PARENT[0]['id']))
					$PARAM['id']=$PARENT[0]['id'];
			}
			$WHERE[]="`t1`.`id`='".intval($PARAM['id'])."'";
		}
		if (!empty($PARAM['parent']))
			$WHERE[]="`t1`.`parent`='".intval($PARAM['parent'])."'";
		if (!empty($PARAM['account']))
			$WHERE[]="`t1`.`account`='".intval($PARAM['account'])."'";
		if (!empty($PARAM['partner']))
			$WHERE[]="`t1`.`partner`='".intval($PARAM['partner'])."'";
		if (!empty($PARAM['login']))
			$WHERE[]="`t1`.`login`='".$this->Framework->db->quote($PARAM['login'])."'";
		if (!empty($PARAM['email']))
			$WHERE[]="`t1`.`email`='".$this->Framework->db->quote($PARAM['email'])."'";
		if (!empty($PARAM['session']))
			$WHERE[]="`t1`.`session`='".$this->Framework->db->quote($PARAM['session'])."'";
		if (isset($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
		if (!empty($PARAM['group'])) {
			if (!is_array($PARAM['group']))
				$PARAM['group']=array($PARAM['group']);
			$where_group='';
			foreach($PARAM['group'] as $value) {
				if (!is_numeric($value)) {
					$PARENT=$this->Framework->library->model->get($this->TABLE['group'], array('key'=>$value));
					if (!empty($PARENT[0]['id']))
						$value=$PARENT[0]['id'];	
				}
				$where_group.=($where_group?' OR ':'')."`t1`.`group`='".intval($value)."'";
			}
			$WHERE[]='('.$where_group.')';
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
		
		$sql="SELECT {$fields}, `t2`.* , COUNT(`t3`.`parent`) as `children` 
					FROM `".$this->TABLE[0]."` `t1`
					LEFT JOIN `".$this->Framework->user->model->param->TABLE[0]."` `t2` ON (`t2`.`user`=`t1`.`id`)
					LEFT JOIN `".$this->TABLE[0]."` `t3` ON (`t3`.`parent`=`t1`.`id`)
					{$where}
					GROUP BY `t1`.`id`
					{$order}
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			if (isset($ROW['id'])) {
				if (!isset($DATA['ID']))
					$DATA['ID']=array();
				$DATA['ID'][]=$ROW['id'];
			}
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
		if (!empty($PARAM['parent']))
			$WHERE[]="`t1`.`parent`='".intval($PARAM['parent'])."'";
		if (!empty($PARAM['partner']))
			$WHERE[]="`t1`.`partner`='".intval($PARAM['partner'])."'";
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
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
		
	
	public function login($PARAM=array()) {
		if (!empty($PARAM['login']) && !empty($PARAM['password']) && (empty($PARAM['captcha_check']) || (!empty($PARAM['captcha']) && $this->Framework->library->captcha->set($PARAM['captcha'])))) {
			$sql="SELECT * FROM `".$this->TABLE[0]."` 
					WHERE 
					`login`='".$this->Framework->db->quote($PARAM['login'])."' AND
					`password`='".$this->Framework->db->quote(md5($PARAM['password']))."' AND
					`status`>0
					LIMIT 1
					";
			$result=$this->Framework->db->set($sql);
			$ROW=$this->Framework->db->get($result);
			if (!empty($ROW)) {
				unset($PARAM);
				$PARAM['id']=$ROW['id'];
				$PARAM['ip']=$this->Framework->CONFIG['ip'];
				$PARAM['session']=$this->Framework->CONFIG['id'];
				$this->set($PARAM);
				return $ROW;
			}
		}
		return false;
	}
	
}//\class
?>