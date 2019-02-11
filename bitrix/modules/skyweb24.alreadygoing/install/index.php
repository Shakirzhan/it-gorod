<?
IncludeModuleLangFile(__FILE__);
Class skyweb24_alreadygoing extends CModule{
	const MODULE_ID = 'skyweb24.alreadygoing';
	var $MODULE_ID = "skyweb24.alreadygoing";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function __construct(){
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage('SKWB24_AG_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('SKWB24_AG_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('SKWB24_AG_PARTNER_NAME'); 
		$this->PARTNER_URI = GetMessage('SKWB24_AG_PARTNER_URI'); 

	}
	function InstallFiles(){
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/upload", $_SERVER["DOCUMENT_ROOT"]."/upload", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		return true;
	}
	
	function UninstallFiles(){
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFilesEx("/upload/".$this->MODULE_ID);
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		return true;
	}

	function DoInstall(){
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage('SKWB24_AG_PARTNER_INSTALL'), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");
	}

	function DoUninstall(){
		global $DOCUMENT_ROOT, $APPLICATION, $DB;
		$this->UninstallFiles();
		$this->UnInstallDB();
		$DB->Query("DELETE FROM b_option WHERE MODULE_ID='".$this->MODULE_ID."'");
		$APPLICATION->IncludeAdminFile(GetMessage('SKWB24_AG_PARTNER_DEINSTALL'), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");
	}
	function InstallDB(){
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main","OnBeforeEndBufferContent", $this->MODULE_ID, "alreadygoing","insertAGBlock");
		RegisterModuleDependences("main","OnPageStart", $this->MODULE_ID, "alreadygoing","setStatistic");
		return true;
	}
	function UnInstallDB(){
		UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "alreadygoing", "insertAGBlock");
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "alreadygoing", "setStatistic");
		UnRegisterModule($this->MODULE_ID);
	}
}
?>