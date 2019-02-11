<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONTROLS" => array("ZOOM","SMALLZOOM","SCALELINE"),
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.74351059379343;s:10:\"yandex_lon\";d:52.41725383616259;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:52.41725383616259;s:3:\"LAT\";d:55.743510593799705;s:4:\"TEXT\";s:8:\"it-gorod\";}}}",
		"MAP_HEIGHT" => "500",
		"MAP_ID" => "yam_1",
		"MAP_WIDTH" => "100%",
		"OPTIONS" => array("ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING")
	)
);?>