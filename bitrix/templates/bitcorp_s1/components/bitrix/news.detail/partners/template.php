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
use \Bitrix\Main\Localization\Loc;
$bImage = ($arResult["MD_DETAIL_PICTURE"] ? true : false);
?>

<div class="row row-in">
	<?if($bImage):?>
		<div class="col-xs-12 col-md-4 mt30">		
			<div class="partners-list--image">
				<img class="img-responsiver" src="<?=$arResult["MD_DETAIL_PICTURE"]["PREVIEW"]["src"]?>" title="<?=$arResult["MD_DETAIL_PICTURE"]["TITLE"]?>" alt="<?=$arResult["MD_DETAIL_PICTURE"]["ALT"]?>" />
			</div>		
		</div>
	<?endif;?>	

	<div class="col-xs-12 <?=$bImage ? 'col-md-8' : 'col-md-12'?>">

		<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
		<div class="h3"><?=$arResult["NAME"]?></div>
		<?endif;?>

		<?if(strlen($arResult['FIELDS']['PREVIEW_TEXT'])):?>			
				<?if(strlen($arResult['FIELDS']['PREVIEW_TEXT'])):?>
					<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
						<p><?=$arResult['FIELDS']['PREVIEW_TEXT'];?></p>
					<?else:?>
						<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
					<?endif;?>
				<?endif;?>	
		<?endif;?>

        <?if(is_array($arResult['PARTNER_FIELDS'])):?>
	        <div class="properties">
	        	<?foreach ($arResult['PARTNER_FIELDS'] as $propCode => $propValue):?>
	        		<?if($propCode == 'WEBSITE'):?>
			            <div class="inner-wrapper">
			                <div class="property">
			                	<?if(is_array($propValue['VALUE'])):?>
			                		<!-- noindex -->
			                		<i class="fa fa-external-link" aria-hidden="true"></i> 
                                    <?foreach($propValue['VALUE'] as $key => $value):?>
                                        <?if($propValue['VALUE'][$key + 1]):?>
                                            <a href="<?=(strpos($value, 'http') === false ? 'http://' : '').$value;?>" rel="nofollow" target="_blank"><?=$value?></a>&nbsp;/
                                        <?else:?>
                                            <a href="<?=(strpos($value, 'http') === false ? 'http://' : '').$value;?>" rel="nofollow" target="_blank"><?=$value?></a>
                                        <?endif;?>
                                    <?endforeach;?>
                                    <!-- /noindex -->
                                <?else:?>
				                	<!-- noindex -->
				                    <a href="<?=(strpos($propValue['VALUE'], 'http') === false ? 'http://' : '').$propValue['VALUE'];?>" rel="nofollow" target="_blank">
										<i class="fa fa-external-link" aria-hidden="true"></i> <?=$propValue['VALUE'];?>
									</a>
									<!-- /noindex -->
								<?endif;?>
			                </div>
			            </div>
		            <?elseif($propCode == 'PHONE'):?>
			            <div class="inner-wrapper">
			                <div class="property">
			                	<?if(is_array($propValue['VALUE'])):?>
			                		<!-- noindex -->
			                		<i class="fa fa-phone"></i> 
			                		<?foreach($propValue['VALUE'] as $key => $value):?>
			                			<?if($propValue['VALUE'][$key + 1]):?>	
			                				<a href="tel:<?=$value;?>"><?=$value?></a>&nbsp;/
			                			<?else:?>
			                				<a href="tel:<?=$value;?>"><?=$value?></a>
			                			<?endif;?>
		                			<?endforeach;?>
			                		<!-- /noindex -->
								<?else:?>			                		
				                	<!-- noindex -->
				                    <a href="tel:<?=$propValue['VALUE'];?>">
										<i class="fa fa-phone"></i> <?=$propValue['VALUE'];?>
									</a>
									<!-- /noindex -->
								<?endif;?>
			                </div>
			            </div>
			        <?elseif($propCode == 'EMAIL'):?>
			        	<div class="inner-wrapper">
			        	    <div class="property">
			        	    	<?if(is_array($propValue['VALUE'])):?>
				        	    	<!-- noindex -->
				        	    	<i class="fa fa-envelope"></i> 
				        	    	<?foreach($propValue['VALUE'] as $key => $value):?>
			        	    			<?if($propValue['VALUE'][$key + 1]):?>
			        	    				<a href="mailto:<?=$value;?>" rel="nofollow"><?=$value?></a>&nbsp;/	
		        	    				<?else:?>
		        	    					<a href="mailto:<?=$value;?>" rel="nofollow"><?=$value?></a>	
		        	    				<?endif;?>
			        	    		<?endforeach;?>
				        	    	<!-- /noindex -->	
		        	    		<?else:?>
						        	<!-- noindex -->
						        	<a href="mailto:<?=$propValue['VALUE'];?>" rel="nofollow">
						        		<i class="fa fa-envelope"></i> <?=$propValue['VALUE']?>	
						        	</a>
						        	<!-- /noindex -->
					        	<?endif;?>
					        </div>
					    </div>
			        <?else:?>
			        	<?if(is_array($propValue['VALUE'])):?>
			        		<?foreach($propValue['VALUE'] as $key => $value):?>
				        		<?if($propValue['VALUE'][$key + 1]):?>
				        			<?=$value?>&nbsp;/
				        		<?else:?>
				        			<?=$value?>		
				        		<?endif;?>
			        		<?endforeach;?>
		        		<?else:?>
			        		<?=$propValue['VALUE']?>
			        	<?endif;?>
			        <?endif;?>
		        <?endforeach?>
	        </div>
        <?endif;?>
	</div>
</div>

<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>			
		<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
			<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
				<p><?=$arResult['FIELDS']['DETAIL_TEXT'];?></p>
			<?else:?>
				<?=$arResult['FIELDS']['DETAIL_TEXT'];?>
			<?endif;?>
		<?endif;?>	
<?endif;?>