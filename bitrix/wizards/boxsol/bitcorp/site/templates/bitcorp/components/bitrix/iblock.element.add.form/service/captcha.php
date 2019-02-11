<?
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
    define('NO_AGENT_CHECK', true);
    define("STOP_STATISTICS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    __IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/template.php");
    echo $APPLICATION->CaptchaGetCode();
}
?>