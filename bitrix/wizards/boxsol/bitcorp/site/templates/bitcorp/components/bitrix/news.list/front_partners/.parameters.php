<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arTemplateParameters = array(
	"PARTNER_BLOCK_TITLE" => Array(
		"NAME"    => GetMessage("MD_FS_BLOCK_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_BLOCK_TITLE_DEFAULT"),
	),
	"PARTNER_BLOCK_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE_DEFAULT"),
		"COLS" => 20,
		"ROWS" => 10,
	),
	"AUTOPLAY_TIME" => array(
		"NAME"    => GetMessage("MD_FP_AUTOPLAY_TEXT"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FP_AUTOPLAY_DEFAULT"),
	),
	"AUTOPLAY" => Array(
		"NAME" => GetMessage("MD_FP_AUTOPLAY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>
