<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\dictionary\model;

final class Model extends \FrameWork\Common {

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
	
	public function get() {
		$DATA=array();
		$GROUP=$this->Framework->dictionary->model->group->get(array('status'=>1), array('sort', 'id'), array(), array(), array());
		$DICTIONARY=$this->Framework->dictionary->model->dictionary->get(array('status'=>1), array('parent', 'sort', 'id'), array(), array(), array('parent', 'id'));
		foreach ($GROUP['ELEMENT'] as $VALUE) {
			$DATA[$VALUE['key']]=$VALUE;
			if (!empty($DICTIONARY['ELEMENT'][$VALUE['id']]) && is_array($DICTIONARY['ELEMENT'][$VALUE['id']]))
				foreach ($DICTIONARY['ELEMENT'][$VALUE['id']] as $ELEMENT) {
					$key=!empty($ELEMENT['key'])?$ELEMENT['key']:$ELEMENT['id'];
					$DATA[$VALUE['key']]['ELEMENT'][$key]=$ELEMENT;
				}
		}
		return $DATA;
	}
}//\class
?>