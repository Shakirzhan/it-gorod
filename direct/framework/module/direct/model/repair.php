<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\model;

final class Repair extends \FrameWork\Common {
	private $debug=false;
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
		$this->get($PARAM);
	}
	
	public function get($PARAM=array()) {
		//Чиним сломавшиеся таблицы//
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['company'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['company']);		
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['group'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['group']);
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['banner'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['banner']);
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['phrase'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['phrase']);
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['retargeting'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['retargeting']);		
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['statistic'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['statistic']);
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['statistic_price'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['statistic_price']);
		if (!$this->Framework->db->check($this->Framework->direct->model->config->TABLE['statistic_price_temp'], true))
			$this->Framework->db->repair($this->Framework->direct->model->config->TABLE['statistic_price_temp']);		
		//\Чиним сломавшиеся таблицы//
	}
	
}//\class
?>