<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>

<div class="infospice-search-module" >

    <?
    if ( $arParams["HIDE_SEARCH_BLOCK"] != "Y" )
    {
        ?>
        <div class="infospice-search-header" >
            <h1 ><?= GetMessage( "INFOSPICE_SEARCH_POISK_PO_SAYTU" ) ?></h1 >

            <form action="" method="get" class="infospice-search-form" >
                <fieldset >
                    <div class="infospice-search-form-keyword" >
                        <input type="text" name="q" value="<?= $arResult["REQUEST"]["QUERY"] ?>" autocomplete="off" />

                        <div class="infospice-search-complete" style="display: none" >
                            <?
                            if ( count( $arResult["AJAX_ITEMS"] ) )
                            {
                                ?>
                                <div class="infospice-search-complete-holder" >
                                    <ul >
                                        <?
                                        foreach ( $arResult['AJAX_ITEMS'] as $key => $arItem )
                                        {
                                            ?>
                                            <li >
                                                <?
                                                if ( $arItem["IMAGE"]["SRC"] )
                                                {
                                                    ?>
                                                    <a href="<?= $arItem["DETAIL_PAGE_URL"]; ?>" >
                                                        <img class="infospice-search-complete-img"
                                                             src="<?= $arItem["IMAGE"]["SRC"] ?>" />
                                                    </a >
                                                    <?
                                                }
                                                ?>
                                                <div class="infospice-search-complete-info" >
                                                    <p >
                                                        <a href="<?= $arItem["DETAIL_PAGE_URL"]; ?>" ><?= $arItem["NAME"]; ?></a >
                                                    </p >
                                                    <?
                                                    if ( !empty($arItem["PRICE"]) )
                                                    {
                                                        ?>
                                                        <?
                                                        if ( $arItem["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
                                                             != $arItem["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"]
                                                        )
                                                        {
                                                            ?>
                                                            <div class="infospice-search-complete-old" >
                                                                <strong ><?= $arItem["PRICE"]["PRINT_VALUE"]; ?></strong >
                                                            </div >
                                                            <?
                                                        }
                                                        ?>
                                                        <div class="infospice-search-complete-new infospice-search-complete-red" >
                                                            <strong ><?= $arItem["PRICE"]["PRINT_DISCOUNT_VALUE"]; ?></strong >
                                                        </div >
                                                        <?
                                                    }
                                                    ?>
                                                </div >
                                            </li >
                                            <?
                                        }
                                        ?>
                                    </ul >
                                    <a class="infospice-search-complete-link" href="?q=<?= urlencode( $arResult["REQUEST"]["~QUERY"] ) ?>" >
                                        <?=GetMessage("INFOSPICE_SEARCH_ALL_RESULTS")?>
                                    </a >
                                </div >
                                <?
                            }
                            ?>
                        </div >
                    </div >
                    <input type="submit" value="<?= GetMessage( "SEARCH_GO" ) ?>" />
                    <input type="hidden" name="how"
                           value="<? echo $arResult["REQUEST"]["HOW"] == "d" ? "d" : "r" ?>" />
                </fieldset >
            </form >
        </div >
        <?
    }
    ?>

    <div class="infospice-search-main" >
        <?
        if ( $arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false )
        {
            // do something ...
        }
        elseif ( count( $arResult["SEARCH2"] ) < 1 )
        {
            ?>

            <div class="infospice-search-sidebar" >
                <h3 ><?= GetMessage( "INFOSPICE_SEARCH_REZULQTATY_POISKA_PO" ) ?></h3 >

                <p class="infospice-search-sidebar-empty" ><?= GetMessage( "INFOSPICE_SEARCH_NET_KATEGORIY" ) ?></p >
            </div >
            <div class="infospice-search-all-items" >
                <h2 class="infospice-search-content-title" ><?= GetMessage(
                        "INFOSPICE_SEARCH_PO_ZAPROSU"
                    ) ?><?= $arResult ["REQUEST"]["QUERY"]; ?><?= GetMessage( "INFOSPICE_SEARCH_NICEGO_NE" ) ?></h2 >

                <?

                if ( $arResult['FORM_EMPTY'] )
                {
                    $textNFound = $arParams["STRING_NO_FOUND"];
                    ?>
                    <p class="infospice-search-content-descr" ><?= $textNFound;?></p >
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:form.result.new" ,
                        "search_form" ,
                        Array(
                            "SEF_MODE"                     => "N" ,
                            "AJAX_MODE"                    => "Y" ,
                            "WEB_FORM_ID"                  => $arResult['FORM_EMPTY']['ID'] ,
                            "LIST_URL"                     => "" ,
                            "EDIT_URL"                     => "" ,
                            "SUCCESS_URL"                  => "" ,
                            "SUCCESS_TEXT"                 => GetMessage( "INFOSPICE_SEARCH_WE_CALL_LATER" ) ,
                            "CHAIN_ITEM_TEXT"              => "" ,
                            "CHAIN_ITEM_LINK"              => "" ,
                            "IGNORE_CUSTOM_TEMPLATE"       => "N" ,
                            "USE_EXTENDED_ERRORS"          => "N" ,
                            "CACHE_TYPE"                   => "A" ,
                            "CACHE_TIME"                   => "3600" ,
                            "CACHE_NOTES"                  => "" ,
                            "VARIABLE_ALIASES_WEB_FORM_ID" => "WEB_FORM_ID" ,
                            "VARIABLE_ALIASES_RESULT_ID"   => "RESULT_ID" ,
                            "SEF_FOLDER"                   => "" ,
                        ) ,
                        $component
                    );
                    ?>
                    <?
                }
                ?>
            </div >
            <?
        }
        else
        {
            $arSearch = array();
            $countElements = 0;

            $static = "";
            if ( count( $arResult["SEARCH_STATIC"] ) > 0 )
            {
                $static = "Y";
                $static_count = count( $arResult["SEARCH_STATIC"] );
            }

            if ( count( $arResult["MENU_SECTIONS"] ) )
            {

                $APPLICATION->IncludeComponent(
                    "infospice.search:infospice.iblock.list" ,
                    $templateName ,
                    array(
                        "NEW_VERSION"   => "Y" ,
                        "FILTER_NAME"   => $iblockArray ,
                        "COUNT_ELEMENT" => $countElements ,
                        "ARRAY_ELEMENT" => $arResult["MENU_SECTIONS"] ,
                        "STATIC"        => $static ,
                        "STATIC_COUNT"  => $static_count
                    ) ,
                    $component ,
                    array( "HIDE_ICONS" => "Y" )
                );

            }
            ?>
            <div class="infospice-search-all-items" >
                <?
                $iblockId = $_GET["iblock"];
                $blogId = $_GET["blog"];
                $forumId = $_GET["forum"];


                $sid = intval( $_GET["section"] );
                $ssid = $_GET["ssid"];


                if ( intval( $iblockId ) > 0 )
                {
                    $arSections = $arResult["SEARCH2"]["IBLOCK"][$iblockId]["SECTIONS"];
                    $iblockId = array( $iblockId );
                }
                elseif ( count( $arResult["SEARCH2"]["IBLOCK"] ) )
                {
                    $arSections = array();
                    $arBlocks = $arResult["SEARCH2"]["IBLOCK"];
                    foreach ( $arBlocks as $arBlock )
                    {
                        $arSections = $arBlock["SECTIONS"];
                        $iblockId[] = $arBlock["ID"];
                    }
                }

                if ( count( $iblockId ) )
                {
                    ?>
                    <h2 class="infospice-search-content-title" >
                        <?= GetMessage(
                            "INFOSPICE_SEARCH_REZULQTATY_POISKA_PO_STR"
                        ) ?><? echo $arResult["userQuery"]; ?><?= GetMessage(
                            "INFOSPICE_SEARCH_"
                        ) ?>
                    </h2 >
                    <?

                    foreach ( $iblockId as $keyIB => $ibID )
                    {

                        if ( count( $iblockId ) > 1 )
                        {
                            ?>
                            <h3 class="infospice-search-content-title" style="font-size:20px;" >
                                <?= $arResult["SEARCH2"]["IBLOCK"][$ibID]["NAME"]; ?>
                            </h3 >
                            <?
                        }

                        $GLOBALS['arrFilter'] = array(
                            "ID" => $arResult["SEARCH2"]["IBLOCK"][$ibID]["ELEMENTS"]
                        );
                        $APPLICATION->IncludeComponent(
                            "infospice.search:infospice.catalog.section" ,
                            $templateName ,
                            array(
                                "AJAX_MODE"                       => "N" ,
                                "IBLOCK_ID"                       => $ibID ,
                                "SECTION_ID"                      => $sid ,
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
                                "PAGE_ELEMENT_COUNT"              => $arParams["PAGE_ELEMENT_COUNT"] ,
                                "LINE_ELEMENT_COUNT"              => $arParams["LINE_ELEMENT_COUNT"] ,
                                "PROPERTY_CODE"                   => $arParams["LIST_PROPERTY_CODE"] ,
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
                            ) ,
                            $component ,
                            array( "HIDE_ICONS" => "Y" )
                        );

                        if ( $arParams["DISPLAY_RELATIV_PAGEN"] )
                        {
                            $APPLICATION->IncludeComponent(
                                "bitrix:system.pagenavigation" ,
                                $arParams["PAGER_TEMPLATE"] ,
                                array(
                                    "NAV_RESULT"  => $arResult["SEARCH2"]["IBLOCK"][$ibID]["NAV_RESULT"] ,
                                    "SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"] ,
                                    "TITLE"       => $arParams["PAGER_TITLE"] ,
                                    "SHOW_ALL"    => $arParams["PAGER_SHOW_ALL"] ,
                                )
                            );
                        }
                    }
                }
                elseif ( !count( $arResult["SEARCH2"]["BLOG"] ) && !count( $arResult["SEARCH2"]["FORUM"] )
                         && !count(
                        $arResult["SEARCH_STATIC"]
                    )
                         && !count( $arResult["SEARCH2"]["STATIC"] )
                )
                {
                    ?>
                    <h2 class="infospice-search-content-title" >
                        <?
                        echo(GetMessage( "SEARCH_NOTHING_TO_FOUND" ));
                        ?>
                    </h2 >
                    <?
                }
                if ( !$_REQUEST['iblock'] )
                {

                    if ( count( $arResult["SEARCH2"]["BLOG"] ) )
                    {
                        ?>
                        <h3 class="infospice-search-content-title" style="font-size:20px;" >
                            <?= GetMessage( "INFOSPICE_SEARCH_BLOGS" ) ?>
                        </h3 >
                        <?
                        $APPLICATION->IncludeComponent(
                            "infospice.search:infospice.blog.list" ,
                            $templateName ,
                            array(
                                "BLOG_POST" => $arResult["SEARCH2"]["BLOG"]
                            ) ,
                            $component ,
                            array( "HIDE_ICONS" => "Y" )
                        );
                    }
                    if ( count( $arResult["SEARCH2"]["FORUM"] ) )
                    {
                        ?>
                        <h3 class="infospice-search-content-title" style="font-size:20px;" >
                            <?= GetMessage( "INFOSPICE_SEARCH_FORUM" ) ?>
                        </h3 >
                        <?
                        $APPLICATION->IncludeComponent(
                            "infospice.search:infospice.forum.list" ,
                            $templateName ,
                            array(
                                "FORUM_TOPICS" => $arResult["SEARCH2"]["FORUM"]
                            ) ,
                            $component ,
                            array( "HIDE_ICONS" => "Y" )
                        );
                    }
                    if ( count( $arResult["SEARCH2"]["STATIC"] ) > 0 )
                    {
                        ?>
                        <h3 class="infospice-search-content-title" style="font-size:20px;" >
                            <?= GetMessage( "INFOSPICE_SEARCH_STATIC" ) ?>
                        </h3 >
                        <?
                        foreach ( $arResult["SEARCH2"]["STATIC"] as $item )
                        {
                            echo '<p><a href="' . $item["URL"] . '">' . $item["TITLE"] . '</a></p>';
                            echo '<p>' . $item["BODY_FORMATED"] . '</p>';
                        }

                        echo $arResult["NAV_STRING_STATIC"];
                    }
                }
                ?>
            </div >
            <?
        }
        ?>
    </div >
</div >
