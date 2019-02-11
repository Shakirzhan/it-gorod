<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Стартовый файл                                  /// 
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////

//Загружаем классы//
include_once(dirname(__FILE__).'/config.php');
include_once($CONFIG['dir'].'framework.php');
//\Загружаем классы//

//Запускаем движок FrameWork//
$Framework=FrameWork\Framework::singleton($CONFIG);
//\Запускаем движок FrameWork//

//Запускаем контроллер//
$Framework->library->controller->set();
//\Запускаем контроллер//
?>