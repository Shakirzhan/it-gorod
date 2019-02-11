<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MARSD_T_IBLOCK_DESC_MENU_ITEMS"),
	"DESCRIPTION" => GetMessage("MARSD_T_IBLOCK_DESC_MENU_DESC"),
	"ICON" => "/images/menu_ext.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "navigation",
			"NAME" => GetMessage("MARSD_MAIN_NAVIGATION_SERVICE")
		)
	),
);

?>