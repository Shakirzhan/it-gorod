<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"USE_SHARE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_USE_SHARE"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"VALUE" => "Y",
		"DEFAULT" =>"N",
		"REFRESH"=> "N",
	),
	"SHOW_OTHER_NEWS" => Array(
		"NAME" => GetMessage("MD_SHOW_OTHER_NEWS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"VALUE" => "Y",
		"DEFAULT" =>"Y",
		"REFRESH"=> "Y",
	),	
);

if ($arCurrentValues["SHOW_OTHER_NEWS"] == "Y"){

	$arTemplateParameters["OTHER_ITEMS_TITLE"] = array(
		"NAME"    => GetMessage("MD_OTHER_ITEMS_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_OTHER_ITEMS_TITLE_DEFAULT"),
	);
}
?>