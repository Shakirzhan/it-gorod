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
	$autoPlay = "";
	if ($arParams["AUTOPLAY"] == "Y") {$autoPlay = "true";}
	$autoPlayTime = "";
	if ($arParams["AUTOPLAY_TIME"] > 0) {$autoPlayTime = (int)$arParams["AUTOPLAY_TIME"];}	

	?>
	<div class="slider slider-wrapper">
	   <div class="owl-carousel owl-slider owl-theme" data-autoplay="<?=$autoPlay?>" data-autoplay-timeout="<?=$autoPlayTime.'000'?>" data-animationin="<?=$arParams["ANIMATION_IN"]?>" data-animationout="<?=$arParams["ANIMATION_OUT"]?>">
	   		<?foreach($arResult["ITEMS"] as $arItem):?>
	   			<?
	   			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	   			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	   			
	   			$type = $arItem['PROPERTIES']['BANNER_TYPE']['VALUE_XML_ID'];
	   			$bannerClass = "";
	   			if($type == "L"){
	   				$bannerClass = "col-xs-12 col-sm-7 col-md-7";
	   			} elseif ($type == "C"){
	   				$bannerClass = "col-xs-12 col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2";
	   			} elseif ($type == "R"){
	   				$bannerClass = "col-xs-12 col-sm-6 col-md-offset-6 col-md-6 col-sm-offset-6";
	   			}	   			
	   			$onlyImage = $type == 'IMG' || !$type;
	   			$textColor = $arItem['PROPERTIES']['BANNER_TEXTCOLOR']['VALUE_XML_ID'];	   				   			
	   			?>	   			
	   			<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">	   			
	   				<div class="slider--item" style="background-image: url('<?=$arItem['DETAIL_PICTURE']['SRC']?>');">
	   					<div class="container">
	   						<div class="row">
	   							<?if(!$onlyImage):?>		   									   							
		   							<div class="<?=$bannerClass?>">	   								
		   								<div class="slider--text <?=$textColor == 'LIGHT' ? 'slider--text_inverse' : ''?> <?=$type == 'C' ? 'tac' : ''?>">
		   									<div class="slider--title">
		   										<?=$arItem["~NAME"]?>
		   									</div>
		   									<div class="slider--descr">
		   										<?=$arItem["DETAIL_TEXT"]?>
		   									</div>
		   									<?if($arItem['PROPERTIES']['BANNER_BUTTONTEXT']['VALUE'] && $arItem['PROPERTIES']['BANNER_BUTTONLINK']['VALUE']):?>
			   									<div class="slider--buttons">
			   										<a href="<?=$arItem['PROPERTIES']['BANNER_BUTTONLINK']['VALUE']?>" class="btn <?=(strlen($arItem['PROPERTIES']['BANNER_BUTTONCLASS']['VALUE']) ? $arItem['PROPERTIES']['BANNER_BUTTONCLASS']['VALUE'] : 'btn-default')?>"><?=$arItem['PROPERTIES']['BANNER_BUTTONTEXT']['VALUE']?></a>
			   									</div>
			   								<?endif;?>
		   								</div>	   								
		   							</div>
				   				<?else:?>
				   					<?if($arItem['PROPERTIES']['BANNER_LINK']['VALUE']):?>
				   						<a class="img-link" href="<?=$arItem['PROPERTIES']['BANNER_LINK']['VALUE']?>" rel="nofollow"></a>
				   					<?endif;?>	
			   					<?endif;?>		   								   							
	   						</div>
	   					</div>		   						   					
	   				</div>	   				
	   			</div>	   			
	   		<?endforeach;?>
	   </div>
	</div>
<?endif;?>