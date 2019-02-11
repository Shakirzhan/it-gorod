<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;


$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arResult = array();

if($this->StartResultCache())
{
	if(!CModule::IncludeModule("iblock") || $arParams["IBLOCK_ID"] < 0)
	{
		$this->AbortResultCache();
	}
	else
	{
		$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"GLOBAL_ACTIVE"=>"Y",
			"IBLOCK_ACTIVE"=>"Y",
			"ACTIVE" => "Y",			
		);
		$arOrder = Array(
			"SORT"=>"ASC"
		);
		$arSelect = Array(
			"ID",
			"NAME",
			"IBLOCK_ID",
			"DETAIL_PAGE_URL"
		);

		$rsElements = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

		while($arElement = $rsElements->GetNextElement()){
			$arFields = $arElement->GetFields();
			$arResult[] = Array(
			$arFields['NAME'],
			$arFields['DETAIL_PAGE_URL'],
					Array(),
					Array(),
					""
			);
		}
		
		$this->EndResultCache();
	}
}
return $arResult;
?>
