<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock"))
    return;

$arTypes = Array(
    Array(
        "ID" => "marsd_bitcorp_requests",
        "SECTIONS" => "Y",
        "IN_RSS" => "N",
        "SORT" => 100,
        "LANG" => Array(),
    ),
    Array(
        "ID" => "marsd_bitcorp",
        "SECTIONS" => "Y",
        "IN_RSS" => "N",
        "SORT" => 200,
        "LANG" => Array(),
    ),   
);

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while ($arLanguage = $rsLanguage->Fetch())
    $arLanguages[] = $arLanguage["LID"];

$iblockType = new CIBlockType;
foreach ($arTypes as $arType) {
    $code = strtoupper($arType["ID"]);
    $arType["ID"] .= '_' . strtolower(WIZARD_SITE_ID);

    $dbType = CIBlockType::GetList(Array(), Array("=ID" => $arType["ID"]));
    if ($dbType->Fetch())
        continue;

    foreach ($arLanguages as $languageID) {
        WizardServices::IncludeServiceLang("type.php", $languageID);

        $arType["LANG"][$languageID]["NAME"] = "[" . WIZARD_SITE_ID . "] " . GetMessage($code . "_TYPE_NAME");
        $arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code . "_ELEMENT_NAME");

        if ($arType["SECTIONS"] == "Y")
            $arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code . "_SECTION_NAME");
    }
    $iblockType->Add($arType);
    
    if($iblockType)
    {
        WizardServices::ReplaceMacrosRecursive(
            WIZARD_SITE_PATH,
            Array(
                $code => $arType["ID"]                
            )
        );
        
        CWizardUtil::ReplaceMacros(
            $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/components/bitrix/news/services/detail.php",
            Array(
                $code => $arType["ID"]                
            )
        );
        CWizardUtil::ReplaceMacros(
            $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/components/bitrix/news/projects/detail.php",
            Array(
                $code => $arType["ID"]                
            )
        );
    }
}
?>