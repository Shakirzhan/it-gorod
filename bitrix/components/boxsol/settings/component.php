<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Application,
	Bitrix\Main\Data\StaticHtmlCache;
global $USER;
$moduleClass = "CBitcorp";
$moduleID = "boxsol.bitcorp";

if(!Loader::IncludeModule($moduleID))
	return;

$request = Application::getInstance()->getContext()->getRequest();

$changeTheme = $request->getPost("CHANGE_THEME");
$theme = $request->getPost("THEME");

if($request->isPost() && $changeTheme == "Y" && check_bitrix_sessid()) {	
	foreach($moduleClass::getParametrs() as $blockCode => $arBlock){
		foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {
			if($arOption["IN_SETTINGS_PANEL"] == "Y" && $theme == "default") {
				$newVal = $arOption["DEFAULT"];
			} else {
				$post = $request->getPost($optionCode);
				if($optionCode == "COLOR_SCHEME_CUSTOM"){
					//$post = $moduleClass::CheckColor($post);				
				}
				$newVal = $post;
				if($arOption["TYPE"] == "multiselectbox") {
					if(!is_array($newVal))
						$newVal = array();
				}
			}			
			$arTab["OPTIONS"][$optionCode] = $newVal;

		}
	}
	
	
	if($moduleClass::isDemoMode() && !$USER->IsAdmin()){
		//save OPTIONS to $_SESSION for demoMode only
		$_SESSION["BITCORP_OPTIONS"][SITE_ID] = serialize((array)$arTab["OPTIONS"]);
	} else {
		Option::set($moduleID, "OPTIONS", serialize((array)$arTab["OPTIONS"]), SITE_ID);
	}
	
	if(CHTMLPagesCache::isOn()) {
		$staticHtmlCache = StaticHtmlCache::getInstance();
		$staticHtmlCache->deleteAll();
	}
	
	BXClearCache(true, "/".SITE_ID."/boxsol/");
	BXClearCache(true, "/".SITE_ID."/bitrix/");
	BXClearCache(true, "/".SITE_ID."/news/");	
	
}

$arResult = array();
$arFrontParametrs = $moduleClass::GetFrontParametrsValues(SITE_ID);

foreach($moduleClass::getParametrs() as $blockCode => $arBlock){
	foreach($arBlock["OPTIONS"] as $optionCode => $arOption){		
		$arResult[$optionCode] = $arOption;		
		$arResult[$optionCode]["VALUE"] = $arFrontParametrs[$optionCode];
		
		if($arResult[$optionCode]["LIST"]){
			foreach($arResult[$optionCode]["LIST"] as $variantCode => $variantTitle){
				if(!is_array($variantTitle)){
					$arResult[$optionCode]["LIST"][$variantCode] = array("TITLE" => $variantTitle);
				}
				if($arResult[$optionCode]["TYPE"] == "selectbox"){
					if($arResult[$optionCode]["VALUE"] == $variantCode){
						$arResult[$optionCode]["LIST"][$variantCode]["CURRENT"] = "Y";
					}
				} elseif($arResult[$optionCode]["TYPE"] == "multiselectbox"){
					if(in_array($variantCode, $arResult[$optionCode]["VALUE"])){
						$arResult[$optionCode]["LIST"][$variantCode]["CURRENT"] = "Y";
					}
				}
			}
		}
	}
}

//\Bitrix\Main\Diag\Debug::dumpToFile($_SESSION);
global $USER;
if( ($USER->IsAdmin() &&  $arResult["SHOW_SETTINGS_PANEL"]["VALUE"] == "Y") || (CBitcorp::isDemoMode())) {
	$this->IncludeComponentTemplate();
}
return $arResult;?>