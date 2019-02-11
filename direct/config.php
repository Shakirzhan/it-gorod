<?php
$CONFIG=array('name'=>'Direct-automate.ru');

//Настройки баз данных//
$CONFIG['DATABASE']=array(
						'class'=>'mysql',//Класс подключения к базе данных (поддерживаются: mysql, sqlite)
						'host'=>'localhost',//Хост
						'port'=>'3306',//Порт
						'user'=>'root',//Имя пользователя
						'password'=>'',//Пароль
						'name'=>'direct',//Название базы данных
						'charset'=>'UTF8',//Кодировка базы данных
						'prefix'=>'direct_',//Префикс таблиц
);
//\Настройки баз данных//

//Временной пояс//
$CONFIG['timezone']='Europe/Moscow';
//\Временной пояс//

//Настройки шаблонизатора//
$CONFIG['TEMPLATE']=array(
						'class'=>'smarty',//Класс шаблонизатора (поддерживаются: smarty, xslt, php)
						'dir'=>dirname(__FILE__).'/files/templates/',//Путь к папке с шаблонами
						'compile'=>dirname(__FILE__).'/files/templates_c/',//Путь к папке с откомпилированными шаблонами
						'cache'=>dirname(__FILE__).'/files/templates_cache/',//Путь к папке с закешированными шаблонами
						'config'=>dirname(__FILE__).'/files/templates_config/',//Путь к папке с конфигом для шаблонов
);
//\Настройки шаблонизатора//

//Параметры контроллера//
$CONFIG['controller']='direct/index/index';//Контроллер по умолчанию (формат: модуль/класс/метод/параметр)
//\Параметры контроллера//

//Настройка путей относительно текущей папки//
$CONFIG['document_root']=dirname(__FILE__).'/';//Путь к корню сайта
$CONFIG['dir']=dirname(__FILE__).'/framework/';//Путь к папке фреймворка
$CONFIG['module_dir']=$CONFIG['dir'].'module/';//Путь к папке с модулями приложения
$CONFIG['files_dir']=dirname(__FILE__).'/files/';//Путь к папке с медиа файлами
$CONFIG['http_dir']=stripslashes(dirname((!empty($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(!empty($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'')).(dirname((!empty($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:(!empty($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:'')))=='/'?'':'/')));//Путь относительно корня сайта к текущей папке без слеш на конце
?>