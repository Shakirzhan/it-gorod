<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

$root = $_SERVER["DOCUMENT_ROOT"];
$path = $root."/bitrix/templates/.default/components/infospice.search/infospice.catalog.section/";
if ($catalogSection = opendir($path)) {
    while (false !== ($file = readdir($catalogSection))) {
        if(!is_dir($file)){
            $templatesCatalogSection[$file] = $file;
        }
    }
    closedir($catalogSection);
}

$pathIblockList = $root."/bitrix/templates/.default/components/infospice.search/infospice.iblock.list/";
if ($iblockList = opendir($pathIblockList)) {
    while (false !== ($file = readdir($iblockList))) {
        if(!is_dir($file)){
            $templatesIblockList[$file] = $file;
        }
    }
    closedir($iblockList);
}

$arTemplateParameters["TEMPLATE_SECTION"] = array(
    "PARENT"   => "VISUAL" ,
    "NAME"     => GetMessage( "TEMPLATE_SECTION" ) ,
    "TYPE"     => "LIST" ,
    "SORT"     => "1000" ,
    "MULTIPLE" => "N" ,
    "DEFAULT"  => ".default" ,
    "VALUES"   => $templatesIblockList ,
);

$arTemplateParameters["TEMPLATE_CATALOG"] = array(
    "PARENT"   => "VISUAL" ,
    "NAME"     => GetMessage( "TEMPLATE_CATALOG" ) ,
    "TYPE"     => "LIST" ,
    "SORT"     => "2000" ,
    "MULTIPLE" => "N" ,
    "DEFAULT"  => "catalog" ,
    "VALUES"   => $templatesCatalogSection ,
);
$arTemplateParameters["TEMPLATE_IBLOCK"]  = array(
    "PARENT"   => "VISUAL" ,
    "NAME"     => GetMessage( "TEMPLATE_IBLOCK" ) ,
    "TYPE"     => "LIST" ,
    "SORT"     => "3000" ,
    "MULTIPLE" => "N" ,
    "DEFAULT"  => "other" ,
    "VALUES"   => $templatesCatalogSection ,
);
$arTemplateParameters["ADD_TO_BASKET_BUTTON_COLOR"]    = array(
    "PARENT"            => "GROUP_BASKET" ,
    "NAME"              => GetMessage( "ADD_TO_BASKET_BUTTON_COLOR" ) ,
    "TYPE"              => "LIST" ,
    "MULTIPLE"          => "N" ,
    "ADDITIONAL_VALUES" => "N" ,
    "VALUES"            => array(
        "red"   => GetMessage( "INFOSPICE_SEARCH_KRASNAA" ) ,
        "green" => GetMessage( "INFOSPICE_SEARCH_ZELENAA" ) ,
        "blue"  => GetMessage( "INFOSPICE_SEARCH_SINAA" ) ,
    ) ,
    "DEFAULT"           => "red" ,
    "SORT"              => 500
);

?>
