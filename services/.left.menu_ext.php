<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"boxsol:menu.elements",
	"", 
	array(		
		"IBLOCK_TYPE" => "marsd_bitcorp_s1",
		"IBLOCK_ID" => "13",		
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