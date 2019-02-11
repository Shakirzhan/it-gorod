<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\direct;
//https://tech.yandex.ru/direct/doc/dg/concepts/about-docpage/
final class Config extends \FrameWork\Common {
	
	private $CONFIG=array(
				'url'=>'https://api.direct.yandex.com/json/v5/',//ссылка на API
				'oauth'=>'https://oauth.yandex.ru/token',
				'authorize'=>'https://oauth.yandex.ru/authorize',
				'id'=>'f5ff8f217d0d44b59d6e6e1d335e13f5',//oauth id приложения
				'updateprices_max'=>1000,
				'updateprices_call_max'=>3000,
				'threads'=>0,
				'max_execution_time'=>10800,
				'version'=>'2.0.0',
				'number'=>100,
				'login'=>'',
				'client_id'=>'',
				'client_secret'=>'',
				'token'=>'',
				'unit'=>'',//Расход за вызов текущего метода АПИ
				'limit'=>'',//Оставшиеся баллы
				'daily'=>'',//Суточный лимит баллов
				'error'=>'',
			);
	
	public function __construct () {
		parent::__construct();
	}
		
	public function __get($name) {
		if (!empty($name))
			if (strtoupper($name)=='CONFIG')
				return $this->CONFIG;
			elseif (isset($this->CONFIG[$name]))
				return $this->CONFIG[$name];
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