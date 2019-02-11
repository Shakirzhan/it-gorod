<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult["ITEMS"] as $key => $arElement) 
{
    if ( count( $arElement['OFFERS'] ) && !$arElement['PRICES'] )
    {
        $arFirstOffer          = $arElement['OFFERS'][0];
        $arElement['PRICES'][] = $arFirstOffer['MIN_PRICE'];
    }

    $arElement["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
    $arElement["BUY_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

    if ( !empty( $arElement[ "PREVIEW_PICTURE" ])) {
        // 
        // ���� � �������� ��� ���� �������� � ���������, 
        // �� ���� � (��� ��������, �� �������, ����� ����������  
        // �� ���������)
        //
        $arPicture = $arElement[ "PREVIEW_PICTURE" ];
    } 
    elseif ( !empty( $arElement[ "DETAIL_PICTURE" ])) {
        //
        // ���� � ��������� ���, �� ���� �� ��� � ��������� ��������
        //
        $arPicture = $arElement[ "DETAIL_PICTURE" ];
    }
    else { 
        // ... �� ������ ����� ��� ��������
    }


    $arFilter      = array( array( "name" => "sharpen", "precision" => 15 ));
    $arPreviewSize = array( "width" => 140, "height" => 140 );
    if ( !empty( $arPicture )) {
        //
        // ��������� � �������
        //
        $arFileTmp = CFile::ResizeImageGet(
            $arPicture,
            $arPreviewSize,
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true, $arFilter
        );

        $arResult[ "ITEMS" ][ $key ][ "PREVIEW_PICTURE" ] = array(
            "SRC"     => $arFileTmp[ "src" ],
            'WIDTH'   => $arFileTmp[ "width" ],
            'HEIGHT'  => $arFileTmp[ "height" ],
        );    
    }
    else {
        //  
        // ���� ��� �������� - ����������� ��������
        // 
        $arResult[ "ITEMS" ][ $key ][ "PREVIEW_PICTURE" ] = array(
            "SRC"     => $this->__folder."/images/none.png",
            'WIDTH'   => 136,
            'HEIGHT'  => 136,
        ); 
    }

    //echo '<pre>'; print_r( $arElement ); echo '</pre>';

}

?>
