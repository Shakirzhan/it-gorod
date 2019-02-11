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
	<div class="inner-catalog">		
		<div class="row mt30">
			<div class="col-md-12">
				<div class="owl-slider-product-page owl-carousel owl-theme">
					<?foreach($arResult["ITEMS"] as $arItem):?>
						<?
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
						$bImage = strlen($arItem["FIELDS"]["PREVIEW_PICTURE"]["SRC"]);							
						?>  
						<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
							<div class="product-item">
								<?if($bImage):?>
									<div class="product-item--image">
										<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
											<img src="<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>">
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
<?endif;?>