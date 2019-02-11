<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\xml;
//https://xml.yandex.ru
final class Config extends \FrameWork\Common {
	private $TABLE=array(
				'limit'=>'api_yandex_xml_limit',

			);
	private $CONFIG=array(
				'api'=>'https://yandex.ru/search/xml',//ссылка на API
				'user'=>'',//пользователь Яндекс.XML
				'key'=>'',//ключ Яндекс.XML
				'region'=>'',//регион Яндекс.XML
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