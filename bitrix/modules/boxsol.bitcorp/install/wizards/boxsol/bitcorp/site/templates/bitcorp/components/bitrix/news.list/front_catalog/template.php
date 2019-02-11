<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $arSetting;?>	
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
	?>	
	<div class="main-catalog padding-default bg-default">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-8 tac-xs">
					<h2><?= ($arParams["CATALOG_BLOCK_TITLE"] ? $arParams["CATALOG_BLOCK_TITLE"] : $arResult["NAME"])?></h2>
					<?if(strlen($arParams["CATALOG_BLOCK_DESCRIPTION"])):?>
						<p><?=$arParams["CATALOG_BLOCK_DESCRIPTION"]?></p>
					<?endif;?>
				</div>
				<?if($arParams["SHOW_ALL_TITLE_BLOCK"] == "Y"):?>					
					<div class="col-xs-12 col-sm-4 col-md-4 tar tac-xs mt5">
						<a href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL'])?>">
							<span class="link_underline"><?=$arParams["SHOW_ALL_TITLE"]?></span>
							<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
						</a>
					</div>					
				<?endif;?>				
			</div>

			<div class="row mt30">
				<div class="col-md-12">
					<div class="owl-slider-main-catalog owl-carousel owl-theme">
						<?foreach($arResult["ITEMS"] as $arItem):?>
							<?
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
							$bImage = strlen($arItem["FIELDS"]["PREVIEW_PICTURE"]["SRC"]);
							$previewPictureSrc = ($bImage && strlen($arItem["PREVIEW_PICTURE_RESIZED"]["src"]))? $arItem["PREVIEW_PICTURE_RESIZED"]["src"] : $arItem["FIELDS"]["PREVIEW_PICTURE"]["SRC"];							
							?>  
							<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
								<div class="product-item">
									<?if($bImage):?>
										<div class="product-item--image">
											<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
												<img src="<?=$previewPictureSrc?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>">
											<?if($bDetailLink):?></a><?endif;?>
										</div>
									<?endif;?>
									<div class="product-item--data">

										<?if($arItem["PROPERTIES"]["HIT"]["VALUE"]):?>
											<div class="product-item--stickers">
												<?foreach($arItem['PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
													<div class="sticker sticker_<?=strtolower($class);?>">
														<?=$arItem['PROPERTIES']['HIT']['VALUE'][$key]?>
													</div>
												<?endforeach?>
											</div>
										<?endif;?>

										<div class="product-item--title">
											<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
												<?=$arItem["~NAME"]?>
											<?if($bDetailLink):?></a><?endif;?>
										</div>

										<div class="product-item--price-block">

											<?if($arItem["PROPERTIES"]["STATUS"]["VALUE"]):?>
												<div class="product-item--stock">
													<span class="status status_<?=strtolower($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'])?>"><?=$arItem["PROPERTIES"]["STATUS"]["VALUE"]?></span>
												</div>
											<?endif;?>

											<div class="product-item--price">
												<?if(strlen($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?>
													<div class="product-item--price-new">
														<?=number_format($arItem["PROPERTIES"]["PRICE"]["VALUE"], 0, '.', ' ');?> <?=$arItem["PROPERTIES"]["CURRENCY"]["VALUE"]?>
													</div>
												<?endif;?>
												<?if(strlen($arItem["PROPERTIES"]["OLD_PRICE"]["VALUE"])):?>
													<div class="product-item--price-old">
														<?=number_format($arItem["PROPERTIES"]["OLD_PRICE"]["VALUE"], 0, '.', ' ');?> <?=$arItem["PROPERTIES"]["CURRENCY"]["VALUE"]?>
													</div>
												<?endif;?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?endforeach;?>					
					</div>
				</div>
			</div>			
		</div>
	</div>	
<?endif;?>