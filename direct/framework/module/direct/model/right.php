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

final class Right extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
	
	}
	
	public function get($PARAM=array()) {
		
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$DATA=$this->Framework->direct->model->path->get($PARAM);
			if ($this->Framework->user->controller->controller->USER['group']==1 || in_array($this->Framework->user->controller->controller->USER['id'], $DATA['USERS']))
				return true;
		}
		return false;
	}
	
	
}//\class
?>