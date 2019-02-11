<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\file\model;

final class File extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function type($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('file'=>$PARAM);
		$type='text';	
		if (!empty($PARAM['file']) && file_exists($PARAM['file'])) {
			$SIZE=getimagesize($PARAM['file']);//1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF
			if (!empty($SIZE[2])) {
				switch ($SIZE[2]) {
					case 1:
						$type='gif';
					break;
					case 2:
						$type='jpeg';
					break;
					case 3:
						$type='png';
					break;
					case 4:
						$type='swf';
					break;
					case 5:
						$type='psd';
					break;
					case 6:
						$type='bmp';
					break;
					case 7:
						$type='tiff';
					break;
					default: 
						$type='text';
					break;
				}
			}
		}
		return $type;
	}
	
	public function set($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=array_merge($_REQUEST, $_FILES);
		if (empty($PARAM['time']))
			$PARAM['time']=DATE('Y-m-d H:i:s');
		
		$this->Framework->library->lib->array_empty($PARAM);
		
		$id=$this->Framework->library->model->set($this->Framework->file->model->config->TABLE[0], $PARAM);
		if ($id) {
			if (empty($PARAM['id']) && !empty($id))
				$PARAM['id']=$id;
		}
		if (!empty($PARAM['id']) && $id)
			return $PARAM['id'];
		return false;
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		if (!empty($PARAM['ID']) && is_array($PARAM['ID']))
			$WHERE[]="(`t1`.`id`='".implode("' OR `t1`.`id`='", $PARAM['ID'])."')";
		if (!empty($PARAM['PARENT']) && is_array($PARAM['PARENT']))
			$WHERE[]="(`t1`.`parent`='".implode("' OR `t1`.`parent`='", $PARAM['PARENT'])."')";
		if (!empty($PARAM['id'])) {
			if (!is_numeric($PARAM['id'])) {
				$ID=$this->Framework->library->model->get($this->Framework->file->model->config->TABLE[0], array('key'=>$PARAM['id']));
				if (!empty($ID[0]['id']))
					$PARAM['id']=$ID[0]['id'];
			}
			$WHERE[]="`t1`.`id`='".intval($PARAM['id'])."'";
		}
		if (!empty($PARAM['parent'])) {
			if (!is_numeric($PARAM['parent'])) {
				$PARENT=$this->Framework->library->model->get($this->Framework->file->model->config->TABLE[0], array('key'=>$PARAM['parent']));
				if (!empty($PARENT[0]['id']))
					$PARAM['parent']=$PARENT[0]['id'];
			}
			$WHERE[]="`t1`.`parent`='".intval($PARAM['parent'])."'";
		}
		if (!empty($PARAM['user']))
			$WHERE[]="`t1`.`user`='".$this->Framework->db->quote($PARAM['user'])."'";
		if (!empty($PARAM['key']))
			$WHERE[]="`t1`.`key`='".$this->Framework->db->quote($PARAM['key'])."'";
		if (!empty($PARAM['status']))
			$WHERE[]="`t1`.`status`='".intval($PARAM['status'])."'";
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
					FROM `".$this->Framework->file->model->config->TABLE[0]."` `t1`
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
					FROM `".$this->Framework->file->model->config->TABLE[0]."` `t1`
					{$where}
					{$order}
		".$DATA['PAGE']['limit'];
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			$DICTIONARY_GROUP=array('file');
			foreach ($DICTIONARY_GROUP as $key) {
				if (!empty($ROW[$key])) {
					$DICTIONARY=$this->Framework->dictionary->model->dictionary->get(array('id'=>$ROW[$key]));
					$ROW['DICTIONARY'][$key]=!empty($DICTIONARY['ELEMENT'][0])?$DICTIONARY['ELEMENT'][0]:'';
				}	
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
	
}//\class
?>