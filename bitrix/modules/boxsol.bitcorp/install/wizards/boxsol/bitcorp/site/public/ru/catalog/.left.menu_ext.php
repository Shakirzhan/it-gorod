<? 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
global $APPLICATION; 
$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections", "", Array( 
   "ID"   =>   "", 
   "IBLOCK_TYPE"   =>   "#MARSD_BITCORP#", 
   "IBLOCK_ID"   =>   "#CATALOG_IBLOCK#", 
   "SECTION_URL"   =>   "#SITE_DIR#catalog/#SECTION_CODE_PATH#/", 
   "DEPTH_LEVEL"   =>   "3", 
   "CACHE_TYPE"   =>   "A", 
   "CACHE_TIME"   =>   "3600" 
   ) 
); 
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt); 
?>