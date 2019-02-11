<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arCountLine = array(
		"6" => "2",
		"4" => "3",
		"3" => "4",
	);

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
	"USE_SHARE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_USE_SHARE"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"VALUE" => "Y",
		"DEFAULT" =>"N",
		"REFRESH"=> "N",
	),	
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
	"USE_CHESS_BACKLIGHT" => Array(
		"NAME"    => GetMessage("MD_FS_USE_CHESS_BACKLIGHT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SERVICE_ITEM_HEIGHT" => Array(
		"NAME"    => GetMessage("MD_FS_SERVICE_ITEM_HEIGHT"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_FS_SERVICE_ITEM_HEIGHT_DEFAULT"),
	),	
);


?>