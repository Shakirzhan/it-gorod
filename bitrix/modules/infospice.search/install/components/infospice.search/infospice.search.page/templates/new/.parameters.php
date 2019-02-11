<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

$arTemplateParameters["TEMPLATE_STYLE"] = array(
    "PARENT"   => "VISUAL" ,
    "NAME"     => GetMessage( "TEMPLATE_STYLE" ) ,
    "TYPE"     => "LIST" ,
    "SORT"     => "10" ,
    "MULTIPLE" => "N" ,
    "DEFAULT"  => "black" ,
    "VALUES"   => array(
        "black"      => GetMessage("TP_BSP_COLOR_NAME_BLACK") ,
        "blue"       => GetMessage("TP_BSP_COLOR_NAME_BLUE") ,
        "light_grey" => GetMessage("TP_BSP_COLOR_NAME_LIGHT_GREY") ,
        "dark_grey"  => GetMessage("TP_BSP_COLOR_NAME_DARK_GREY") ,
    )
);


?>
