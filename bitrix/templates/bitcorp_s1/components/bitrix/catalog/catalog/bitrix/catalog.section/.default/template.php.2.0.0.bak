<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
<?if (!empty($arResult['ITEMS'])):?>

	<?if($arParams["SECTION_SHOW_PREVIEW_TEXT"] == "Y" && $arParams["SECTION_SHOW_PREVIEW_TEXT_POSITION"] == "top"):?>
		<div class="row row-in">
			<div class="col-md-12 mb30">
				<?if(strlen($arResult['DESCRIPTION'])):?>
					<?if($arResult['DESCRIPTION_TYPE'] == 'text'):?>	
						<p><?=$arResult['DESCRIPTION']?></p>
					<?else:?>
						<?=$arResult['DESCRIPTION']?>							
					<?endif;?>
				<?endif;?>			
			</div>
		</div>
	<?endif;?>

	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<div class="row row-in">
			<div class="col-md-12 mb30">
				<?=$arResult['NAV_STRING']?>
			</div>
		</div>
	<?endif;?>

	<div class="row row-in product-items row-equal ">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
			$bImage = strlen($arItem["PREVIEW_PICTURE"]["SRC"]);						
			$isOrder = $arItem["PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"];
			$isQuestion = $arItem["PROPERTIES"]["FORM_QUESTION"]["VALUE_XML_ID"];
			$previewPictureSrc = ($bImage && strlen($arItem["PREVIEW_PICTURE_RESIZED"]["src"]))? $arItem["PREVIEW_PICTURE_RESIZED"]["src"] : $arItem["PREVIEW_PICTURE"]["SRC"];			
			?>
			<div class="col-xs-12 col-sm-6 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="product-item">

					<?if($bImage):?>
						<div class="product-item--image">
							<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
								<img src="<?=$previewPictureSrc?>" title="<?= ($arItem['PREVIEW_PICTURE']['TITLE'] ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" alt="<?= ($arItem['PREVIEW_PICTURE']['ALT'] ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>">
							<?if($bDetailLink):?></a><?endif;?>
						</div>
					<?endif;?>

					<div class="product-item--data">
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

					<?if($arItem["PROPERTIES"]["HIT"]["VALUE"]):?>
						<div class="product-item--stickers">
							<?foreach($arItem['PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
								<div class="sticker sticker_<?=strtolower($class);?>">
									<?=$arItem['PROPERTIES']['HIT']['VALUE'][$key]?>
								</div>
							<?endforeach?>
						</div>
					<?endif;?>
					
				</div>
			</div>
		<?endforeach;?>
	</div>

	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
		<div class="row row-in">
			<div class="col-md-12 mt20">
				<?=$arResult['NAV_STRING']?>
			</div>
		</div>
	<?endif;?>

	<?if($arParams["SECTION_SHOW_PREVIEW_TEXT"] == "Y" && $arParams["SECTION_SHOW_PREVIEW_TEXT_POSITION"] == "bottom"):?>
		<div class="row row-in">
			<div class="col-md-12">
				<?if(strlen($arResult['DESCRIPTION'])):?>
					<?if($arResult['DESCRIPTION_TYPE'] == 'text'):?>	
						<p><?=$arResult['DESCRIPTION']?></p>
					<?else:?>
						<?=$arResult['DESCRIPTION']?>							
					<?endif;?>
				<?endif;?> 
			</div>
		</div>
	<?endif;?>

<?else:?>
	<div class="color-primary">
		<?=GetMessage('CT_BCS_CATALOG_NO_ITEMS')?>
	</div>
<?endif;?>