<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_IBLOCK_DESC_LIST"),
	"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LIST_DESC"),
	"ICON" => "/images/news_all.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "concept",
        "NAME" => GetMessage("T_IBLOCK_DESC_WIZARD"),
		"SORT" => 100,
		"CHILD" => array(
			"ID" => "pages",
			"NAME" => GetMessage("T_IBLOCK_DESC_WIZARD_DESC"),
			"SORT" => 5,
			"CHILD" => array(
				"ID" => "page_cmpx",
			),
		),
	),
);

?>