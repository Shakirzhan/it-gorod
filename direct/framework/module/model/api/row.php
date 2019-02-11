<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\model\api;

final class Row extends \FrameWork\Common {
	
	private $table='';
	
	public function __construct () {
		parent::__construct();
		$this->table=$this->Framework->model->config->TABLE['row'];
	}
	
	public function set($PARAM=array()) {
		return $this->Framework->model->api->model->set($this->table, $PARAM);
	}
	
	public function get($PARAM=array(), $ORDER=array('id'), $FIELDS=array(), $LIMIT=array('page'=>0, 'number'=>0), $GROUP=array()) {
		$DATA=array();
		$DATA=$this->Framework->model->api->model->get($this->table, $PARAM, $ORDER, $FIELDS, $LIMIT, $GROUP);
		return $DATA;
	}
	
	public function delete($PARAM=array()) {
	
	}
	
}//\class
?>