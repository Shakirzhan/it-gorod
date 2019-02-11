<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("ROBOTS", "noindex, nofollow");
$APPLICATION->SetTitle("test");
?><?$APPLICATION->IncludeComponent(
	"concept:conceptquiz",
	"",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"WIZ_SECTION_NAME" => ""
	)
);?><?$APPLICATION->IncludeComponent(
	"concept:conceptquiz.list",
	"",
	Array(
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CURRENT_SECTION_ID" => "",
		"IBLOCK_ID" => "22",
		"IBLOCK_TYPE" => "concept_quiz"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>