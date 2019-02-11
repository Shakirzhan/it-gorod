<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
	<?
	/** @var array $arParams */
	/** @var array $arResult */
	/** @global CMain $APPLICATION */
	/** @global CUser $USER */
	/** @global CDatabase $DB */
	/** @var CBitrixComponentTemplate $this */
	/** @var string $templateName */
	/** @var string $templateFile */
	/** @var string $templateFolder */
	/** @var string $componentPath */
	/** @var CBitrixComponent $component */
	$this->setFrameMode(true);
	$itemsCnt = count($arResult['ITEMS']);
	$colmd = CMarsHelper::getMdColInfo($itemsCnt);
	$colsm = CMarsHelper::getSmColInfo($itemsCnt);
	
?>	
	<div class="clearfix"></div>
	<div class="main-teasers teasers_line">
		<div class="container"> 
			<div class="row row-equal">
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					$teaserType = $arItem['PROPERTIES']['TEASER_TYPE']['VALUE_XML_ID'];	
					$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
					$arImage = ($bImage ? CFile::ResizeImageGet($arItem['FIELDS']['PREVIEW_PICTURE']['ID'], array('width' => 80, 'height' => 80), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
					if($arImage['src'])
						$arImage['src'] = CUtil::GetAdditionalFileURL($arImage['src'], true);				
					?>
					<div class="col-xs-12 col-sm-<?=$colsm?> col-md-<?=$colmd?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<div class="teaser">							
								<?if($teaserType == "ICON" && strlen($arItem['PROPERTIES']['ICON_NAME']['VALUE'])):?>
									<div class="teaser--title">
										<i class="fa <?=$arItem['PROPERTIES']['ICON_NAME']['VALUE']?>"></i>
									</div>	
								<?elseif($teaserType == "IMAGE" && $bImage):?>
									<div class="teaser--title">
										<img src="<?=$arImage['src']?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>">
									</div>
								<?elseif($teaserType == "TEXT" && strlen($arItem['FIELDS']['NAME']) && $arParams['SHOW_NAME'] == "Y"):?>
									<div class="teaser--title">
										<span><?=$arItem["NAME"]?></span>
									</div>
								<?else:?>
									<div class="teaser--title">
										<span><?=$arItem["NAME"]?></span>
									</div>
								<?endif;?>								
							<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
								<div class="teaser--text">
									<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
								</div>
							<?endif;?>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
<?endif;?>