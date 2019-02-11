<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
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
	$serviceLineCount = ($arParams['SERVICE_LINE_COUNT'] ? $arParams['SERVICE_LINE_COUNT'] : 3);					
	?>	
	<div class="main-services">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-8 col-md-offset-2 tac">
					<h2 class="tac"><?= ($arParams["SERVICES_BLOCK_TITLE"] ? $arParams["SERVICES_BLOCK_TITLE"] : $arResult["NAME"])?></h2>
					<?if(strlen($arParams["SERVICES_BLOCK_DESCRIPTION"])):?>
						<p><?=$arParams["SERVICES_BLOCK_DESCRIPTION"]?></p>
					<?endif;?>
				</div>
			</div>

			<div class="row row-equal mt50">
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
					$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
					$serviceType = $arItem['PROPERTIES']['SERVICE_TYPE']['VALUE_XML_ID'];
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
			<?if($arParams["SHOW_ALL_TITLE_BLOCK"] == "Y"):?>
				<div class="row">
					<div class="col-xs-12 mt40 tac">
						<a href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL'])?>">
							<span class="link_underline"><?=$arParams["SHOW_ALL_TITLE"]?></span>
							<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
						</a>
					</div>
				</div>
			<?endif;?>
		</div>
	</div>
	
<?endif;?>