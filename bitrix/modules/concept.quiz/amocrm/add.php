<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//error_reporting(-1);

$root=$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/concept.quiz/amocrm/";

require $root.'prepare.php'; #Здесь будут производиться подготовительные действия, объявления функций и т.д.
require $root.'auth.php'; #Здесь будет происходить авторизация пользователя
require $root.'account_current.php'; #Здесь мы будем получать информацию об аккаунте
require $root.'fields_info.php'; #Получим информацию о полях
require $root.'lead_add.php'; #Здесь будет происходить добавление сделки
require $root.'contacts_list.php'; #Получим информацию о контактах
require $root.'contact_update.php'; #Обновляем контакт
require $root.'contact_add.php'; #Здесь будет происходить добавление контакта
?>

<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>