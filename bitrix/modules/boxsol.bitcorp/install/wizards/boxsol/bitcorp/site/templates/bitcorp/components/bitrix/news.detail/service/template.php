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
use \Bitrix\Main\Localization\Loc;
$bActiveDate = ($arResult['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
$bShowTopBanner = (isset($arResult['BANNER_VISIBLE'] ) && $arResult['BANNER_VISIBLE'] == true);
?>

<?//top banner?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("top_banner");?>
		<?CBitcorp::ShowTopBanner($arResult);?>		
	<?$this->EndViewTarget();?>

	<?$this->SetViewTarget("hide_breadcrubs_start");?>
		<!-- 
	<?$this->EndViewTarget();?>

	<?$this->SetViewTarget("hide_breadcrubs_end");?>
		--> 
	<?$this->EndViewTarget();?>
<?endif?>

<div class="detail-page-wrapper">
	<?if($bActiveDate):?>
		<div class="g-news-add-date">		
			<i class="fa fa-calendar" aria-hidden="true"></i> <?=$arResult['DISPLAY_ACTIVE_FROM']?>		
		</div>	
		<br>
	<?endif;?>

	<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>			
			<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
				<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
					<p><?=$arResult['FIELDS']['DETAIL_TEXT'];?></p>
				<?else:?>
					<?=$arResult['FIELDS']['DETAIL_TEXT'];?>
				<?endif;?>
			<?endif;?>				
	<?endif;?>

	<?//gallery?>
	<?if($arResult['GALLERY']):?>
		<?if($arResult['DISPLAY_PROPERTIES']['GALLERY_TITLE']['VALUE']):?>
			<h2><?=$arResult['DISPLAY_PROPERTIES']['GALLERY_TITLE']['VALUE']?></h2>
		<?else:?>
			<h2><?=Loc::getMessage('MD_GALLERY_TITLE')?></h2>
		<?endif;?>

		<div class="row row-in">
			<?foreach($arResult['GALLERY'] as $arPhoto):?>
			<div class="col-xxs-12 col-xs-6 col-sm-4 col-md-4">	
				<a class="gallery-item" href="<?=$arPhoto['DETAIL']['SRC']?>" data-fancybox="gallery" data-caption="<?=$arPhoto['TITLE']?>">
					<span class="gallery-item--image" style="background-image: url(<?=$arPhoto['THUMB']['src']?>);"></span>
					<span class="gallery-item--caption-pos">
						<span class="gallery-item--caption">
							<span class="caption--title"><?=$arPhoto['GALLERY_TITLE']?></span>							
						</span>
					</span>
				</a>
				
			</div>
			<?endforeach;?>
		</div>
	<?endif;?>

	<?//docs?>
	<?if($arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE']):?>
		<?if($arResult['DISPLAY_PROPERTIES']['DOCUMENTS_TITLE']['VALUE']):?>
			<h2><?=$arResult['DISPLAY_PROPERTIES']['DOCUMENTS_TITLE']['VALUE']?></h2>
		<?else:?>
			<h2><?=Loc::getMessage('MD_DOCUMENTS_TITLE')?></h2>
		<?endif;?>

		<div class="row row-in">
			<?foreach($arResult['PROPERTIES']['DOCUMENTS']['VALUE'] as $fileID):?>
				<?
					$arFile = CBitcorp::getFileInfo($fileID);
					$fileName = (strlen($arFile['DESCRIPTION']) ? $arFile['DESCRIPTION'] : $arFile['ORIGINAL_NAME']);
				?>
				<div class="col-xs-12 col-sm-6 col-md-4">
					<a href="<?=$arFile['SRC']?>" class="file file_<?=$arFile['FILE_TYPE']?>" target="_blank" <?= $arFile['FILE_TYPE'] == 'jpg' || $arFile['FILE_TYPE'] == 'png' ? 'data-fancybox="doc-gallery"' : ''?>>
						<span class="file--name"><?=$fileName?></span>
						<span class="file--size"><?=$arFile['FILE_SIZE']?></span>
					</a>
				</div>

			<?endforeach;?>
				
		</div>
		<br>
	<?endif;?>
</div>