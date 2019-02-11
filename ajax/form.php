<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent(
	"boxsol:forms", 
	"popup", 
	array(
		"IBLOCK_TYPE" => "marsd_bitcorp_requests_s1",
		"IBLOCK_ID" => "16",		
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