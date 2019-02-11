<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>
<div id="search-module" >
<form action="" method="get" class="search-form" >
    <fieldset >
        <input type="text" class="text" name="q" value="<?= $arResult["REQUEST"]["QUERY"] ?>" />
        <input type="submit" class="submit" value="<?= GetMessage( "SEARCH_GO" ) ?>" />
        <input
            type="hidden" name="how"
            value="<? echo $arResult["REQUEST"]["HOW"] == "d" ? "d" : "r" ?>" />
    </fieldset >
</form >

<? if ( $arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false ): ?>
    <? // do something ... ?>

<? elseif ( count( $arResult["SEARCH2"] ) < 1 ): ?>
    <? ShowNote( GetMessage( "SEARCH_NOTHING_TO_FOUND" ) ); ?>

<?
else: ?>

    <?
    if ( count( $arResult["SEARCH2"]["IBLOCK"] ) )
    {
        foreach ( $arResult["SEARCH2"]["IBLOCK"] as $k => $v )
        {
            $iblockArray[]    = $k;
            $countElement[$k] = count( $v["ELEMENTS"] );
        }
    };
    $arr_sort = $arResult['ELEMENTS_ID'];

    $static = "";
    if ( count( $arResult["SEARCH_STATIC"] ) > 0 )
    {
        $static       = "Y";
        $static_count = count( $arResult["SEARCH_STATIC"] );
    }

    # список инфоблоков
    $APPLICATION->IncludeComponent(
        "infospice.search:infospice.iblock.list" ,
        $arParams["TEMPLATE_SECTION"] ,
        array(
            "FILTER_NAME"   => $iblockArray ,
            "COUNT_ELEMENT" => $countElement ,
            "ARRAY_ELEMENT" => $arr_sort ,
            "STATIC"        => $static ,
            "STATIC_COUNT"  => $static_count
        ) ,
        $component ,
        array( "HIDE_ICONS" => "Y" )
    );

    ?>


    <? if ( $_GET["page"] == "static" ): ?>

        <? if ( count( $arResult["SEARCH_STATIC"] ) > 0 ): ?>
            <? foreach ( $arResult["SEARCH_STATIC"] as $item ): ?>
                <p >
                    <a href="<? echo $item["URL"]; ?>" ><? echo $item["TITLE"]; ?></a >
                </p >
                <p ><? echo $item["BODY_FORMATED"]; ?></p >
            <? endforeach; ?>
            <? echo $arResult["NAV_STRING_STATIC"] ?>
        <? endif; ?>

    <? else: ?>
        <?


        if ( count( $arr_sort ) )
        {
            foreach ( $arr_sort as $catalog_id => $arItemss )
            {

                # список элементов
                if ( $_GET["iblock"] )
                {
                    $get_iblock = intval( $_GET["iblock"] );
                    $get_sect = intval( $_GET["section"] );
                    if ( $get_iblock != $catalog_id )
                    {
                        continue;
                    }
                }
                else
                {
                    $get_iblock           = $catalog_id;
                }

                $GLOBALS['arrFilter'] = array(
                    "ID" => $arResult["SEARCH2"]["IBLOCK"][$catalog_id]["ELEMENTS"]
                );
                $template = "catalog";
                # установка шаблонов
                if ( !empty($get_iblock) )
                {
                    if ( CModule::IncludeModule( "catalog" ) )
                    {
                        # определяем - каталог или обычный инфоблок
                        $checkCatalog = CCatalog::GetByID( $get_iblock );
                        if ( is_array( $checkCatalog ) )
                        {
                            $template = $arParams["TEMPLATE_CATALOG"];
                        }
                        else
                        {
                            # Опять же - проверяем является ли ИБ торговым каталогом
                            $res = CCatalog::GetList(
                                array() ,
                                array( "PRODUCT_IBLOCK_ID" => $get_iblock )
                            );
                            if ( $arIBlock = $res->Fetch() )
                            {
                                $template = $arParams["TEMPLATE_CATALOG"];
                            }
                            else
                            {
                                $template = $arParams["TEMPLATE_IBLOCK"];
                            }
                        }
                    }
                    else
                    {
                        $template = $arParams["TEMPLATE_IBLOCK"];
                    }
                }


                $APPLICATION->IncludeComponent(
                    "infospice.search:infospice.catalog.section" ,
                    $template ,
                    array(
                        "AJAX_MODE"                       => "N" ,
                        "IBLOCK_ID"                       => $get_iblock ,
                        "SECTION_ID"                      => $_REQUEST["SECTION_ID"] ,
                        "SECTION_CODE"                    => "" ,
                        "SECTION_USER_FIELDS"             => "" ,
                        "ELEMENT_SORT_FIELD"              => $arParams["ELEMENT_SORT_FIELD"] ,
                        "ELEMENT_SORT_ORDER"              => $arParams["ELEMENT_SORT_ORDER"] ,
                        "ELEMENT_SORT_FIELD2"             => $arParams["ELEMENT_SORT_FIELD2"] ,
                        "ELEMENT_SORT_ORDER2"             => $arParams["ELEMENT_SORT_ORDER2"] ,
                        "FILTER_NAME"                     => "arrFilter" ,
                        "INCLUDE_SUBSECTIONS"             => "Y" ,
                        "SHOW_ALL_WO_SECTION"             => "Y" ,
                        "SECTION_URL"                     => "" ,
                        "DETAIL_URL"                      => "" ,
                        "BASKET_URL"                      => "/personal/cart/" ,
                        "ACTION_VARIABLE"                 => "action" ,
                        "PRODUCT_ID_VARIABLE"             => "id" ,
                        "PRODUCT_QUANTITY_VARIABLE"       => "quantity" ,
                        "PRODUCT_PROPS_VARIABLE"          => "prop" ,
                        "SECTION_ID_VARIABLE"             => "SECTION_ID" ,
                        "META_KEYWORDS"                   => "-" ,
                        "META_DESCRIPTION"                => "-" ,
                        "BROWSER_TITLE"                   => "-" ,
                        "ADD_SECTIONS_CHAIN"              => "N" ,
                        "DISPLAY_COMPARE"                 => "N" ,
                        "SET_TITLE"                       => "N" ,
                        "SET_STATUS_404"                  => "N" ,
                        "PAGE_TITLE"                      => $arParams["PAGE_TITLE"] ,
                        "PAGE_ELEMENT_COUNT"              => $arParams["PAGE_ELEMENT_COUNT"] ,
                        "LINE_ELEMENT_COUNT"              => $arParams["LINE_ELEMENT_COUNT"] ,
                        "PROPERTY_CODE"                   => $arParams["LIST_PROPERTY_CODE"] ,
                        "LIST_PROPERTY_CODE"              => $arParams["LIST_PROPERTY_CODE"] ,
                        "ADD_TO_BASKET_BUTTON"            => $arParams["ADD_TO_BASKET_BUTTON"] ,
                        "ADD_TO_BASKET_BUTTON_TITLE"      => $arParams["ADD_TO_BASKET_BUTTON_TITLE"] ,
                        "ADD_TO_BASKET_BUTTON_COLOR"      => $arParams["ADD_TO_BASKET_BUTTON_COLOR"] ,
                        "CHECK_ITEMS_ON_BASKET"           => $arParams["CHECK_ITEMS_ON_BASKET"] ,
                        "ALREADY_ON_BASKET_TITLE"         => $arParams["ALREADY_ON_BASKET_TITLE"] ,
                        "PRICE_CODE"                      => $arParams["PRICE_CODE"] ,
                        "CONVERT_CURRENCY"                => $arParams["CONVERT_CURRENCY"],
                        "CURRENCY_ID"                     => $arParams["CURRENCY_ID"] ,
                        "OFFERS_FIELD_CODE"               => array(
                            0 => "NAME" ,
                            1 => "PREVIEW_PICTURE" ,
                            2 => "DETAIL_PICTURE" ,
                            3 => "" ,
                        ) ,
                        "OFFERS_PROPERTY_CODE"            => $arParams["OFFERS_PROPERTY_CODE"] ,
                        "LIST_OFFERS_LIMIT"               => "10" ,
                        "OFFERS_SORT_FIELD"               => "sort" ,
                        "OFFERS_SORT_ORDER"               => "asc" ,
                        "OFFERS_SORT_FIELD2"              => "id" ,
                        "OFFERS_SORT_ORDER2"              => "desc" ,
                        "USE_PRICE_COUNT"                 => "N" ,
                        "SHOW_PRICE_COUNT"                => "1" ,
                        "PRICE_VAT_INCLUDE"               => "Y" ,
                        "PRODUCT_PROPERTIES"              => "" ,
                        "USE_PRODUCT_QUANTITY"            => "N" ,
                        "CACHE_TYPE"                      => "A" ,
                        "CACHE_TIME"                      => "36000000" ,
                        "CACHE_NOTES"                     => "" ,
                        "CACHE_FILTER"                    => "N" ,
                        "CACHE_GROUPS"                    => "N" ,
                        "DISPLAY_TOP_PAGER"               => $arParams["DISPLAY_TOP_PAGER"] ,
                        "DISPLAY_BOTTOM_PAGER"            => $arParams["DISPLAY_BOTTOM_PAGER"] ,
                        "PAGER_TITLE"                     => $arParams["PAGER_TITLE"] ,
                        "PAGER_SHOW_ALWAYS"               => $arParams["PAGER_SHOW_ALWAYS"] ? "Y" : "N" ,
                        "PAGER_SHOW_ALL"                  => $arParams["PAGER_SHOW_ALL"] ,
                        "PAGER_TEMPLATE"                  => $arParams["PAGER_TEMPLATE"] ,
                        "PAGER_DESC_NUMBERING"            => "N" ,
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000" ,
                        "AJAX_OPTION_JUMP"                => "N" ,
                        "AJAX_OPTION_STYLE"               => "Y" ,
                        "AJAX_OPTION_HISTORY"             => "N" ,
                        "AJAX_OPTION_ADDITIONAL"          => ""
                    ),
                    $component ,
                    array( "HIDE_ICONS" => "Y" )
                );


                if($arParams["DISPLAY_RELATIV_PAGEN"])
                {
                    $APPLICATION->IncludeComponent(
                        "bitrix:system.pagenavigation" ,
                        $arParams["PAGER_TEMPLATE"] ,
                        array(
                            "NAV_RESULT"  => $arResult["SEARCH2"]["IBLOCK"][$catalog_id]["NAV_RESULT"] ,
                            "SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"] ,
                            "TITLE"       => $arParams["PAGER_TITLE"] ,
                            "SHOW_ALL"    => $arParams["PAGER_SHOW_ALL"] ,
                        )
                    );
                }
            }

        }

        //echo "<pre>"; print_r($arParams); echo "</pre>";


        ?>

    <?endif; ?>

<?endif; ?>

</div >
