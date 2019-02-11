<?
use Bitrix\Iblock;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
if(strlen($arResult['DETAIL_PICTURE']['SRC'])){
	$arDetailPictureResized = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"]["ID"] , array('width' => 2320, 'height' => 320), BX_RESIZE_IMAGE_PROPORTIONAL_ALT , true);
	if($arDetailPictureResized['src']){
		$arResult['DETAIL_PICTURE']['RESIZED']['SRC'] = $arDetailPictureResized['src'];	
	}
	
}
if(!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])){
	foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $img){
		$arResult['GALLERY'][] = array(
			'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
			//'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 490, 'height' => 490), BX_RESIZE_PROPORTIONAL_ALT, true),
			'THUMB' => CFile::ResizeImageGet($img , array('width' => 92, 'height' => 92), BX_RESIZE_IMAGE_EXACT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
		);
	}
}

if($arResult['DISPLAY_PROPERTIES']){
	$arResult['PROPS'] = array();	
	foreach($arResult['DISPLAY_PROPERTIES'] as $propCode => $arProp){
		if(!in_array($arProp['CODE'], array('FORM_ORDER', 'FORM_QUESTION', 'PRICE', 'OLD_PRICE', 'CURRENCY', 'HIT', 'STATUS', 'ART_NUMBER', 'LINK_GOODS')) && ($arProp['PROPERTY_TYPE'] != 'E' && $arProp['PROPERTY_TYPE'] != 'G')){
			if($arProp["VALUE"] || strlen($arProp["VALUE"])){				
				$arResult['PROPS'][$propCode] = $arProp;				
			}
		}
	}
}
?>