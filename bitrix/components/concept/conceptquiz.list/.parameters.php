<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
        "CURRENT_SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_SECTION_ID"),
			"TYPE" => "STRING",
			"VALUES" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
?>