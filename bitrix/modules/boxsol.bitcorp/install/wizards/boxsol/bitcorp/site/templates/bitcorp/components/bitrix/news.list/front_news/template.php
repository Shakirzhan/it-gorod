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
<div class="main-news bg-default padding-default">
	<div class="container">
		<div class="row">
			<div class="col-xxs-12 col-xs-7 col-sm-9">
				<h2><?= ($arParams["NEWS_BLOCK_TITLE"] ? $arParams["NEWS_BLOCK_TITLE"] : $arResult["NAME"])?></h2>
			</div>
			<div class="col-xxs-12 col-xs-5 col-sm-3 tar tal-xs mt10">
				<?if(strlen($arParams["SHOW_ALL_TITLE"])):?>
					<a href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL'])?>">
						<span class="link_underline"><?=$arParams["SHOW_ALL_TITLE"]?></span>
						<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
					</a>
				<?endif;?>
			</div>
		</div>
		<div class="main-news-items">
			<div class="row mt40">	
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

					$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
					$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
					$arImage = ($bImage ? CFile::ResizeImageGet($arItem['FIELDS']['PREVIEW_PICTURE']['ID'], array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
					$bActiveDate = ($arItem['DISPLAY_ACTIVE_FROM'] || in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
					if($arImage['src'])
						$arImage['src'] = CUtil::GetAdditionalFileURL($arImage['src'], true);
					?>
					<div class="col-xs-12 col-sm-4" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<div class="m-news <?=($bImage ? 'm-news_with-img' : '')?>">
							<?if($bImage):?>
								<div class="m-news--image">
									<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
										<img src="<?=$arImage['src']?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>"/>
									<?if($bDetailLink):?></a><?endif;?>								
								</div>
							<?endif;?>
							<?if($bActiveDate):?>
								<div class="m-news--date">
									<?if($arItem['DISPLAY_ACTIVE_FROM']):?>
										<?=$arItem['DISPLAY_ACTIVE_FROM']?>	
									<?elseif($arItem['FIELDS']['DATE_ACTIVE_FROM']):?>
										<?=$arItem['FIELDS']['DATE_ACTIVE_FROM']?>
									<?endif;?>
								</div>
							<?endif;?>
							<div class="m-news--text">
								<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
									<?=$arItem['NAME']?>
								<?if($bDetailLink):?></a><?endif;?>
							</div>
						</div>
					</div>	
				<?endforeach;?>
			</div>
		</div>
	</div>
</div>			
<?endif;?>