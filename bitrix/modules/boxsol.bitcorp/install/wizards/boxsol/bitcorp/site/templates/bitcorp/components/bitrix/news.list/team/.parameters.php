<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arTemplateParameters = array(
	"TEAM_BLOCK_TITLE" => Array(
		"NAME"    => GetMessage("MD_FS_BLOCK_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_BLOCK_TITLE_DEFAULT"),
	),
	"TEAM_BLOCK_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE_DEFAULT"),
		"COLS" => 20,
		"ROWS" => 10,
	),
);
?>
