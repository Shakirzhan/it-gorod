<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;
global $APPLICATION;
global $USER;

global $CACHE_MANAGER;

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Main\Page\Asset as Asset;

CModule::IncludeModule("iblock");

//
$arParams["IBLOCK_CODE"] = 'concept_quiz_questions';


if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}


$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;


$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}


if($this->startResultCache(false, array($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS))
{
	$arResult = array();
	//osvobozhdenie massivov, na vzyakii slychai
	unset($arResult["SECTION_INFO"]);
	unset($arResult["ITEMS"]);

	// polychenie osnovnuh svoistv razdela 
	$arSelect = Array('SORT','IBLOCK_ID', 'ID', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'CODE');
	$arFilter = Array('ID'=>$arParams["WIZ_SECTION_NAME"], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y');
	$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arSelect);

	while($ar_result = $db_list->GetNext())
	    $arResult["SECTION_INFO"] = $ar_result;

	if(empty($arResult["SECTION_INFO"]))
		return false;


	// polychenie dopolnitelnuh svoistv razdela 
	$arFilter = Array('IBLOCK_ID' => $arResult["SECTION_INFO"]["IBLOCK_ID"], 'ID' => $arResult["SECTION_INFO"]["ID"]);
	$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, array("UF_*"));

	while($ar_result = $db_list->GetNext())
    	$arResult["SECTION_INFO"] = $ar_result;
	

	
    if($arResult["SECTION_INFO"]["UF_CWIZ_PROGR_VIEW"]>0)
    {
    	$arResult["SECTION_INFO"]["UF_CWIZ_PROGR_VIEW"] = CUserFieldEnum::GetList(array(), array(
		"ID" => $arResult["SECTION_INFO"]["UF_CWIZ_PROGR_VIEW"],
		))->GetNext();

    }
	
	if($arResult["SECTION_INFO"]["UF_CWIZ_TYPE_VIEW"]>0)
	{
		$arResult["SECTION_INFO"]["UF_CWIZ_TYPE_VIEW"] = CUserFieldEnum::GetList(array(), array(
		"ID" => $arResult["SECTION_INFO"]["UF_CWIZ_TYPE_VIEW"],
		))->GetNext();
	}

	

	if($arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"] > 0)
	{
		$arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"] = CUserFieldEnum::GetList(array(), array(
		"ID" => $arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"],
		))->GetNext();
	}

	if($arResult["SECTION_INFO"]["UF_QUIZ_MASK"]> 0)
	{
	    $arResult["SECTION_INFO"]["UF_QUIZ_MASK_ENUM"] = CUserFieldEnum::GetList(array(), array(
	        "ID" => $arResult["SECTION_INFO"]["UF_QUIZ_MASK"],
	    ))->GetNext();
	}
	
	if(strlen($arResult["SECTION_INFO"]["UF_TYPE_CALC"]) > 0)
    {
        $arResult["SECTION_INFO"]["UF_TYPE_CALC_ENUM"] = CUserFieldEnum::GetList(array(), array(
            "ID" => $arResult["SECTION_INFO"]["UF_TYPE_CALC"],
        ))->GetNext();
    }



	// sobiraetsya massiv c voprosami, isklychaya elementu s resyltatami
	$arFilter = Array('IBLOCK_ID' => $arResult["SECTION_INFO"]["IBLOCK_ID"], "SECTION_ID" => $arResult["SECTION_INFO"]["ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false);
	while($ob = $res->GetNextElement()){ 
			
		$arItem = $ob->GetFields();  
		$arItem["PROPERTIES"] = $ob->GetProperties();

		if($arItem["PROPERTIES"]["TYPE_ELEMENT"]["VALUE_XML_ID"] == 'result')
			continue;

		$arResult["ITEMS"][] = $arItem;
		
	}

	// 
	$this->includeComponentTemplate();
	
}

?>

