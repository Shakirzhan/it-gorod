<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/md_bitcorp_catalog.xml";
$iblockCode = "md_bitcorp_catalog_".WIZARD_SITE_ID;
$iblockCodeNative = "md_bitcorp_catalog";
$iblockType = "marsd_bitcorp_". strtolower(WIZARD_SITE_ID);

// include lang messages
$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch()) $lang = $arSite["LANGUAGE_ID"];
if(!strlen($lang)) $lang = "ru";
WizardServices::IncludeServiceLang("links", $lang);

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
	
	
	// copy original xml file for replacing macroses in new xml file
	//to make correct iblock links
	if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile)){
		@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
	}
	CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", Array("XML_WIZARD_SITE_ID" => WIZARD_SITE_ID));
	CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", Array("CUSTOM_SITE_DIR" => WIZARD_SITE_DIR));
	$iblockID = WizardServices::ImportIBlockFromXML($iblockXMLFile.".back", $iblockCodeNative, $iblockType, WIZARD_SITE_ID, $permissions);

	if ($iblockID){
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			unlink($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
		}	
	} else{
		return;
	}
	
	/*
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockCodeNative,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);
	*/

	//ADD USER FIELDS
	if($iblockID){

		// add UF_SECTION_TITLE user field
		$arFields = array(
			"FIELD_NAME" => "UF_SECTION_TITLE",
			"USER_TYPE_ID" => "string",
			"XML_ID" => "UF_SECTION_TITLE",
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(
				"SIZE" => 100,
				"ROWS" => 20,
			)
		);

		$arLangs = array(
			"EDIT_FORM_LABEL"   => array(
		        "ru"    => GetMessage("UF_SECTION_TITLE"),
		        "en"    => "UF_SECTION_TITLE",
		    ),
		    "LIST_COLUMN_LABEL" => array(
		        "ru"    => GetMessage("UF_SECTION_TITLE"),
		        "en"    => "UF_SECTION_TITLE",
		    )
		);
		$arUserFieldSectionTitle = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_SECTION_TITLE"))->Fetch();
		
		if(!$arUserFieldSectionTitle)
		{
			$ob = new CUserTypeEntity();
			$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION"), $arLangs));
		}
		else
		{
			$ob = new CUserTypeEntity();
			$ob->Update($arUserFieldSectionTitle["ID"], $arLangs);
		}

		// add UF_SECTION_DESCR user field
		$arFields = array(
			"FIELD_NAME" => "UF_SECTION_DESCR",
			"USER_TYPE_ID" => "string",
			"XML_ID" => "UF_SECTION_DESCR",
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(
				"SIZE" => 100,
				"ROWS" => 100,
			)
		);

		$arLangs = array(
			"EDIT_FORM_LABEL"   => array(
		        "ru"    => GetMessage("UF_SECTION_DESCR"),
		        "en"    => "UF_SECTION_DESCR",
		    ),
		    "LIST_COLUMN_LABEL" => array(
		        "ru"    => GetMessage("UF_SECTION_DESCR"),
		        "en"    => "UF_SECTION_DESCR",
		    )
		);
		$arUserFieldSectionDescr = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_SECTION_DESCR"))->Fetch();
		
		if(!$arUserFieldSectionDescr)
		{
			$ob = new CUserTypeEntity();
			$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION"), $arLangs));
		}
		else
		{
			$ob = new CUserTypeEntity();
			$ob->Update($arUserFieldSectionDescr["ID"], $arLangs);
		}

		// add UF_BG_COLOR user field
		$arFields = array(
			"FIELD_NAME" => "UF_BG_COLOR",
			"USER_TYPE_ID" => "string",
			"XML_ID" => "UF_BG_COLOR",
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(
				"SIZE" => 100,
				"ROWS" => 1,
			)
		);
		$arLangs = array(
			"EDIT_FORM_LABEL"   => array(
		        "ru"    => GetMessage("UF_BG_COLOR"),
		        "en"    => "UF_BG_COLOR",
		    ),
		    "LIST_COLUMN_LABEL" => array(
		        "ru"    => GetMessage("UF_BG_COLOR"),
		        "en"    => "UF_BG_COLOR",
		    )
		);
		$arUserFieldSectionBgColor = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_BG_COLOR"))->Fetch();
		
		if(!$arUserFieldSectionBgColor)
		{
			$ob = new CUserTypeEntity();
			$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION"), $arLangs));
		}
		else
		{
			$ob = new CUserTypeEntity();
			$ob->Update($arUserFieldSectionBgColor["ID"], $arLangs);
		}

		// add UF_SHOW_ON_INDEX user field
		$arFields = array(
			"FIELD_NAME" => "UF_SHOW_ON_INDEX",
			"USER_TYPE_ID" => "boolean",
			"XML_ID" => "UF_SHOW_ON_INDEX",
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(
				"DEFAULT_VALUE" => "1",
				"SIZE" => 100,
				"ROWS" => 1,
			)
		);
		$arLangs = array(
			"EDIT_FORM_LABEL"   => array(
		        "ru"    => GetMessage("UF_SHOW_ON_INDEX"),
		        "en"    => "UF_SHOW_ON_INDEX",
		    ),
		    "LIST_COLUMN_LABEL" => array(
		        "ru"    => GetMessage("UF_SHOW_ON_INDEX"),
		        "en"    => "UF_SHOW_ON_INDEX",
		    )
		);
		$arUserFieldSectionShowIndex = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_SHOW_ON_INDEX"))->Fetch();
		
		if(!$arUserFieldSectionShowIndex)
		{
			$ob = new CUserTypeEntity();
			$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION"), $arLangs));
		}
		else
		{
			$ob = new CUserTypeEntity();
			$ob->Update($arUserFieldSectionShowIndex["ID"], $arLangs);
		}

		// add UF_TITLE_BG user field
		$arFields = array(
			"FIELD_NAME" => "UF_TITLE_BG",
			"USER_TYPE_ID" => "boolean",
			"XML_ID" => "UF_TITLE_BG",
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(
				"DEFAULT_VALUE" => "1",
				"SIZE" => 100,
				"ROWS" => 1,
			)
		);
		$arLangs = array(
			"EDIT_FORM_LABEL"   => array(
		        "ru"    => GetMessage("UF_TITLE_BG"),
		        "en"    => "UF_TITLE_BG",
		    ),
		    "LIST_COLUMN_LABEL" => array(
		        "ru"    => GetMessage("UF_TITLE_BG"),
		        "en"    => "UF_TITLE_BG",
		    )
		);
		$arUserFieldSectionTitleBg = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_TITLE_BG"))->Fetch();
		
		if(!$arUserFieldSectionTitleBg)
		{
			$ob = new CUserTypeEntity();
			$FIELD_ID = $ob->Add(array_merge($arFields, array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION"), $arLangs));
		}
		else
		{
			$ob = new CUserTypeEntity();
			$ob->Update($arUserFieldSectionTitleBg["ID"], $arLangs);
		}


		//add UF_SECTION_SIZE user field
		$arUserFieldViewType = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION", "FIELD_NAME" => "UF_SECTION_SIZE"))->Fetch();
		$resUserFieldViewTypeEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $arUserFieldViewType["ID"]));
		while($arUserFieldViewTypeEnum = $resUserFieldViewTypeEnum->GetNext()){
			$obEnum = new CUserFieldEnum;
			$obEnum->SetEnumValues($arUserFieldViewType["ID"], array($arUserFieldViewTypeEnum["ID"] => array("DEL" => "Y")));
		}
		$obEnum = new CUserFieldEnum;
		$obEnum->SetEnumValues($arUserFieldViewType["ID"], array(
			"n0" => array(
				"VALUE" => GetMessage("UF_SECTION_SIZE_WIDE"),
				"XML_ID" => "wide",
			),
			"n1" => array(
				"VALUE" => GetMessage("UF_SECTION_SIZE_NARROW"),
				"XML_ID" => "narrow",
			),			
		));

		$resUserFieldViewTypeEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $arUserFieldViewType["ID"]));	
		while($arUserFieldViewTypeEnum = $resUserFieldViewTypeEnum->GetNext()){
			$arUserFieldViewTypeEnums[$arUserFieldViewTypeEnum["XML_ID"]] = $arUserFieldViewTypeEnum["ID"];		
		}

		//set sections UF_SECTION_SIZE value for user prop
		$bs = new CIBlockSection;

		$arCatalogSectionsNameFilter = array(
			GetMessage("SECTION_NAME_FILTERS"),
			GetMessage("SECTION_NAME_LIGHT"),
			GetMessage("SECTION_NAME_WARM")
		);

		$SectionsList = CIBlockSection::GetList(
			$arSort,
			array("IBLOCK_ID"=> $iblockID, "NAME" => $arCatalogSectionsNameFilter),
			false,
			array("ID")
		);

		while ($SectListGet = $SectionsList->GetNext())
		{
			$res = $bs->Update($SectListGet["ID"], array("UF_SECTION_SIZE" => $arUserFieldViewTypeEnums["narrow"]));
		}


	}
	// end add user fields

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
				"IS_REQUIRED" => "Y",
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
				"IS_REQUIRED" => "Y",
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

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/.left.menu_ext.php", array("CATALOG_IBLOCK" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/indexblocks.php", array("CATALOG_IBLOCK" => $iblockID));
?>