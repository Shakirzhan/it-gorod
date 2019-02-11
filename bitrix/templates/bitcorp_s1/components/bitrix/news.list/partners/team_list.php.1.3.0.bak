<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
$bDetailLink = (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || strlen($arItem["DETAIL_TEXT"]) ? true : false);
$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
?>							
<div class="partners-list--item row row-in" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	<?if($bImage):?>
	    <div class="partners-list--left col-xs-12 col-sm-4 col-md-3">
	    	<?if($bDetailLink):?>
	        	<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
	        <?endif?>
	            <div class="partners-list--image">
	                <img src="<?=$arItem['FIELDS']['PREVIEW_PICTURE']['SRC']?>" alt="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?= ($arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] ? $arItem['FIELDS']['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>">
	            </div>
	        <?if($bDetailLink):?>
    			</a>
    		<?endif;?>
	    </div>
    <?endif;?>

    <div class="partners-list--right col-xs-12 <?=$bImage ? 'col-sm-8 col-md-9' : 'col-sm-12 col-md-12'?>">
        <div class="partners-list--title">
    		<?if($bDetailLink):?>
    	    	<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['~NAME']?></a>
    	    <?else:?>
    	    	<?=$arItem['~NAME']?>
    	    <?endif?>        		
        </div>

        <?if(strlen($arItem["FIELDS"]["PREVIEW_TEXT"])):?>
	        <div class="partners-list--descr">	            
	            <?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>	
	            	<p><?=$arItem["FIELDS"]["PREVIEW_TEXT"]?></p>
	            <?else:?>
	            	<?=$arItem["FIELDS"]["PREVIEW_TEXT"]?>						
	            <?endif;?>
	        </div>
	    <?else:?>
	    	<br/>
        <?endif?>

        <?if(is_array($arItem['PARTNER_FIELDS'])):?>
	        <div class="properties">
	        	<?foreach ($arItem['PARTNER_FIELDS'] as $propCode => $propValue):?>
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