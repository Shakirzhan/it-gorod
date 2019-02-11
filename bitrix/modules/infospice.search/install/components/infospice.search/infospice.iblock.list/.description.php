<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ISEARCH_IBLOCK_LIST_NAME"),
	"DESCRIPTION" => GetMessage("ISEARCH_IBLOCK_LIST_DESCRIPTION"),
	"ICON" => "/images/search_page.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "search",
			"NAME" => GetMessage("ISEARCH_IBLOCK_LIST_SERVICE")
		)
	),
);

?>
