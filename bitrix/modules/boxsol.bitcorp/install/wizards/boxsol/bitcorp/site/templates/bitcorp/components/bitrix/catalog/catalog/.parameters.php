<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
global $USER_FIELD_MANAGER;
if (!Loader::includeModule('iblock'))
	return;

//params for hide
$arTemplateParameters = array(    
   "BASKET_URL" => Array(
       "HIDDEN" => 'Y',
   ),
   "USE_PRODUCT_QUANTITY" => Array(
       "HIDDEN" => 'Y',
   ), 
   "ADD_PROPERTIES_TO_BASKET" => Array(
       "HIDDEN" => 'Y',
   ), 
   "PRODUCT_PROPS_VARIABLE" => Array(
       "HIDDEN" => 'Y',
   ),
   "PARTIAL_PRODUCT_PROPERTIES" => Array(
       "HIDDEN" => 'Y',
   ),
   "PRODUCT_PROPERTIES" => Array(
       "HIDDEN" => 'Y',
   ),
   "SECTION_BACKGROUND_IMAGE" => Array(
       "HIDDEN" => 'Y',
   ),
   "DETAIL_BACKGROUND_IMAGE" => Array(
       "HIDDEN" => 'Y',
   ),
   "USE_STORE" => Array(
       "HIDDEN" => 'Y',
   ),
   "USE_COMPARE" => Array(
       "HIDDEN" => 'Y',
   ), 
);

$arProperty_UF = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);
foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
  $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
  $arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
}

$arTemplateParameters['MESS_BTN_ORDER'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('TITLE_MESS_BTN_ORDER'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('MESS_BTN_ORDER_DEFAULT')
);
$arTemplateParameters['MESS_BTN_QUESTION'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('TITLE_MESS_BTN_QUESTION'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('MESS_BTN_QUESTION_DEFAULT')
);
$arTemplateParameters['MESS_TAB_DESCRIPTION'] = array(
  'PARENT' => 'VISUAL',
  'NAME' => GetMessage('TITLE_MESS_TAB_DESCRIPTION'),
  'TYPE' => 'STRING',
  'DEFAULT' => GetMessage('MESS_TAB_DESCRIPTION_DEFAULT')
);
$arTemplateParameters['MESS_TAB_PROPS'] = array(
  'PARENT' => 'VISUAL',
  'NAME' => GetMessage('TITLE_MESS_TAB_PROPS'),
  'TYPE' => 'STRING',
  'DEFAULT' => GetMessage('MESS_TAB_PROPS_DEFAULT')
);
$arTemplateParameters['MESS_TAB_FILES'] = array(
  'PARENT' => 'VISUAL',
  'NAME' => GetMessage('TITLE_MESS_TAB_FILES'),
  'TYPE' => 'STRING',
  'DEFAULT' => GetMessage('MESS_TAB_FILES_DEFAULT')
);
$arTemplateParameters['MESS_OTHER_ITEMS'] = array(
  'PARENT' => 'VISUAL',
  'NAME' => GetMessage('TITLE_MESS_OTHER_ITEMS'),
  'TYPE' => 'STRING',
  'DEFAULT' => GetMessage('MESS_OTHER_ITEMS_DEFAULT')
);

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

$arTemplateParameters['SECTION_SHOW_PREVIEW_TEXT'] = array(
    "PARENT" => "LIST_SETTINGS",
    "NAME" => GetMessage("CP_BCSL_SECTION_SHOW_PREVIEW_TEXT"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "Y",
    "REFRESH" => "Y",
  );

if (isset($arCurrentValues['SECTION_SHOW_PREVIEW_TEXT']) && 'Y' == $arCurrentValues['SECTION_SHOW_PREVIEW_TEXT']){
  $arTemplateParameters['SECTION_SHOW_PREVIEW_TEXT_POSITION'] = array(
      "PARENT" => "LIST_SETTINGS",
      "NAME" => GetMessage("CP_BCSL_SECTION_SHOW_PREVIEW_TEXT_POSITION"),
      "TYPE" => "LIST",
      "MULTIPLE" => "N",
      "ADDITIONAL_VALUES" => "N",
      "VALUES" => array(
        "top" => GetMessage('PREVIEW_TEXT_POSITION_TOP'),
        "bottom" => GetMessage('PREVIEW_TEXT_POSITION_BOTTOM'),
      ),
    );
}
?>