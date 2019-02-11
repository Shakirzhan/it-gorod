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

final class Dictionary extends \FrameWork\Common {
	private $TABLE=array(
			'dictionary'=>'user_dictionary',		
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
		$this->Framework->library->lib->array_empty($PARAM);
		foreach ($PARAM as $user=>$GROUP) {
			foreach ($GROUP as $DICTIONARY) {
				if (!empty($DICTIONARY) && !is_array($DICTIONARY))
					$DICTIONARY=array($DICTIONARY);
				foreach ($DICTIONARY as $dictionary) {
					if (!empty($dictionary)) {
						$id=$this->Framework->library->model->set($this->TABLE[0], array('user'=>$user, 'dictionary'=>$dictionary));
					}
				}
			}
		}
		return false;
	}
	
	public function delete($PARAM=array()) {
		$this->Framework->library->model->delete($this->TABLE[0], array('user'=>$PARAM));
		return false;
	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAM['user']))
			$WHERE[]="`t1`.`user`='".intval($PARAM['user'])."'";

		$where=(count($WHERE)>0)?' WHERE '.implode(' AND ',$WHERE).' ':'';
		
		$sql="SELECT 
			`t1`.*, 
			`t2`.`key` as `dictionary_key`, 
			`t2`.`name` as `dictionary_name`, 
			`t2`.`value` as `dictionary_value`, 
			`t3`.`id` as `group`,
			`t3`.`key` as `group_key`,
			`t3`.`name` as `group_name`,
			`t3`.`value` as `group_value`
			FROM `".$this->TABLE[0]."` `t1`
			INNER JOIN `".$this->Framework->dictionary->model->config->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`dictionary`)
			INNER JOIN `".$this->Framework->dictionary->model->config->TABLE[1]."` `t3` ON (`t3`.`id`=`t2`.`parent`)
			{$where}
			ORDER BY `t1`.`user`, `t3`.`sort`, `t3`.`id`, `t2`.`sort`, `t2`.`id`
		";
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			if (!isset($DATA[$ROW['group_key']]))
				$DATA[$ROW['group_key']]=array('id'=>$ROW['group'], 'key'=>$ROW['group_key'], 'name'=>$ROW['group_name'], 'value'=>$ROW['group_value']);
			$key=$ROW['dictionary_key']?$ROW['dictionary_key']:$ROW['dictionary'];
			$DATA[$ROW['group_key']]['ELEMENT'][]=array('id'=>$ROW['dictionary'], 'key'=>$ROW['dictionary_key'], 'name'=>$ROW['dictionary_name'], 'value'=>$ROW['dictionary_value']);
			$DATA[$ROW['group_key']]['ID'][]=$ROW['dictionary'];
		}

		return $DATA;
	}
	
}//\class
?>