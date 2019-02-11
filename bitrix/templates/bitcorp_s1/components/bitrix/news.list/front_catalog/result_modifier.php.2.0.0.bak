<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

//resize items preview images
if(count($arResult["ITEMS"])){
	foreach($arResult["ITEMS"] as $key => $arItem){
		if(strlen($arItem["FIELDS"]["PREVIEW_PICTURE"]["SRC"])){
			$arPreviewPictureResized = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"]["ID"] , array('width' => 240, 'height' => 240), BX_RESIZE_IMAGE_PROPORTIONAL_ALT , true);
			if(is_array($arPreviewPictureResized)){
				$arResult["ITEMS"][$key]["PREVIEW_PICTURE_RESIZED"] = $arPreviewPictureResized;	
			}			
		}
	}
}
?>
<pre><?//print_r($arResult["ITEMS"])?></pre>