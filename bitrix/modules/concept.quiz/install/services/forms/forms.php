<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");
global $DB, $DBType, $APPLICATION;

use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
function CONCEPT_addEV($arEventFields=array()) {
	global $DB;
	$EventTypeID = 0;
	$et = new CEventType;
	$EventTypeID = $et->Add($arEventFields);
	return $EventTypeID;
}


$arData = array(
	'CONCEPT_UNIVERSAL_USER_INFO',
	'CONCEPT_UNIVERSAL_FOR_USER',
);

$arSites = array();
$rsSites = CSite::GetList($by="sort", $order="desc", array());
while ($arSite = $rsSites->Fetch()) {
	$arSites[] = $arSite['LID'];
}

if( is_array($arData) && count($arData)>0 ) {

	$ev = new CEventMessage;

	foreach($arData as $EVENT_TYPE) {
		$EventTypeID = 0;
		$arEventFields = array(
			'LID'           => 'ru',
			'EVENT_NAME'    => $EVENT_TYPE,
			'NAME'          => GetMessage('EVENT_NAME.'.$EVENT_TYPE),
			'DESCRIPTION'   => GetMessage('EVENT_DESCRIPTION.'.$EVENT_TYPE),
		);
		$EventTypeID = CONCEPT_addEV($arEventFields);
		if($EventTypeID>0) {
			$arTemplate = array(
				'ACTIVE' 		=> 'Y',
				'EVENT_NAME' 	=> $EVENT_TYPE,
				'LID'			=> $arSites,
				'EMAIL_FROM'	=> '#EMAIL_FROM#',
				'EMAIL_TO'		=> '#EMAIL_TO#',
				'BCC'			=> '',
				'SUBJECT'		=> GetMessage('TEMPLATE_SUBJECT.'.$EVENT_TYPE),
				'BODY_TYPE'		=> 'html',
				'MESSAGE'		=> GetMessage('TEMPLATE_MESSAGE.'.$EVENT_TYPE),
			);
			$EventTemplateID = $ev->Add($arTemplate);
		}
	}

}