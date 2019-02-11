<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

require $_SERVER["DOCUMENT_ROOT"]
        . '/bitrix/components/infospice.search/infospice.search.page/include/function.php';

if ( !CModule::IncludeModule( "search" ) )
{
    ShowError( GetMessage( "SEARCH_MODULE_UNAVAILABLE" ) );
    return;
}
CPageOption::SetOptionString(
    "main" ,
    "nav_page_in_session" ,
    "N"
);


if ( !isset($arParams["CACHE_TIME"]) )
{
    $arParams["CACHE_TIME"] = 3600;
}

// activation rating
//CRatingsComponentsMain::GetShowRating(&$arParams);

$arParams["SHOW_WHEN"] = $arParams["SHOW_WHEN"] == "Y";
$arParams["SHOW_WHERE"] = $arParams["SHOW_WHERE"] != "N";
if ( !is_array( $arParams["arrWHERE"] ) )
{
    $arParams["arrWHERE"] = array();
}
$arParams["PAGE_RESULT_COUNT"] = intval( $arParams["PAGE_RESULT_COUNT"] );
if ( $arParams["PAGE_RESULT_COUNT"] <= 0 )
{
    $arParams["PAGE_RESULT_COUNT"] = 50;
}
$maxCountOnce = 500000;


$arParams["PAGER_TITLE"] = trim( $arParams["PAGER_TITLE"] );
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] != "N";
$arParams["USE_TITLE_RANK"] = $arParams["USE_TITLE_RANK"] == "Y";
$arParams["PAGER_TEMPLATE"] = trim( $arParams["PAGER_TEMPLATE"] );

$arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] = $arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] == "Y";
$arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] = $arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] == "Y";
$PRICE_ID = -1;

if ( $arParams["DEFAULT_SORT"] !== "date" )
{
    $arParams["DEFAULT_SORT"] = "rank";
}

if ( strlen( $arParams["FILTER_NAME"] ) <= 0
     || !preg_match(
        "/^[A-Za-z_][A-Za-z01-9_]*$/" ,
        $arParams["FILTER_NAME"]
    )
)
{
    $arFILTERCustom = array();
}
else
{
    $arFILTERCustom = $GLOBALS[$arParams["FILTER_NAME"]];
    if ( !is_array( $arFILTERCustom ) )
    {
        $arFILTERCustom = array();
    }
}

$exFILTER = CSearchParameters::ConvertParamsToFilter(
    $arParams ,
    "arrFILTER"
);

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"] == "Y";

//options
if ( isset($_REQUEST["tags"]) )
{
    $tags = trim( $_REQUEST["tags"] );
}
else
{
    $tags = false;
}
if ( isset($_REQUEST["q"]) )
{
    $q = trim( $_REQUEST["q"] );
}
else
{
    $q = false;
}

if ( $arParams["SHOW_WHEN"] && isset($_REQUEST["from"]) && is_string( $_REQUEST["from"] )
     && strlen(
        $_REQUEST["from"]
    )
     && CheckDateTime( $_REQUEST["from"] )
)
{
    $from = $_REQUEST["from"];
}
else
{
    $from = "";
}

if ( $arParams["SHOW_WHEN"] && isset($_REQUEST["to"]) && is_string( $_REQUEST["to"] )
     && strlen(
        $_REQUEST["to"]
    )
     && CheckDateTime( $_REQUEST["to"] )
)
{
    $to = $_REQUEST["to"];
}
else
{
    $to = "";
}

$where = $arParams["SHOW_WHERE"] ? trim( $_REQUEST["where"] ) : "";

$how = trim( $_REQUEST["how"] );
if ( $how == "d" )
{
    $how = "d";
}
elseif ( $how == "r" )
{
    $how = "";
}
elseif ( $arParams["DEFAULT_SORT"] == "date" )
{
    $how = "d";
}
else
{
    $how = "";
}

$aSort = array(
    "CUSTOM_RANK" => "DESC" ,
    "TITLE_RANK"  => "DESC" ,
    "RANK"        => "DESC" ,
    "DATE_CHANGE" => "DESC"
);

/*************************************************************************
 * Operations with cache
 *************************************************************************/
$arrDropdown = array();

$obCache = new CPHPCache;

if ( $arParams["CACHE_TYPE"] == "N"
     || ($arParams["CACHE_TYPE"] == "A"
         && COption::GetOptionString(
            "main" ,
            "component_cache_on" ,
            "Y"
        ) == "N")
)
{
    $arParams["CACHE_TIME"] = 0;
}

if ( $obCache->StartDataCache(
    $arParams["CACHE_TIME"] ,
    $this->GetCacheID() ,
    "/" . SITE_ID . $this->GetRelativePath()
)
)
{
    // Getting of the Information block types
    $arIBlockTypes = array();
    if ( CModule::IncludeModule( "iblock" ) )
    {
        $rsIBlockType = CIBlockType::GetList(
            array( "sort" => "asc" ) ,
            array( "ACTIVE" => "Y" )
        );
        while ( $arIBlockType = $rsIBlockType->Fetch() )
        {
            if ( $ar = CIBlockType::GetByIDLang(
                $arIBlockType["ID"] ,
                LANGUAGE_ID
            )
            )
            {
                $arIBlockTypes[$arIBlockType["ID"]] = $ar["~NAME"];
            }
        }
    }

    // Creating of an array for drop-down list
    foreach ( $arParams["arrWHERE"] as $code )
    {
        list($module_id , $part_id) = explode(
            "_" ,
            $code ,
            2
        );
        if ( strlen( $module_id ) > 0 )
        {
            if ( strlen( $part_id ) <= 0 )
            {
                switch ( $module_id )
                {
                    case "forum":
                        $arrDropdown[$code] = GetMessage( "SEARCH_FORUM" );
                        break;
                    case "blog":
                        $arrDropdown[$code] = GetMessage( "SEARCH_BLOG" );
                        break;
                    case "socialnetwork":
                        $arrDropdown[$code] = GetMessage( "SEARCH_SOCIALNETWORK" );
                        break;
                    case "intranet":
                        $arrDropdown[$code] = GetMessage( "SEARCH_INTRANET" );
                        break;
                }
            }
            else
            {
                // if there is additional information specified besides ID then
                switch ( $module_id )
                {
                    case "iblock":
                        if ( isset($arIBlockTypes[$part_id]) )
                        {
                            $arrDropdown[$code] = $arIBlockTypes[$part_id];
                        }
                        break;
                }
            }
        }
    }
    $obCache->EndDataCache( $arrDropdown );
}
else
{
    $arrDropdown = $obCache->GetVars();
}

$arResult["DROPDOWN"] = htmlspecialcharsex( $arrDropdown );
$arResult["REQUEST"]["HOW"] = htmlspecialchars( $how );
$arResult["REQUEST"]["~FROM"] = $from;
$arResult["REQUEST"]["FROM"] = htmlspecialchars( $from );
$arResult["REQUEST"]["~TO"] = $to;
$arResult["REQUEST"]["TO"] = htmlspecialchars( $to );

if ( $q !== false )
{
    if ( $arParams["USE_LANGUAGE_GUESS"] == "N" || isset($_REQUEST["spell"]) )
    {
        $arResult["REQUEST"]["~QUERY"] = $q;
        $arResult["REQUEST"]["QUERY"] = htmlspecialcharsex( $q );
    }
    else
    {

        $arLang = CSearchLanguage::GuessLanguage( $q );

        if ( is_array( $arLang ) && $arLang["from"] != $arLang["to"] )
        {
            $arResult["REQUEST"]["~ORIGINAL_QUERY"] = $q;
            $arResult["REQUEST"]["ORIGINAL_QUERY"] = htmlspecialcharsex( $q );

            $arResult["REQUEST"]["~QUERY"] = CSearchLanguage::ConvertKeyboardLayout(
                $arResult["REQUEST"]["~ORIGINAL_QUERY"] ,
                $arLang["from"] ,
                $arLang["to"]
            );
            $arResult["REQUEST"]["QUERY"] = htmlspecialcharsex( $arResult["REQUEST"]["~QUERY"] );
        }
        else
        {
            $arResult["REQUEST"]["~QUERY"] = $q;
            $arResult["REQUEST"]["QUERY"] = htmlspecialcharsex( $q );
        }
    }

}
else
{
    $arResult["REQUEST"]["~QUERY"] = false;
    $arResult["REQUEST"]["QUERY"] = false;
}

if ( $tags !== false )
{
    $arResult["REQUEST"]["~TAGS_ARRAY"] = array();
    $arTags = explode(
        "," ,
        $tags
    );
    foreach ( $arTags as $tag )
    {
        $tag = trim( $tag );
        if ( strlen( $tag ) > 0 )
        {
            $arResult["REQUEST"]["~TAGS_ARRAY"][$tag] = $tag;
        }
    }
    $arResult["REQUEST"]["TAGS_ARRAY"] = htmlspecialcharsex( $arResult["REQUEST"]["~TAGS_ARRAY"] );
    $arResult["REQUEST"]["~TAGS"] = implode(
        "," ,
        $arResult["REQUEST"]["~TAGS_ARRAY"]
    );
    $arResult["REQUEST"]["TAGS"] = htmlspecialcharsex( $arResult["REQUEST"]["~TAGS"] );
}
else
{
    $arResult["REQUEST"]["~TAGS_ARRAY"] = array();
    $arResult["REQUEST"]["TAGS_ARRAY"] = array();
    $arResult["REQUEST"]["~TAGS"] = false;
    $arResult["REQUEST"]["TAGS"] = false;
}
$arResult["REQUEST"]["WHERE"] = htmlspecialchars( $where );

$arResult["URL"] = $APPLICATION->GetCurPage() . "?q=" . urlencode(
        $q
    ) . (isset($_REQUEST["spell"]) ? "&amp;spell=1" : "") . "&amp;where=" . urlencode(
                       $where
                   ) . ($tags !== false ? "&amp;tags=" . urlencode( $tags ) : "");

if ( isset($arResult["REQUEST"]["~ORIGINAL_QUERY"]) )
{
    $arResult["ORIGINAL_QUERY_URL"] = $APPLICATION->GetCurPage() . "?q=" . urlencode(
            $arResult["REQUEST"]["~ORIGINAL_QUERY"]
        ) . "&amp;spell=1" . "&amp;where=" . urlencode(
                                          $arResult["REQUEST"]["WHERE"]
                                      ) . ($arResult["REQUEST"]["HOW"] == "d" ? "&amp;how=d" : "")
                                      . ($arResult["REQUEST"]["FROM"] ? '&amp;from=' . urlencode(
                $arResult["REQUEST"]["~FROM"]
            ) : "") . ($arResult["REQUEST"]["TO"] ? '&amp;to=' . urlencode(
                $arResult["REQUEST"]["~TO"]
            ) : "") . ($tags !== false ? "&amp;tags=" . urlencode( $tags ) : "");
}

if ( $this->InitComponentTemplate( $templatePage ) )
{
    $template = &$this->GetTemplate();
    $arResult["FOLDER_PATH"] = $folderPath = $template->GetFolder();

    if ( strlen( $folderPath ) > 0 )
    {
        $arFilter = array(
            "SITE_ID" => SITE_ID ,
            "QUERY"   => $arResult["REQUEST"]["~QUERY"] ,
            "TAGS"    => $arResult["REQUEST"]["~TAGS"] ,
        );
        $arFilter = array_merge(
            $arFILTERCustom ,
            $arFilter
        );

        if ( strlen( $where ) > 0 )
        {
            list($module_id , $part_id) = explode(
                "_" ,
                $where ,
                2
            );
            $arFilter["MODULE_ID"] = $module_id;
            if ( strlen( $part_id ) > 0 ) $arFilter["PARAM1"] = $part_id;
        }
        if ( $from )
        {
            $arFilter[">=DATE_CHANGE"] = $from;
        }
        if ( $to )
        {
            $arFilter["<=DATE_CHANGE"] = $to;
        }
        $obSearch = new CSearch();

        //When restart option is set we will ignore error on query with only stop words
        $obSearch->SetOptions(
            array(
                "ERROR_ON_EMPTY_STEM" => $arParams["RESTART"] != "Y" ,
                "NO_WORD_LOGIC"       => $arParams["NO_WORD_LOGIC"] == "Y" ,
            )
        );

        $arResult["SEARCH2"] = array();

        $arFilterStatic = $arFilter;
        $arFilterStatic["MODULE_ID"] = "main";

        $obSearch->Search(
            $arFilterStatic ,
            $aSort ,
            $exFILTER
        );
        $obSearch->NavStart(
            $arParams["PAGE_IBLOCK_COUNT"] ,
            false
        );
        $ar = $obSearch->GetNext();
        while ( $ar )
        {
            $arResult["SEARCH2"]["STATIC"][] = $ar;
            $ar = $obSearch->GetNext();
        }

        $arResult["NAV_STRING_STATIC"] = $obSearch->GetPageNavStringEx(
            $navComponentObject ,
            $arParams["PAGER_TITLE"] ,
            $arParams["PAGER_TEMPLATE"] ,
            $arParams["PAGER_SHOW_ALWAYS"]
        );
        $pQua = ($arParams["HIDE_ELEMENTS_IF_QUANTITY_0"] == "Y");
        $pImg = ($arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] == "Y");
        $pPri = ($arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] == "Y");

        if ( $pQua || $pImg || $pPri )
        {
            $arLogicFilter = array(
                "LOGIC" => "OR" ,
                0       => array(
                    "=MODULE_ID" => "iblock" ,
                    "PARAMS"     => array(
                        "IS_CATALOG" => "Y"
                    )
                ) ,
                1       => array(
                    "=MODULE_ID" => "iblock" ,
                    "PARAMS"     => array(
                        "IS_CATALOG" => "N"
                    )
                ) ,
                2       => array(
                    "!=MODULE_ID" => "iblock"
                )
            );
            if ( $pQua && CModule::IncludeModule( "catalog" ) )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_QUANTITY"] = "Y";
            }
            if ( $pImg )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_IMAGE"] = "Y";
            }
            if ( $pPri && CModule::IncludeModule( "catalog" ) )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_PRICE"] = "Y";
            }

            $arFilter[] = $arLogicFilter;
        }
        $obSearch->Search(
            $arFilter ,
            $aSort ,
            $exFILTER
        );

        $arIblockCatalogs = array();
        $arResult["ERROR_CODE"] = $obSearch->errorno;
        $arResult["ERROR_TEXT"] = $obSearch->error;

        if ( $obSearch->errorno == 0 )
        {
            $obSearch->NavStart(
                $maxCountOnce ,
                false
            );
            $ar = $obSearch->Fetch();
            //Search restart
            if ( !$ar && ($arParams["RESTART"] == "Y") && $obSearch->Query->bStemming )
            {

                $exFILTER["STEMMING"] = false;
                $obSearch = new CSearch();
                $obSearch->Search(
                    $arFilter ,
                    $aSort ,
                    $exFILTER
                );

                $arResult["ERROR_CODE"] = $obSearch->errorno;
                $arResult["ERROR_TEXT"] = $obSearch->error;

                if ( $obSearch->errorno == 0 )
                {
                    $obSearch->NavStart(
                        $arParams["PAGE_RESULT_COUNT"] ,
                        false
                    );
                    $ar = $obSearch->Fetch();
                }
            }

            while ( $ar )
            {
                if ( strpos(
                         $ar["ITEM_ID"] ,
                         "S"
                     ) === 0
                )
                {
                    $ar["ITEM_ID"] = substr(
                        $ar["ITEM_ID"] ,
                        1
                    );
                    $ar['TYPE_ELEMENT'] = "section";
                }
                else
                {
                    $ar['TYPE_ELEMENT'] = "element";
                }
                $moduleId = $ar["MODULE_ID"];
                $itemId = $ar["ITEM_ID"];
                $categoriesId = $ar["PARAM2"];
                if ( $moduleId == "iblock" && CModule::IncludeModule( "iblock" ))
                {
                    if ( $ar["TYPE_ELEMENT"] == "section" )
                    {
                        $arResult["SEARCH2"]["STATIC"][] = $ar;
                    }
                    else
                    {
                        $arResult["ELEMENTS_ID"][$ar["PARAM2"]][] = $ar["ITEM_ID"];
                        $arSearch[$moduleId][$categoriesId]["ELEMENTS"][$itemId] = $itemId;
                    }
                }
                else
                {
                    $arSearch[$moduleId][$categoriesId][] = $itemId;
                }
                $ar = $obSearch->Fetch();
            }
        }
        $userQuery = (!empty($_GET["q"])) ? trim( $_GET["q"] ) : "";

        $arIBlockElements = $arSearch['iblock'];
        $arBlogElements = $arSearch['blog'];
        $arForumElements = $arSearch['forum'];

        if ( is_array( $arIBlockElements ) && CModule::IncludeModule( "catalog" ) )
        {
            $arIdIblocks = array_keys( $arIBlockElements );
            $arFilterIblock = array(
                "ID" => $arIdIblocks
            );

            $arAllIblocks = array();

            $obCache = new CPHPCache();
            if ( $obCache->InitCache(
                7200 ,
                serialize( $arIdIblocks ) ,
                "/search/iblock"
            )
            )
            {
                $arAllIblocks = $obCache->GetVars();
            }
            elseif ( $obCache->StartDataCache() )
            {
                $rsIblock = CIBlock::GetList(
                    array() ,
                    $arFilterIblock
                );
                $arAllIblocks = array_combine( $arIdIblocks , $arIdIblocks );
                while ( $arIblocks = $rsIblock->Fetch() )
                {
                    $arAllIblocks[$arIblocks["ID"]] = $arIblocks;
                }

                if ( defined( "BX_COMP_MANAGED_CACHE" ) )
                {
                    global $CACHE_MANAGER;
                    $CACHE_MANAGER->StartTagCache( "/search/iblock" );
                    $CACHE_MANAGER->RegisterTag( "iblock_all_id" );
                    $CACHE_MANAGER->EndTagCache();
                }
                $obCache->EndDataCache( $arAllIblocks );
            }
            $getIblockID = intval($_REQUEST["iblock"]);
            $getSectionID = intval($_REQUEST["section"]);

            foreach ( $arAllIblocks as $iblockId => $arIblock )
            {
                $arIdElements = $arIBlockElements[$iblockId];
                if ( is_array( $arIdElements["ELEMENTS"] ) )
                {
                    $arIdSections = getIblockSectionsFromSearch( $arIdElements["ELEMENTS"] );
                    $arSortElementBySections = sortIBlockElementsBySections(
                        $iblockId ,
                        $arIdElements["ELEMENTS"] ,
                        $userQuery ,
                        $arIdSections["SECTIONS"]
                    );
                    $arIBlock = $arAllIblocks[$iblockId];

                    if($getIblockID == $iblockId && $getSectionID > 0 && $arSortElementBySections[$getSectionID])
                    {
                        $arNewId = array();
                        $arInheritSections = $arSortElementBySections[$getSectionID]["SECTIONS"];
                        $arInheritSections[$arSortElementBySections[$getSectionID]["ID"]] = $arSortElementBySections[$getSectionID]["ID"];

                        foreach ( $arInheritSections as $keyS => $SECTION_ID )
                        {
                            if ( is_array($arIdSections["ELEMENTS"][$SECTION_ID] ) )
                            {
                                $arNewId = array_merge($arNewId , $arIdSections["ELEMENTS"][$SECTION_ID]);
                            }
                        }

                        $arIdElements["ELEMENTS"] = $arNewId;
                    }
                    $arResult["SEARCH2"]["IBLOCK"][$arIBlock["ID"]] = array(
                        "ID"       => $arIBlock["ID"] ,
                        "NAME"     => $arIBlock["NAME"] ,
                        "COUNT"    => count( $arIdElements["ELEMENTS"] ) ,
                        "URL"      => htmlspecialchars(
                            $APPLICATION->GetCurPage() . "?q=" . $userQuery . "&iblock=" . $arIBlock["ID"]
                        ) ,
                        "ELEMENTS" => $arIdElements["ELEMENTS"] ,
                        "SECTIONS" => $arSortElementBySections
                    );
                }
            }
            $relativ = ($arParams["ELEMENT_SORT_FIELD"] == "rank"
                        || $arParams["ELEMENT_SORT_FIELD2"] == "rank");

            if ( $relativ )
            {

                foreach ( $arResult["SEARCH2"]["IBLOCK"] as $keyIbl => &$arIblock )
                {
                    if ( !empty($arIblock["ELEMENTS"])
                         && $arParams["PAGE_ELEMENT_COUNT"] < count( $arIblock["ELEMENTS"] )
                    )
                    {
                        $arParams["DISPLAY_TOP_PAGER"] = "N";
                        $arParams["DISPLAY_BOTTOM_PAGER"] = "N";
                        $arParams["DISPLAY_RELATIV_PAGEN"] = "Y";

                        $dbResult = new CDBResult();

                        $arNavParams = array(
                            "nPageSize"          => $arParams["PAGE_ELEMENT_COUNT"] ,
                            "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"] ,
                            "bShowAll"           => $arParams["PAGER_SHOW_ALL"] ,
                        );
                        $arNavigation = CDBResult::GetNavParams( $arNavParams );
                        $arNavigation["PAGEN"] = "S_" . $keyIbl;

                        $pageCount = ceil( count( $arIblock["ELEMENTS"] ) / $arParams["PAGE_ELEMENT_COUNT"] );
                        $pageNumber = intval( $_REQUEST["PAGEN_" . $arNavigation["PAGEN"]] );
                        if ( $pageNumber < 1 )
                        {
                            $pageNumber = 1;
                        }

                        $dbResult->NavPageSize = $arParams["PAGE_ELEMENT_COUNT"];
                        $dbResult->NavRecordCount = count( $arIblock["ELEMENTS"] );
                        $dbResult->NavPageCount = $pageCount;
                        $dbResult->NavPageNomer = $pageNumber;
                        $dbResult->bShowAll = $arNavigation["SHOW_ALL"];
                        $dbResult->NavShowAll = $arNavigation["SHOW_ALL"];
                        $dbResult->NavNum = $arNavigation["PAGEN"];
                        $dbResult->bDescPageNumbering = "N";
                        $dbResult->add_anchor = false;
                        $dbResult->nPageWindow = $pageCount;

                        $arIblock["NAV_RESULT"] = $dbResult;

                        $arPagen = array_chunk( $arIblock["ELEMENTS"] , $arParams["PAGE_ELEMENT_COUNT"] );
                        $arIblock["ELEMENTS"] = $arPagen[$pageNumber - 1];
                    }
                }

            }

        }
        if ( is_array( $arBlogElements ) && CModule::IncludeModule( "blog" ) )
        {
            foreach ( $arBlogElements as $blogId => $arIdElements )
            {

                if ( is_array( $arIdElements ) )
                {
                    $arBlog = CBlog::GetByID( $blogId );
                    if ( is_array( $arBlog ) )
                    {
                        $arResult["SEARCH2"]["BLOG"][$arBlog["ID"]] = array(
                            "ID"       => $arBlog["ID"] ,
                            "NAME"     => $arBlog["NAME"] ,
                            "BLOG_URL" => $arBlog["URL"] ,
                            "COUNT"    => count( $arIdElements ) ,
                            "URL"      => htmlspecialchars(
                                $APPLICATION->GetCurPage() . "?q=" . $userQuery . "&blog=" . $arBlog["ID"]
                            ) ,
                            "POSTS"    => sortBlogPostsBySections( $arIdElements ) ,
                        );
                    }
                }
            }
        }


        if ( is_array( $arForumElements ) && CModule::IncludeModule( "forum" ) )
        {
            foreach ( $arForumElements as $forumId => $arIdElements )
            {
                if ( is_array( $arIdElements ) )
                {
                    $arForum = CForumNew::GetByID( $forumId );
                    if ( is_array( $arForum ) )
                    {
                        $arResult["SEARCH2"]["FORUM"][$arForum["ID"]] = array(
                            "ID"     => $arForum["ID"] ,
                            "NAME"   => $arForum["NAME"] ,
                            "MID"    => $arForum["MID"] ,
                            "TOPICS" => sortForumTopicsBySections( $arIdElements ) ,
                            "COUNT"  => count( $arIdElements ) ,
                            "URL"    => htmlspecialchars(
                                $APPLICATION->GetCurPage() . "?q=" . $userQuery . "&forum=" . $arForum["ID"]
                            ) ,
                        );
                    }
                }
            }
        }

        $arResult["TAGS_CHAIN"] = array();
        $url = array();
        foreach ( $arResult["REQUEST"]["~TAGS_ARRAY"] as $key => $tag )
        {
            $url_without = $arResult["REQUEST"]["~TAGS_ARRAY"];
            unset($url_without[$key]);
            $url[$tag] = $tag;
            $result = array(
                "TAG_NAME"    => $tag ,
                "TAG_PATH"    => $APPLICATION->GetCurPageParam(
                    "tags=" . urlencode(
                        implode(
                            "," ,
                            $url
                        )
                    ) ,
                    array( "tags" )
                ) ,
                "TAG_WITHOUT" => $APPLICATION->GetCurPageParam(
                    "tags=" . urlencode(
                        implode(
                            "," ,
                            $url_without
                        )
                    ) ,
                    array( "tags" )
                ) ,
            );
            $arResult["TAGS_CHAIN"][] = $result;
        }

        $arResult['MENU_SECTIONS'] = $arResult["SEARCH2"];

        if ( isset($_REQUEST['iblock']) )
        {
            $arResult["SEARCH2"]["FORUM"] = array();
            $arResult["SEARCH2"]["BLOG"] = array();
            $arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( isset($_REQUEST['forum']) )
        {
            $idBlog = intval( $_REQUEST['forum'] );
            if ( $idBlog > 0 )
            {
                $arResult["SEARCH2"]["FORUM"] = array(
                    0 => $arResult["SEARCH2"]["FORUM"][$idBlog]
                );
            }

            $arResult["SEARCH2"]["IBLOCK"] = array();
            $arResult["SEARCH2"]["BLOG"] = array();
            $arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( isset($_REQUEST['blog']) )
        {
            $idBlog = intval( $_REQUEST['blog'] );
            if ( $idBlog > 0 )
            {
                $arResult["SEARCH2"]["BLOG"] = array(
                    0 => $arResult["SEARCH2"]["BLOG"][$idBlog]
                );
            }
            $arResult["SEARCH2"]["IBLOCK"] = array();
            $arResult["SEARCH2"]["FORUM"] = array();
            $arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( isset($_REQUEST['static']) )
        {
            $arResult["SEARCH2"]["IBLOCK"] = array();
            $arResult["SEARCH2"]["FORUM"] = array();
            $arResult["SEARCH2"]["BLOG"] = array();
        }


        $this->ShowComponentTemplate();
    }
}
else
{
    $this->__ShowError(
        str_replace(
            "#PAGE#" ,
            $templatePage ,
            str_replace(
                "#NAME#" ,
                $this->__templateName ,
                "Can not find '#NAME#' template with page '#PAGE#'"
            )
        )
    );
}