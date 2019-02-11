<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с заголовками           ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

include_once dirname(__FILE__).'/morphy/src/common.php';

final class Morphy extends \phpMorphy {
	private $Framework=null;
	
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
		parent::__construct(dirname(__FILE__).'/morphy/dicts', 'ru_RU', array(
	
					'storage' => PHPMORPHY_STORAGE_FILE,
	
					'predict_by_suffix' => true,
	
					'predict_by_db' => true,
	
					'graminfo_as_text' => true,
	
				));
	}
	
	private function __clone()
	{
	}
	
	public function get($string='') {
		return $this->getAllForms( array(0=>mb_strtoupper($string)) );
	}


}//\class
?>