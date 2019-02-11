<?
$RIGHT = $APPLICATION->GetGroupRight('ambersite.independentmetatags');
if ($RIGHT >= "R") :
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"sort" => 1,
	"text" => GetMessage("NEZAVISIMUE_META_TEGI"),
	"icon" => "ambersite_independentmetatags_menu_icon",
	"page_icon" => "ambersite_independentmetatags_page_icon",
	"items_id" => "ambersite_independentmetatags",
	"items" => array(
		array(
			"text" => GetMessage("ZAPISI"),
			"url" => "/bitrix/admin/ambersite_independentmetatags_list.php",
			"more_url" => array('/bitrix/admin/ambersite_independentmetatags_edit.php')
		),
		array(
			"text" => GetMessage("NASTROJKI"),
			"url" => "/bitrix/admin/ambersite_independentmetatags_settings.php",
			"more_url" => array('/bitrix/admin/ambersite_independentmetatags_settings.php')
		)
	),
);
return $aMenu;

endif;
?> 
