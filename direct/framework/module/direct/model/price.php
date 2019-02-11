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

final class Client extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array($PARAM);
		$Result = $this->Framework->direct->model->api->get ( 'GetClientInfo', $PARAM );
		if (!empty($Result->data[0]->Login))
			return $this->Framework->library->lib->objectToArray($Result->data);
		elseif (!empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить список клиентов ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return array();
	}
	
}//\class
?>