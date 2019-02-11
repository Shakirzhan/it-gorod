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
			'GALLERY_TITLE' => strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : '',
		);
	}
}

//prepare info props
$arHideProps = array('SHOW_FRONT_PAGE', 'FORM_ORDER', 'SERVICE_TYPE', 'ICON_NAME', 'DOCUMENTS_TITLE', 'GALLERY_TITLE', 'LINK_FAQ', 'LINK_REVIEWS', 'LINK_STAFF', 'BANNER_VISIBLE', 'BANNER_TEXT_CODE', 'BANNER_TEXT', 'DOCUMENTS', 'MORE_PHOTO', 'BANNER_IMAGE', 'BANNER_BG', 'BANNER_BUTTON_TEXT', 'LINK_PROJECTS', 'BANNER_SIZE', 'BANNER_TEXT_COLOR', 'BANNER_BG_COLOR');

foreach ($arResult['DISPLAY_PROPERTIES'] as $propCode => $propValue) {
	if(!in_array($propCode, $arHideProps)){			
		$arResult['INFO_PROPS'][$propCode] = $propValue;
	}	
}

if(isset($arResult['PROPERTIES']['BANNER_VISIBLE']) && $arResult['PROPERTIES']['BANNER_VISIBLE']['VALUE_XML_ID'] == 'Y')
{
	$cp = $this->__component;
	if(is_object($cp))
	{
		$cp->arResult['BANNER_VISIBLE'] = true;
	    $cp->SetResultCacheKeys( array('BANNER_VISIBLE') );
	}
}
?>