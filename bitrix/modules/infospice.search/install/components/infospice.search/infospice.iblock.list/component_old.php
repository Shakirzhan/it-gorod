<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>
<?

# �������� � �������� $arParams["ARRAY_ELEMENT"] ���� ������ ��������
if ( count( $arParams["ARRAY_ELEMENT"] ) > 0 )
{
    foreach ( $arParams["ARRAY_ELEMENT"] as $k => $v )
    {
        # �������� ������ ������ ��� ���������� ���������
        if ( $_GET["iblock"] == $k )
        {
            # ������� ������������ ������� ��������� �� ��������
            # ������������ /infospice.search/include/
            $arr_sort = parseArrayBySections( $v );
        }
    }

    if ( is_array( $arr_sort ) )
    {
        # � $arResult ������� �������� ������ � ���������� ��������� � ���� ������
        foreach ( $arr_sort as $key => $value )
        {
            if ( !empty($key) )
            {
                $arFilter = Array( "ID" => $key );
                $db_list  = CIBlockSection::GetList(
                    Array() ,
                    $arFilter ,
                    false
                );
                if ( $section = $db_list->Fetch() )
                {
                    $data                  = array(
                        "ID"    => $section["ID"] ,
                        "NAME"  => $section["NAME"] ,
                        "COUNT" => count( $value )
                    );
                    $arResult["SECTION"][] = $data;
                }
            }
        }
        // ����������
        usort(
            $arResult["SECTION"] ,
            'compareSectionsByName'
        );


        $arResult["SECTION_URL"] = "Y";
    }
    else
    {
        # ����� ����������
        $res = CIBlock::GetList(
            Array() ,
            Array(
                'ID' => $arParams["FILTER_NAME"]
            ) ,
            true
        );
        while ( $ar_res = $res->Fetch() )
        {
            $arrListIblock[$ar_res["ID"]] = $ar_res;
        }
        # ���������� ���������� ��������� ��������� � ���������
        foreach ( $arrListIblock as $k => $elements )
        {
            $elements["COUNT"]     = $arParams["COUNT_ELEMENT"][$k];
            $arResult["SECTION"][] = $elements;
        }
    }
}

?>
