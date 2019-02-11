<?
global $MESS;
use \Bitrix\Main\Config\Option;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));



Class concept_quiz extends CModule
{

	var $MODULE_ID = "concept.quiz";
	var $IBLOCK_TYPE = 'concept_quiz';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";


    function concept_quiz()
    {

        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);

		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];

		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->PARTNER_NAME = GetMessage("SCOM_QUIZ_INSTALL_PARTNER");

		$this->PARTNER_URI = "http://concept360.ru/";

		$this->MODULE_NAME = GetMessage("SCOM_QUIZ_INSTALL_NAME");

		$this->MODULE_DESCRIPTION = GetMessage("SCOM_QUIZ_INSTALL_DESCRIPTION");

		return true;

    }

	function InstallDB($install_quiz = true)
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule($this->MODULE_ID);
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->registerEventHandler("main", "OnEpilog", "concept.quiz", "CConceptWqec", "AddConceptWqec");
		$eventManager->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", "concept.quiz", "CConceptWqec", "CConceptWqecUpdateHandler");
		$eventManager->registerEventHandler("iblock", "OnAfterIBlockElementAdd", "concept.quiz", "CConceptWqec", "CConceptWqecUpdateHandler");
		$eventManager->registerEventHandler('main', 'OnEpilog',"concept.quiz", 'CConceptWqecSelectTab', 'ConceptWqecSelectTab');
		$eventManager->registerEventHandler("main", "OnEndBufferContent", "concept.quiz", "CConceptWqec", "AddSiteCquizBuf");


		


		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/install/services/iblock/iblock.php');
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/install/services/forms/forms.php');
		
		Option::set("concept.quiz", "wqec_mailfrom", "noreply@quizmail.ru");
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;
		UnRegisterModule($this->MODULE_ID);
		Option::delete($this->MODULE_ID);

		if (cModule::IncludeModule("iblock")){
			CIBlockType::Delete($this->IBLOCK_TYPE);
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->unRegisterEventHandler("main", "OnEpilog", "concept.quiz", "CConceptWqec", "AddConceptWqec");
			$eventManager->unRegisterEventHandler("iblock", "OnAfterIBlockElementUpdate", "concept.quiz", "CConceptWqec", "CConceptWqecUpdateHandler");
			$eventManager->unRegisterEventHandler("iblock", "OnAfterIBlockElementAdd", "concept.quiz", "CConceptWqec", "CConceptWqecUpdateHandler");
			$eventManager->unRegisterEventHandler('main', 'OnEpilog',"concept.quiz", 'CConceptWqecSelectTab', 'ConceptWqecSelectTab');

			$eventManager->unRegisterEventHandler("main", "OnEndBufferContent", "concept.quiz", "CConceptWqec", "AddSiteCquizBuf");

			$arData = array(
	    	   'CONCEPT_UNIVERSAL_USER_INFO',
	           'CONCEPT_UNIVERSAL_FOR_USER'
	        );
	        
	        
	        foreach($arData as $EVENT_TYPE) 
	        {
	            $arFilter = Array(
	                "TYPE_ID" => array($EVENT_TYPE),
	            );
	                
	            $rsMess = CEventMessage::GetList($by="site_id", $order="asc", $arFilter);
	            
	            while($arMess = $rsMess->Fetch())
	            {
	                $em = new CEventMessage;
	    
	                $DB->StartTransaction();
	                
	                if(!$em->Delete(intval($arMess["ID"])))
	                    $DB->Rollback();
	                else 
	                    $DB->Commit();
	            }
	            
	            $et = new CEventType;
	            $et->Delete($EVENT_TYPE);
	        
	        }
		}
		else return false;
		return true;
	}


	function InstallFiles()
	{
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/concept.quiz/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/concept.quiz/install/files/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/concept.quiz/install/files/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/concept.quiz/install/files/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);

		return true;
	}

	function UnInstallFiles()
	{        
		DeleteDirFilesEx('/bitrix/components/concept/conceptquiz/');
		DeleteDirFilesEx('/bitrix/components/concept/conceptquiz.list/');
		DeleteDirFilesEx("/bitrix/js/concept.quiz");
        DeleteDirFilesEx("/bitrix/css/concept.quiz");
        DeleteDirFilesEx("/bitrix/admin/concept_quiz.php");
		return true;
	}

	function InstallPublic()
	{
		return true;
	}

	function UnInstallPublic()
	{
		return true;
	}


    
    function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}


	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallDB(false);
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallPublic();
	
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
        $this->UnInstallPublic();
	}
}
?>
