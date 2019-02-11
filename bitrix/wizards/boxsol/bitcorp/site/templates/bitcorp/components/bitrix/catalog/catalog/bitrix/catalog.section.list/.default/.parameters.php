<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER_FIELD_MANAGER;

$arProperty_UF = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);
foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
  $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
  $arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
}

$arTemplateParameters['SECTION_FIELDS'] = CIBlockParameters::GetSectionFieldCode(
      GetMessage("CP_BCSL_SECTION_FIELDS"),
      "SECTIONS_SETTINGS",
      array()
    );

$arTemplateParameters['SECTION_USER_FIELDS'] = array(
    "PARENT" => "SECTIONS_SETTINGS",
    "NAME" => GetMessage("CP_BCSL_SECTION_USER_FIELDS"),
    "TYPE" => "LIST",
    "MULTIPLE" => "Y",
    "ADDITIONAL_VALUES" => "Y",
    "VALUES" => $arProperty_UF,
  );

$arTemplateParameters['SECTION_SHOW_DESCR'] = array(
    "PARENT" => "SECTIONS_SETTINGS",
    "NAME" => GetMessage("CP_BCSL_SECTION_SHOW_DESCR"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "N",
  );
?>