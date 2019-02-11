<?php
/////////////////////////////////////////////////////////////
/// Name: Файл для запуска по CRON                        ///
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
if ($Framework->CONFIG['http'])
	$Framework->library->header->get('http');

$Framework->cron->execute->set(array('time'=>$Framework->direct->model->config->CONFIG['max_execution_time']));
//\Запускаем контроллер//

//Обработка ошибок//
$Framework->library->error->get(2);
if ($Framework->CONFIG['http']) 
	echo (time()-$Framework->CONFIG['time'])." sec. / ".date('H:i:s d/m/Y ').' <META HTTP-EQUIV="REFRESH" CONTENT="300">';
//\Обработка ошибок//
?>