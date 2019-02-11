<?php
namespace Infospice\Search;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\DB\Exception;
use Bitrix\Main\Page\Asset;

class Component extends \CBitrixComponent
{
    private $moduleCode = "infospice.search";

    protected $arFilterCustom = array();
    protected $arFilter = array();
    protected $exFILTER = array();
    protected $arSearch = array();
    protected $arModule = array();
    protected $arRequest = array();
    /**
     * @var array
     */
    protected $cacheAdditionalId = array();

    protected $obSearch = false;
    protected $AJAX = false;
    protected $relativ = false;

    protected $userQuery = "";
    protected $formSID = "INFOSPICE_SEARCH_FORM";

    protected $maxCountOnce = 1000;

    public $to;
    public $from;

    public $arSort = array(
        "CUSTOM_RANK" => "DESC" ,
        "TITLE_RANK"  => "DESC" ,
        "RANK"        => "DESC" ,
        "DATE_CHANGE" => "DESC"
    );

    public function onPrepareComponentParams( $arParams )
    {

        \CPageOption::SetOptionString(
            "main" ,
            "nav_page_in_session" ,
            "N"
        );

        if ( !isset($arParams["CACHE_TIME"]) )
        {
            $arParams["CACHE_TIME"] = 3600;
        }

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

        $arParams["AJAX_COUNT_PRODUCT"] = intval( $arParams["AJAX_COUNT_PRODUCT"] );
        if ( $arParams["AJAX_COUNT_PRODUCT"] <= 0 )
        {
            $arParams["AJAX_COUNT_PRODUCT"] = 5;
        }

        $arParams["PAGER_TITLE"] = trim( $arParams["PAGER_TITLE"] );
        $arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] != "N";
        $arParams["USE_TITLE_RANK"] = $arParams["USE_TITLE_RANK"] == "Y";
        $arParams["PAGER_TEMPLATE"] = trim( $arParams["PAGER_TEMPLATE"] );

        $arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] = $arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] == "Y";
        $arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] = $arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] == "Y";

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
        $this->arFilterCustom = $arFILTERCustom;

        $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"] == "Y";


        $this->relativ = ($arParams["ELEMENT_SORT_FIELD"] == "rank"
                          || $arParams["ELEMENT_SORT_FIELD2"] == "rank");

        return $arParams;
    }

    /**
     *  Generate safe request
     */
    public function requestParams()
    {

        $this->arRequest = array(
            "iblock"  => intval( $_REQUEST["iblock"] ) ,
            "section" => intval( $_REQUEST["section"] ) ,
            "blog"    => htmlspecialchars( $_REQUEST["blog"] ) ,
            "static"  => htmlspecialchars( $_REQUEST["static"] ) ,
            "forum"   => htmlspecialchars( $_REQUEST["forum"] ) ,
            "q"       => htmlspecialchars( $_REQUEST["q"] ) ,
            "how"     => htmlspecialchars( $_REQUEST["how"] ) ,
            "r"       => htmlspecialchars( $_REQUEST["r"] ) ,
            "where"   => htmlspecialchars( $_REQUEST["where"] ) ,
            "to"      => htmlspecialchars( $_REQUEST["to"] ) ,
            "from"    => htmlspecialchars( $_REQUEST["from"] ) ,
            "spell"   => htmlspecialchars( $_REQUEST["spell"] )
        );

        if ( $this->AJAX )
        {
            global $APPLICATION;
            $this->arRequest["q"] = $APPLICATION->ConvertCharset( $this->arRequest["q"] , "UTF-8" , SITE_CHARSET );
        }

        foreach ( $_REQUEST as $keyRequest => $valRequest )
        {
            if ( strpos( $keyRequest , "PAGEN_" ) !== false )
            {
                $this->arRequest[$keyRequest] = intval( $valRequest );
            }
        }

    }

    /**
     * Cache init
     *
     * @return bool
     */
    protected function startCache()
    {

        global $USER;

        if ( $this->arParams['CACHE_TYPE'] && $this->arParams['CACHE_TYPE'] !== 'N'
             && $this->arParams['CACHE_TIME'] > 0
             && !$this->AJAX
        )
        {
            $this->cacheAdditionalId[] = $this->arResult["REQUEST"]["~QUERY"];
            $this->cacheAdditionalId[] = $this->arRequest;

            if ( $this->arParams['CACHE_GROUPS'] === 'Y' )
            {
                $this->cacheAdditionalId[] = $USER->GetGroups();
            }
            $this->cacheAdditionalId = serialize( $this->cacheAdditionalId );

            if ( $this->StartResultCache(
                $this->arParams['CACHE_TIME'] ,
                $this->cacheAdditionalId
            )
            )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    public function onIncludeComponentLang()
    {

        $this->includeComponentLang( basename( __FILE__ ) );
        Loc::loadMessages( __FILE__ );
    }

    /**
     * Include all requirement module
     * @throws Exception
     * @throws \Bitrix\Main\LoaderException
     */
    public function includeModule()
    {

        if ( !Loader::includeModule( "search" ) )
        {
            throw new Exception( Loc::getMessage( "SEARCH_MODULE_UNAVAILABLE" ) );
        }

        $this->arModule["iblock"] = Loader::includeModule( "iblock" );
        $this->arModule["catalog"] = Loader::includeModule( "catalog" );
        $this->arModule["blog"] = Loader::includeModule( "blog" );
        $this->arModule["forum"] = Loader::includeModule( "forum" );
        $this->arModule["form"] = Loader::includeModule( "form" );
    }

    /**
     * Include css and js
     */
    public function includeStyle()
    {

        $asset = Asset::getInstance();

        if ( $this->arParams['USE_JQUERY'] == "Y" )
        {
            $asset->addJs(
                '/bitrix/components/' . $this->moduleCode . '/infospice.search.page/js/jquery-2.1.0.min.js'
            );
            $asset->addJs(
                '/bitrix/components/' . $this->moduleCode . '/infospice.search.page/js/jquery-ui-1.10.4.custom.js'
            );
        }
        $asset->addJs( '/bitrix/components/' . $this->moduleCode . '/infospice.search.page/js/jquery.main.js' );

        $asset->addCss(
            $this->arResult["FOLDER_PATH"] . '/' . $this->arParams['TEMPLATE_STYLE'] . '.css' ,
            true
        );
    }

    /**
     * Get extra info from search table
     * @param array $elementIds
     * @return bool
     */
    public function getIblockSectionsFromSearch( $elementIds = array() )
    {

        $connection = \Bitrix\Main\Application::getConnection();
        $arIdSections = false;
        $sqlHelper = $connection->getSqlHelper();

        if ( is_array( $elementIds ) && !empty($elementIds) )
        {
            $sqlQuery = "
                SELECT SC.ID , SC.ITEM_ID , SP.SEARCH_CONTENT_ID , SP.PARAM_NAME , SP.PARAM_VALUE FROM b_search_content SC
                    LEFT JOIN b_search_content_param SP ON SP.SEARCH_CONTENT_ID=SC.ID
                    WHERE ITEM_ID IN (" . implode( "," , $elementIds ) . ")
                        AND SP.PARAM_NAME='S_SECTION_ID'
            ";
            $recordset = $connection->query( $sqlQuery );

            while ( $arOb = $recordset->fetch() )
            {
                $arIdSections["SECTIONS"][$arOb["PARAM_VALUE"]] = $arOb["PARAM_VALUE"];
                $arIdSections["ELEMENTS"][$arOb["PARAM_VALUE"]][] = $arOb["ITEM_ID"];
            }
        }

        return $arIdSections;
    }

    /**
     * Get blogs
     * @param $arPIDs
     * @param int $blogID
     * @return array
     */
    public function sortBlogPostsBySections( $arPIDs , $blogID = 0 )
    {

        $arResult = array();
        $arIdPosts = array();

        foreach ( $arPIDs as $strPID )
        {
            $arIdPosts[] = ( int )substr(
                $strPID ,
                1
            );
        }

        $arOrder = array(
            "DATE_PUBLISH" => "DESC" ,
            "NAME"         => "ASC"
        );
        $arFilter = array( "ID" => $arIdPosts );
        $dbPosts = \CBlogPost::GetList(
            $arOrder ,
            $arFilter
        );
        while ( $arPost = $dbPosts->Fetch() )
        {
            $arResult[] = $arPost;
        }

        return $arResult;
    }

    /**
     * Get topics
     * @param $FIDs
     * @return array
     */
    public function sortForumTopicsBySections( $FIDs )
    {

        $arResult = array();

        $arOrder = array(
            "SORT"           => "ASC" ,
            "LAST_POST_DATE" => "DESC"
        );
        $arFilter = array( "FORUM_ID" => $FIDs );
        $dbTopics = \CForumTopic::GetList(
            $arOrder ,
            $arFilter
        );
        while ( $arTopic = $dbTopics->Fetch() )
        {
            $arResult[] = $arTopic;
        }

        return $arResult;
    }

    /**
     * Create tree for iblock menu with sections and elements
     *
     * @param $iblockId
     * @param $elementIds
     * @param $userQuery
     * @param bool|false $arIdSections
     * @return array
     * @throws Exception
     */
    public function sortIBlockElementsBySections( $iblockId , $elementIds , $userQuery , $arIdSections = false )
    {

        global $APPLICATION;
        try
        {

            $arResult = array();
            if ( !$arIdSections )
            {
                $arIdSections = array();

                $arElements = array();

                //
                // 1. Для начала выясним какие элементы в каких разделах
                //    находятся
                //
                $arFilter = array(
                    "ID"     => $elementIds ,
                    "ACTIVE" => "Y"
                );
                $arSelect = array(
                    "ID" ,
                    "IBLOCK_SECTION_ID"
                );
                $dbList = \CIBlockElement::GetList(
                    array() ,
                    $arFilter ,
                    false ,
                    false ,
                    $arSelect
                );
                while ( $arElement = $dbList->Fetch() )
                {
                    $sectionId = $arElement["IBLOCK_SECTION_ID"];
                    if ( !empty($sectionId) )
                    {
                        $arIdSections[$sectionId] = $sectionId;          // вместо индекса SECTION_ID, это чтобы array_unique не делать
                        $arElements[$sectionId][] = $arElement;                        // это нам ещё понадобится
                    }

                }
            }

            $arIdSections = array_values( $arIdSections );             // переиндексация массива

            //
            // 2. Теперь запросим названия разделов и посчитаем количество
            //    найденных элементов в них
            //

            if ( !empty($arIdSections) )
            {
                $arFilter = array(
                    "ID"            => $arIdSections ,
                    "ACTIVE"        => "Y" ,
                    "GLOBAL_ACTIVE" => "Y"
                );
                $arOrder = array( "left_margin" => "ASC" );                  // сразу сортируем результат
                $arSelect = array(
                    "ID" ,
                    "IBLOCK_ID" ,
                    "IBLOCK_SECTION_ID" ,
                );

                $obCache = new \CPHPCache();
                if ( $obCache->InitCache(
                    3600 ,
                    serialize( $arFilter ) ,
                    "/search/section_path"
                )
                )
                {
                    $arResult = $obCache->GetVars();
                }
                elseif ( $obCache->StartDataCache() )
                {
                    $dbList = \CIBlockSection::GetList(
                        $arOrder ,
                        $arFilter ,
                        false ,
                        $arSelect
                    );
                    $arSelectSection = array(
                        "ID" ,
                        "IBLOCK_SECTION_ID" ,
                        "DEPTH_LEVEL" ,
                        "NAME" ,
                        "SECTION_PAGE_URL"
                    );
                    while ( $arSection = $dbList->Fetch() )
                    {
                        $nav = \CIBlockSection::GetNavChain(
                            $arSection["IBLOCK_ID"] ,
                            $arSection["ID"] ,
                            $arSelectSection
                        );
                        while ( $arNav = $nav->Fetch() )
                        {
                            if ( !$arResult[$arNav["ID"]]["URL"] )
                            {
                                $arNav['SECTIONS'] = $arResult[$arNav["ID"]]['SECTIONS'];
                                $arNav["URL"] = htmlspecialchars(
                                    $APPLICATION->GetCurPage() . "?q=" . $userQuery . "&iblock=" . $arSection["IBLOCK_ID"]
                                    . "&section=" . $arNav["ID"]
                                );
                                $arNav['ELEMENTS'] = $arElements[$arNav["ID"]];
                                $arResult[$arNav["ID"]] = $arNav;
                            }
                            $arResult[$arNav["ID"]]['SECTIONS'][$arSection["ID"]] = $arSection["ID"];
                            if ( $arNav["IBLOCK_SECTION_ID"] )
                            {
                                $arResult[$arNav["IBLOCK_SECTION_ID"]]['SECTIONS'][$arNav["ID"]] = $arNav["ID"];
                                $arResult[$arNav["IBLOCK_SECTION_ID"]]['SECTIONS'] = $arResult[$arNav["IBLOCK_SECTION_ID"]]['SECTIONS']
                                                                                     + $arResult[$arNav["ID"]]['SECTIONS'];
                            }
                        }
                    }

                    $obCache->EndDataCache( $arResult );
                }

            }
        }
        catch ( Exception $e )
        {
            throw new Exception(
                'Error sortIBlockElementsBySections:' , 0 , $e
            );
        }

        return $arResult;
    }

    /**
     *  Get select list for search module
     */
    public function getDropDown()
    {

        $arrDropdown = array();

        $obCache = new \CPHPCache;

        if ( $obCache->StartDataCache(
            $this->arParams["CACHE_TIME"] ,
            $this->GetCacheID() ,
            "/" . SITE_ID . $this->GetRelativePath()
        )
        )
        {
            // Getting of the Information block types
            $arIBlockTypes = array();
            if ( $this->arModule["catalog"] )
            {
                $rsIBlockType = \CIBlockType::GetList(
                    array( "sort" => "asc" ) ,
                    array( "ACTIVE" => "Y" )
                );
                while ( $arIBlockType = $rsIBlockType->Fetch() )
                {
                    if ( $ar = \CIBlockType::GetByIDLang(
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
            foreach ( $this->arParams["arrWHERE"] as $code )
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
                                $arrDropdown[$code] = Loc::getMessage( "SEARCH_FORUM" );
                                break;
                            case "blog":
                                $arrDropdown[$code] = Loc::getMessage( "SEARCH_BLOG" );
                                break;
                            case "socialnetwork":
                                $arrDropdown[$code] = Loc::getMessage( "SEARCH_SOCIALNETWORK" );
                                break;
                            case "intranet":
                                $arrDropdown[$code] = Loc::getMessage( "SEARCH_INTRANET" );
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

        $this->arResult["DROPDOWN"] = htmlspecialcharsex( $arrDropdown );
    }

    /**
     * Get safe and right query for search
     */
    public function getQuery()
    {

        if ( $this->arRequest["q"] )
        {
            $q = trim( $this->arRequest["q"] );
        }
        else
        {
            $q = false;
        }

        if ( $q !== false )
        {
            if ( $this->arParams["USE_LANGUAGE_GUESS"] == "N" || $this->arRequest["spell"] )
            {
                $this->arResult["REQUEST"]["~QUERY"] = $q;
                $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex( $q );
            }
            else
            {

                $arLang = \CSearchLanguage::GuessLanguage( $q );

                if ( is_array( $arLang ) && $arLang["from"] != $arLang["to"] )
                {
                    $this->arResult["REQUEST"]["~ORIGINAL_QUERY"] = $q;
                    $this->arResult["REQUEST"]["ORIGINAL_QUERY"] = htmlspecialcharsex( $q );

                    $this->arResult["REQUEST"]["~QUERY"] = \CSearchLanguage::ConvertKeyboardLayout(
                        $this->arResult["REQUEST"]["~ORIGINAL_QUERY"] ,
                        $arLang["from"] ,
                        $arLang["to"]
                    );
                    $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex(
                        $this->arResult["REQUEST"]["~QUERY"]
                    );
                }
                else
                {
                    $this->arResult["REQUEST"]["~QUERY"] = $q;
                    $this->arResult["REQUEST"]["QUERY"] = htmlspecialcharsex( $q );
                }
            }

        }
        else
        {
            $this->arResult["REQUEST"]["~QUERY"] = false;
            $this->arResult["REQUEST"]["QUERY"] = false;
        }

        $this->arResult["REQUEST"]["TAGS"] = false;
    }

    /**
     *  Get select for search module
     */
    public function getWhere()
    {

        if ( $this->arParams["SHOW_WHEN"] && $this->arRequest["from"]
             && is_string(
                 $this->arRequest["from"]
             )
             && strlen(
                 $this->arRequest["from"]
             )
             && CheckDateTime( $this->arRequest["from"] )
        )
        {
            $this->from = $this->arRequest["from"];
        }
        else
        {
            $this->from = "";
        }

        if ( $this->arParams["SHOW_WHEN"] && $this->arRequest["to"]
             && is_string(
                 $this->arRequest["to"]
             )
             && strlen(
                 $this->arRequest["to"]
             )
             && CheckDateTime( $this->arRequest["to"] )
        )
        {
            $this->to = $this->arRequest["to"];
        }
        else
        {
            $this->to = "";
        }

        $where = $this->arParams["SHOW_WHERE"] ? trim( $this->arRequest["where"] ) : "";
        $this->arResult["REQUEST"]["WHERE"] = htmlspecialchars( $where );
    }

    /**
     *  Get filter for search module
     */
    public function getFilter()
    {

        $arFilter = array(
            "SITE_ID" => SITE_ID ,
            "QUERY"   => $this->arResult["REQUEST"]["~QUERY"] ,
            "TAGS"    => $this->arResult["REQUEST"]["~TAGS"] ,
        );
        $arFilter = array_merge(
            $this->arFilterCustom ,
            $arFilter
        );

        if ( strlen( $this->arResult["REQUEST"]["WHERE"] ) > 0 )
        {
            list($module_id , $part_id) = explode(
                "_" ,
                $this->arResult["REQUEST"]["WHERE"] ,
                2
            );
            $arFilter["MODULE_ID"] = $module_id;
            if ( strlen( $part_id ) > 0 ) $arFilter["PARAM1"] = $part_id;
        }
        if ( $this->from )
        {
            $arFilter[">=DATE_CHANGE"] = $this->from;
        }
        if ( $this->to )
        {
            $arFilter["<=DATE_CHANGE"] = $this->to;
        }

        $this->arFilter = $arFilter;

        $this->exFILTER = \CSearchParameters::ConvertParamsToFilter(
            $this->arParams ,
            "arrFILTER"
        );
    }

    /**
     *  Main method for search
     */
    protected function executeSearch()
    {

        $this->searchStatic();
        $this->filterCatalog();
        $this->prepareSort();

        $this->mainSearch();

        $this->prepareIblockResult();
        $this->prepareSectionResult();
        $this->prepareBlogResult();
        $this->prepareForumResult();

        $this->prepareAllResult();
    }

    /**
     *  Search static
     */
    protected function searchStatic()
    {

        $obSearch = new \CSearch();

        //When restart option is set we will ignore error on query with only stop words
        $obSearch->SetOptions(
            array(
                "ERROR_ON_EMPTY_STEM" => $this->arParams["RESTART"] != "Y" ,
                "NO_WORD_LOGIC"       => $this->arParams["NO_WORD_LOGIC"] == "Y" ,
            )
        );

        $this->arResult["SEARCH2"] = array();

        $arFilterStatic = $this->arFilter;
        $arFilterStatic["MODULE_ID"] = "main";

        $obSearch->Search(
            $arFilterStatic ,
            $this->arSort ,
            $this->exFILTER
        );
        $obSearch->NavStart(
            $this->arParams["PAGE_IBLOCK_COUNT"] ,
            false
        );
        while ( $ar = $obSearch->GetNext() )
        {
            $this->arResult["SEARCH2"]["STATIC"][] = $ar;
        }

        $this->arResult["NAV_STRING_STATIC"] = $obSearch->GetPageNavStringEx(
            $navComponentObject ,
            $this->arParams["PAGER_TITLE"] ,
            $this->arParams["PAGER_TEMPLATE"] ,
            $this->arParams["PAGER_SHOW_ALWAYS"]
        );
        $this->obSearch = $obSearch;
    }

    /**
     *  Get extra filter for search module for iblock elements
     */
    protected function filterCatalog()
    {

        $pQua = ($this->arParams["HIDE_ELEMENTS_IF_QUANTITY_0"] == "Y");
        $pImg = ($this->arParams["HIDE_ELEMENTS_WITHOUT_PICTURE"] == "Y");
        $pPri = ($this->arParams["HIDE_ELEMENTS_WITHOUT_PRICE"] == "Y");

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
            if ( $pQua && $this->arModule["catalog"] )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_QUANTITY"] = "Y";
            }
            if ( $pImg )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_IMAGE"] = "Y";
            }
            if ( $pPri && $this->arModule["catalog"] )
            {
                $arLogicFilter[0]["PARAMS"]["HAS_PRICE"] = "Y";
            }

            $this->arFilter[] = $arLogicFilter;
        }
    }

    /**
     * Prepare sort for search module
     */
    public function prepareSort()
    {

        if ( $this->relativ )
        {
            $this->arSort = array(
                "CUSTOM_RANK" => $this->arParams["ELEMENT_SORT_ORDER"] ,
                "TITLE_RANK"  => $this->arParams["ELEMENT_SORT_ORDER"] ,
                "RANK"        => $this->arParams["ELEMENT_SORT_ORDER"] ,
                "DATE_CHANGE" => $this->arParams["ELEMENT_SORT_ORDER"]
            );
        }
    }

    /**
     *  Main method of search module
     */
    public function mainSearch()
    {

        $obSearch = $this->obSearch;

        $obSearch->Search(
            $this->arFilter ,
            $this->arSort ,
            $this->exFILTER
        );

        $this->arResult["ERROR_CODE"] = $obSearch->errorno;
        $this->arResult["ERROR_TEXT"] = $obSearch->error;

        if ( $obSearch->errorno == 0 )
        {
            $obSearch->NavStart(
                $this->maxCountOnce ,
                false
            );
            $ar = $obSearch->Fetch();
            //Search restart
            if ( !$ar && ($this->arParams["RESTART"] == "Y") && $obSearch->Query->bStemming )
            {
                $this->exFILTER["STEMMING"] = false;
                $obSearch = new \CSearch();
                $obSearch->Search(
                    $this->arFilter ,
                    $this->arSort ,
                    $this->exFILTER
                );

                $this->arResult["ERROR_CODE"] = $obSearch->errorno;
                $this->arResult["ERROR_TEXT"] = $obSearch->error;

                if ( $obSearch->errorno == 0 )
                {
                    $obSearch->NavStart(
                        $this->arParams["PAGE_RESULT_COUNT"] ,
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
                if ( $moduleId == "iblock" && $this->arModule["iblock"] )
                {
                    if ( $ar["TYPE_ELEMENT"] == "section" )
                    {
                        $this->arResult["SEARCH2"]["STATIC"][] = $ar;
                    }
                    else
                    {
                        $this->arResult["ELEMENTS_ID"][$ar["PARAM2"]][] = $ar["ITEM_ID"];
                        $this->arSearch[$moduleId][$categoriesId]["ELEMENTS"][$itemId] = $itemId;
                    }
                }
                else
                {
                    $this->arSearch[$moduleId][$categoriesId][] = $itemId;
                }
                $ar = $obSearch->Fetch();
            }
        }
    }

    /**
     * Prepare iblock result and tree of sections and elements
     * @throws Exception
     */
    public function prepareIblockResult()
    {

        global $APPLICATION;
        $this->userQuery = urlencode( $this->arResult["REQUEST"]["QUERY"] );
        $this->arResult["userQuery"] = $this->arResult["REQUEST"]["QUERY"];

        $arIBlockElements = $this->arSearch['iblock'];

        if ( is_array( $arIBlockElements ) && $this->arModule["iblock"] )
        {
            $arIdIblocks = array_keys( $arIBlockElements );
            $arFilterIblock = array(
                "ID" => $arIdIblocks
            );

            $arAllIblocks = array();

            $obCache = new \CPHPCache();
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
                $rsIblock = \CIBlock::GetList(
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
            $getIblockID = $this->arRequest["iblock"];
            $getSectionID = $this->arRequest["section"];

            foreach ( $arAllIblocks as $iblockId => $arIblock )
            {
                $arIdElements = $arIBlockElements[$iblockId];
                if ( is_array( $arIdElements["ELEMENTS"] ) )
                {
                    $arIdSections = $this->getIblockSectionsFromSearch( $arIdElements["ELEMENTS"] );
                    $arSortElementBySections = $this->sortIBlockElementsBySections(
                        $iblockId ,
                        $arIdElements["ELEMENTS"] ,
                        $this->userQuery ,
                        $arIdSections["SECTIONS"]
                    );
                    $arIBlock = $arAllIblocks[$iblockId];

                    if ( $getIblockID == $iblockId && $getSectionID > 0
                         && $arSortElementBySections[$getSectionID]
                         && !$this->AJAX
                    )
                    {
                        $arNewId = array();
                        $arInheritSections = $arSortElementBySections[$getSectionID]["SECTIONS"];
                        $arInheritSections[$arSortElementBySections[$getSectionID]["ID"]] = $arSortElementBySections[$getSectionID]["ID"];
                        foreach ( $arInheritSections as $keyS => $SECTION_ID )
                        {
                            if ( is_array( $arIdSections["ELEMENTS"][$SECTION_ID] ) )
                            {
                                $arNewId = array_merge($arNewId , $arIdSections["ELEMENTS"][$SECTION_ID]);
                            }
                        }
                        $arNewId = array_combine($arNewId , $arNewId);
                        $arIdElements["ELEMENTS"] = self::sortByArray( $arNewId , $arIdElements["ELEMENTS"]  , false);
                    }

                    $this->arResult["SEARCH2"]["IBLOCK"][$arIBlock["ID"]] = array(
                        "ID"       => $arIBlock["ID"] ,
                        "NAME"     => $arIBlock["NAME"] ,
                        "COUNT"    => count( $arIdElements["ELEMENTS"] ) ,
                        "URL"      => htmlspecialchars(
                            $APPLICATION->GetCurPage() . "?q=" . $this->userQuery . "&iblock=" . $arIBlock["ID"]
                        ) ,
                        "ELEMENTS" => $arIdElements["ELEMENTS"] ,
                        "SECTIONS" => $arSortElementBySections
                    );
                }
            }

            if ( $this->relativ )
            {

                foreach ( $this->arResult["SEARCH2"]["IBLOCK"] as $keyIbl => &$arIblock )
                {
                    if ( !empty($arIblock["ELEMENTS"])
                         && $this->arParams["PAGE_ELEMENT_COUNT"] < count( $arIblock["ELEMENTS"] )
                    )
                    {
                        $this->arParams["DISPLAY_TOP_PAGER"] = "N";
                        $this->arParams["DISPLAY_BOTTOM_PAGER"] = "N";
                        $this->arParams["DISPLAY_RELATIV_PAGEN"] = "Y";

                        $dbResult = new \CDBResult();

                        $arNavParams = array(
                            "nPageSize"          => $this->arParams["PAGE_ELEMENT_COUNT"] ,
                            "bDescPageNumbering" => $this->arParams["PAGER_DESC_NUMBERING"] ,
                            "bShowAll"           => $this->arParams["PAGER_SHOW_ALL"] ,
                        );
                        $arNavigation = \CDBResult::GetNavParams( $arNavParams );
                        $arNavigation["PAGEN"] = "S_" . $keyIbl;

                        $pageCount = ceil(
                            count( $arIblock["ELEMENTS"] ) / $this->arParams["PAGE_ELEMENT_COUNT"]
                        );
                        $pageNumber = intval( $this->arRequest["PAGEN_" . $arNavigation["PAGEN"]] );
                        if ( $pageNumber < 1 )
                        {
                            $pageNumber = 1;
                        }

                        $dbResult->NavPageSize = $this->arParams["PAGE_ELEMENT_COUNT"];
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

                        $arPagen = array_chunk(
                            $arIblock["ELEMENTS"] ,
                            $this->arParams["PAGE_ELEMENT_COUNT"]
                        );
                        $arIblock["ELEMENTS"] = $arPagen[$pageNumber - 1];
                    }
                }

            }

        }
    }

    /**
     * Prepare alone sections
     * @throws \Bitrix\Main\ArgumentException
     */
    public function prepareSectionResult()
    {

        $arSectionsID = array();
        $arStatic = $this->arResult["SEARCH2"]["STATIC"];
        if ( is_array( $arStatic ) && $this->arModule["iblock"] )
        {
            foreach ( $arStatic as $stId => $static )
            {
                if ( $static["MODULE_ID"] == "iblock" )
                {
                    $arSectionsID[$static["ITEM_ID"]] = $stId;
                }
            }
        }

        if ( count( $arSectionsID ) )
        {
            $params = array(
                "filter" => array(
                    "ID" => array_keys( $arSectionsID )
                ) ,
                "select" => array(
                    "ID" ,
                    "PICTURE"
                )
            );

            $rsSection = SectionTable::getList( $params );
            while ( $arSection = $rsSection->fetch() )
            {
                $arImage = self::resizeImage(
                    $arSection["PICTURE"] ,
                    0 ,
                    105 ,
                    100
                );
                $this->arResult["SEARCH2"]["STATIC"][$arSectionsID[$arSection["ID"]]]["IMAGE"] = $arImage;

                if ( $arImage["SRC"] )
                {
                    $this->arResult["SEARCH2"]["STATIC"][$arSectionsID[$arSection["ID"]]]
                    ["BODY_FORMATED"] = "<img style='float: left;' src='" . $arImage["SRC"] . "'/>"
                                        . $this->arResult["SEARCH2"]["STATIC"][$arSectionsID[$arSection["ID"]]]["BODY_FORMATED"];
                }
            }
        }
    }

    /**
     *  Prepare posts
     */
    public function prepareBlogResult()
    {

        global $APPLICATION;
        $arBlogElements = $this->arSearch['blog'];
        if ( is_array( $arBlogElements ) && $this->arModule["blog"] )
        {
            foreach ( $arBlogElements as $blogId => $arIdElements )
            {

                if ( is_array( $arIdElements ) )
                {
                    $arBlog = \CBlog::GetByID( $blogId );
                    if ( is_array( $arBlog ) )
                    {
                        $this->arResult["SEARCH2"]["BLOG"][$arBlog["ID"]] = array(
                            "ID"       => $arBlog["ID"] ,
                            "NAME"     => $arBlog["NAME"] ,
                            "BLOG_URL" => $arBlog["URL"] ,
                            "COUNT"    => count( $arIdElements ) ,
                            "URL"      => htmlspecialchars(
                                $APPLICATION->GetCurPage() . "?q=" . $this->userQuery . "&blog=" . $arBlog["ID"]
                            ) ,
                            "POSTS"    => $this->sortBlogPostsBySections( $arIdElements ) ,
                        );
                    }
                }
            }
        }
    }

    /**
     *  Prepare topics
     */
    public function prepareForumResult()
    {

        global $APPLICATION;
        $arForumElements = $this->arSearch['forum'];

        if ( is_array( $arForumElements ) && $this->arModule["forum"] )
        {
            foreach ( $arForumElements as $forumId => $arIdElements )
            {
                if ( is_array( $arIdElements ) )
                {
                    $arForum = \CForumNew::GetByID( $forumId );
                    if ( is_array( $arForum ) )
                    {
                        $arResult["SEARCH2"]["FORUM"][$arForum["ID"]] = array(
                            "ID"     => $arForum["ID"] ,
                            "NAME"   => $arForum["NAME"] ,
                            "MID"    => $arForum["MID"] ,
                            "TOPICS" => $this->sortForumTopicsBySections( $arIdElements ) ,
                            "COUNT"  => count( $arIdElements ) ,
                            "URL"    => htmlspecialchars(
                                $APPLICATION->GetCurPage() . "?q=" . $this->userQuery . "&forum=" . $arForum["ID"]
                            ) ,
                        );
                    }
                }
            }
        }
    }

    /**
     *  Clear empty modules result
     */
    public function prepareAllResult()
    {

        $this->arResult['MENU_SECTIONS'] = $this->arResult["SEARCH2"];

        if ( $this->arRequest['iblock'] )
        {
            $this->arResult["SEARCH2"]["FORUM"] = array();
            $this->arResult["SEARCH2"]["BLOG"] = array();
            $this->arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( $this->arRequest['forum'] )
        {
            $idBlog = intval( $this->arRequest['forum'] );
            if ( $idBlog > 0 )
            {
                $this->arResult["SEARCH2"]["FORUM"] = array(
                    0 => $this->arResult["SEARCH2"]["FORUM"][$idBlog]
                );
            }

            $this->arResult["SEARCH2"]["IBLOCK"] = array();
            $this->arResult["SEARCH2"]["BLOG"] = array();
            $this->arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( $this->arRequest['blog'] )
        {
            $idBlog = intval( $this->arRequest['blog'] );
            if ( $idBlog > 0 )
            {
                $this->arResult["SEARCH2"]["BLOG"] = array(
                    0 => $this->arResult["SEARCH2"]["BLOG"][$idBlog]
                );
            }
            $this->arResult["SEARCH2"]["IBLOCK"] = array();
            $this->arResult["SEARCH2"]["FORUM"] = array();
            $this->arResult["SEARCH2"]["STATIC"] = array();
        }
        elseif ( $this->arRequest['static'] )
        {
            $this->arResult["SEARCH2"]["IBLOCK"] = array();
            $this->arResult["SEARCH2"]["FORUM"] = array();
            $this->arResult["SEARCH2"]["BLOG"] = array();
        }
    }

    /**
     *  Init template and set path for template style
     */
    public function includeTemplate()
    {

        if ( $this->initComponentTemplate( "" ) )
        {
            $template = $this->GetTemplate();
            $this->arResult["FOLDER_PATH"] = $folderPath = $template->GetFolder();
            $this->showComponentTemplate();
        }
        else
        {
            $this->abortResultCache();
            $this->__showError(
                str_replace(
                    array( "#PAGE#" , "#NAME#" ) ,
                    array( "" , $this->getTemplateName() ) ,
                    "Cannot find '#NAME#' template with page '#PAGE#'"
                )
            );
        }
    }

    /**
     *  Pre-press in absence of search results
     */
    public function checkForm()
    {

        if ( count( $this->arResult["SEARCH2"] ) < 1 && $this->arParams['SHOW_FORM_SEARCH_PRODUCT'] == 'Y'
             && $this->arModule["form"]
        )
        {
            $arFilter = array(
                "SID" => $this->formSID
            );
            $is_filtered = false;
            $rsForms = \CForm::GetList( $by = "s_id" , $order = "desc" , $arFilter , $is_filtered );
            if ( $arForm = $rsForms->Fetch() )
            {
                $this->AbortResultCache(); // error with inner ajax component
                $this->arResult["FORM_EMPTY"] = $arForm;
            }
        }
    }

    /**
     *  ajax start
     */
    public function startAjax()
    {

        global $APPLICATION;
        $this->AJAX = ($_REQUEST["ajax_search"] == 'Y');

        if ( $this->AJAX )
        {
            $APPLICATION->RestartBuffer();
        }
    }

    /**
     *  end ajax
     */
    public function endAjax()
    {

        if ( $this->AJAX )
        {
            die();
        }
    }

    /**
     * Sort by array
     * @param $arMas
     * @param $arKeyToId
     * @return array
     */
    public static function sortByArray( $arMas , $arKeyToId , $fillOld = true )
    {
        $arNewItems = array();
        foreach ( $arKeyToId as $kID )
        {
            if ( $arMas[$kID] )
            {
                $arNewItems[$kID] = $arMas[$kID];
            }
            unset($arMas[$kID]);
        }
        if ( !empty($arMas) && $fillOld )
        {
            $arNewItems += $arMas;
        }

        return $arNewItems;
    }

    /**
     *  Prepare items for search string in ajax mode
     */
    public function prepareAjax()
    {

        if ( !$this->AJAX )
        {
            return;
        }

        $countMax = $this->arParams["AJAX_COUNT_PRODUCT"];
        $arElements = array();
        $arIblockID = array();

        $this->arResult["SEARCH2"] = self::sortByArray( $this->arResult["SEARCH2"] , array( "IBLOCK" , "FORUM" , "BLOG" ) );

        if ( count( $this->arResult["SEARCH2"] ) )
        {
            foreach ( $this->arResult["SEARCH2"] as $keyModule => $arModule )
            {
                if ( !count( $arModule ) )
                {
                    continue;
                }
                switch ( $keyModule )
                {
                    case "IBLOCK" :
                        foreach ( $arModule as $keyIblock => $arIblock )
                        {
                            foreach ( $arIblock["ELEMENTS"] as $key => $ID_ITEM )
                            {
                                $arElements[$ID_ITEM] = $ID_ITEM;
                                $arIblockID[] = $ID_ITEM;
                                $countMax--;
                                if ( $countMax <= 0 )
                                {
                                    break 4;
                                }
                            }
                        }
                        break;
                    case "FORUM" :
                    case "BLOG" :
                        $keyAr = ($keyModule == "FORUM") ? "TOPICS" : "POSTS";
                        foreach ( $arModule as $keyIblock => $arPart )
                        {
                            if ( !count( $arPart ) )
                            {
                                continue;
                            }
                            foreach ( $arPart[$keyAr] as $key => $arItem )
                            {
                                $arItem["DETAIL_PAGE_URL"] = $arItem["URL_WO_PARAMS"];
                                $arItem["NAME"] = $arItem["TITLE"];
                                $arElements[$keyModule . "_" . $arItem["ID"]] = $arItem;

                                $countMax--;
                                if ( $countMax <= 0 )
                                {
                                    break 4;
                                }
                            }
                        }
                        break;
                    default:
                        foreach ( $arModule as $key => $arItem )
                        {
                            $arItem["DETAIL_PAGE_URL"] = $arItem["URL"];
                            $arItem["NAME"] = $arItem["TITLE"];
                            $arElements[$keyModule . "_" . $arItem["ID"]] = $arItem;


                            $countMax--;
                            if ( $countMax <= 0 )
                            {
                                break 3;
                            }
                        }
                        break;

                }
            }

            if ( count( $arIblockID ) )
            {
                global $USER;
                $arSelect = array( "ID" , "NAME" , "IBLOCK_ID" , "DETAIL_PICTURE" , "PREVIEW_PICTURE" , "DETAIL_PAGE_URL" );
                $arFilter = array( "ID" => $arIblockID );

                $rsElement = \CIBlockElement::GetList( array() , $arFilter , false , false , $arSelect );
                while ( $arElement = $rsElement->GetNext() )
                {
                    $arElement["IMAGE"] = self::resizeImage(
                        $arElement["PREVIEW_PICTURE"] ,
                        $arElement["DETAIL_PICTURE"] ,
                        105 ,
                        100
                    );
                    if ( $this->arModule["catalog"] )
                    {
                        $offersExist = \CCatalogSKU::GetInfoByProductIBlock( $arElement["IBLOCK_ID"] );
                        if ( $offersExist )
                        {
                            $arPrice = false;
                            $arSelectOffer = array(
                                "ID" ,
                                "CATALOG_QUANTITY" ,
                                "PREVIEW_PICTURE" ,
                                "DETAIL_PICTURE" ,
                            );
                            $arFilterOffer = array(
                                '=PROPERTY_' . $offersExist['SKU_PROPERTY_ID'] => $arElement["ID"]
                            );

                            $rsOffer = \CIBlockElement::GetList(
                                array() ,
                                $arFilterOffer ,
                                false ,
                                false ,
                                $arSelectOffer
                            );
                            while ( $arOffer = $rsOffer->Fetch() )
                            {
                                if ( empty($arElement["IMAGE"]) )
                                {
                                    $arElement["IMAGE"] = self::resizeImage(
                                        $arOffer["PREVIEW_PICTURE"] ,
                                        $arOffer["DETAIL_PICTURE"] ,
                                        105 ,
                                        100
                                    );
                                }

                                if ( empty($arPrice) )
                                {
                                    $arPrice = \CCatalogProduct::GetOptimalPrice(
                                        $arOffer["ID"] ,
                                        1 ,
                                        $USER->GetUserGroupArray()
                                    );
                                    $arElement["PRICE"] = $arPrice;
                                }

                                if ( !empty($arPrice) && !empty($arElement["IMAGE"]) )
                                {
                                    break;
                                }
                            }
                        }
                        else
                        {
                            $arElement["PRICE"] = \CCatalogProduct::GetOptimalPrice(
                                $arElement["ID"] ,
                                1 ,
                                $USER->GetUserGroupArray()
                            );
                        }
                        $arElement["PRICE"]["PRINT_DISCOUNT_VALUE"] = SaleFormatCurrency(
                            $arElement["PRICE"]["DISCOUNT_PRICE"] ,
                            $arElement["PRICE"]["PRICE"]["CURRENCY"]
                        );
                        $arElement["PRICE"]["PRINT_VALUE"] = SaleFormatCurrency(
                            $arElement["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] ,
                            $arElement["PRICE"]["PRICE"]["CURRENCY"]
                        );
                        $arElements[$arElement["ID"]] = $arElement;
                    }
                }
            }

            $this->arResult["AJAX_ITEMS"] = $arElements;
        }
    }

    /**
     * Resize image
     * @param $id
     * @param $width
     * @param $height
     * @return array
     */
    public static function resizeImage( $detail_picture = 0 , $preview_picture = 0 , $width , $height ,
        $type = BX_RESIZE_IMAGE_PROPORTIONAL )
    {

        $imageID = 0;
        if ( $detail_picture > 0 )
        {
            $imageID = $detail_picture;
        }
        elseif ( $preview_picture > 0 )
        {
            $imageID = $preview_picture;
        }

        $image = array();
        if ( $imageID )
        {
            $image = \CFile::ResizeImageGet(
                $imageID ,
                array(
                    'width'  => $width ,
                    'height' => $height
                ) ,
                $type ,
                true
            );


            $image = array(
                "SRC"      => $image['src'] ,
                "WIDTH"    => $image['width'] ,
                "HEIGHT"   => $image['height'] ,
                "REAL_SRC" => $image['REAL_SRC']
            );
        }

        return $image;
    }


    /**
     * Execute component
     */
    public function executeComponent()
    {

        try
        {

            $this->startAjax();
            $this->includeModule();
            $this->requestParams();
            $this->getDropDown();
            $this->getQuery();
            $this->getWhere();

            if ( $this->startCache() )
            {
                $this->getFilter();
                $this->executeSearch();
                $this->prepareAjax();
                $this->checkForm();
                $this->includeTemplate();
                $this->endResultCache();
            }

            $this->includeStyle();
            $this->endAjax();

        }
        catch ( \Exception $e )
        {
            $this->AbortResultCache();
            ShowError( $e->getMessage() );
        }
    }
}