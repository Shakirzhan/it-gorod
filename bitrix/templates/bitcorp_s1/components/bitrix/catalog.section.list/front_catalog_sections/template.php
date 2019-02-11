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



$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

?>
<?if($arResult['RESULT_SECTIONS']):?>
	<div class="main-categories padding-default slider-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-8 tac-xs">
					<h2><?= ($arParams["CATALOG_BLOCK_TITLE"] ? $arParams["CATALOG_BLOCK_TITLE"] : $arResult["NAME"])?></h2>
					<?if(strlen($arParams["CATALOG_BLOCK_DESCRIPTION"])):?>
						<p><?=$arParams["CATALOG_BLOCK_DESCRIPTION"]?></p>
					<?endif;?>
				</div>
				<?if($arParams["SHOW_ALL_TITLE_BLOCK"] == "Y"):?>
					<div class="col-xs-12 col-sm-6 col-md-4 tar tac-xs mt5">					
						<a href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arParams['SHOW_ALL_TITLE_LINK'])?>">
							<span class="link_underline"><?=$arParams["SHOW_ALL_TITLE"]?></span>
							<i class="icon-rounded fa fa-angle-right" aria-hidden="true"></i>
						</a>
					</div>
				<?endif;?>
			</div>
			<div class="row mt30 row-equal">
				<?foreach ($arResult['RESULT_SECTIONS'] as &$arSection):?>
					<?
					$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
					$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
					$bImage = strlen($arSection["PICTURE"]["SRC"]);
					$bTitleBg = $arSection["UF_TITLE_BG"] ? true : false;
					$bShowOnIndex = $arSection["UF_SHOW_ON_INDEX"] ? true : false;
					$sectionSize = $arSection["UF_SECTION_SIZE"] == 'narrow' ? 'col-lg-4' : 'col-lg-6';
					$sectionBgColor = strlen($arSection["UF_BG_COLOR"]) ? $arSection["UF_BG_COLOR"] : '#EDF7FA';
					if(!$bShowOnIndex || $arSection["RELATIVE_DEPTH_LEVEL"] > 2)
						continue;	
					?>
					<div class="col-sm-12 col-md-6 <?=$sectionSize?> mt30" id="<?= $this->GetEditAreaId($arSection['ID']);?>">
						<div class="cat-category" style="background-color: <?= ($sectionBgColor)?>; <?= ($bImage) ? 'background-image: url('.$arSection["PICTURE"]["SRC"].');' : '' ?> ">
							<div class="cat-category--content <?= $bTitleBg ? 'cat-category--content_with-bg' : ''?>">
								<div class="cat-category--title">
									<a href="<?=$arSection['SECTION_PAGE_URL']?>">
										<?if($arSection["~UF_SECTION_TITLE"]):?>
											<?=$arSection["~UF_SECTION_TITLE"]?>
										<?else:?>
											<?=$arSection["NAME"]?>
										<?endif;?>
									</a>
								</div>

								<?if($arSection["UF_SECTION_DESCR"]):?>
									<div class="cat-category--descr">
										<?=$arSection["~UF_SECTION_DESCR"]?>
									</div>
								<?endif;?>

								<?if(is_array($arSection["SUBSECTIONS"])):?>
									<div class="cat-category--links">
										<?foreach($arSection["SUBSECTIONS"] as $arSubsection):?>
											<?
											$this->AddEditAction($arSubsection['ID'], $arSubsection['EDIT_LINK'], $strSectionEdit);
											$this->AddDeleteAction($arSubsection['ID'], $arSubsection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
											if($arSubsection["RELATIVE_DEPTH_LEVEL"] > 2)
												continue;
											?>
											<a href="<?=$arSubsection['SECTION_PAGE_URL']?>" id="<?= $this->GetEditAreaId($arSubsection['ID']);?>">
												<?=$arSubsection["NAME"]?>
											</a>
										<?endforeach;?>
									</div>
								<?endif;?>

							</div>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
<?endif;?>