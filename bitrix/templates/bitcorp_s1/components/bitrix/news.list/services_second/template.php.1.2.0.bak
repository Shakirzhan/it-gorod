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
		$serviceLineCount = ($arParams['SERVICE_LINE_COUNT'] ? $arParams['SERVICE_LINE_COUNT'] : 3);		
		$backlightClass = "";
		if($arParams['USE_CHESS_BACKLIGHT'] == "Y"){
			$backlightClass = "services-2_chess";
		}else{
			$backlightClass = "services-2_standart";
		}
		$serviceHeight = (strlen($arParams['SERVICE_ITEM_HEIGHT']) ? "style='height:".intval($arParams['SERVICE_ITEM_HEIGHT'])."px'" : "");			
		?>
		<div class="row row-in main-block-header">			
			<?if(strlen($arParams["SERVICES_BLOCK_DESCRIPTION"])):?>
				<div class="col-xs-12 col-md-12">
					<?=$arParams["SERVICES_BLOCK_DESCRIPTION"]?>
				</div>
			<?endif;?>
		</div>

		<?// top pagination?>
		<?if($arParams['DISPLAY_TOP_PAGER']):?>
			<?=$arResult['NAV_STRING']?>
			<br>
		<?endif;?>

		<div class="services-2 <?=$backlightClass?> row row-in">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
				?>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-<?=$serviceLineCount?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?if($bDetailLink):?><a class="service-2--link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif?>
						<div class="service-2 <?= $bDetailLink ? '' : 'service-2_nolink'?>">
							<div class="service-2--text" <?=$serviceHeight?>>
								<div class="service-2--title">
									<span><?=$arItem['~NAME']?></span>
								</div>

								<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT']) && $arParams['SERVICE_SHOW_DESCRIPTION'] == 'Y'):?>										
									<div class="service-2--descr">
										<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
									</div>
								<?endif;?>									
							</div>
							<?if($bDetailLink):?>
								<div class="service-2--more">
									<span class="link_underline"><?=GetMessage('MD_READ_MORE_LINK');?></span>
									<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
								</div>
							<?endif;?>

						</div>
					<?if($bDetailLink):?></a><?endif?>
				</div>						
			<?endforeach;?>
		</div>

		<?// bottom pagination?>
		<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
			<br>
			<?=$arResult['NAV_STRING']?>
		<?endif;?>

	<?endif;?>