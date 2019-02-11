<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\model;

final class Query extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($table='', $PARAM=array()) {
		if (!empty($table) && !empty($PARAM) && is_array($PARAM)) {
			$TABLE=$this->Framework->model->api->table->get(array('id'=>$table));
			if (!empty($TABLE['ELEMENT'][0]))
				$TABLE=array_shift($TABLE['ELEMENT']);
			if (!empty($TABLE['id'])) {
				$FIELD=$this->Framework->model->api->field->get(array('table'=>$TABLE['id']), array(), array('id', 'key', 'type'), array(), array('key'));
				if (!empty($FIELD['ELEMENT'])) {
					
					foreach ($PARAM as $key=>&$value) {
						if (empty($key) || !in_array($key, $FIELD['KEY']) || is_array($value) || is_object($value))
							if ($key!='id')
								unset($PARAM[$key]);
					}
					
					if (!empty($PARAM) && is_array($PARAM)) {
						//Изменяем строку таблицы//
						$row=$this->Framework->model->api->row->set(array(
							'table'=>$TABLE['id'], 
							'id'=>(!empty($PARAM['id'])?(int)$PARAM['id']:0),
							'sort'=>(!empty($PARAM['status'])?(int)$PARAM['sort']:0),
							'status'=>(!empty($PARAM['status'])?1:0),
							'time'=>date('Y-m-d H:i:s'),
							)
						);
						if (!empty($PARAM['id']))
							unset($PARAM['id']);
						//\Изменяем строку таблицы//
						
						if (!empty($row)) {
						//Изменяем данные таблицы//
							$TYPE=$this->Framework->model->api->type->get(array(), array(), array('key'), array(), array('id'));
							
							foreach ($PARAM as $key=>&$value) {
								if (!empty($TYPE['ELEMENT'][$FIELD['ELEMENT'][$key]['type']]['key']) && !empty($FIELD['ELEMENT'][$key]['id'])) {
									
									//Читаем данные//
									$VALUE=$this->Framework->model->api->{$TYPE['ELEMENT'][$FIELD['ELEMENT'][$key]['type']]['key']}->get(array(
											'field'=>$FIELD['ELEMENT'][$key]['id'],
											'row'=>$row,
										),
										array(),
										array('id')
									);
									//\Читаем данные//
									
									//Записываем данные//
									$data=$this->Framework->model->api->{$TYPE['ELEMENT'][$FIELD['ELEMENT'][$key]['type']]['key']}->set(array(
											'id'=>!empty($VALUE['FIRST']['id'])?$VALUE['FIRST']['id']:0,
											'field'=>$FIELD['ELEMENT'][$key]['id'],
											'row'=>$row,
											'value'=>$value,
											'time'=>$this->Framework->library->time->datetime(),
										)
									);
									//\Записываем данные//
									
								}
							}
						//\Изменяем данные таблицы//
						} 
						
					}
				}
			}
		}
	}
	
	public function get($table='', $PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$WHERE=array();
		
		if (!empty($table)) {
			$TABLE=$this->Framework->model->api->table->get(array('id'=>$table));
			if (!empty($TABLE['ELEMENT'][0]))
				$TABLE=array_shift($TABLE['ELEMENT']);
			if (!empty($TABLE['id'])) {
				$FIELD=$this->Framework->model->api->field->get(array('table'=>$TABLE['id']), array(), array(), array(), array('id'));
				$ROW=$this->Framework->model->api->row->get(array('table'=>$TABLE['id']));
				$STRING=$this->Framework->model->api->string->get(array('row'=>$ROW['ID']), array(), array(), array(), array('row', 'field'));
				$TEXT=$this->Framework->model->api->text->get(array('row'=>$ROW['ID']), array(), array(), array(), array('row', 'field'));
				//echo '<pre>'.print_r($FIELD, true).'</pre>';
				//echo '<pre>'.print_r($ROW, true).'</pre>';
				//echo '<pre>'.print_r($STRING, true).'</pre>';
				//echo '<pre>'.print_r($TEXT, true).'</pre>';
			
				foreach ($ROW['ELEMENT'] as $key=>&$row) {
					//echo '='.$key.'-'.$row['id'].'<br>';
					foreach ($FIELD['ELEMENT'] as &$field) {
							if (isset($STRING['ELEMENT'][$row['id']][$field['id']]))
								$DATA['ELEMENT'][$key][$field['key']]=$STRING['ELEMENT'][$row['id']][$field['id']]['value'];
							if (isset($TEXT['ELEMENT'][$row['id']][$field['id']]))
								$DATA['ELEMENT'][$key][$field['key']]=$TEXT['ELEMENT'][$row['id']][$field['id']]['value'];
							
					}
				}
			}
		}
		
		
		return $DATA;
	}
	
	public function delete($PARAM=array()) {
	
	}
	
}//\class
?>