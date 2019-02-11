<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;
 
if (WIZARD_INSTALL_DEMO_DATA)
{
	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"); 
	
	$handle = @opendir($path);
	if ($handle)
	{
		$counterToBreakWhile = 0;
		while ($file = readdir($handle))
		{
			if (in_array($file, array(".", "..")))
				continue;

			CopyDirFiles(
				$path.$file,
				WIZARD_SITE_PATH."/".$file,
				$rewrite = true, 
				$recursive = true,
				$delete_after_copy = false,
				$exclude = ""				
			);
			$counterToBreakWhile++;
			if ($counterToBreakWhile == 100) {
				break;
			}
		}
		CModule::IncludeModule("search");
		CSearch::ReIndexAll(false, 0, Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
	}

	//HTACCESS//
	WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

	//REPLACE_MACROS//
	$themeReplaceDirs = array("company", "contacts", "news", "price", "projects", "search", "services", "catalog");

	foreach ($themeReplaceDirs as $replaceDir) {
		WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH. $replaceDir. "/", Array("SITE_DIR" => WIZARD_SITE_DIR));
	}
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH. "ajax/", Array("SITE_DIR" => WIZARD_SITE_DIR));
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH. "include/", Array("SITE_DIR" => WIZARD_SITE_DIR));

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."index_header.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."index_footer.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."indexblocks.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/.left.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));


	//URLREWRITE//
	$arUrlRewrite = array();
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}
	$arNewUrlRewrite = array(		
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."company/staff/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."company/staff/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."company/partners/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."company/partners/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."projects/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."projects/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."services/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."news/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."news/index.php",
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."catalog/#",
			"RULE" => "",
			"ID" => "bitrix:catalog",
			"PATH" => WIZARD_SITE_DIR."catalog/index.php",
		),		
	);

	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
	}
}

CheckDirPath(WIZARD_SITE_PATH."include/");

if (WIZARD_INSTALL_DEMO_DATA)
{ 
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));
}

// if need add to init.php
//$file = fopen(WIZARD_SITE_ROOT_PATH."/bitrix/php_interface/init.php", "ab");
//fwrite($file, file_get_contents(WIZARD_ABSOLUTE_PATH."/site/services/main/bitrix/init.php"));
//fclose($file);
?>