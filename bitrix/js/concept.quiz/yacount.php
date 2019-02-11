<?
$site_id = trim($_REQUEST["site_id"]);
define("SITE_ID", $site_id);
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');


$iblock_id = trim($_REQUEST["iblock_id"]);
$quiz_id = trim($_REQUEST["quiz_id"]);
$type = trim($_REQUEST["type"]);
$res_id = trim($_REQUEST["res_id"]);


$arSelect = array("ID", "UF_CWIZ_YA_ID", "UF_CWIZ_GOAL_BEGIN","UF_ID_GOOGLE","UF_ID_GTM","UF_GA_GOAL_CATEGORY","UF_GA_GOAL_ACTION","UF_GTM_EVENT","UF_GTM_GOAL_CATEGORY","UF_GTM_GOAL_ACTION");
$arFilter = Array('IBLOCK_ID' => $iblock_id, 'ID' => $quiz_id);
$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arSelect);

while($ar_result = $db_list->GetNext())
    $arResult["IDS"] = $ar_result;


$idYa = $arResult["IDS"]["UF_CWIZ_YA_ID"];
$idGoogle = $arResult["IDS"]["UF_ID_GOOGLE"];
$idGtm = $arResult["IDS"]["UF_ID_GTM"];


if($type == "begin")
{

	$yaGoal = $arResult['IDS']['UF_CWIZ_GOAL_BEGIN'];

	$gogCat = $arResult['IDS']['UF_GA_GOAL_CATEGORY'];
	$gogAct = $arResult['IDS']['UF_GA_GOAL_ACTION'];

	$gtmEvn = $arResult['IDS']['UF_GTM_EVENT'];
	$gtmCat = $arResult['IDS']['UF_GTM_GOAL_CATEGORY'];
	$gtmAct = $arResult['IDS']['UF_GTM_GOAL_ACTION'];

}

if($type == "result")
{

	$arSelect = array("PROPERTY_GOOGLE_GOAL_CATEGORY_RESULT", "PROPERTY_GTM_GOAL_CATEGORY_RESULT","PROPERTY_GOOGLE_GOAL_ACTION_RESULT","PROPERTY_GTM_GOAL_ACTION_RESULT","PROPERTY_GTM_EVENT_RESULT", "PROPERTY_GOAL_RESULT");
	$arFilter = Array("IBLOCK_ID" => $iblock_id,"ID"=> $res_id, "SECTION_ID" => $quiz_id);

	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false,false, $arSelect);
	while(($ob = $res->GetNextElement())){ 
	    $arResult["GOALS"]= $ob->GetFields();
	}



	$yaGoal = $arResult['GOALS']['PROPERTY_GOAL_RESULT_VALUE'];

	$gogCat = $arResult["GOALS"]['PROPERTY_GOOGLE_GOAL_CATEGORY_RESULT_VALUE'];
	$gogAct = $arResult["GOALS"]['PROPERTY_GOOGLE_GOAL_ACTION_RESULT_VALUE'];

	$gtmEvn = $arResult["GOALS"]['PROPERTY_GTM_EVENT_RESULT_VALUE'];
	$gtmCat = $arResult["GOALS"]['PROPERTY_GTM_GOAL_CATEGORY_RESULT_VALUE'];
	$gtmAct = $arResult["GOALS"]['PROPERTY_GTM_GOAL_ACTION_RESULT_VALUE'];

}

if($type == "send")
{
	$arSelect = array("PROPERTY_GOOGLE_GOAL_CATEGORY_SEND","PROPERTY_GOOGLE_GOAL_ACTION_SEND","PROPERTY_GTM_GOAL_CATEGORY_SEND","PROPERTY_GTM_GOAL_ACTION_SEND","PROPERTY_GTM_EVENT_SEND", "PROPERTY_GOAL_SEND");
	$arFilter = Array("IBLOCK_ID" => $iblock_id,"ID"=> $res_id, "SECTION_ID" => $quiz_id);

	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false,false, $arSelect);
	while(($ob = $res->GetNextElement())){ 
	    $arResult["GOALS"]= $ob->GetFields();
	}

	$yaGoal = $arResult['GOALS']['PROPERTY_GOAL_SEND_VALUE'];

	$gogCat = $arResult["GOALS"]['PROPERTY_GOOGLE_GOAL_CATEGORY_SEND_VALUE'];
	$gogAct = $arResult["GOALS"]['PROPERTY_GOOGLE_GOAL_ACTION_SEND_VALUE'];

	$gtmEvn = $arResult["GOALS"]['PROPERTY_GTM_EVENT_SEND_VALUE'];
	$gtmCat = $arResult["GOALS"]['PROPERTY_GTM_GOAL_CATEGORY_SEND_VALUE'];
	$gtmAct = $arResult["GOALS"]['PROPERTY_GTM_GOAL_ACTION_SEND_VALUE'];
}



if(strlen($idYa) > 0 && strlen($yaGoal) > 0)
{
    echo '<script>yaCounter'.trim($idYa).'.reachGoal("'.trim($yaGoal).'");</script>';
}

if(strlen($idGoogle) > 0 && strlen($gogCat) > 0 && strlen($gogAct) > 0)
{
    echo '<script>ga("send", "event", "'.trim($gogCat).'", "'.trim($gogAct).'");</script>';
}

if(strlen($idGtm) > 0 && strlen($gtmEvn) > 0)
{
    echo '<script>dataLayer.push({"event": "'.trim($gtmEvn).'", "eventCategory": "'.trim($gtmCat).'", "eventAction": "'.trim($gtmAct).'"});</script>';
}
?>
