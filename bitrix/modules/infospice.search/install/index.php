<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

IncludeModuleLangFile( __FILE__ );
define(
'MODULE_INFOSPICE_SEARCH_PATH' , substr(
    __FILE__ ,
    0 ,
    -18
)
);

class infospice_search extends CModule
{
    var $MODULE_ID = "infospice.search";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function infospice_search()
    {

        $arModuleVersion = array();

        $path = str_replace(
            "\\" ,
            "/" ,
            __FILE__
        );
        $path = substr(
            $path ,
            0 ,
            strlen( $path ) - strlen( "/index.php" )
        );
        include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage( 'MODULE_INFOSPICE_SEARCH_NAME' );
        $this->MODULE_DESCRIPTION = GetMessage( 'MODULE_INFOSPICE_SEARCH_DESCRIPTION' );

        $INFOSPICE = GetMessage( 'MODULE_INFOSPICE_NAME' );
        $this->PARTNER_NAME = "$INFOSPICE";
        $this->PARTNER_URI = "http://www.1c-bitrix.ru/partners/40433.php";
    }

    function DoInstall()
    {

        global $APPLICATION , $step;

        $step = IntVal( $step );

        if ( $step < 2 )
        {
            $APPLICATION->IncludeAdminFile(
                GetMessage( "MODULE_INFOSPICE_SEARCH_INSTALL_TITLE" ) ,
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/infospice.search/install/step1.php"
            );
        }
        else
        {
            if ( $_REQUEST["search_install"] == "Y" )
            {
                $this->InstallFiles();
                $this->InstallDB( false );
                $this->InstallEvents();
                $this->InstallPublic();
                $this->installForm();
                $this->installNotif();

                $APPLICATION->IncludeAdminFile(
                    GetMessage( "MODULE_INFOSPICE_SEARCH_INSTALL_TITLE" ) ,
                    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/infospice.search/install/step2.php"
                );
            }
            else
            {
                LocalRedirect( SITE_SERVER_NAME . "/bitrix/admin/module_admin.php" );
            }
        }
    }

    function DoUninstall()
    {

        global $APPLICATION , $step;

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $APPLICATION->IncludeAdminFile(
            GetMessage( "SCOM_UNINSTALL_TITLE" ) ,
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/infospice.search/install/unstep.php"
        );
    }


    function InstallDB()
    {

        global $DB , $DBType , $APPLICATION;

        RegisterModule( "infospice.search" );

        return true;

    }

    function UnInstallDB( $arParams = Array() )
    {

        global $DB , $DBType , $APPLICATION;

        UnRegisterModuleDependences(
            "main" ,
            "OnBeforeProlog" ,
            "infospice.search" ,
            "InfospiceSearch" ,
            "ShowPanel"
        );
        UnRegisterModule( 'infospice.search' );

        return true;
    }

    function InstallEvents()
    {

        RegisterModuleDependences(
            "search" ,
            "BeforeIndex" ,
            "infospice.search" ,
            "\Infospice\Search\Handlers" ,
            "BeforeIndexHandler"
        );
        return true;
    }

    function UnInstallEvents()
    {

        UnRegisterModuleDependences(
            "search" ,
            "BeforeIndex" ,
            "infospice.search" ,
            "\Infospice\Search\Handlers" ,
            "BeforeIndexHandler"
        );
        return true;
    }

    function installForm()
    {

        if ( CModule::IncludeModule( "form" ) )
        {
            $arFilter = array(
                "SID" => "INFOSPICE_SEARCH_FORM"
            );
            $is_filtered = false;
            $rsForms = CForm::GetList( $by = "s_id" , $order = "desc" , $arFilter , $is_filtered );
            if ( !$arForm = $rsForms->Fetch() )
            {
                $arFields = array(
                    "NAME"   => GetMessage( "INFOSPICE_SEARCH_FORM_NAME" ) ,
                    "SID"    => "INFOSPICE_SEARCH_FORM" ,
                    "C_SORT" => 300 ,
                    "arSITE" => array( "s1" ) ,
                    "arMENU" => array( LANGUAGE_ID => GetMessage( "INFOSPICE_SEARCH_FORM_NAME" ) ) ,
                    "BUTTON" => GetMessage( "INFOSPICE_SEARCH_FORM_BUTTON" )
                );

                $FORM_ID = CForm::Set( $arFields );

                if ( $FORM_ID )
                {
                    $arTemplates = CForm::SetMailTemplate( $FORM_ID );
                    CForm::Set( array( "arMAIL_TEMPLATE" => $arTemplates ) , $FORM_ID );

                    $em = new CEventMessage;
                    $arFieldseEvent = Array(
                        "MESSAGE"       => GetMessage("INFOSPICE_SEARCH_FORM_EVENT_MESSAGE"),
                    );
                    $em->Update($arTemplates[0], $arFieldseEvent);

                    $arFieldsStatus = array(
                        "FORM_ID"             => $FORM_ID ,
                        "C_SORT"              => 100 ,
                        "ACTIVE"              => "Y" ,
                        "DEFAULT_VALUE"       => "Y" ,
                        "TITLE"               => GetMessage( "INFOSPICE_SEARCH_FORM_STATUS" ) ,
                        "arPERMISSION_VIEW"   => array( 2 ) ,
                        "arPERMISSION_MOVE"   => array( 2 ) ,
                        "arPERMISSION_EDIT"   => array( 2 ) ,
                        "arPERMISSION_DELETE" => array( 2 ) ,
                    );
                    CFormStatus::Set( $arFieldsStatus );

                    $arQuestions = array(
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_NAME" ) ,
                            "REQUIRED" => "Y" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"     => " " ,
                                    "C_SORT"      => 100 ,
                                    "ACTIVE"      => "Y" ,
                                    "FIELD_TYPE"  => "text" ,
                                    "FIELD_PARAM" => 'placeholder="' . GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_NAME" ) . '*"'
                                )
                            )
                        ) ,
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_MAIL" ) ,
                            "REQUIRED" => "Y" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"     => " " ,
                                    "C_SORT"      => 100 ,
                                    "ACTIVE"      => "Y" ,
                                    "FIELD_TYPE"  => "text" ,
                                    "FIELD_PARAM" => 'placeholder="' . GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_MAIL" ) . 'l*"'
                                )
                            )
                        ) ,
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_PHONE" ) ,
                            "REQUIRED" => "N" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"     => " " ,
                                    "C_SORT"      => 100 ,
                                    "ACTIVE"      => "Y" ,
                                    "FIELD_TYPE"  => "text" ,
                                    "FIELD_PARAM" => 'placeholder="' . GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_PHONE" ) . '"'
                                )
                            )
                        ) ,
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_TIME" ) ,
                            "REQUIRED" => "N" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"     => " " ,
                                    "C_SORT"      => 100 ,
                                    "ACTIVE"      => "Y" ,
                                    "FIELD_TYPE"  => "text" ,
                                    "FIELD_PARAM" => 'placeholder="' . GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_TIME" ) . '"'
                                )
                            )
                        ) ,
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_QUERY" ) ,
                            "REQUIRED" => "È" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_QUERY" ) ,
                                    "C_SORT"     => 100 ,
                                    "ACTIVE"     => "Y" ,
                                    "FIELD_TYPE" => "hidden" ,
                                )
                            )
                        ) ,
                        array(
                            "TITLE"    => GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_DESC" ) ,
                            "REQUIRED" => "È" ,
                            "arANSWER" => array(
                                array(
                                    "MESSAGE"     => " " ,
                                    "C_SORT"      => 100 ,
                                    "ACTIVE"      => "Y" ,
                                    "FIELD_TYPE"  => "textarea" ,
                                    "FIELD_PARAM" => 'placeholder="' . GetMessage( "INFOSPICE_SEARCH_FORM_F_NAME_DESC" ) . '"'
                                )
                            )
                        ) ,
                    );

                    foreach ( $arQuestions as $keyQ => $question )
                    {
                        $arFields = array(
                            "FORM_ID"              => $FORM_ID ,
                            "ACTIVE"               => "Y" ,
                            "TITLE_TYPE"           => "text" ,
                            "SID"                  => "INFOSPICE_SEARCH_FORM_" . $keyQ ,
                            "arFILTER_ANSWER_TEXT" => array( "dropdown" ) ,
                        );
                        $arFields["C_SORT"] = ($keyQ + 1) * 100;

                        $arFields = array_merge( $arFields , $question );

                        CFormField::Set( $arFields );
                    }
                }
            }
        }

    }

    function installNotif()
    {
        \CAdminNotify::add(
            array(
                "MESSAGE"        => GetMessage(
                    "IBLOCK_NOTIFY_PROPERTY_REINDEX" ,
                    array(
                        "#LINK#" => "/bitrix/admin/search_reindex.php?lang="
                                    . \Bitrix\Main\Application::getInstance()
                                        ->getContext()->getLanguage() ,
                    )
                ) ,
                "TAG"            => "inf_search_reindex" ,
                "MODULE_ID"      => "infospice.search" ,
                "ENABLE_CLOSE"   => "Y" ,
                "PUBLIC_SECTION" => "N" ,
            )
        );
    }

    function InstallFiles()
    {

        if ( !CopyDirFiles(
            MODULE_INFOSPICE_SEARCH_PATH . '/install/components' ,
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components" ,
            true ,
            true
        )
        )
        {
            throw new Exception(
                'Rights violation: Can not copy components files to ' . $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components"
            );
        }
        if ( !CopyDirFiles(
            MODULE_INFOSPICE_SEARCH_PATH . '/install/templates/infospice.search' ,
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/infospice.search" ,
            true ,
            true
        )
        )
        {
            throw new Exception(
                'Rights violation: Can not copy components files to ' . $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components"
            );
        }
        if ( !CopyDirFiles(
            MODULE_INFOSPICE_SEARCH_PATH . '/install/templates/pagenavigation' ,
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/system.pagenavigation" ,
            true ,
            true
        )
        )
        {
            throw new Exception(
                'Rights violation: Can not copy components files to ' . $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components"
            );
        }
        return true;
    }

    function InstallPublic()
    {
    }

    function UnInstallFiles()
    {

        DeleteDirFilesEx( "/bitrix/components/infospice.search" );
        return true;
    }
}

?>