<?
$arCustomFields = array("SHOW_FRONT_PAGE", "POST");

foreach ($arResult["DISPLAY_PROPERTIES"] as $propCode => $propValue) {
	if(!in_array($propCode, $arCustomFields)){			
		$arResult["TEAM_FIELDS"][$propCode] = $propValue;
	}	
}

//preapre detail picture
if($arParams["DISPLAY_PICTURE"] != "N"){
	$picture = ($arResult["FIELDS"]["DETAIL_PICTURE"] ? "DETAIL_PICTURE" : "PREVIEW_PICTURE");
	$arPhoto = $arResult[$picture];

	if($arPhoto){
		$arResult["MD_DETAIL_PICTURE"] = array(
			'DETAIL' => $arPhoto,
			'PREVIEW' => CFile::ResizeImageGet($arPhoto['ID'], array('width' => 243, 'height' => 243), BX_RESIZE_PROPORTIONAL_ALT, true),		
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME'])),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])),
		);
	}
}	
?>