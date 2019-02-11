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
		$itemOrder = 0;
		$bShowFirstItem = false;			
		?>		

		<?if($bNoSections):?>
			<div class="accordion mt20">
				<?//show elements with sections?>
				<?foreach($arResult['SECTIONS'] as $key => $arSection):?>		
					<?
					// section edit/add/delete buttons
					$bEditMode = (isset($_GET['bitrix_include_areas']) &&  strtoupper($_GET['bitrix_include_areas'])=='Y') ? true : false;			
					$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
					$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
					$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					if($arParams["SHOW_FIRST_TAB"] == "Y"){
						$bShowFirstItem = true;					
						$bFirstItem = ($itemOrder == 0 ? true : false);
						$itemOrder++;
					}
					?>

					<?if($bEditMode):?><div id="<?=$this->GetEditAreaId($arSection['ID'])?>"><?endif;?>
						
							<section class="accordion-item <?= ($bShowFirstItem && $bFirstItem ? 'accordion-item_opened' : '')?>">
								<div class="accordion-item--title h4">
									<?if(strlen($arSection['NAME'])):?>
										<?=$arSection['NAME']?>
									<?else:?>
										<?=Loc::getMessage('MD_OTHER_SECTION')?>
									<?endif;?>
								</div>
								<div class="accordion-item--content" style="<?= ($bShowFirstItem && $bFirstItem ? 'display: block;' : 'display: none;')?>">
									<div class="table-responsiver">
										<table>
											<thead>
												<tr>
													<?foreach($arSection["ITEMS"][0]["DISPLAY_PROPERTIES"] as $pid => $arProperty):?>
										                <th><?=$arProperty["NAME"]?></th>
										             <?endforeach?>														
												</tr>
											</thead>

											<tbody>										
												<?foreach($arSection["ITEMS"] as $arItem):?>											
													<?
													$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
													$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
													?>													
													<?	
														if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/items_list.php")){
															include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/items_list.php");	
														}
													?>
												<?endforeach;?>
											</tbody>
										</table>	
									</div>
								</div>
							</section>				
						
					<?if($bEditMode):?></div><?endif;?>					
				<?endforeach;?>	
			</div>
		<?else:?>
			<?//show elements without sections?>
			<div class="table-responsiver">
				<table class="table-lr-border">
					<thead>
						<tr>
							<?foreach($arResult["ITEMS"][0]["DISPLAY_PROPERTIES"] as $pid => $arProperty):?>
				                <th><?=$arProperty["NAME"]?></th>
				             <?endforeach?>														
						</tr>
					</thead>

					<tbody>											
						<?foreach($arResult["ITEMS"] as $arItem):?>
							<?
								if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/items_list.php")){
									include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/items_list.php");	
								}
							?>												
						<?endforeach;?>
					</tbody>
				</table>	
			</div>			

		<?endif;?>

	<?endif;?>