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
		 $this->addExternalCss(SITE_TEMPLATE_PATH . "/assets/slick/slick.css");
		 $this->addExternalCss(SITE_TEMPLATE_PATH . "/assets/slick/slick-theme.css");
		 $this->addExternalCss(SITE_TEMPLATE_PATH . "/assets/fancybox/jquery.fancybox-1.3.4.css");
		 $this->addExternalJs(SITE_TEMPLATE_PATH . "/assets/slick/slick.min.js");	
		 $this->addExternalJs(SITE_TEMPLATE_PATH . "/assets/fancybox/jquery.fancybox-1.3.4.js");
		?>		

			    <div class="test" >

							<div class="project-reviews">
								<h2>Отзывы</h2>		
								<div class="project-slider-reviews" >
								
									<?/*<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Мясникова.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Мясникова.jpg" alt="мясникова отзыв" /></a></div>
									<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Abacus.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Abacus.jpg" alt="абакус отзыв" /></a></div>
									<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/GoodHouse.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/GoodHouse.jpg" alt="абакус отзыв" /></a></div>
									<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Скан_001.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Скан_001.jpg" alt="мясникова отзыв" /></a></div>
									<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/татекст16.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/татекст16.jpg" alt="абакус отзыв" /></a></div>
									<div class="item"><a data-fancybox="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Театро.jpg"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Театро.jpg" alt="мясникова отзыв" /></a></div>
									*/?>

									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/ekraniprosto.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/ekraniprosto.jpg" alt="Экраны просто" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Мясникова.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Мясникова.jpg" alt="мясникова отзыв" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Abacus.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Abacus.jpg" alt="абакус отзыв" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/GoodHouse.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/GoodHouse.jpg" alt="абакус отзыв" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Скан_001.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Скан_001.jpg" alt="мясникова отзыв" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/татекст16.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/татекст16.jpg" alt="абакус отзыв" /></div></a>
									<a rel="images" class="fancy-group" href="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Театро.jpg"><div class="item"><img src="<?=SITE_TEMPLATE_PATH?>/img/otzivy/Театро.jpg" alt="мясникова отзыв" /></div></a>
								</div>
							</div>						
			
				</div>
				<script>
						$('.project-slider-reviews').slick({
							infinite: true,
							slidesToShow: 3,
							slidesToScroll: 3,
							variableWidth: true,
						});

						$("a.fancy-group").fancybox({
							'transitionIn'		: 'none',
							'transitionOut'		: 'none',
							'titlePosition' 	: 'over',
						});
				</script>
				<?if(strlen($arParams["PROJECTS_BLOCK_DESCRIPTION"])):?>
					<div class="row row-in">
						<div class="col-xs-12">						
							<p><?=$arParams["PROJECTS_BLOCK_DESCRIPTION"]?></p>						
						</div>
					</div>
				<?endif;?>		
				
				
				<?// top pagination?>
				<?if($arParams['DISPLAY_TOP_PAGER']):?>
					<?=$arResult['NAV_STRING']?>
					<br/>
				<?endif;?>

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
			<?// bottom pagination?>
			<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
				<div class="clear"></div>
				<br/>
				<?=$arResult['NAV_STRING']?>
			<?endif;?>
				
	<?endif;?>