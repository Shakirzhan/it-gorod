<?
/**
 * ���������� �������� �� ��������
 */
if ( !function_exists( "compareSectionsByName" ) )
{
    function compareSectionsByName( $a , $b )
    {

        return strcmp(
            $a["NAME"] ,
            $b["NAME"]
        );
    }
}

if ( !function_exists( "parseArrayBySections" ) )
{
    /**
     * ������� ������������ ������� ��������� �� ��������
     */
    function parseArrayBySections( $forSort )
    {

        foreach ( $forSort as $id )
        {
            $arSelect = Array(
                "ID" ,
                "IBLOCK_SECTION_ID"
            );
            $arFilter = Array( "ID" => $id );
            $res = CIBlockElement::GetList(
                Array() ,
                $arFilter ,
                false ,
                Array() ,
                $arSelect
            );
            if ( $arrRes = $res->Fetch() )
            {
                $sectionAndElements[] = $arrRes;
            }
        }
        if ( count( $sectionAndElements ) )
        {
            # �������� ������, � ������� ���� ��������� ��������
            foreach ( $sectionAndElements as $v )
            {
                if ( $v["IBLOCK_SECTION_ID"] )
                {
                    $sections[] = $v["IBLOCK_SECTION_ID"];
                }
            }
        }
        if ( count( $sections ) )
        {


            $sections = array_unique( $sections );
            $sections = array_values( $sections );

            if ( count( $sections ) > 0 )
            {
                foreach ( $sections as $section )
                {
                    foreach ( $sectionAndElements as $element )
                    {
                        if ( $section == $element["IBLOCK_SECTION_ID"] )
                        {
                            $sectionSelect[$section][] = $element["ID"];
                        }
                    }
                }
            }

        }

        return $sectionSelect;
    }
}

if ( !function_exists( "getIblockSectionsFromSearch" ) )
{
    function getIblockSectionsFromSearch( $elementIds = array() )
    {

        global $DB;
        $arIdSections = false;

        if ( is_array( $elementIds ) && !empty($elementIds) )
        {
            $sqlQuery = "
                SELECT SC.ID , SC.ITEM_ID , SP.SEARCH_CONTENT_ID , SP.PARAM_NAME , SP.PARAM_VALUE FROM b_search_content SC
                    LEFT JOIN b_search_content_param SP ON SP.SEARCH_CONTENT_ID=SC.ID
                    WHERE ITEM_ID IN (" . implode( "," , $elementIds ) . ")
                        AND SP.PARAM_NAME='S_SECTION_ID'
            ";
            $res = $DB->Query( $sqlQuery );

            while ( $arOb = $res->Fetch() )
            {
                $arIdSections["SECTIONS"][$arOb["PARAM_VALUE"]] = $arOb["PARAM_VALUE"];
                $arIdSections["ELEMENTS"][$arOb["PARAM_VALUE"]][] = $arOb["ITEM_ID"];
            }
        }

        return $arIdSections;
    }
}

if ( !function_exists( "sortIBlockElementsBySections" ) )
{
    /**
     * ������� ������������ ������� ��������� ��������� �� ��������
     */
    function sortIBlockElementsBySections( $iblockId , $elementIds , $userQuery , $arIdSections = false )
    {

        global $APPLICATION;
        try
        {

            $arResult = array();
            if ( !$arIdSections )
            {
                $arIdSections = array();

                $arElements = array();
                $arSections = array();

                //
                // 1. ��� ������ ������� ����� �������� � ����� ��������
                //    ���������
                //
                $arFilter = array(
                    "ID"     => $elementIds ,
                    "ACTIVE" => "Y"
                );
                $arSelect = array(
                    "ID" ,
                    "IBLOCK_SECTION_ID"
                );
                $dbList = CIBlockElement::GetList(
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
                        $arIdSections[$sectionId] = $sectionId;          // ������ ������� SECTION_ID, ��� ����� array_unique �� ������
                        $arElements[$sectionId][] = $arElement;                        // ��� ��� ��� �����������
                    }

                }
            }

            $arIdSections = array_values( $arIdSections );             // �������������� �������

            //
            // 2. ������ �������� �������� �������� � ��������� ����������
            //    ��������� ��������� � ���
            //

            if ( !empty($arIdSections) )
            {
                $arFilter = array(
                    "ID"            => $arIdSections ,
                    "ACTIVE"        => "Y" ,
                    "GLOBAL_ACTIVE" => "Y"
                );
                $arOrder = array( "left_margin" => "ASC" );                  // ����� ��������� ���������
                $arSelect = array(
                    "ID" ,
                    "IBLOCK_ID" ,
                    "IBLOCK_SECTION_ID" ,
                );

                $obCache = new CPHPCache();
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
                    $dbList = CIBlockSection::GetList(
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
                        $nav = CIBlockSection::GetNavChain(
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
            echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ERROR<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<";
            throw new Exception(
                'Something really gone wrong' , 0 , $e
            );
        }

        //
        // done.
        //
        return $arResult;
    }
}

if ( !function_exists( "sortBlogPostsBySections" ) )
{
    /**
     * ������� ������������ ������� ��������� ����� �� ��������
     */
    function sortBlogPostsBySections( $arPIDs , $blogID = 0 )
    {

        //
        // 0. init
        //
        $arResult = array();

        foreach ( $arPIDs as $strPID )
        {
            $arIdPosts[] = ( int )substr(
                $strPID ,
                1
            );
        }

        // TODO
        $arOrder = array(
            "DATE_PUBLISH" => "DESC" ,
            "NAME"         => "ASC"
        );
        $arFilter = array( "ID" => $arIdPosts );
        $dbPosts = CBlogPost::GetList(
            $arOrder ,
            $arFilter
        );
        while ( $arPost = $dbPosts->Fetch() )
        {
            //echo '<pre>'; print_r( $arPost); echo '</pre>';
            $arResult[] = $arPost;
        }

        //
        // done.
        //
        return $arResult;
    }
}
if ( !function_exists( "sortForumTopicsBySections" ) )
{
    /**
     * ������� ������������ ������� ��������� ������ �� ��������
     */
    function sortForumTopicsBySections( $FIDs )
    {

        //
        // 0. init
        //
        $arResult = array();

        $arOrder = array(
            "SORT"           => "ASC" ,
            "LAST_POST_DATE" => "DESC"
        );
        $arFilter = array( "FORUM_ID" => $FIDs );
        $dbTopics = CForumTopic::GetList(
            $arOrder ,
            $arFilter
        );
        while ( $arTopic = $dbTopics->Fetch() )
        {
            //echo $arTopic["TITLE"]."<br>";
            $arResult[] = $arTopic;
        }

        //
        // done.
        //
        return $arResult;
    }
}

?>