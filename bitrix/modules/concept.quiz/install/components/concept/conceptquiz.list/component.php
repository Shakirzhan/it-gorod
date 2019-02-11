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

use Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock;

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);

$arParams["TOP_DEPTH"] = intval($arParams["TOP_DEPTH"]);
if($arParams["TOP_DEPTH"] <= 0)
	$arParams["TOP_DEPTH"] = 2;
    

$arResult["SECTIONS"]=array();

/*************************************************************************
			Work with cache
*************************************************************************/
if($this->startResultCache())
{
	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_CODE" => $arParams["IBLOCK_CODE"],
		"CNT_ACTIVE" => "Y",
	);

	$arResult["SECTION"] = false;
	$intSectionDepth = 0;
    

	$arSelect = Array('SORT','IBLOCK_ID', 'ID', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'CODE', 'NAME', 'IBLOCK_TYPE_ID');
	$arFilter = Array('IBLOCK_CODE'=>$arParams["IBLOCK_CODE"], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y');
	$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arSelect);

	while($ar_result = $db_list->GetNext())
	{	
		
	    $arResult["SECTIONS"][] = $ar_result;
	}

	 $arResult["IBLOCK_ID"] = $arResult['SECTIONS'][0]["IBLOCK_ID"];
	 $arResult["IBLOCK_TYPE_ID"] = $arResult['SECTIONS'][0]["IBLOCK_TYPE_ID"];

	 

	$this->includeComponentTemplate();
}

?>
