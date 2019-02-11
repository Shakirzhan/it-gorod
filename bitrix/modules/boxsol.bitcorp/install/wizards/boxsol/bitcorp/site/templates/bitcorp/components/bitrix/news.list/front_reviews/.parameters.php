<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arTemplateParameters = array(
	"REVIEW_BLOCK_TITLE" => Array(
		"NAME"    => GetMessage("MD_FR_BLOCK_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FR_BLOCK_TITLE_DEFAULT"),
	),
	"REVIEW_BLOCK_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FR_BLOCK_DESCRIPTION_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FR_BLOCK_DESCRIPTION_TITLE_DEFAULT"),
		"COLS" => 20,
		"ROWS" => 10,
	),
	"AUTOPLAY_TIME" => array(
		"NAME"    => GetMessage("MD_FR_AUTOPLAY_TEXT"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FR_AUTOPLAY_DEFAULT"),
	),
	"AUTOPLAY" => Array(
		"NAME" => GetMessage("MD_FR_AUTOPLAY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>
