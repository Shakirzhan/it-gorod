<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин работа с данными                   ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Model {
	private $Framework=false;
	private $table='';
	
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
	}
	
	public function __call ($name, $ARGUMENTS=array()) {		
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}//\function
	
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
	public function __get($name) {
		$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;		
	}
	
	private function __clone()
	{
	}	
	
	//Метод модель запись в таблицу//
	public function set ($table='', $PARAMS=array(), $primary_key='id', $replace=false, $insert=false) {
		if (empty($primary_key))
			$primary_key='id';
		$FIELD=$this->Framework->db->field($table);
		$SET=array();
		$WHERE=array();
		foreach($PARAMS as $key=>$value)
		{
		if (in_array($key, $FIELD))
			if ($key!=$primary_key || ($key==$primary_key && !empty($value) && !is_array($value))) {
				$SET[]="`".$key."`='".$this->Framework->db->quote($value)."'";
				if ($key==$primary_key)
					$WHERE[]="`".$primary_key."` ='". $this->Framework->db->quote($PARAMS[$primary_key])."'";
			}
			elseif ($key==$primary_key && !empty($value) && is_array($value)) {
				$set='';
				foreach ($value as $val)
					if (!empty($val))
						$set.=(!empty($set)?' OR ':'')."`".$key."`='".$this->Framework->db->quote($val)."'";
				$WHERE[]='('.$set.')';
			}
		}
	
		if (count($SET)>0)
		{	
			if (!empty($PARAMS[$primary_key]) && empty($replace) && empty($insert))
			{
				$SELECT=$this->get($table, array($primary_key=>$PARAMS[$primary_key]));
			}
			
			if (!empty($PARAMS[$primary_key]) && !empty($SELECT[0]) && empty($insert))
			{
				$sql = "UPDATE
					`".$this->Framework->db->quote($table)."`
				SET
					".implode(',',$SET)."
				WHERE
					".implode(' AND ',$WHERE)."";
				$result = $this->Framework->db->set($sql);
			} elseif (!empty($replace) && empty($insert)) {
				$sql = "REPLACE 
					`".$this->Framework->db->quote($table)."`
				SET
					".implode(',',$SET)."
				";
				$result = $this->Framework->db->set($sql);
				$PARAMS[$primary_key] = $this->Framework->db->id();
			} else {
				$sql = "INSERT INTO
					`".$this->Framework->db->quote($table)."`
				SET
					".implode(',',$SET)."
				";
				$result = $this->Framework->db->set($sql);
				$PARAMS[$primary_key] = $this->Framework->db->id();
			}
			return ($result?$PARAMS[$primary_key]:0);
		}//\if
		return false;
	}
	//\Метод модель запись в таблицу//
	
	//Метод модель получить записи из таблицы//
	public function get ($table='', $PARAMS=array(), $ORDER=array(), $FIELDS=array())
	{
		$DATA=array();

		//Условия выборки//	
		$WHERE=array();
		foreach($PARAMS as $key=>$value)
		{
			if (is_array($value)) {
				$set='';
				foreach ($value as $val)
					$set.=(!empty($set)?' OR ':'')."`".$key."`='".$this->Framework->db->quote($val)."'";
				$WHERE[]='('.$set.')';
			} else
				$WHERE[]="`".$key."`='".$this->Framework->db->quote($value)."'";
		}
		//Условия выборки//
		
		//Сортировка//
		$ORDERS=array();
		foreach($ORDER as $key=>$value)
		{
			if (is_numeric($key))
				$ORDERS[]="`".$value."` ASC";
			else
				$ORDERS[]="`".$key."` ".($value?$value:'ASC')."";
		}		
		//\Сортировка//
		
		//Поля//
		$fields='';

		if (!empty($FIELDS) && is_array($FIELDS) && !empty($GROUP) && is_array($GROUP)) 
			$FIELDS=array_merge($FIELDS, $GROUP);
		
		if (!empty($FIELDS) && is_array($FIELDS)) {
			foreach ($FIELDS as $value) {
				$fields.=($fields?',':'').'`'.$this->Framework->db->quote($value).'`';			
			}
		}
		if (!$fields)
			$fields='*';
		//\Поля//
			
		if ($table)
		{
	
		$sql = "SELECT {$fields} FROM
					`".$this->Framework->db->quote($table)."`
				".(count($WHERE)>0?" WHERE ".implode(' AND ',$WHERE)." ":'').
				(count($ORDERS)>0?' ORDER BY '.implode(', ',$ORDERS).' ':'');
			$result = $this->Framework->db->set($sql);
			if ($result)
			{
				while ($ROW = $this->Framework->db->get($result))
				{
					$DATA[]=$ROW;
				}//\while
			}//\if
	
		}//\if
	
		return $DATA;
	}
	//\Метод модель получить записи из таблицы//
	
	//Метод модель удалить записи из таблицы//
	public function delete ($table='', $PARAMS=array())
	{
		$DATA=array();
	
		$WHERE=array();
		foreach($PARAMS as $key=>$value)
		{
			$WHERE[]="`".$key."`='".$this->Framework->db->quote($value)."'";
		}
	
		$where=count($WHERE)>0?' WHERE '.implode(' AND ',$WHERE).' ':'';
	
		$sql = "DELETE FROM
				`".$this->Framework->db->quote($table)."`
				{$where}
			";
		$result = $this->Framework->db->set($sql);
	
		return $DATA;
	}
	//\Метод модель удалить записи из таблицы//
	
	//Метод модель получить родителей из таблицы//
	public function parent ($table='', $id=0, $level=0, $FIELD=array('id'=>'id', 'parent'=>'parent'), $step=0)
	{
		$DATA=array();
			
		if ($table && intval($id)>0 && !empty($FIELD['id']) && !empty($FIELD['parent']) && (empty($level) || $step<$level))
		{
	
			$sql = "SELECT `".$this->Framework->db->quote($FIELD['parent'])."` as `id` FROM
					`".$this->Framework->db->quote($table)."`
					WHERE
					`".$this->Framework->db->quote($FIELD['id'])."`='".intval($id)."'
				";
			$result = $this->Framework->db->set($sql);
			if ($result)
			{
				$ROW = $this->Framework->db->get($result);
				if (!empty($ROW['id'])) {
					$DATA=array_merge($DATA, array($ROW['id']), $this->parent($table, $ROW['id'], $level, $FIELD, $step+1));
					
				}
				
				
			}//\if
	
		}//\if
	
		return $DATA;
	}
	//\Метод модель получить записи из таблицы//
	
	public function tree_set ($DATA=array(), $id=0) {
		if (!empty($id)) {
			$result=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->CONFIG['TABLES']['tree']."` 
													WHERE `id`='".intval($id)."'");
			$ROW=$this->Framework->db->get($result);
			if (!empty($ROW['id'])) {
				$id=$ROW['id'];
			}
			else {
				$this->Framework->library->error->set('Нет строки с таким id="'.$id.'" в таблице: "'.$this->Framework->CONFIG['TABLES']['tree'].'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				return false;
			}
		} 
		else
			$id=0;
		
		$this->__insert ($DATA, $id);

		return true;
	}
	
	public function tree_get ($id=0, $level=0, $list=false) {
		$id=intval($id);
		if (!$id>0)
			$id=0;
		$level=intval($level);
		if (!$level>=0)
			$level=-1;
			
		return $this->__select ($id, $level, $list);
	}
	
	private function __type (&$var) {
		$type=gettype($var);
			
		switch ($type) {
			case 'double':
				$type='double';
				$value=&$var;
			break;
			
			case 'string':
				$type='string';
				$value=&$var;
			break;
			
			case 'array':
			case 'object':
				$type='integer';
				$value=0;
			break;
			
			case 'boolean':
			case 'integer':
			case 'resource':
			case 'NULL':
			default: 
				$type='integer';
				$value=intval($var);
			break;
		}
	return array(0=>$type, 1=>$var, 'field'=>$type, 'value'=>$value);
	}
	
	private function __insert ($DATA=array(), $id=0) {
		if (!is_array($DATA) && !is_object($DATA)) 
			$DATA=array($DATA);
		foreach ($DATA as $key=>$VALUE) {
			if (!is_array($VALUE) && !is_object($VALUE)) {
				$TYPE=$this->__type($VALUE);
				$result=$this->Framework->db->set("INSERT INTO `".$this->Framework->CONFIG['TABLES']['data']."` 
														SET 
														`".$TYPE['field']."`='".$this->Framework->db->quote($TYPE['value'])."'
														");
				if ($result) {
					$data=$this->Framework->db->id();
					$result=$this->Framework->db->set("INSERT INTO `".$this->Framework->CONFIG['TABLES']['tree']."` 
															SET 
															`parent`='".intval($id)."',
															`key`='".$this->Framework->db->quote($key)."',
															`data`='".$data."'
															");
				}
			}
			elseif (is_array($VALUE) || is_object($VALUE)) {
				$result=$this->Framework->db->set("INSERT INTO `".$this->Framework->CONFIG['TABLES']['tree']."` 
														SET 
														`parent`='".intval($id)."',
														`key`='".$this->Framework->db->quote($key)."',
														`data`='0'
														");
				if ($result) {
					$parent=$this->Framework->db->id();
					if ($parent>0)
						$this->__insert ($VALUE, $parent);
				}
			}
		}
	}
	
	private function __select ($id=0, $level=0, $list=false, $deep=0) {
		$DATA=array();
		if ($level==0 && $id==0)
			return array();
		if ($id>0 && $deep==0) {
			$result=$this->Framework->db->set("SELECT `t1`.*, `t2`.`string`, `t2`.`integer`, `t2`.`double` FROM `".$this->Framework->CONFIG['TABLES']['tree']."` `t1`
													LEFT JOIN `".$this->Framework->CONFIG['TABLES']['data']."` `t2` ON (`t2`.`id`=`t1`.`data`)
													WHERE `t1`.`id`='".intval($id)."'
													");
			$ROW=$this->Framework->db->get($result);
			if (!empty($ROW['id'])) {
				$ROW['level']=$deep;
				$DATA[]=$ROW;
			} 
			else {
				$this->Framework->library->error->set('Нет строки с таким id="'.$id.'" в таблице: "'.$this->Framework->CONFIG['TABLES']['tree'].'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				return array();
			}
		}
		elseif ($id==0 && $deep==0 && !$list) {
			$DATA[]=array('id'=>0, 'parent'=>NULL, 'key'=>0, 'data'=>0, 'string'=>NULL, 'integer'=>NULL, 'double'=>NULL, 'level'=>$deep);
		}

		if (($level>0 && $deep<$level) || $level==-1) {
			$result=$this->Framework->db->set("SELECT `t1`.*, `t2`.`string`, `t2`.`integer`, `t2`.`double` FROM `".$this->Framework->CONFIG['TABLES']['tree']."` `t1`
													LEFT JOIN `".$this->Framework->CONFIG['TABLES']['data']."` `t2` ON (`t2`.`id`=`t1`.`data`)
													WHERE `t1`.`parent`='".intval($id)."'
													ORDER BY `t1`.`id`
													");
			while ($ROW=$this->Framework->db->get($result)) {
				if (!empty($ROW['id'])) {
					$ROW['level']=$deep+($list?0:1);
					$DATA[]=$ROW;
					$DATA=array_merge($DATA, $this->__select($ROW['id'], $level, $list, $deep+1));
				}
			}
		}
		
		if ($deep==0) {
			if ($list) {
				$ARRAY=array();
				for($i=0; $i<count($DATA); $i++) {
					$ARRAY[$i]=array(
						'id'=>&$DATA[$i]['id'],
						'parent'=>&$DATA[$i]['parent'],
						'key'=>&$DATA[$i]['key'],
						'data'=>&$DATA[$i]['data'],
						'level'=>&$DATA[$i]['level'],
					);
					if (!$DATA[$i]['data']>0) {
						$ARRAY[$i]['value']=NULL;
						$ARRAY[$i]['type']=NULL;
					}
					elseif ($DATA[$i]['string']!=NULL) {
						$ARRAY[$i]['value']=&$DATA[$i]['string'];
						$ARRAY[$i]['type']='string';
					}
					elseif ($DATA[$i]['integer']!=NULL) {
						$ARRAY[$i]['value']=&$DATA[$i]['integer'];
						$ARRAY[$i]['type']='integer';
					}
					elseif ($DATA[$i]['double']!=NULL) {
						$ARRAY[$i]['value']=&$DATA[$i]['double'];
						$ARRAY[$i]['type']='double';
					}
					else {
						$ARRAY[$i]['value']=NULL;
						$ARRAY[$i]['type']=NULL;
					}
				}
			}
			else {
				$ARRAY=array();
				$KEYS=array();
				for($i=0; $i<count($DATA); $i++) {
						$KEYS=array_slice($KEYS, 0, $DATA[$i]['level']);
						if (count($KEYS)>0) 
							$ELEMENT=&$this->Framework->lib()->get_array_element_by_keys($ARRAY, $KEYS);
						else
							$ELEMENT=&$ARRAY;
						
						if (!$DATA[$i]['data']>0)
							$ELEMENT[$DATA[$i]['key']]=NULL;
						elseif ($DATA[$i]['string']!=NULL)
							$ELEMENT[$DATA[$i]['key']]=&$DATA[$i]['string'];
						elseif ($DATA[$i]['integer']!=NULL)
							$ELEMENT[$DATA[$i]['key']]=&$DATA[$i]['integer'];
						elseif ($DATA[$i]['double']!=NULL)
							$ELEMENT[$DATA[$i]['key']]=&$DATA[$i]['double'];
						else
							$ELEMENT[$DATA[$i]['key']]=NULL;
		
						$KEYS[]=$DATA[$i]['key'];
				}
			}
			return $ARRAY;
		} 
		else
			return $DATA;
	}
	
	public function search($PARAMS=array(), $ORDERS=array(), $LIMITS=array()) {
		$DATA=array();
		if (!is_array($PARAMS))
			$PARAMS=array($PARAMS);
		$PARAM=array();
		if (isset($PARAMS[0]))	{
			$PARAM['id']=array();
			foreach ($PARAMS as $key=>$VALUE) 
				if (is_numeric($key)) 
					if (!is_array($VALUE))		
						$PARAM['id']=array_merge($PARAM['id'], array($VALUE));
					else
						$PARAM['id']=array_merge($PARAM['id'], $VALUE);
		}
		else 
			foreach ($PARAMS as $key=>$VALUE) {
				if (is_string($key)) {
					$PARAM[$key]=array();
					if (!is_array($VALUE))		
						$PARAM[$key]=array_merge($PARAM[$key], array($VALUE));
					else
						$PARAM[$key]=array_merge($PARAM[$key], $VALUE);
				}
			}

		$where='';
		foreach ($PARAM as $key=>$VALUE) {
			if (is_array($VALUE) && count($VALUE)>0) {
				$where.=($where?' AND ':'').'(';
				$count=0;
				foreach ($VALUE as $value) {
					$where.=($count>0?' OR ':'').'`'.$key."`='".$value."'";
					$count++;
				}
				$where.=')';
			}
		}
		$where=$where?' WHERE '.$where.' ':'';
					
		$sql="SELECT * FROM `".$this->Framework->CONFIG['TABLES']['data']."` 
			  {$where}  
			";
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result))
			$DATA[]=$ROW;	
		//echo '<pre>'.print_r($ROW, true).'</pre>';
		
		return $DATA;
	}
	
	//Метод преобразование массива таблиц//
	public function table (&$TABLE, $db=0) {
		if (!empty($TABLE) && is_array($TABLE) && isset($this->Framework->CONFIG['DATABASE'][$db]['prefix'])) {

			foreach ($TABLE as $key=>$value) {
				$TABLE[$key]=$this->Framework->CONFIG['DATABASE'][$db]['prefix'].$value;
			}
			$count=0;
			foreach ($TABLE as $key=>$value)
				if (!is_numeric($key)) {
					if (empty($TABLE[$count]))
						$TABLE[$count]=$TABLE[$key];
					$count++;
				}
					
		}
	}
	//\Метод преобразование массива таблиц//
	
}//\class
?>