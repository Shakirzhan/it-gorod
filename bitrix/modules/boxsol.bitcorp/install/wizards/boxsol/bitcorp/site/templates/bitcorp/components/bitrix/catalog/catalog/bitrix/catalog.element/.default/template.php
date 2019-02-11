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

$bImage = strlen($arResult['DETAIL_PICTURE']['SRC']);
$bThumbImages = is_array($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']);
$bOrder = $arResult['PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'Y' ? true : false;
$bQuestion = $arResult['PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'Y' ? true : false;
$detailPictureSrc = ($bImage && $arResult['DETAIL_PICTURE']['RESIZED']['SRC']) ? $arResult['DETAIL_PICTURE']['RESIZED']['SRC'] : $arResult['DETAIL_PICTURE']['SRC'];	
?>
<div class="row row-in product-page" itemscope itemtype="http://schema.org/Product">
	<div itemprop="name" style="display: none;"><?=$arResult['NAME']?></div>
	<?if($bImage || $arResult['GALLERY']):?>
		<div class="col-sm-6 col-md-6">
			<div class="product-page--image-wrap">
				<div class="product-page--image">
					<a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" data-fancybox="gallery" data-caption="<?=$arResult['NAME']?>">
						
						<img src="<?=$detailPictureSrc?>" title="<?= ($arResult['PREVIEW_PICTURE']['TITLE'] ? $arResult['PREVIEW_PICTURE']['TITLE'] : $arResult['NAME'])?>" alt="<?= ($arResult['PREVIEW_PICTURE']['ALT'] ? $arResult['PREVIEW_PICTURE']['ALT'] : $arResult['NAME'])?>" itemprop="image"/>
						<div class="product-page--image-zoom">
							<span class="btn-zoom">
								<i class="fa fa-search-plus" aria-hidden="true"></i>
							</span>
						</div>
					</a>				

					<?if($arResult["PROPERTIES"]["HIT"]["VALUE"]):?>
						<div class="product-page--stickers">
							<?foreach($arResult['PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
								<div class="sticker sticker_<?=strtolower($class);?>">
									<?=$arResult['PROPERTIES']['HIT']['VALUE'][$key]?>
								</div>
							<?endforeach?>
						</div>
					<?endif;?>				
				</div>
			</div>

			<?if($arResult['GALLERY']):?>
				<div class="product-page--thumbs row-flex">
					<?foreach ($arResult['GALLERY'] as $arPhoto):?>
						<a rel="nofollow" class="active_" href="<?=$arPhoto['DETAIL']['SRC']?>" data-fancybox="gallery" data-caption="<?=$arPhoto['TITLE']?>">
							<img src="<?=$arPhoto['THUMB']['src']?>">
						</a>
					<?endforeach;?>
				</div>
			<?endif;?>
		</div>
	<?endif;?>
	<div class="col-sm-6 col-md-6" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<div class="product-page--price">
			<?if(strlen($arResult["PROPERTIES"]["PRICE"]["VALUE"])):?>
				<div class="new-price" itemprop="price" content="<?=$arResult["PROPERTIES"]["PRICE"]["VALUE"]?>">
					<?=number_format($arResult["PROPERTIES"]["PRICE"]["VALUE"], 0, '.', ' ');?> <?=$arResult["PROPERTIES"]["CURRENCY"]["VALUE"]?>
				</div>
				<span style="display: none;" class="currency" itemprop="priceCurrency" content="<?=$arResult["PROPERTIES"]["CURRENCY"]["VALUE_XML_ID"]?>"></span>
			<?endif;?>
			<?if(strlen($arResult["PROPERTIES"]["OLD_PRICE"]["VALUE"])):?>
				<div class="old-price">
					<?=number_format($arResult["PROPERTIES"]["OLD_PRICE"]["VALUE"], 0, '.', ' ');?> <?=$arResult["PROPERTIES"]["CURRENCY"]["VALUE"]?>
				</div>
			<?endif;?>			
		</div>

		<?if($arResult["PROPERTIES"]["STATUS"]["VALUE"]):?>
			<div class="product-item--stock">
				<span class="status status_<?=strtolower($arResult['PROPERTIES']['STATUS']['VALUE_XML_ID'])?>"><?=$arResult["PROPERTIES"]["STATUS"]["VALUE"]?></span>
			</div>
		<?endif;?>
		
		<div class="product-page--top-descr">
			<?if(strlen($arResult['DISPLAY_PROPERTIES']['ART_NUMBER']['VALUE'])):?>
				<p><strong><?=GetMessage('MD_CD_ART_NUMBER')?></strong>&nbsp;<?=$arResult['DISPLAY_PROPERTIES']['ART_NUMBER']['VALUE']?></p>
			<?endif;?>

			<?if(strlen($arResult['PREVIEW_TEXT'])):?>
				<div itemprop="description">					
					<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
						<p><?=$arResult['PREVIEW_TEXT'];?></p>
					<?else:?>
						<?=$arResult['PREVIEW_TEXT'];?>
					<?endif;?>
				</div>				
			<?endif;?>

			
		</div>

		<?if($bOrder || $bQuestion):?>
			<div class="product-page--buttons">
				<?if($bOrder):?>
					<a data-param-item="<?=$arResult['NAME']?>" href="#" data-event="jqm" data-ajax="<?=SITE_DIR?>ajax/catalog.php" data-name="catalog" class="btn btn-primary" rel="nofollow">
						<?=($arParams["MESS_BTN_ORDER"] ? $arParams["MESS_BTN_ORDER"] : Loc::getMessage("CD_MESS_BTN_ORDER_DEFAULT"));?>
					</a>
				<?endif;?>
				<?if($bQuestion):?>
					<a data-param-item="<?=$arResult['NAME']?>" href="#" data-event="jqm" data-ajax="<?=SITE_DIR?>ajax/catalog_question.php" data-name="catalog-question" class="btn" rel="nofollow">
						<?=($arParams["MESS_BTN_QUESTION"] ? $arParams["MESS_BTN_QUESTION"] : Loc::getMessage("CD_MESS_BTN_QUESTION_DEFAULT"));?>
					</a>
				<?endif;?>
			</div>
		<?endif;?>

	</div>
</div>

<?
	$bShowDetailTextTab = strlen($arResult['DETAIL_TEXT']);	
	$bShowPropsTab = !empty($arResult['PROPS']);
	$bShowDocsTab = !empty($arResult['PROPERTIES']['DOCUMENTS']['VALUE']);		
?>
<?if($bShowDetailTextTab || $bShowPropsTab || $bShowDocsTab):?>
	<div class="col-md-12 mt60">
		<div class="tabs">
			<ul class="nav nav-tabs clearfix">				
				<?if($bShowDetailTextTab):?>
					<li class="active">
						<a rel="nofollow" href="javascript:boid(0)"><?=($arParams["MESS_TAB_DESCRIPTION"] ? $arParams["MESS_TAB_DESCRIPTION"] : Loc::getMessage("CD_MESS_TAB_DESCRIPTION_DEFAULT"));?></a>
					</li>
				<?endif;?>
				<?if($bShowPropsTab):?>
					<li>
						<a rel="nofollow" href="javascript:void(0)"><?=($arParams["MESS_TAB_PROPS"] ? $arParams["MESS_TAB_PROPS"] : Loc::getMessage("CD_MESS_TAB_PROPS_DEFAULT"));?></a>
					</li>
				<?endif;?>
				<?if($bShowDocsTab):?>
					<li>
						<a rel="nofollow" href="javascript:void(0)"><?=($arParams["MESS_TAB_FILES"] ? $arParams["MESS_TAB_FILES"] : Loc::getMessage("CD_MESS_TAB_FILES_DEFAULT"));?></a>
					</li>
				<?endif;?>			
			</ul>
			<div class="tab-content">

				<?//detail text tab?>
				<?if($bShowDetailTextTab):?>
					<div class="tab-pane active" itemprop="description">
						<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
							<p><?=$arResult['DETAIL_TEXT'];?></p>
						<?else:?>
							<?=$arResult['DETAIL_TEXT'];?>
						<?endif;?>
					</div>
				<?endif;?>

				<?//props tab?>
				<?if($bShowPropsTab):?>
					<div class="tab-pane">
						<?foreach($arResult['PROPS'] as $arProp):?>
						<div class="char clearfix">
							<div class="char--left">
								<span><?=$arProp['NAME']?></span>
							</div>
							<div class="char--right">
								<span>
									<?if(is_array($arProp['DISPLAY_VALUE'])):?>
										<?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
											<?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
												<?=$value.'&nbsp;/ '?>
											<?else:?>
												<?=$value?>
											<?endif;?>
										<?endforeach;?>
									<?else:?>
										<?=$arProp['DISPLAY_VALUE']?>
									<?endif;?>										
								</span>
							</div>
						</div>
						<?endforeach;?>						
					</div>
				<?endif;?>

				<?//files tab?>
				<?if($bShowDocsTab):?>
					<div class="tab-pane">
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
					</div>
				<?endif;?>

			</div>
		</div>
	</div>
<?endif;?>