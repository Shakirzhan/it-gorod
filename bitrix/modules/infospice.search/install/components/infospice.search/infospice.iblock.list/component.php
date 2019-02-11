<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

if ( $arParams["NEW_VERSION"] != "Y" )
{
    require $_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/infospice.search/infospice.search.page/include/function.php';
    include "component_old.php";
}
else
{
    if ( count( $arParams["ARRAY_ELEMENT"] ) > 0 )
    {
        $arResult = $arParams["ARRAY_ELEMENT"];
    }
}


$this->IncludeComponentTemplate();
?>
