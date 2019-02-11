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
		<div class="projects row mt50">			
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
				$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
				$projectSizeValue = $arItem['PROPERTIES']['BANNER_SIZE']['VALUE_XML_ID'];
				if($projectSizeValue == "narrow"){
					$projectSize = "";
				} elseif($projectSizeValue == "normal"){
					$projectSize = "project_w-33";
				}elseif($projectSizeValue == "wide"){
					$projectSize = "project_w-50";
				}
				?>							
				<div class="project <?=$projectSize;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="project--inner" style="background-image: url(<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>);">
						<?if($bDetailLink):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?>
							<div class="project--name">
								<span><?=$arItem['~NAME']?></span>
								<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT']) && $arParams['PROJECTS_SHOW_DESCRIPTION'] == 'Y'):?>
									<div class="project--descr">
										<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
									</div>
								<?endif;?>
							</div>
						<?if($bDetailLink):?></a><?endif;?>
					</div>														
				</div>
			<?endforeach;?>			
		</div>		
	<?endif;?>