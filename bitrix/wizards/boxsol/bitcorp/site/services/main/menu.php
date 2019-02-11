<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!defined("WIZARD_SITE_ID"))
	return;

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('fileman');
$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);

if(!isset($arMenuTypes['top']))
	$arMenuTypes['top'] = Loc::getMessage('WIZ_MENU_TOP_DEFAULT');
if(!isset($arMenuTypes['left']))
	$arMenuTypes['left'] = Loc::getMessage('WIZ_MENU_LIGHT_TOP');
if(!isset($arMenuTypes['bottom']))
	$arMenuTypes['bottom'] = Loc::getMessage('WIZ_MENU_BOTTOM');


SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set("fileman", "num_menu_param", 2, WIZARD_SITE_ID);
?>