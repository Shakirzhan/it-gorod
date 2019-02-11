<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arTemplateParameters = array(	
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
