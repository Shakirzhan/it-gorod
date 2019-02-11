<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();
IncludeModuleLangFile( __FILE__ );

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    'infospice.search' ,
    array(
        '\Infospice\Search\Handlers' => 'lib/handlers.php' ,
    )
);