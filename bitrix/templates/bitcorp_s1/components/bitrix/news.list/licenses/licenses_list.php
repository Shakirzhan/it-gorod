<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

$bPreviewPicture = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
$bDetailPicture = strlen($arItem['FIELDS']['DETAIL_PICTURE']['SRC']);

$PreviewPictureSrc = ($bPreviewPicture ? $arItem['FIELDS']['PREVIEW_PICTURE']['SRC'] : false);
$DetailPictureSrc = ($bDetailPicture ? $arItem['FIELDS']['DETAIL_PICTURE']['SRC'] : false);

$previewText = strlen($arItem['FIELDS']['PREVIEW_TEXT']) ? $arItem['FIELDS']['PREVIEW_TEXT'] : '';
?>
<?if($PreviewPictureSrc || $DetailPictureSrc):?>						
	<div class="col-xs-6 col-sm-4 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID']);?>">	
		<a class="gallery-item gallery-item_bordered" href="<?= ($DetailPictureSrc ? $DetailPictureSrc : $PreviewPictureSrc)?>" data-fancybox="gallery" data-caption="<?=$arItem['NAME']?>">
			<span class="gallery-item--image" style="background-image: url(<?= ($PreviewPictureSrc ? $PreviewPictureSrc : $DetailPictureSrc)?>);"></span>
			<?if($arItem['NAME']):?>
				<span class="gallery-item--caption-pos">
					<span class="gallery-item--caption">
						<span class="caption--title"><?=$arItem['NAME']?></span>
						<span class="caption--text"><?=$previewText?></span>
					</span>
				</span>
			<?endif;?>
		</a>
	</div>
<?endif;?>