<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Application;
Loc::loadMessages(__FILE__);
CBitcorp::setColorCSS();			
?>
<?if(!$isIndex):?>
			<?if($isLeftSidebar):?>
				</div><!-- /.col-md-9 -->
			<?elseif($isRightSidebar):?>
				</div><!-- /.col-md-9 -->
				<div class="col-md-3 col-sm-3 hide-sm hide-md">
					<aside class="sidebar">
						<?$APPLICATION->IncludeComponent(
							"bitrix:menu",
							"left",
							array(
								"ROOT_MENU_TYPE" => "left",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600000",
								"MENU_CACHE_USE_GROUPS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"MAX_LEVEL" => "4",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "Y",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "Y",
								"COMPONENT_TEMPLATE" => "left"
							),
							false
						);?>
					</aside>
				</div>
			<?else:?>
				</div><!-- /.col-md-12 -->
			<?endif;?>

		</div><!-- /.row -->
	</div><!-- /.container -->	
</div><!-- /.content-wrapper -->
<?endif;?>

<?
$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    ".default",
    Array(
        "AREA_FILE_SHOW" => "page",
        "AREA_FILE_SUFFIX" => "footer",
        "AREA_FILE_RECURSIVE" => "N",
        "EDIT_TEMPLATE" => ""
    ),
    false
);
?>


		<div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-xs-9">
						<? $APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
						    "ROOT_MENU_TYPE" => "bottom",
						    "MAX_LEVEL" => "1",
						    "CHILD_MENU_TYPE" => "left",
						    "USE_EXT" => "N",
						    "MENU_CACHE_TYPE" => "A",
						    "MENU_CACHE_TIME" => "36000000",
						    "MENU_CACHE_USE_GROUPS" => "Y",
						    "MENU_CACHE_GET_VARS" => ""
						),
						    false						    
						); ?>						
					</div>
					<div class="col-xs-3 tar">
						<?
						$APPLICATION->IncludeComponent(
	"boxsol:variable.set", 
	"bottom_social", 
	array(
		"COMPONENT_TEMPLATE" => "bottom_social",
		"LINK_VK" => "https://vk.com/itgorodrus",
		"LINK_FB" => "",
		"LINK_TWITTER" => "",
		"LINK_INSTAGRAM" => "",
		"LINK_YOUTUBE" => "",
		"LINK_ODNIKLASSNIKI" => "",
		"LINK_GOOGLEPLUS" => ""
	),
	false
);
						?>						 
					</div>
				</div>
				<div class="row footer--info">					
					<div class="col-xs-12 col-sm-6 col-md-6 pull-right">
						<div class="footer--contacts">
							<div class="f-phone-number">
								<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/footer-phone.php", Array(), Array("MODE" => "html", "NAME" => "header-phone"));?>
							</div>
							<div class="f-email">
								<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-email.php", Array(), Array("MODE" => "html", "NAME" => "header-email"));?>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="cop">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/footer-copyrights.php", Array(), Array("MODE" => "html", "NAME" => "footer-copyrights"));?>					
						</div>
					</div>
				</div>
				<div class="row">
					<div id="bx-composite-banner"></div>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/footer-metrika.php", Array(), Array("MODE" => "html", "NAME" => "File for metriks:Yandex.Metrika, Google.Analytics, JivoSite etc."));?>
				</div>
			</div>
		</div>
		</div><!-- /.md-wrapper-->

		<script data-skip-moving="true">
        (function(w,d,u){
                var s=d.createElement('script');s.async=1;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn.bitrix24.ru/b5586969/crm/site_button/loader_2_zte8q5.js');
		</script>
<style type="text/css">
	#wh-widget-send-button.wh-widget-right {
    right: 35px;
	}
	.b24-widget-button-wrapper.b24-widget-button-position-bottom-right.b24-widget-button-visible {
		bottom: 90px;
	}
	@media (max-width: 768px) {
		#wh-widget-send-button.wh-widget-right { right: -10px; }	
		.b24-widget-button-wrapper.b24-widget-button-position-bottom-right.b24-widget-button-visible { right: 5px; }
	}
</style>
<!-- WhatsHelp.io widget -->

<!-- /WhatsHelp.io widget -->
<script type="text/javascript">
	/**
	$( window ).load(function() {
		$( ".b24-crm-button-icon-active" ).click(function() {
  		$('#wh-widget-send-button').css({'margin' : '0',
  																		 'padding' : '0',
  																		 'position' : 'fixed',
    																	 'z-index' : '16000160',
																	     'bottom' : '440px',
																	     'text-align' : 'center',
																	     'visibility' : 'visible',
																	     'transition' : 'none',
																	     'right' : '264px'});
  		$('#wh-widget-send-button').attr('id', 'wh-widget-send-button-1');
  	});
  	$( '.b24-widget-button-close' ).click(function() {
  		$('#wh-widget-send-button').css({'bottom' : '170px'});
  	});
	});
	*/
</script>
	</body>
</html>