<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult["ITEMS"])):?>
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
	$bShowFirstItem = false;			
	?>	
	<div class="accordion mt20">	
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			if($arParams["SHOW_FIRST_TAB"] == "Y"){
				$bShowFirstItem = true;
				$bFirstItem = ($key == 0 ? true : false);	
			}
			
			?>
			<section class="accordion-item <?= ($bFirstItem && $bShowFirstItem ? 'accordion-item_opened' : '')?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="accordion-item--title h4">
					<?=$arItem['NAME']?> 
				</div>
				<div class="accordion-item--content" style="<?= ($bFirstItem && $bShowFirstItem ? 'display: block;' : 'display: none;')?>">
					<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>						
							<?=$arItem['FIELDS']['PREVIEW_TEXT']?>						
					<?endif;?>					
				</div>
			</section>
		<?endforeach;?>
	</div>
<?endif;?>