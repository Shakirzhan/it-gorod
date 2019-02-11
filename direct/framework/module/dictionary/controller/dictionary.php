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
namespace FrameWork\module\dictionary\controller;

final class Dictionary extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
	}	

}//\class
?>