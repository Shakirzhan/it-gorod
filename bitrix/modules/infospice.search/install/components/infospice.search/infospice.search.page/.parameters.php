<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true )
{
    die();
}

if ( !CModule::IncludeModule( "search" ) )
{
    return;
}

$arProperty = array();
$arProperty_N = array();

if ( CModule::IncludeModule( 'iblock' ) )
{
    $arOrder = array(
        "sort" => "asc" ,
        "name" => "asc"
    );
    $arFilter = array( "ACTIVE" => "Y" );

    // Сейчас $arCurrentValues[ "IBLOCK_ID" ] нигде не устанавливается, возможно это вообще здесь не нужно
    if ( !empty($arCurrentValues["IBLOCK_ID"]) )
    {
        $arFilter["IBLOCK_ID"] = $arCurrentValues["IBLOCK_ID"];
    }

    $rsProp = CIBlockProperty::GetList(
        $arOrder ,
        $arFilter
    );
    while ( $arr = $rsProp->Fetch() )
    {
        if ( $arr["PROPERTY_TYPE"] != "F" )
        {
            $arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
        }

        if ( $arr["PROPERTY_TYPE"] == "N" )
        {
            $arProperty_N[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
        }
    }
}
$arProperty_LNS = $arProperty;


$arPrice = array();
if ( CModule::IncludeModule( "catalog" ) )
{
    $rsPrice = CCatalogGroup::GetList(
        $v1 = "sort" ,
        $v2 = "asc"
    );
    while ( $arr = $rsPrice->Fetch() ) $arPrice[$arr["NAME"]] = "[" . $arr["NAME"] . "] " . $arr["NAME_LANG"];
}
else
{
    $arPrice = $arProperty_N;
}

$arProperty_Offers = array();

$rsProp = CIBlockProperty::GetList(
    Array(
        "sort" => "asc" ,
        "name" => "asc"
    ) ,
    Array( "ACTIVE" => "Y" )
);
while ( $arr = $rsProp->Fetch() )
{
    if ( $arr["PROPERTY_TYPE"] != "F" )
    {
        $arProperty_Offers[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    }
};

$arSortIblock = CIBlockParameters::GetElementSortFields(
    array( 'SHOWS' , 'SORT' , 'TIMESTAMP_X' , 'NAME' , 'ID' , 'ACTIVE_FROM' , 'ACTIVE_TO' ) ,
    array( 'KEY_LOWERCASE' => 'Y' )
);
$arSort = array(
    "rank" => GetMessage( "CP_SP_DEFAULT_SORT_RANK" )
);
$arSort = array_merge( $arSort , $arSortIblock );
if ( CModule::IncludeModule( "catalog" ) )
{
    $arSort = array_merge( $arSort , CCatalogIBlockParameters::GetCatalogSortFields() );
}

$arAscDesc = array(
    "asc"  => GetMessage( "IBLOCK_SORT_ASC" ) ,
    "desc" => GetMessage( "IBLOCK_SORT_DESC" ) ,
);


$arComponentParameters = array(
    "GROUPS"     => array(
        "PAGER_SETTINGS" => array(
            "NAME" => GetMessage( "GROUP_NAME_SEARCH_PAGER" ) ,
        ) ,
        "GROUP_PRICES"   => array(
            "NAME" => GetMessage( "GROUP_NAME_PRICES" ) ,
        ) ,
        "GROUP_BASKET"   => array(
            "NAME" => GetMessage( "GROUP_NAME_BASKET" ) ,
        ) ,
    ) ,
    "PARAMETERS" => array(

        "RESTART"                       => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_RESTART" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "NO_WORD_LOGIC"                 => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "CP_BSP_NO_WORD_LOGIC" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "USE_LANGUAGE_GUESS"            => Array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "CP_BSP_USE_LANGUAGE_GUESS" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "Y" ,
            "SORT"    => 500
        ) ,
        "CHECK_DATES"                   => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_CHECK_DATES" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "USE_TITLE_RANK"                => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_USE_TITLE_RANK" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "ELEMENT_SORT_FIELD"            => array(
            "PARENT"            => "BASE" ,
            "NAME"              => GetMessage( "IBLOCK_ELEMENT_SORT_FIELD" ) ,
            "TYPE"              => "LIST" ,
            "VALUES"            => $arSort ,
            "ADDITIONAL_VALUES" => "Y" ,
            "DEFAULT"           => "rank" ,
        ) ,
        "ELEMENT_SORT_ORDER"            => array(
            "PARENT"            => "BASE" ,
            "NAME"              => GetMessage( "IBLOCK_ELEMENT_SORT_ORDER" ) ,
            "TYPE"              => "LIST" ,
            "VALUES"            => $arAscDesc ,
            "DEFAULT"           => "asc" ,
            "ADDITIONAL_VALUES" => "Y" ,
        ) ,
        "ELEMENT_SORT_FIELD2"           => array(
            "PARENT"            => "BASE" ,
            "NAME"              => GetMessage( "IBLOCK_ELEMENT_SORT_FIELD2" ) ,
            "TYPE"              => "LIST" ,
            "VALUES"            => $arSort ,
            "ADDITIONAL_VALUES" => "Y" ,
            "DEFAULT"           => "id" ,
        ) ,
        "ELEMENT_SORT_ORDER2"           => array(
            "PARENT"            => "BASE" ,
            "NAME"              => GetMessage( "IBLOCK_ELEMENT_SORT_ORDER2" ) ,
            "TYPE"              => "LIST" ,
            "VALUES"            => $arAscDesc ,
            "DEFAULT"           => "desc" ,
            "ADDITIONAL_VALUES" => "Y" ,
        ) ,
        "FILTER_NAME"                   => array(
            "PARENT" => "BASE" ,
            "NAME"   => GetMessage( "CP_BSP_FILTER_NAME" ) ,
            "TYPE"   => "STRING" ,
            "SORT"   => 500
        ) ,
        "USE_JQUERY"                    => array(
            "PARENT" => "BASE" ,
            "NAME"   => GetMessage( "CP_BSP_USE_JQUERY" ) ,
            "TYPE"   => "CHECKBOX" ,
            "SORT"   => 500
        ) ,
        "LIST_PROPERTY_CODE"            => array(
            "PARENT"            => "VISUAL" ,
            "NAME"              => GetMessage( "LIST_SETTINGS_IBLOCK_PROPERTY" ) ,
            "TYPE"              => "LIST" ,
            "MULTIPLE"          => "Y" ,
            "VALUES"            => $arProperty_LNS ,
            "ADDITIONAL_VALUES" => "Y" ,
            "SORT"              => 500
        ) ,
        "HIDE_ELEMENTS_WITHOUT_PICTURE" => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SHOW_ELEMENTS_WITHOUT_PICTURE" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "ADD_TO_BASKET_BUTTON"          => array(
            "PARENT"  => "GROUP_BASKET" ,
            "NAME"    => GetMessage( "ADD_TO_BASKET_BUTTON" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "ADD_TO_BASKET_BUTTON_TITLE"    => array(
            "PARENT"  => "GROUP_BASKET" ,
            "NAME"    => GetMessage( "ADD_TO_BASKET_BUTTON_TITLE" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => GetMessage( "ADD_TO_BASKET_BUTTON_TITLE_DEFAULT" ) ,
            "SORT"    => 500
        ) ,
        "ALREADY_ON_BASKET_TITLE"       => array(
            "PARENT"  => "GROUP_BASKET" ,
            "NAME"    => GetMessage( "ALREADY_ON_BASKET_TITLE" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => GetMessage( "ALREADY_ON_BASKET_TITLE_DEFAULT" ) ,
            "SORT"    => 500
        ) ,
        "CHECK_ITEMS_ON_BASKET"         => array(
            "PARENT"  => "GROUP_BASKET" ,
            "NAME"    => GetMessage( "CHECK_ITEMS_ON_BASKET" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
            "SORT"    => 500
        ) ,
        "CACHE_TIME"                    => Array( "DEFAULT" => 3600 ) ,
        "PAGER_TITLE"                   => array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_TITLE" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => "" ,
        ) ,
        "DISPLAY_TOP_PAGER"             => Array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_DISPLAY_TOP_PAGER" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "Y" ,
        ) ,
        "DISPLAY_BOTTOM_PAGER"          => Array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_DISPLAY_BOTTOM_PAGER" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "Y" ,
        ) ,
        "PAGER_SHOW_ALWAYS"             => array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_SHOW_ALWAYS" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "Y" ,
        ) ,
        "PAGER_TEMPLATE"                => array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_TEMPLATE" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => "infospice.search.pagenav.new" ,
        ) ,
        "PAGE_ELEMENT_COUNT"            => array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_RESULT_COUNT" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => "9" ,
        ) ,
        "LINE_ELEMENT_COUNT"            => array(
            "PARENT"  => "PAGER_SETTINGS" ,
            "NAME"    => GetMessage( "SEARCH_PAGER_ELEMENT_IN_LINE_COUNT" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => "3" ,
        ) ,
        "PRICE_CODE"                    => array(
            "PARENT"   => "GROUP_PRICES" ,
            "NAME"     => GetMessage( "PRICE_CODE" ) ,
            "TYPE"     => "LIST" ,
            "MULTIPLE" => "Y" ,
            "VALUES"   => $arPrice ,
        ) ,
        "HIDE_ELEMENTS_WITHOUT_PRICE"   => array(
            "PARENT"  => "GROUP_PRICES" ,
            "NAME"    => GetMessage( "PRICE_SHOW_ELEMENTS_WITHOUT_PRICE" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
        ) ,
        "HIDE_ELEMENTS_IF_QUANTITY_0"   => array(
            "PARENT"  => "GROUP_PRICES" ,
            "NAME"    => GetMessage( "PRICE_SHOW_ELEMENTS_WITHOUT_QUANT" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
        ) ,
        "OFFERS_PROPERTY_CODE"          => array(
            "PARENT"            => "VISUAL" ,
            "NAME"              => GetMessage( "CP_BCS_OFFERS_PROPERTY_CODE" ) ,
            "TYPE"              => "LIST" ,
            "MULTIPLE"          => "Y" ,
            "VALUES"            => $arProperty_Offers ,
            "ADDITIONAL_VALUES" => "Y" ,
        ) ,
        "HIDE_SEARCH_BLOCK"   => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_HIDE_SEARCH_BLOCK" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
        ) ,
        "SHOW_FORM_SEARCH_PRODUCT"   => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_SHOW_FORM_SEARCH_PRODUCT" ) ,
            "TYPE"    => "CHECKBOX" ,
            "DEFAULT" => "N" ,
        ) ,
        "AJAX_COUNT_PRODUCT"   => array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_AJAX_COUNT_PRODUCT" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => "5" ,
        ) ,
        "STRING_NO_FOUND"=> array(
            "PARENT"  => "BASE" ,
            "NAME"    => GetMessage( "SEARCH_STRING_NO_FOUND" ) ,
            "TYPE"    => "STRING" ,
            "DEFAULT" => GetMessage( "SEARCH_STRING_NO_FOUND_DEFAULT" )  ,
        ) ,
    ) ,
);


if ( CModule::IncludeModule( 'currency' ) )
{
    $arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
        'PARENT'  => 'GROUP_PRICES' ,
        'NAME'    => GetMessage( 'PRICE_CONVERT_CURRENCY' ) ,
        'TYPE'    => 'CHECKBOX' ,
        'DEFAULT' => 'N' ,
        'REFRESH' => 'Y' ,
    );

    if ( isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'] )
    {
        $arCurrencyList = array();
        $by = 'SORT';
        $order = 'ASC';
        $rsCurrencies = CCurrency::GetList(
            $by ,
            $order
        );
        while ( $arCurrency = $rsCurrencies->Fetch() )
        {
            $arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
        }
        $arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
            'PARENT'            => 'GROUP_PRICES' ,
            'NAME'              => GetMessage( 'PRICE_CURRENCY_ID' ) ,
            'TYPE'              => 'LIST' ,
            'VALUES'            => $arCurrencyList ,
            'DEFAULT'           => CCurrency::GetBaseCurrency() ,
            "ADDITIONAL_VALUES" => "Y" ,
        );
    }
}

if ( $arCurrentValues["SHOW_WHERE"] == "N" )
{
    unset($arComponentParameters["PARAMETERS"]["arrWHERE"]);
}

CSearchParameters::AddFilterParams(
    $arComponentParameters ,
    $arCurrentValues ,
    "arrFILTER" ,
    "DATA_SOURCE"
);
?>
