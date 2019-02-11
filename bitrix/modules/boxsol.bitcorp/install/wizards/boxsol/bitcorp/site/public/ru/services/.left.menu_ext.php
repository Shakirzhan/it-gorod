<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"boxsol:menu.elements",
	"", 
	array(		
		"IBLOCK_TYPE" => "#MARSD_BITCORP#",
		"IBLOCK_ID" => "#SERVICES_IBLOCK#",		
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false,
	array(
		"HIDE_ICONS" => "Y",
	)
);
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>