<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view", 
	".default", 
	array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.74695799999372;s:10:\"yandex_lon\";d:37.620150999999986;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.620193915344224;s:3:\"LAT\";d:55.74696405159894;s:4:\"TEXT\";s:42:\"Россия, Москва, Софийская набережная, 26/1\";}}}",
		"MAP_WIDTH" => "100%",
		"MAP_HEIGHT" => "500",
		"CONTROLS" => array(
			0 => "ZOOM",
			1 => "SMALLZOOM",
			2 => "SCALELINE",
		),
		"OPTIONS" => array(
			0 => "ENABLE_DBLCLICK_ZOOM",
			1 => "ENABLE_DRAGGING",
		),
		"MAP_ID" => "yam_1",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
