<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arCountLine = array(
		"6" => "2",
		"4" => "3",
		"3" => "4",
	);
$arTemplateParameters = array(	
	"SERVICES_BLOCK_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_BLOCK_DESCRIPTION_TITLE_DEFAULT"),
		"COLS" => 20,
		"ROWS" => 10,
	),	
	"SERVICE_LINE_COUNT" => Array(
		"NAME"    => GetMessage("MD_FS_SERVICE_COUNT_LINE"),
		"TYPE" => "LIST",
		"DEFAULT" => "",
		"VALUES" => $arCountLine,
	),
	"SERVICE_SHOW_DESCRIPTION" => Array(
		"NAME"    => GetMessage("MD_FS_SERVICE_SHOW_DESCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),

);
?>
