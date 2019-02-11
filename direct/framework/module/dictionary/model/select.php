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

final class Select extends \FrameWork\Common {
	
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
	
	public function set($PARAM=array()) {
		
	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		$count=0;
		foreach ($PARAM as $key=>$VALUE) {
			if (!empty($key)) {
				$group=$key;
				if (!is_numeric($key)) {
					$GROUP=$this->Framework->library->model->get($this->Framework->dictionary->model->config->TABLE['group'], array('key'=>$key));
					if (!empty($GROUP[0]['id']))
						$group=$GROUP[0]['id'];
					else
						$group=null;
				}
				if (!empty($VALUE) && !empty($group)) {
					if (!is_array($VALUE))
						$VALUE=array($VALUE);
					foreach ($VALUE as $value) {
						if (!empty($value) && !is_array($value) && !is_object($value)) {
							if (!is_numeric($value)) {
								$DICTIONARY=$this->Framework->library->model->get($this->Framework->dictionary->model->config->TABLE['dictionary'], array('parent'=>$group, 'key'=>$value));
								if (!empty($DICTIONARY[0]['id']))
									$value=$DICTIONARY[0]['id'];
								else
									$value=null;
							}
							if (!empty($value))
								$DATA[$key][]=$value;
						}
					}
					$count++;
				}
			}
			
		}
		return $DATA;
	}
	
}//\class
?>