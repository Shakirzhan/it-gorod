<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if (!Loader::includeModule('iblock'))
	return;
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCS_TPL_MESS_NOT_AVAILABLE_DEFAULT')
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
      "MULTIPLE" => "Y",
      "ADDITIONAL_VALUES" => "Y",
      "VALUES" => array(
        "top" => GetMessage('PREVIEW_TEXT_POSITION_TOP'),
        "bottom" => GetMessage('PREVIEW_TEXT_POSITION_BOTTOM'),
      ),
    );
}
?>