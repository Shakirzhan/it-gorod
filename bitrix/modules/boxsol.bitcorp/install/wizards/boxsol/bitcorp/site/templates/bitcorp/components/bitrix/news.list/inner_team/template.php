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
		<div class="row row-equal row-in inner-team">					
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
				$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
				$imgSrc = $bImage ? $arItem['FIELDS']['PREVIEW_PICTURE']['SRC'] : $this->GetFolder().'/images/people-default.jpg';
				?>							
				<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 mt40" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="people">
						<?if($bDetailLink):?>
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
						<?endif;?>
								<div class="people--photo" style="background-image: url(<?=$imgSrc?>);"></div>
								<div class="people--name">
									<span><?=$arItem['~NAME']?></span>
								</div>
								<?if($arItem['DISPLAY_PROPERTIES']['POST']['VALUE']):?>
									<div class="people--info">
										<?=$arItem['DISPLAY_PROPERTIES']['POST']['VALUE']?>
									</div>
								<?endif;?>
						<?if($bDetailLink):?>
							</a>
						<?endif;?>
						<?if(is_array($arItem['TEAM_FIELDS'])):?>
							<div class="people--contacts">
								<?foreach ($arItem['TEAM_FIELDS'] as $propCode => $propValue):?>
									<div class="people--contact">
										<?if(strlen($propValue['DESCRIPTION'])):?>
											<i class="fa <?=$propValue['DESCRIPTION']?>"></i>
										<?endif;?>
										<?if($propCode == 'EMAIL'):?>
											<!-- noindex -->
											<a href="mailto:<?=$propValue['VALUE'];?>" rel="nofollow">
												<?=$propValue['VALUE']?>	
											</a>
											<!-- /noindex -->
										<?elseif($propCode == 'SKYPE'):?>
											<!-- noindex -->
											<a href="skype:<?=$propValue['VALUE'];?>" rel="nofollow">
												<?=$propValue['VALUE']?>	
											</a>
											<!-- /noindex -->
										<?else:?>
											<?=$propValue['VALUE']?>
										<?endif;?>
									</div>
								<?endforeach;?>											
							</div>
						<?endif;?>
					</div>
				</div>
			<?endforeach;?>					
		</div>				
			
	<?endif;?>