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
<div class="catalog-sections">

	<div class="row row-in row-equal product-items">
		<?foreach ($arResult['RESULT_SECTIONS'] as &$arSection):?>
			<?
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
			$bDetailImage = strlen($arSection["DETAIL_PICTURE"]["SRC"]);		
					
			if($arSection["RELATIVE_DEPTH_LEVEL"] > 2)
				continue;
			?>
			<div class="col-xs-12 col-sm-6 col-md-3" id="<?= $this->GetEditAreaId($arSection['ID']);?>">
				<div class="product-item product-item_category">
					<?if($bDetailImage):?>
						<div class="product-item--image">
							<a href="<?=$arSection['SECTION_PAGE_URL']?>">
								<img src="<?=$arSection["DETAIL_PICTURE"]["SRC"]?>" alt="<?= $arSection['IPROPERTY_VALUES']['SECTION_DETAIL_PICTURE_FILE_ALT'] ? $arSection['IPROPERTY_VALUES']['SECTION_DETAIL_PICTURE_FILE_ALT'] : $arSection['NAME']?>" title="<?= $arSection['IPROPERTY_VALUES']['SECTION_DETAIL_PICTURE_FILE_TITLE'] ? $arSection['IPROPERTY_VALUES']['SECTION_DETAIL_PICTURE_FILE_TITLE'] : $arSection['NAME']?>">
							</a>
						</div>					
					<?endif;?>
					<div class="product-item--data">
						<div class="product-item--title">
							<a href="<?=$arSection['SECTION_PAGE_URL']?>">
								<?=$arSection["NAME"]?>
							</a>
						</div>
						<?if($arParams["SECTION_SHOW_DESCR"] == "Y" && $arSection["UF_SECTION_DESCR"]):?>
							<div class="cat-category--descr">
								<?=$arSection["~UF_SECTION_DESCR"]?>
							</div>
						<?endif;?>

					</div>
				</div>
			</div>
		<?endforeach;?>
	</div>
	
</div>