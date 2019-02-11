<?
$site_id = trim($_REQUEST["cwizsite_id"]);
define("SITE_ID", $site_id);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule("concept.quiz");
$arVal = array();
$arVal = CConceptWqec::QuizTotal($_REQUEST, $_REQUEST["cquiz_type_point"]);

echo $arVal["TOTAL_POINTS"];
?>