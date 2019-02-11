<?php
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Интерфейс для классов работы с базой данных     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\DataBase;

interface db {
    public function set($sql);
    public function id();
    public function get($result, $both);
    public function count($res);
    public function quote($el);
    public function connect();       
    public function install();
}

?>