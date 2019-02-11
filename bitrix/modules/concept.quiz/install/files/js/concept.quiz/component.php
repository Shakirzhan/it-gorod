<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule('iblock');

$wqecSectionCode = trim($_REQUEST["wqecSectionCode"]);
$type = trim($_REQUEST["type"]);

$APPLICATION->IncludeComponent(
	"concept:conceptquiz",
	".default",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"WIZ_SECTION_NAME" => $wqecSectionCode,
		"COMPOSITE_FRAME_MODE" => "Y",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "TYPE" => $type
        
	)
);


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>