<?
use Bitrix\Main\ModuleManager,
    Bitrix\Main\EventManager,
    Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

class sva_tinypng extends CModule
{
    var $MODULE_ID = "sva.tinypng";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME = "";

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = GetMessage("SVA_TINY_PNG_PARTNER_NAME");
        $this->PARTNER_URI = 'http://vyacheslav.kz/';

        $this->MODULE_NAME = GetMessage("SVA_TINY_PNG_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("SVA_TINY_PNG_MODULE_DESC");
    }



    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;

        ModuleManager::registerModule($this->MODULE_ID);

        $this->RegisterEvents();
        $this->InstallDB();
        $this->InstallFiles();

        $APPLICATION->IncludeAdminFile(GetMessage("SVA_TINY_PNG_INSTALL"), $DOCUMENT_ROOT."/bitrix/modules/sva.tinypng/install/step.php");

        return true;
    }

    public function InstallDB() {
        global $DB, $DBType, $APPLICATION;

        $errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID.'/install/db/'.$DBType.'/install.sql');
        if (!empty($errors)) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }
    }

    function RegisterEvents(){

        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, "Sva\\TinyPng\\TinyPNGClient", "OnAfterIBlockElementUpdate");
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "Sva\\TinyPng\\TinyPNGClient", "OnAfterIBlockElementUpdate");

        $eventManager->registerEventHandler("iblock", "OnAfterIBlockSectionAdd", $this->MODULE_ID, "Sva\\TinyPng\\TinyPNGClient", "OnAfterIBlockElementUpdate");
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockSectionUpdate", $this->MODULE_ID, "Sva\\TinyPng\\TinyPNGClient", "OnAfterIBlockSectionUpdate");

        $eventManager->registerEventHandler("main", "OnFileSave", $this->MODULE_ID, "Sva\\TinyPng\\TinyPNGClient", "OnFileSave");
        return true;
    }

    public function InstallFiles() {

        CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/themes/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);

    }


    /*******************************************************************************************************************/
    /*                                              UNINSTALL                                                          */
    /*******************************************************************************************************************/
    function DoUninstall() {

        global $DOCUMENT_ROOT, $APPLICATION;

        if ($_REQUEST['step'] < 2) {
            $this->ShowDataSaveForm();
        } elseif ($_REQUEST['step'] == 2) {
            $this->UnInstallDB();
            $this->UnRegisterEvents();
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));
        }

        return true;
    }

    public function UnInstallDB() {
        global $DB, $DBType;
        if ($_REQUEST['savedata'] != 'Y') {
            $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.$DBType.'/uninstall.sql');
        }
    }

    function UnRegisterEvents(){
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main","OnFileSave",$this->MODULE_ID);
        return true;
    }

    public function UnInstallFiles() {

        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/themes/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);

    }


    /*******************************************************************************************************************/
    /*                                              UTILS                                                              */
    /*******************************************************************************************************************/
    private function ShowForm($type, $message, $buttonName='') {

        global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

        $keys = array_keys($GLOBALS);
        for($i=0; $i<count($keys); $i++) {
            if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath') {
                global ${$keys[$i]};
            }
        }

        $PathInstall = str_replace('\\', '/', __FILE__);
        $PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
        IncludeModuleLangFile($PathInstall.'/install.php');

        $GLOBALS['APPLICATION']->SetTitle(GetMessage('ASD_SEO_MODULE_NAME'));
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
        echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
        ?>
        <form action="<?= $GLOBALS['APPLICATION']->GetCurPage()?>" method="get">
            <p>
                <input type="hidden" name="lang" value="<?= LANG?>" />
                <input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
            </p>
        </form>
        <?
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
        die();
    }

    private function ShowDataSaveForm()
    {
        global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

        $keys = array_keys($GLOBALS);
        for($i=0; $i<count($keys); $i++) {
            if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath') {
                global ${$keys[$i]};
            }
        }

        $PathInstall = str_replace('\\', '/', __FILE__);

        $PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
        IncludeModuleLangFile($PathInstall.'/install.php');

        $GLOBALS['APPLICATION']->SetTitle(GetMessage('SVA_TINY_PNG_MODULE_NAME'));
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
        ?>
        <form action="<?= $GLOBALS['APPLICATION']->GetCurPage()?>" method="get">
            <?= bitrix_sessid_post()?>
            <input type="hidden" name="lang" value="<?= LANG?>" />
            <input type="hidden" name="id" value="<?= $this->MODULE_ID?>" />
            <input type="hidden" name="uninstall" value="Y" />
            <input type="hidden" name="step" value="2" />
            <?CAdminMessage::ShowMessage(GetMessage('MOD_UNINST_WARN'))?>
            <p><?echo GetMessage('MOD_UNINST_SAVE')?></p>
            <p>
                <input type="checkbox" name="savedata" id="savedata" value="Y" checked="checked" />
                <label for="savedata"><?echo GetMessage('MOD_UNINST_SAVE_TABLES')?></label><br />
            </p>
            <input type="submit" name="inst" value="<?echo GetMessage('MOD_UNINST_DEL')?>" />
        </form>
        <?
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
        die();
    }
}