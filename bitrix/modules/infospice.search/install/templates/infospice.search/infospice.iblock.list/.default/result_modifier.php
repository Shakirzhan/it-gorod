<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


// $arResult[ "IBLOCK_ID"  ] = $iblockId    = ( !empty( $_GET[ "iblock" ]  )) ? ( int )$_GET[ "iblock" ]  : 0;
// $arResult[ "SECTION_ID" ] = $sectionId   = ( !empty( $_GET[ "section" ] )) ? ( int )$_GET[ "section" ] : 0;
// $arResult[ "USER_QUERY" ] = $userQuery   = ( !empty( $_GET[ "q" ] ))       ?  trim( $_GET[ "q" ])      : "";
// $arResult[ "PAGE_TYPE"  ] = $pageType    = ( !empty( $_GET[ "page" ] ))    ?  trim( $_GET[ "page" ])   : "";
// $arResult[ "IS_STATIC"  ] = $isStatic    = ( boolean )( $pageType == "static" );
// $arResult[ "URL" ]        = $url         = $APPLICATION->GetCurPage()."?q=".$arResult[ "USER_QUERY" ];

 
// if ( $iblockId > 0 )
// {
// 	$res = CIBlock::GetByID( $iblockId );
// 	if( $arIBlock = $res->GetNext())
// 		$arResult[ "IBLOCK_NAME"  ] = $arIBlock['NAME'];
// }

$userQuery   = ( !empty( $_GET[ "q"    ] )) ? trim ( $_GET[ "q" ])  : "";
$module      = ( !empty( $_GET[ "m"    ] )) ? trim ( $_GET[ "m"  ]) : "";
$id          = ( !empty( $_GET[ "id"   ] )) ? ( int )$_GET[ "id" ]  : 0; 
$pageType    = ( !empty( $_GET[ "page" ] )) ? trim ( $_GET[ "page" ])   : "";
$isStatic    = ( boolean )( $pageType == "static" );
	
$iblockId    = ( !empty( $_GET[ "iblock" ]  )) ? ( int )$_GET[ "iblock" ]  : 0;
$sectionId   = ( !empty( $_GET[ "section" ] )) ? ( int )$_GET[ "section" ] : 0;
$userQuery   = ( !empty( $_GET[ "q" ] ))       ? trim( $_GET[ "q" ])       : "";
$pageType    = ( !empty( $_GET[ "page" ] ))    ? trim( $_GET[ "page" ])    : "";
$isStatic    = ( boolean )( $pageType == "static" );
$url         = $APPLICATION->GetCurPage()."?q=".$userQuery;

    if ( $iblockId  > 0 && $sectionId  > 0 ) $arResult[ "BACK_URL"  ] = $url."&iblock=".$iblockId;
elseif ( $iblockId  > 0 && $sectionId == 0 ) $arResult[ "BACK_URL"  ] = $url;
elseif ( $iblockId == 0 && $sectionId == 0 ) $arResult[ "BACK_URL"  ] = "";


?>