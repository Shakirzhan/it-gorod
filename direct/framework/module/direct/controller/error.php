<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс контроллер                                ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\controller;

final class Error extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
		
	}
	
	public function index($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['ERROR']=$this->Framework->model->error->get(array(), array('session'=>'desc', 'id'=>'desc'), array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));	
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	
	public function config($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['ERROR']=array(
				'php'=>$this->Framework->CONFIG['php'],
				'mysql'=>$this->Framework->db->version(),
				'fopen'=>$this->Framework->CONFIG['fopen'],
				'curl'=>$this->Framework->CONFIG['curl'],
				'json'=>function_exists('json_decode')?1:0,
				'ssl'=>function_exists('openssl_open')?1:0,
				'ip'=>$this->Framework->CONFIG['address'],
				'disable_functions'=>ini_get('disable_functions'),
				'max_execution_time'=>ini_get('max_execution_time'),
				'memory_limit'=>ini_get('memory_limit'),
				'max_input_vars'=>ini_get('max_input_vars'),
				'post_max_size'=>ini_get('post_max_size'),
				'default_socket_timeout'=>ini_get('default_socket_timeout'),
				'mbstring'=>function_exists('mb_internal_encoding')?1:0,
				'disable_classes'=>ini_get('disable_classes'),
				'PRIVILEGE'=>array(
					'status'=>$this->Framework->library->data->in_array(array('Alter', 'Create', 'Delete', 'Drop', 'Index', 'Insert', 'Select', 'Update'), $this->Framework->db->privilege()),
					'name'=>implode(', ', $this->Framework->db->privilege()),
				),
			);
			$DATA['TABLE']=$this->tables();
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}	
	
	public function table($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
			if (!empty($PARAM) && !empty($PARAM['table'])) {
				if (!empty($PARAM['repair'])) 
					$this->Framework->db->repair($PARAM['table']);
				if (!empty($PARAM['optimize'])) 
					$this->Framework->db->optimize($PARAM['table']);
				if (!empty($PARAM['truncate']) && in_array($PARAM['table'], array($this->Framework->direct->model->config->TABLE['statistic'], $this->Framework->direct->model->config->TABLE['statistic_price'], $this->Framework->direct->model->config->TABLE['statistic_price_temp'], $this->Framework->model->config->TABLE['error']))) 
					$this->Framework->db->truncate($PARAM['table']);
			}
			
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['TABLE']=$this->tables(true);
			$DATA['TRUNCATE']=array($this->Framework->direct->model->config->TABLE['statistic'], $this->Framework->direct->model->config->TABLE['statistic_price'], $this->Framework->direct->model->config->TABLE['statistic_price_temp'], $this->Framework->model->config->TABLE['error']);
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}	
	
	private function tables($check=false) {
		$DATA=array();
		$DATA['ELEMENT']=$this->Framework->db->table();
		$DATA['size']=0;
		foreach ($DATA['ELEMENT'] as &$VALUE) {
			$DATA['size']+=$VALUE['size'];
			if ($check)
				$VALUE['status']=$this->Framework->db->check($VALUE['name'], true);
		}
		$DATA['mb']=ceil($DATA['size']/1024000);
		return $DATA;
	}
	
	public function update() {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			
			$this->Framework->template->set('DATA', $DATA);
			echo $this->Framework->template->get('index.html');
		}
	}
	
}//\class
?>