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
	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

		$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
		$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);		
		$bActiveDate = strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arItem['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));		
		?>
		<div class="g-news" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="row row-in">				
				<?if($bImage):?>									
					<div class="col-xs-12 col-md-3">
						<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
							<img class="img-responsiver" src="<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" />
						<?if($bDetailLink):?></a><?endif;?>
					</div>
				<?endif;?>

				<div class="<?= ($bImage) ? 'col-xs-12 col-md-9' : 'col-xs-12 col-md-12'?>">
					<?if($bActiveDate):?>
						<div class="g-news--date">
							<?if(strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
								<?=$arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?>
							<?elseif($arItem['DISPLAY_ACTIVE_FROM']):?>
								<?=$arItem['DISPLAY_ACTIVE_FROM']?>	
							<?elseif($arItem['DISPLAY_ACTIVE_FROM']):?>
								<?=$arItem['FIELDS']['DATE_ACTIVE_FROM']?>
							<?endif;?>
						</div>
					<?endif;?>

					<div class="g-news--title">
						<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
							<?=$arItem['NAME']?>
						<?if($bDetailLink):?></a><?endif;?>
					</div>

					<?if($arItem["PREVIEW_TEXT"]):?>
						<div class="g-news--text">
							<?=$arItem['PREVIEW_TEXT']?> 
						</div>
					<?endif;?>

					<?if($bDetailLink):?>
						<div class="g-news--link">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
								<span class="link_underline"><?=GetMessage('MD_READ_MORE_LINK');?></span>
								<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
							</a>
						</div>
					<?endif;?>
				</div>				
			</div>	
		</div>
	<?endforeach;?>	
	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>		
<?endif;?>