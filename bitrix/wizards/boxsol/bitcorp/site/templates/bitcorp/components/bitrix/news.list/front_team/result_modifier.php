<?
$arCustomFields = array("SHOW_FRONT_PAGE", "POST");

foreach ($arResult["ITEMS"] as $key => $arItem) {
	foreach ($arItem["DISPLAY_PROPERTIES"] as $propCode => $propValue) {
		if(!in_array($propCode, $arCustomFields)){			
			$arResult["ITEMS"][$key]["TEAM_FIELDS"][$propCode] = $propValue;
		}	
	}	
}
?>