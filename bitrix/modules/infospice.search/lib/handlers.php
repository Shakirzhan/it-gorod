<?
namespace Infospice\Search;

class Handlers
{
    function BeforeIndexHandler( $arFields )
    {

        if ( \CModule::IncludeModule( "iblock" ) )
        {
            if ( $arFields["MODULE_ID"] == "iblock" )
            {
                $isSection = (strpos( $arFields["ITEM_ID"] , "S" ) === 0);
                $arFields["PARAMS"]["IS_CATALOG"] = "N";
                if ( !$isSection )
                {
                    $arSelect = array(
                        "ID" ,
                        "IBLOCK_ID" ,
                        "IBLOCK_SECTION_ID" ,
                        "PREVIEW_PICTURE" ,
                        "DETAIL_PICTURE" ,
                        "CATALOG_QUANTITY"
                    );
                    $arFilter = array(
                        "ID"            => $arFields["ITEM_ID"] ,
                        "ACTIVE"        => "Y" ,
                        "IBLOCK_ACTIVE" => "Y" ,
                        "ACTIVE_DATE"   => "Y"
                    );

                    $rsElement = \CIBlockElement::GetList( array() , $arFilter , false , false , $arSelect );
                    if ( $arElement = $rsElement->Fetch() )
                    {
                        $arFields["PARAMS"]["S_SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
                        if ( $arElement["CATALOG_QUANTITY"] != null )
                        {
                            $arFields["PARAMS"]["IS_CATALOG"] = "Y";
                        }
                        $arFields["PARAMS"]["HAS_IMAGE"] = ($arElement["PREVIEW_PICTURE"]
                                                            || $arElement["DETAIL_PICTURE"]) ? "Y" : "N";

                        $offersExist = false;
                        $isCatalog = \CModule::IncludeModule( "catalog" );

                        if ( $isCatalog )
                        {
                            $offersExist = \CCatalogSKU::GetInfoByProductIBlock( $arElement["IBLOCK_ID"] );
                        }
                        if ( $offersExist )
                        {
                            $arSelectOffer = array(
                                "ID" ,
                                "PREVIEW_PICTURE" ,
                                "DETAIL_PICTURE" ,
                                "CATALOG_QUANTITY"
                            );
                            $arFilterOffer = array(
                                '=PROPERTY_' . $offersExist['SKU_PROPERTY_ID'] => $arElement["ID"] ,
                                "ACTIVE"                                       => "Y" ,
                                "IBLOCK_ACTIVE"                                => "Y" ,
                                "ACTIVE_DATE"                                  => "Y"
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
                                if ( $arFields["PARAMS"]["HAS_QUANTITY"] != "Y" )
                                {
                                    $arFields["PARAMS"]["HAS_QUANTITY"] = ($arOffer["CATALOG_QUANTITY"] > 0) ? "Y" : "N";
                                }

                                $arFields["PARAMS"]["HAS_IMAGE"] = ($arOffer["PREVIEW_PICTURE"]
                                                                    || $arOffer["DETAIL_PICTURE"])
                                    ? "Y" : $arFields["PARAMS"]["HAS_IMAGE"];

                                if ( $arFields["PARAMS"]["HAS_PRICE"] != "Y" )
                                {
                                    $db_res = \CPrice::GetList(
                                        array() ,
                                        array(
                                            "PRODUCT_ID" => $arOffer["ID"] ,
                                            ">PRICE"     => 0
                                        )
                                    );
                                    $arFields["PARAMS"]["HAS_PRICE"] = ($db_res->Fetch()) ? "Y" : "N";
                                }

                            }
                        }
                        else
                        {
                            $arFields["PARAMS"]["HAS_QUANTITY"] = ($arElement["CATALOG_QUANTITY"] > 0) ? "Y" : "N";
                            if ( $isCatalog )
                            {
                                $db_res = \CPrice::GetList(
                                    array() ,
                                    array(
                                        "PRODUCT_ID" => $arElement["ID"] ,
                                        ">PRICE"     => 0
                                    )
                                );
                                $arFields["PARAMS"]["HAS_PRICE"] = ($db_res->Fetch()) ? "Y" : "N";
                            }
                        }
                    }
                    else
                    {
                        unset($arFields["BODY"]);
                        unset($arFields["TITLE"]);
                    }
                }

            }
        }
        return $arFields;
    }
}