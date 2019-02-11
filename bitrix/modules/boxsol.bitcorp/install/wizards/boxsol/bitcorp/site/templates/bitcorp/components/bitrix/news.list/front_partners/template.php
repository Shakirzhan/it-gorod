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
		$autoPlay = "";
		if ($arParams["AUTOPLAY"] == "Y") {$autoPlay = "true";}
		$autoPlayTime = "";
		if ($arParams["AUTOPLAY_TIME"] > 0) {$autoPlayTime = (int)$arParams["AUTOPLAY_TIME"];}			
		?>	
		<div class="main-partners padding-default">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-md-8">
						<h2><?= ($arParams["PARTNER_BLOCK_TITLE"] ? $arParams["PARTNER_BLOCK_TITLE"] : $arResult["NAME"])?></h2>
						<?if(strlen($arParams["PARTNER_BLOCK_DESCRIPTION"])):?>
							<p><?=$arParams["PARTNER_BLOCK_DESCRIPTION"]?></p>
						<?endif;?>
					</div>
				</div>

				<div class="row mt40">
					<div class="owl-slider-partners owl-carousel owl-theme" data-autoplay="<?=$autoPlay?>" data-autoplay-timeout="<?=$autoPlayTime.'000'?>">					
						<?foreach($arResult["ITEMS"] as $arItem):?>
							<?
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
							$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);							
							?>
							<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
								<?if($bDetailLink):?>
									<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<?endif;?>
									<div class="partner-square">
										<?if($bImage):?>
											<img src="<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>">
										<?endif;?>
									</div>
								<?if($bDetailLink):?>
									</a>
								<?endif;?>
								
							</div>
						<?endforeach;?>
					</div>				
				</div>				
			</div>
		</div>		
	<?endif;?>