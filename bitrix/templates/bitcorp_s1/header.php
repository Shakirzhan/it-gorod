<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Page\Asset; 

if(!defined("BITCORP_MODULE_NAME"))
{
    define("BITCORP_MODULE_NAME", "boxsol.bitcorp");
}
CJSCore::Init(array("jquery", "fx"));
IncludeTemplateLangFile(__FILE__);

$arCurrentSite = CSite::GetByID(SITE_ID)->Fetch();
$request = Application::getInstance()->getContext()->getRequest();
\Bitrix\Main\Loader::includeModule(BITCORP_MODULE_NAME);
?> 
<!doctype html>
<html dir="ltr" lang="<?= $arCurrentSite["LANGUAGE_ID"] ?>">

<head>
	<?Asset::getInstance()->addString('<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">');?>
	<?Asset::getInstance()->addString('<meta content="telephone=no" name="format-detection">');?>
	<?Asset::getInstance()->addString('<meta http-equiv="X-UA-Compatible" content="IE=edge" />');?>		
	<?Asset::getInstance()->addString('<script>BX.message('.CUtil::PhpToJSObject($MESS, false).')</script>', true);?>
	<?CBitcorp::ShowFaviconImg();?>	
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=cyrillic" rel="stylesheet">	
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

	<?$APPLICATION->ShowHead();?>

	<?
		$moduleStatus = CModule::IncludeModuleEx(BITCORP_MODULE_NAME);		    
		if($moduleStatus == 0){
			die(GetMessage("BITCORP_NOT_INSTALLED"));
		} elseif ($moduleStatus == 3){
		    die(GetMessage("BITCORP_DEMO_EXPIRED"));
		} else {		
			CBitcorp::init();
			//for demo site only
			//CBitcorp::setDemoMode();			
		}
	?>	

	<title><?$APPLICATION->ShowTitle()?></title>

	<meta property="og:title" content="<?=$APPLICATION->ShowTitle();?>"/>
    <meta property="og:description" content="<?=$APPLICATION->GetProperty("description");?>"/>
    <meta property="og:image" content="<?=$APPLICATION->ShowProperty("ogimage");?>">
    <meta property="og:type" content="article"/>
    <meta property="og:url" content= "<?=$request->getRequestUri();?>" />
<meta name="google-site-verification" content="flGSnMldfM0rqu-bGgG0ivDVtRKgk7Bzp6nb9YMeURY" />
<meta name="yandex-verification" content="17247c7f6d12c5a2" />
<!-- WhatsHelp.io widget -->
<!-- <script type="text/javascript" async>
    (function () {
        var options = {
            whatsapp: "+79600464155", // WhatsApp number
            company_logo_url: "//static.whatshelp.io/img/flag.png", // URL of company logo (png, jpg, gif)
            greeting_message: "Здравствуйте! Отправьте нам сообщение через любой из мессенджеров.", // Text of greeting message
            call_to_action: "Напишите нам", // Call to action
            position: "right", // Position may be 'right' or 'left'
        };
        var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
    })();
</script> -->
<!-- /WhatsHelp.io widget -->
</head>

<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<body>	
<a  href="https://api.whatsapp.com/send?phone=79600464155" target="_blank" id="whatsapphelp" style="display: block;width: 48px;height: 48px;position: fixed;right: 59px;bottom: 23px;z-index: 10000;cursor: pointer;"> <img style="display: block;max-width: 100%;" src="/bitrix/templates/bitcorp_s1/img/whatsapp_512x512.png"></a>
	<div class="md-wrapper slider-wrapper">
	<?
		global $arSetting, $arSite, $headerType, $menuType, $is404;
		$arSetting = $APPLICATION->IncludeComponent("boxsol:settings", "", array(), false, array("HIDE_ICONS" => "Y"));		
		$arSite = CSite::GetByID(SITE_ID)->Fetch();
		$isIndex = CSite::inDir(SITE_DIR."index.php");
		$isLeftSidebar = ($APPLICATION->GetProperty('SIDEBAR_LEFT') == "Y" ? true : false);
		$isRightSidebar = ($APPLICATION->GetProperty('SIDEBAR_RIGHT') == "Y" ? true : false);
		$is404 = defined("ERROR_404") && ERROR_404 === "Y";	
		$headerType = $arSetting["HEADER_TYPE"]["VALUE"];	
		$menuType = ($arSetting['MENU_TYPE']['VALUE'] == "WHITE" ? "main-menu-wrapper_white" : ($arSetting['MENU_TYPE']['VALUE'] == "DARK" ? "main-menu-wrapper_black" : ""));

	?>

	<?if($arSetting['MENU_TYPE']['VALUE'] == 'DARK' && $arSetting['HEADER_TYPE']['VALUE'] != 'SECOND'):?>
		<div class="black-menu">
	<?endif;?>

		<div class="header <?=($headerType == 'SECOND' ? 'header_compact' : '');?>">			

			<?if($headerType == "FIRST"):?>
		<style>
@media (max-width: 1200px) {
  .hider {display: none !important;}
}
</style>
				<div class="container ">
					<div class="row header--row">
						<div class="col-sm-3 col-md-2 col-lg-2">
							<a class="logo" href="<?=SITE_DIR?>">							
								<?=CBitcorp::ShowLogoImg();?>
							</a>
						</div>
						<div class="col-sm-5 col-md-3 col-lg-3 hide-sm">
							<div class="top-tagline">
								<small><?$APPLICATION->IncludeFile(SITE_DIR."include/header/slogan.php", Array(), Array("MODE" => "html", "NAME" => "slogan"));?></small>					
							</div>
						</div>
						<div class="col-sm-5 col-md-3 col-lg-3 hide-sm"><div class="h-phone">
								<p class="h-phone--worktime">
									<?$APPLICATION->IncludeFile(SITE_DIR."include/header/work-time.php", Array(), Array("MODE" => "html", "NAME" => "work-time"));?>						
								</p> </div></div>
						<div class="col-sm-5 col-md-3 col-lg-3 hide-sm hider" style="padding-top: 10px; padding-bottom: 10px;"><div class="h-phone"><p class="h-phone--number">
									<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-phone.php", Array(), Array("MODE" => "html", "NAME" => "header-phone"));?>						
								</p></div></div>
						<div class="col-sm-4 col-md-2 col-lg-2 header-contacts">
							
							<div class="h-button">
								<a class="btn btn-primary" data-event="jqm" data-ajax="<?=SITE_DIR?>ajax/form.php" data-name="callback">
									<?=GetMessage("MD_ORDER_BUTTON_TITLE")?>  
								</a>
							</div>
						</div>
					</div>
				</div>
			<?elseif($headerType == "SECOND"):?>
				<div class="top-block hide-sm">
					<div class="container">
						<div class="row header--row">
							<div class="col-sm-6">
								<div class="top-block--info">
									<i class="fa fa-map-marker" aria-hidden="true"></i>
									<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-adress.php", Array(), Array("MODE" => "html", "NAME" => "header-adress"));?>
								</div>
							</div>
							<div class="col-sm-6 tar">
								<div class="h-phone">								
									<p class="h-phone--number">
										<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-phone.php", Array(), Array("MODE" => "html", "NAME" => "header-phone"));?>
									</p>
								</div>
								<a class="btn btn-xs btn-primary" data-event="jqm" data-ajax="<?=SITE_DIR?>ajax/form.php" data-name="callback"><?=GetMessage("MD_ORDER_BUTTON_TITLE")?>  </a>
							</div>
						</div>
					</div>
				</div>
				<div class="second_header">
					<div class="container">
						<div class="row header--row">
							<div class="col-sm-3 col-md-3 col-lg-3">
								<a class="logo" href="<?=SITE_DIR?>">									
									<?=CBitcorp::ShowLogoImg();?>
								</a>
							</div>
							<div class="col-sm-9 col-md-9 col-lg-9 hide-sm">
								<? $APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel_second", array(
								    "ROOT_MENU_TYPE" => "top",
								    "MAX_LEVEL" => "3",
								    "CHILD_MENU_TYPE" => "left",
								    "USE_EXT" => "Y",
								    "MENU_CACHE_TYPE" => "A",
								    "MENU_CACHE_TIME" => "36000000",
								    "MENU_CACHE_USE_GROUPS" => "Y",
								    "MENU_CACHE_GET_VARS" => ""
								),
								    false						    
								); ?>
							</div>
							
						</div>
					</div> 
				</div>

			<?endif;?> 
		</div>

		<?if($headerType == "FIRST"):?>		
			<div class="main-menu-wrapper hide-sm <?=$menuType?>">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<? $APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel_first", array(
							    "ROOT_MENU_TYPE" => "top",
							    "MAX_LEVEL" => "3",
							    "CHILD_MENU_TYPE" => "left",
							    "USE_EXT" => "Y",
							    "MENU_CACHE_TYPE" => "A",
							    "MENU_CACHE_TIME" => "36000000",
							    "MENU_CACHE_USE_GROUPS" => "Y",
							    "MENU_CACHE_GET_VARS" => ""
							),
							    false						    
							); ?>						
						</div>
					</div>
				</div>
			</div>
		
		<?endif;?>

		<div class="mobile-menu--btn show-sm dn">
			<span></span>
		</div>
		<? $APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel_mobile", array(
		    "ROOT_MENU_TYPE" => "top",
		    "MAX_LEVEL" => "3",
		    "CHILD_MENU_TYPE" => "left",
		    "USE_EXT" => "Y",
		    "MENU_CACHE_TYPE" => "A",
		    "MENU_CACHE_TIME" => "36000000",
		    "MENU_CACHE_USE_GROUPS" => "Y",
		    "MENU_CACHE_GET_VARS" => ""
		),
		    false,
		    array(
		        "HIDE_ICONS" => "Y",
		        "ACTIVE_COMPONENT" => "Y"
		    )
		); ?>

		<?$APPLICATION->ShowViewContent('hide_breadcrubs_start')?>
		<?/* page title and breadcrumbs block */?>
		<?if (!$isIndex && !$is404):?>
			<div class="page-head page-head_compact">
				<div class="container">
					<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "bitcorp", array(
					    "START_FROM" => "0",
					    "PATH" => "",
					    "SITE_ID" => SITE_ID
						),
						false				   
					);?>
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<h1><?$APPLICATION->ShowTitle(false);?></h1>
						</div>
					</div>
				</div>
			</div>		
		<?endif;?>

	<?if($arSetting['MENU_TYPE']['VALUE'] == 'DARK' && $arSetting['HEADER_TYPE'] != 'SECOND'):?>
		</div>
	<?endif;?>

	<?$APPLICATION->ShowViewContent('hide_breadcrubs_end')?>
	
	<?$APPLICATION->ShowViewContent('top_banner')?>

	<?
	$APPLICATION->IncludeComponent(
	    "bitrix:main.include",
	    ".default",
	    Array(
	        "AREA_FILE_SHOW" => "page",
	        "AREA_FILE_SUFFIX" => "header",
	        "AREA_FILE_RECURSIVE" => "N",
	        "EDIT_TEMPLATE" => ""
	    ),
	    false,
	    array(
	        "HIDE_ICONS" => "Y",
	        "ACTIVE_COMPONENT" => "Y"
		    )
	);
	?>
	<?if($isIndex):?>
		<?@include(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'/indexblocks.php'));?>		
	<?else:?>
		<?
		    $isProjectsDetail = false;
			if (strpos($APPLICATION->GetCurDir(), 'projects') !== false && strlen($APPLICATION->GetCurDir()) > 10) {
				$isProjectsDetail = true;
			}
		?>
		<div class="content-wrapper <?= $is404 ? 'content-wrapper_bg-default' : $isProjectsDetail ? 'project-detail' : '' ?>">	
			<div class="container">
				<div class="row">
					<?if($is404):?>

					<?elseif($isLeftSidebar):?>
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
						<div class="col-md-9">
							<div class="first-elem"></div>
					<?elseif($isRightSidebar):?>
						<div class="col-md-9">
							<div class="first-elem"></div>
					<?else:?>
						<div class="col-md-12">
							<div class="first-elem"></div>
					<?endif;?>
	<?endif;?>
