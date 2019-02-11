<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" => GetMessage("PORTAL_WIZARD_NAME"), 
	"DESCRIPTION" => GetMessage("PORTAL_WIZARD_DESC"), 
	"VERSION" => "1.0.0",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "/images/solution_small.jpg",
	"PARENT" => "wizard_sol",
	"TEMPLATES" => Array(
		Array("SCRIPT" => "wizard_sol")
	)
);

$themeId = $_REQUEST["__wiz_bitcorp_themeID"];

if (in_array($themeId, array("shop"))) {
	$stepsArray = Array("SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "CatalogSettings", "ShopSettings", "PersonType", "PaySystem", "DataInstallStep" ,"FinishStep");
} else {
	$stepsArray = Array("SelectTemplateStep", "SelectThemeStep", "SiteSettingsStep", "DataInstallStep" ,"FinishStep");
}

if (!defined("WIZARD_DEFAULT_SITE_ID")) {
	array_unshift($stepsArray, "SelectSiteStep");
}

$arWizardDescription["STEPS"] = $stepsArray;
?>