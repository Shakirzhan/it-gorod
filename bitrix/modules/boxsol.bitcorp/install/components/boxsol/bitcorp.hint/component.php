<?
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;
$showHint = $APPLICATION->get_cookie("SHOW_HINT");
$arResult["SHOW_HINT"] = $showHint;

if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["action"])>0 && $_POST["action"]=="bitcorpHintClose" && check_bitrix_sessid())
{	
	//set cookie to one day
	$APPLICATION->set_cookie("SHOW_HINT", "N", time()+60*60*24, SITE_DIR);
	//$APPLICATION->RestartBuffer();
	//die();	
}
$this->IncludeComponentTemplate();
?>