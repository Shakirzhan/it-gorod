<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/md_bitcorp_requests_callback.xml";
$iblockCode = "md_bitcorp_requests_callback_".WIZARD_SITE_ID;
$iblockCodeNative = "md_bitcorp_requests_callback";
$iblockType = "marsd_bitcorp_requests_". strtolower(WIZARD_SITE_ID);

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
	}
}

if($iblockID == false)
{
	$permissions = Array(
		"1" => "X",
		"2" => "R"
	);
	$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
	if($arGroup = $dbGroup -> Fetch())
	{
		$permissions[$arGroup["ID"]] = 'W';
	};
	
	/*
	// copy original xml file for replacing macroses in new xml file
	//to make correct iblock links
	if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile)){
		@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
	}
	CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", Array("XML_WIZARD_SITE_ID" => WIZARD_SITE_ID));
	$iblockID = WizardServices::ImportIBlockFromXML($iblockXMLFile.".back", $iblockCodeNative, $iblockType, WIZARD_SITE_ID, $permissions);

	if ($iblockID){
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			unlink($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
		}	
	} else{
		return;
	}
	*/

	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockCodeNative,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);
	

	//IBlock fields
	$iblock = new CIBlock;
	$arFields = array(
		"ACTIVE" => "Y",
		"NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
		"CODE" => $iblockCode,
		"XML_ID" => $iblockCode,
		"FIELDS" => array(
			"IBLOCK_SECTION" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			),
			"ACTIVE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE"=> "Y",
			),
			"ACTIVE_FROM" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			),
			"ACTIVE_TO" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			),
			"SORT" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "0",
			), 
			"NAME" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => "",
			), 
			"PREVIEW_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"FROM_DETAIL" => "Y",
					"SCALE" => "Y",
					"WIDTH" => "1500",
					"HEIGHT" => "1500",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 100,
					"DELETE_WITH_DETAIL" => "N",
					"UPDATE_WITH_DETAIL" => "N",
				),
			), 
			"PREVIEW_TEXT_TYPE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => "text",
			), 
			"PREVIEW_TEXT" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"DETAIL_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"SCALE" => "Y",
					"WIDTH" => "1500",
					"HEIGHT" => "1500",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 100,
				),
			), 
			"DETAIL_TEXT_TYPE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => "html",
			), 
			"DETAIL_TEXT" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"XML_ID" =>  array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"CODE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"UNIQUE" => "Y",
					"TRANSLITERATION" => "Y",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N",
				),
			),
			"TAGS" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"SECTION_NAME" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => "",
			), 
			"SECTION_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"FROM_DETAIL" => "Y",
					"SCALE" => "Y",
					"WIDTH" => "1500",
					"HEIGHT" => "1500",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 100,
					"DELETE_WITH_DETAIL" => "N",
					"UPDATE_WITH_DETAIL" => "N",
				),
			), 
			"SECTION_DESCRIPTION_TYPE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => "text",
			), 
			"SECTION_DESCRIPTION" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"SECTION_DETAIL_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"SCALE" => "Y",
					"WIDTH" => "1500",
					"HEIGHT" => "1500",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 100,
				),
			), 
			"SECTION_XML_ID" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => "",
			), 
			"SECTION_CODE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"UNIQUE" => "Y",
					"TRANSLITERATION" => "Y",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N",
				),
			), 
		),
	);

	$iblock->Update($iblockID, $arFields);
}
else
{
	$arSites = array();
	$db_res = CIBlock::GetSite($iblockID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"];
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/ajax/form.php", array("REQUESTS_CALLBACK_IBLOCK" => $iblockID));
?>