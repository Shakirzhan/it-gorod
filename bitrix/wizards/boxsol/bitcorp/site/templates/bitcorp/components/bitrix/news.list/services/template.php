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
		?>
		<?if(strlen($arParams["SERVICES_BLOCK_DESCRIPTION"])):?>		
			<div class="row row-in">
				<div class="col-xs-12">				
					<p><?=$arParams["SERVICES_BLOCK_DESCRIPTION"]?></p>				
				</div>
			</div>
		<?endif;?>

		<?// top pagination?>
		<?if($arParams['DISPLAY_TOP_PAGER']):?>
			<?=$arResult['NAV_STRING']?>
		<?endif;?>
		<div class="services">
			<div class="row row-in row-equal mt50">
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
					$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
					$serviceType = $arItem['PROPERTIES']['SERVICE_TYPE']['VALUE_XML_ID'];
					$serviceLineCount = ($arParams['SERVICE_LINE_COUNT'] ? $arParams['SERVICE_LINE_COUNT'] : 4);

					?>
					<div class="col-xs-12 col-sm-6 col-md-4 col-lg-<?=$serviceLineCount?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<?if($bDetailLink):?><a class="service--link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
							<div class="service">
								<?if($serviceType == "ICON" && strlen($arItem['PROPERTIES']['ICON_NAME']['VALUE'])):?>
									<div class="service--img service--img_icon">
										<i class="fa <?=$arItem['PROPERTIES']['ICON_NAME']['VALUE']?>"></i>
										
									</div>
								<?elseif($serviceType == "IMAGE" && $bImage):?>
									<div class="service--img service--img_icon">
										<img src="<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>"/>
									</div>
								<?endif;?>
								<div class="service--title">
									<span><?=$arItem['~NAME']?></span>
								</div>
								<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT']) && $arParams['SERVICE_SHOW_DESCRIPTION'] == 'Y'):?>
									<div class="service--text">
										<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
									</div>
								<?endif;?>
							</div>
						<?if($bDetailLink):?></a><?endif;?>
					</div>
				<?endforeach;?>
			</div>
		</div>

		<?// bottom pagination?>
		<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
			<?=$arResult['NAV_STRING']?>
		<?endif;?>
					
	<?endif;?>