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

$bActiveDate = strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arResult['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
$bImage = strlen($arResult['FIELDS']['DETAIL_PICTURE']['SRC']);
$Title = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE'] : $arResult['NAME']));
$Alt = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT'] : $arResult['NAME']));
?>
<?if($bImage):?>
	<?
	$APPLICATION->SetPageProperty("ogimage", $arResult['FIELDS']['DETAIL_PICTURE']['SRC']);
	$APPLICATION->AddHeadString('<link rel="image_src" href="'.$arResult['FIELDS']['DETAIL_PICTURE']['SRC'].'"  />', true);
	?>
	<div class="news-detail-image pull-left">
		<a class="gallery-item" href="<?=$arResult['FIELDS']['DETAIL_PICTURE']['SRC']?>" data-fancybox="gallery_detail" data-caption="<?=$Title?>">
			<img style="border: 0;" src="<?=$arResult['FIELDS']['DETAIL_PICTURE']['SRC']?>" title="<?=$Title?>" alt="<?=$Alt?>" class="img-responsiver">
		</a>
	</div>
<?endif;?>
<?if($bActiveDate):?>
	<div class="g-news-add-date">
		<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
			<?=$arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?>
		<?else:?>
			<?=$arResult['DISPLAY_ACTIVE_FROM']?>
		<?endif;?> 
	</div>	
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
		<h3><?=$arResult['DISPLAY_PROPERTIES']['GALLERY_TITLE']['VALUE']?></h3>
	<?else:?>
		<h3><?=Loc::getMessage('MD_GALLERY_TITLE')?></h3>
	<?endif;?>

	<div class="row row-in">
		<?foreach($arResult['GALLERY'] as $arPhoto):?>
		<div class="col-xxs-12 col-xs-6 col-sm-4 col-md-4">	
			<a class="gallery-item" href="<?=$arPhoto['DETAIL']['SRC']?>" data-fancybox="gallery" data-caption="<?=$arPhoto['TITLE']?>">
				<span class="gallery-item--image" style="background-image: url(<?=$arPhoto['THUMB']['src']?>);"></span>
			</a>
			
		</div>
		<?endforeach;?>
	</div>
<?endif;?>

<?//docs?>
<?if($arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE']):?>
	<?if($arResult['DISPLAY_PROPERTIES']['DOCUMENTS_TITLE']['VALUE']):?>
		<h3><?=$arResult['DISPLAY_PROPERTIES']['DOCUMENTS_TITLE']['VALUE']?></h3>
	<?else:?>
		<h3><?=Loc::getMessage('MD_DOCUMENTS_TITLE')?></h3>
	<?endif;?>

	<div class="row row-in">
		<?foreach($arResult['PROPERTIES']['DOCUMENTS']['VALUE'] as $fileID):?>
			<?
				$arFile = CBitcorp::getFileInfo($fileID);
				$fileName = (strlen($arFile['DESCRIPTION']) ? $arFile['DESCRIPTION'] : $arFile['ORIGINAL_NAME']);
			?>
			<div class="col-xs-12 col-sm-6 col-md-4">
				<a href="<?=$arFile['SRC']?>" class="file file_<?=$arFile['FILE_TYPE']?>" target="_blank" <?= $arFile['FILE_TYPE'] == 'jpg' || $arFile['FILE_TYPE'] == 'png' ? 'data-fancybox="gallery"' : ''?>>
					<span class="file--name"><?=$fileName?></span>
					<span class="file--size"><?=$arFile['FILE_SIZE']?></span>
				</a>
			</div>

		<?endforeach;?>
			
	</div>
	<br>
<?endif;?>