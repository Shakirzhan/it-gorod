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
	"PROJECTS_BLOCK_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FP_BLOCK_DESCRIPTION_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FP_BLOCK_DESCRIPTION_TITLE_DEFAULT"),
		"COLS" => 20,
		"ROWS" => 10,
	),	
	"PROJECTS_SHOW_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FP_SERVICE_SHOW_DESCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>