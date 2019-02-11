<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use \Bitrix\Main\Localization\Loc;?>
	<?if (!empty($arResult['ITEMS'])):?>
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
		$bNoSections = count($arResult["SECTIONS"]);		
		?>	
		<?if($arParams['SHOW_IBLOCK_DESCRIPTION'] == 'Y'):?>
			<?//iblock description?>
			<?if(strlen($arResult['DESCRIPTION'])):?>
				<?if($arResult['DESCRIPTION_TYPE'] == 'text'):?>	
					<p><?=$arResult['DESCRIPTION']?></p>
				<?else:?>
					<?=$arResult['DESCRIPTION']?>							
				<?endif;?>
			<?endif;?>	
		<?endif;?>

		<?if($bNoSections):?>
			<?//show elements with sections?>
			<?foreach($arResult['SECTIONS'] as $arSection):?>		
				<?
				// section edit/add/delete buttons
				$bEditMode = (isset($_GET['bitrix_include_areas']) &&  strtoupper($_GET['bitrix_include_areas'])=='Y') ? true : false;			
				$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
				$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
				$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>

				<?if($bEditMode):?><div id="<?=$this->GetEditAreaId($arSection['ID'])?>"><?endif;?>
					
					<?if($arParams['SHOW_SECTION_NAME'] == 'Y'):?>					
						<?if(strlen($arSection['NAME'])):?>
							<h3><?=$arSection['NAME']?></h3>
						<?else:?>
							<h3><?=Loc::getMessage('MD_OTHER_SECTION')?></h3>
						<?endif;?>
					<?endif;?>

					<?if($arParams['SHOW_SECTION_PREVIEW_DESCRIPTION'] == 'Y'):?>
						<?if(strlen($arSection['DESCRIPTION'])):?>
							<?if($arSection['DESCRIPTION_TYPE'] == 'text'):?>	
								<p><?=$arSection['DESCRIPTION']?></p>
							<?else:?>
								<?=$arSection['DESCRIPTION']?>							
							<?endif;?>
						<?endif;?>						
					<?endif;?>

					<div class="row row-in">										
						<?foreach($arSection["ITEMS"] as $arItem):?>
							<?
								if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/licenses_list.php")){
									include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/licenses_list.php");	
								}
							?>												
						<?endforeach;?>					
					</div>
				<?if($bEditMode):?></div><?endif;?>					
			<?endforeach;?>	
		<?else:?>
			<?//show elements without sections?>

			<?// top pagination?>
			<?if($arParams['DISPLAY_TOP_PAGER']):?>
				<?=$arResult['NAV_STRING']?>
			<?endif;?>

			<div class="row row-in">										
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
						if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/licenses_list.php")){
							include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/licenses_list.php");	
						}
					?>												
				<?endforeach;?>					
			</div>

			<?// bottom pagination?>
			<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
				<?=$arResult['NAV_STRING']?>
			<?endif;?>

		<?endif;?>

	<?endif;?>