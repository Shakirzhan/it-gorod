<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $DB;

if( strlen( $_REQUEST['CAPTCHA_WORD'] ) <= 0 || strlen( $_REQUEST['CAPTCHA_SID'] ) <= 0 ){ echo 'false';}

$res = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '".$DB->ForSQL( $_REQUEST['CAPTCHA_SID'], 32 )."' ");

$ar = $res->Fetch(); 

if ($ar["CODE"]!=strtoupper($_REQUEST["CAPTCHA_WORD"])){
	echo 'false';
} else{
	echo 'true';
}