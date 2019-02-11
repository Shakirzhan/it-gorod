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
		<div class="company-contacts">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$bFilialText = strlen($arItem['FIELDS']['PREVIEW_TEXT']);
			$bAddress = count($arItem['DISPLAY_PROPERTIES']['ADDRESS']['VALUE']);
			$bPhone = count($arItem['DISPLAY_PROPERTIES']['PHONE']['VALUE']);
			$bEmail = count($arItem['DISPLAY_PROPERTIES']['MAIL']['VALUE']);
			$bSkype = count($arItem['DISPLAY_PROPERTIES']['SKYPE']['VALUE']);		
			?>							
			<div class="office" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<h3><?=$arItem["NAME"]?></h3>
				<?if($bFilialText):?>
					<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
						<p><?=$arItem['FIELDS']['PREVIEW_TEXT']?></p>
					<?else:?>
						<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
					<?endif;?>
				<?endif;?>

				<?if($bAddress):?>
					<?foreach ($arItem['DISPLAY_PROPERTIES']['ADDRESS']['VALUE'] as $address):?>
						<div class="office--adress">
							<i class="fa fa-map-marker" aria-hidden="true"></i> <?=$address?>
						</div>
					<?endforeach;?>
				<?endif;?>

				<?if($bPhone || $bEmail || $bSkype):?>
					<div class="office--contacts">

						<?if($bAddress):?>
							<?foreach ($arItem['DISPLAY_PROPERTIES']['PHONE']['VALUE'] as $phone):?>
								<a href="tel:<?=$phone?>" class="">
									<i class="fa fa-phone" aria-hidden="true"></i> <?=$phone?>
								</a>
							<?endforeach;?>
						<?endif;?>

						<?if($bEmail):?>
							<?foreach ($arItem['DISPLAY_PROPERTIES']['MAIL']['VALUE'] as $mail):?>
								<a href="mailto:<?=$mail?>">
									<i class="fa fa-envelope-o" aria-hidden="true"></i> <?=$mail?>
								</a>
							<?endforeach;?>
						<?endif;?>

						<?if($bSkype):?>
							<?foreach ($arItem['DISPLAY_PROPERTIES']['SKYPE']['VALUE'] as $skype):?>
								<a href="skype:<?=$skype?>">
									<i class="fa fa-skype" aria-hidden="true"></i> <?=$skype?>
								</a>
							<?endforeach;?>
						<?endif;?>
					</div>
				<?endif;?>
			</div>
		<?endforeach;?>					
		</div>
	<?endif;?>