<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arTemplateParameters = array(	
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
