<?
foreach ($arResult["ITEMS"] as $key => $arItem) {
	foreach ($arItem["DISPLAY_PROPERTIES"] as $propCode => $propValue) {
		if(strpos($propCode, 'SOCIAL') !== false && $propValue['VALUE'])
		{
			if($arItem['DISPLAY_PROPERTIES'][$propCode])
				unset($arItem['DISPLAY_PROPERTIES'][$propCode]);
			$arItem['SOCIAL_PROPS'][] = $propValue;
			$arResult["ITEMS"][$key]["SOCIAL_PROPS"][$propCode] = $propValue;
		}	
	}	
}
?>