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
	"SHOW_SECTION_NAME" => array(
		"PARENT" => "LIST_SETTINGS",
		"SORT" => 500,
		"NAME" => GetMessage("MD_SHOW_SECTION_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_SECTION_PREVIEW_DESCRIPTION" => array(
		"PARENT" => "LIST_SETTINGS",
		"SORT" => 500,
		"NAME" => GetMessage("MD_SHOW_SECTION_PREVIEW_DESCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_IBLOCK_DESCRIPTION" => array(
		"PARENT" => "LIST_SETTINGS",
		"SORT" => 500,
		"NAME" => GetMessage("MD_SHOW_IBLOCK_DESCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);

?>