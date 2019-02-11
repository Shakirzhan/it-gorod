<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//echo "<pre>"; print_r($arParams); echo "</pre>";

$arBasketItems = array();

if ( $arParams[ "CHECK_ITEMS_ON_BASKET" ] == "Y" && CModule::IncludeModule("sale"))
{
    // Получим актуальную корзину для текущего пользователя
    $dbBasketItems = CSaleBasket::GetList(
            array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
            array(
                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL"
                ),
            false,
            false,
            array("ID", "PRODUCT_ID", "QUANTITY", "NAME" )
        );
    while ($arItems = $dbBasketItems->Fetch())
        $arBasketItems[ $arItems[ "PRODUCT_ID" ]] = $arItems;
    
    //echo "<pre>"; print_r($arBasketItems); echo "</pre>";
}

foreach ($arResult["ITEMS"] as $key => &$arElement) 
{
    if ( count( $arElement['OFFERS'] ) && !$arElement['PRICES'] )
    {
        $arFirstOffer          = $arElement['OFFERS'][0];
        $arElement['PRICES'][] = $arFirstOffer['MIN_PRICE'];
    }

    $arElement["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
    
    $arElement["BUY_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
    
    $arElement["ALREADY_ON_BASKET"] = ( array_key_exists( $arElement[ "ID" ], $arBasketItems )) ? "Y" : "N";


    $arPicture = array();
    if ( !empty( $arElement[ "PREVIEW_PICTURE" ])) 
    {
        // 
        // если у элемента уже есть картинка в аннотации, 
        // то берём её (она запросто, по задумке, может отличаться  
        // от детальной)
        //
        $arPicture = $arElement[ "PREVIEW_PICTURE" ];
    } 
    elseif ( !empty( $arElement[ "DETAIL_PICTURE" ])) 
    {
        //
        // если в аннотации нет, то берём ту что в детальном описании
        //
        $arPicture = $arElement[ "DETAIL_PICTURE" ];
    }
    else 
    { 
        // ... ну значит будем без картинки
    }


    $arFilter      = array( array( "name" => "sharpen", "precision" => 15 ));
    $arPreviewSize = array( "width" => 128, "height" => 128 );
    if ( !empty( $arPicture )) 
    {
        //
        // уменьшаем и кэширем
        //
        $arFileTmp = CFile::ResizeImageGet(
            $arPicture,
            $arPreviewSize,
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true, 
            $arFilter
        );

        $arResult[ "ITEMS" ][ $key ][ "PREVIEW_PICTURE" ] = array(
            "SRC"     => $arFileTmp[ "src" ],
            'WIDTH'   => $arFileTmp[ "width" ],
            'HEIGHT'  => $arFileTmp[ "height" ],
        );    
    }
    else 
    {
        //  
        // если нет картинки - подставляем пустышку
        // 
        $arResult[ "ITEMS" ][ $key ][ "PREVIEW_PICTURE" ] = array(
            "SRC"     => $this->__folder."/images/none.png",
            'WIDTH'   => 128,
            'HEIGHT'  => 128,
        ); 
    }

    $arProperties = array();
    if ( !empty( $arParams[ "LIST_PROPERTY_CODE" ] ))
    {
        foreach ( $arParams[ "LIST_PROPERTY_CODE" ] as $key => $code ) 
        {
            if ( !empty( $code ))
            {
                $dbRes = CIBlockElement::GetProperty( $arElement[ "IBLOCK_ID" ], $arElement[ "ID" ], array(), array(  "CODE" => $code ));
                while ( $arProperty = $dbRes->Fetch())
                {
                    if ( !empty( $arProperty[ "VALUE"] ))
                    {   
                        $arProperties[] = $arProperty;
                    }
                }
            }
        }
    }
    $arElement[ "PROPERTIES" ] = $arProperties;

    //echo '<pre>'; print_r( $arElement ); echo '</pre>';
    unset( $arElement );
}

?>