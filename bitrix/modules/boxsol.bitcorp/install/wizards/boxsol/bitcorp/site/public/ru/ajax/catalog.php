<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent(
	"boxsol:forms", 
	"popup", 
	array(
		"IBLOCK_TYPE" => "#MARSD_BITCORP_REQUESTS#",
		"IBLOCK_ID" => "#REQUESTS_CATALOG_IBLOCK#",		
		"ELEMENT_NAME" => "",
		"BUTTON_TITLE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"SHOW_PERSONAL_DATA" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>