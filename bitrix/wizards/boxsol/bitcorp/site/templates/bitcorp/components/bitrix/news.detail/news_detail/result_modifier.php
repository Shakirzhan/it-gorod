<?
if($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE'] && is_array($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE'])){
	$arResult['GALLERY'] = array();

	foreach($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE'] as $img){
		$arResult['GALLERY'][] = array(
			'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
			'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1500, 'height' => 1500), BX_RESIZE_PROPORTIONAL_ALT, true),
			'THUMB' => CFile::ResizeImageGet($img, array('width' => 350, 'height' => 200), BX_RESIZE_IMAGE_EXACT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
		);
	}
}
?>