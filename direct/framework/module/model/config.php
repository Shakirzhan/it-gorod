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

final class Config extends \FrameWork\Common {
	private $TABLE=array(
				'access'=>'framework_access',
				'access_matrix'=>'framework_access_matrix',
				'cache'=>'framework_cache',
				'date'=>'framework_date',
				'datetime'=>'framework_datetime',
				'double'=>'framework_double',				
				'field'=>'framework_field',
				'file'=>'framework_file',
				'integer'=>'framework_integer',
				'row'=>'framework_row',
				'string'=>'framework_string',
				'table'=>'framework_table',
				'text'=>'framework_text',
				'time'=>'framework_time',
				'type'=>'framework_type',
				'user'=>'framework_user',
				'user_access'=>'framework_user_access',
				'error'=>'framework_error',
			);
	
	private $CONFIG=array(
			'error_day'=>31,
			'error_number'=>10000,
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
	
	public function __set($name, $value=null) {
		if (isset($this->CONFIG[$name]))
			$this->CONFIG[$name]=$value;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
	}
	
}//\class
?>