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
namespace FrameWork\module\file\controller;

final class File extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
	}	
	
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>(string)$PARAM);
		if (!empty($PARAM['id'])) {
			$FILE=$this->Framework->file->model->file->get(array('id'=>$PARAM['id']));
			$FILE=!empty($FILE['ELEMENT'][0])?$FILE['ELEMENT'][0]:array();
			
			if (!empty($FILE)) {
				header("Expires: " . gmdate("D, d M Y H:i:s") . "GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
				header("Content-Type: ".$FILE['DICTIONARY']['file']['value']);
				echo $FILE['value'];
			}
		}
	}

}//\class
?>