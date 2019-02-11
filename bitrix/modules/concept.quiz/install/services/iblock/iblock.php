<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

if(!CModule::IncludeModule("iblock"))
    return;
$iblockTYPE = "concept_quiz";
$ibRes =  CIBlockType::GetList(array("SORT"=>"ASC"), array("ID"=>$iblockTYPE));
if (!($iblockType = $ibRes->GetNext())){
    $ibType = new CIBlockType();
    $arFields = Array(
        'ID'=>$iblockTYPE,
        'SECTIONS'=>'Y',
        'IN_RSS'=>'N',
        'SORT'=>100,
        'LANG'=>Array(
            'ru'=>Array(
                'NAME'=>GetMessage("QUIZ_IBLOCK_TYPE_NAME"),
                'SECTION_NAME'=>GetMessage("QUIZ_IBLOCK_SECTION_NAME"),
                'ELEMENT_NAME'=>GetMessage("QUIZ_IBLOCK_ELEMENT_NAME")
                )
            )
        );
        $ibType->Add($arFields);
}

$arSites = array();
$rsSites = CSite::GetList($by="sort", $order="desc");
while ($arSite = $rsSites->Fetch())
{
  $arSites[] = $arSite["LID"];
}

// QUESTS
$iblockXMLFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/install/services/iblock/xml/ru/quiz.xml';
$iblockCode = "concept_quiz_questions";
$iblockType = $iblockTYPE;

    $permissions = Array(
            "1" => "X",
            "2" => "R"
        );
    $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
    if($arGroup = $dbGroup -> Fetch())
    {
        $permissions[$arGroup["ID"]] = 'W';
    };
    $iblockID = WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockCode,
        $iblockType,
        $arSites,
        $permissions
    );

    if ($iblockID < 1)
        return;

    //IBlock fields
    $iblock = new CIBlock;
    $arFields = Array(
        "ACTIVE" => "Y",
        "FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
        "CODE" => $iblockCode, 
        "XML_ID" => $iblockCode,
        "NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
    );
    
    $iblock->Update($iblockID, $arFields);

    $quiz_IB = $iblockID;


 // iblock user fields

    $arProperty = array();
    $dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
    while($arProp = $dbProperty->Fetch())
        $arProperty[$arProp["CODE"]] = $arProp["ID"];

    $arUF = array();
    $rsData = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID"=>"IBLOCK_".$iblockID."_SECTION"));
    while($arRes = $rsData->Fetch())
        $arUF[$arRes["FIELD_NAME"]] = $arRes["ID"];


    $section_tabs = 'edit1--#--'.GetMessage("CQUIZ_SECT_1").'--,--'.'ACTIVE--#--'.GetMessage("CQUIZ_SECT_2").'--,--'.'UF_SEND_RES--#--'.GetMessage("CQUIZ_SECT_3").'--,--'.'UF_TYPE_CALC--#--'.GetMessage("CQUIZ_SECT_4").'--,--'.'NAME--#--'.GetMessage("CQUIZ_SECT_5").'--,--'.'UF_CWIZ_TITLE--#--'.GetMessage("CQUIZ_SECT_6").'--,--'.'UF_CWIZ_TYPE_VIEW--#--'.GetMessage("CQUIZ_SECT_7").'--,--'.'PICTURE--#--'.GetMessage("CQUIZ_SECT_8").'--,--'.'UF_CWIZ_PROGR_VIEW--#--'.GetMessage("CQUIZ_SECT_9").'--,--'.'UF_USER_BTN_RESULT--#--'.GetMessage("CQUIZ_SECT_10").'--,--'.'cedit1_csection1--#----'.GetMessage("CQUIZ_SECT_11").'--,--'.'DETAIL_PICTURE--#--'.GetMessage("CQUIZ_SECT_12").'--,--'.'UF_CWIZ_DESC--#--'.GetMessage("CQUIZ_SECT_13").'--,--'.'UF_CWIZARD_PIC--#--'.GetMessage("CQUIZ_SECT_14").'--,--'.'UF_CWIZARD_NAME--#--'.GetMessage("CQUIZ_SECT_15").'--,--'.'UF_CWIZARD_PROF--#--'.GetMessage("CQUIZ_SECT_16").'--,--'.'edit1_csection1--#----'.GetMessage("CQUIZ_SECT_17").'--,--'.'UF_QUIZ_MASK--#--'.GetMessage("CQUIZ_SECT_18").'--,--'.'UF_QUIZ_USER_MASK--#--'.GetMessage("CQUIZ_SECT_19").'--;--'.'cedit2--#--'.GetMessage("CQUIZ_SECT_20").'--,--'.'UF_CWIZ_SHARE--#--'.GetMessage("CQUIZ_SECT_21").'--,--'.'UF_CWIZ_SHARE_TEXT--#--'.GetMessage("CQUIZ_SECT_22").'--,--'.'DESCRIPTION--#--'.GetMessage("CQUIZ_SECT_23").'--,--'.'cedit2_csection2--#----'.GetMessage("CQUIZ_SECT_24").'--,--'.'UF_CWIZ_YA_ID--#--'.GetMessage("CQUIZ_SECT_25").'--,--'.'UF_ID_GOOGLE--#--'.GetMessage("CQUIZ_SECT_26").'--,--'.'UF_ID_GTM--#--'.GetMessage("CQUIZ_SECT_27").'--,--'.'cedit2_csection1--#----'.GetMessage("CQUIZ_SECT_28").'--,--'.'UF_CWIZ_GOAL_BEGIN--#--'.GetMessage("CQUIZ_SECT_29").'--,--'.'UF_GA_GOAL_CATEGORY--#--'.GetMessage("CQUIZ_SECT_30").'--,--'.'UF_GA_GOAL_ACTION--#--'.GetMessage("CQUIZ_SECT_31").'--,--'.'UF_GTM_GOAL_CATEGORY--#--'.GetMessage("CQUIZ_SECT_32").'--,--'.'UF_GTM_GOAL_ACTION--#--'.GetMessage("CQUIZ_SECT_33").'--,--'.'UF_GTM_EVENT--#--'.GetMessage("CQUIZ_SECT_34").'--;--'.'cedit1--#--'.GetMessage("CQUIZ_SECT_35").'--,--'.'cedit1_csection1--#----'.GetMessage("CQUIZ_SECT_36").'--,--'.'UF_CWIZ_ADMIN_MAIL--#--'.GetMessage("CQUIZ_SECT_37").'--,--'.'UF_CWIZ_FROM_MAIL--#--'.GetMessage("CQUIZ_SECT_38").'--,--'.'cedit1_csection2--#----'.GetMessage("CQUIZ_SECT_39").'--,--'.'UF_SAVE_IN_IB--#--'.GetMessage("CQUIZ_SECT_40").'--,--'.'cedit1_csection3--#----'.GetMessage("CQUIZ_SECT_41").'--,--'.'UF_BX_ON--#--'.GetMessage("CQUIZ_SECT_42").'--,--'.'UF_BX_URL--#--'.GetMessage("CQUIZ_SECT_43").'--,--'.'UF_BX_LOGIN--#--'.GetMessage("CQUIZ_SECT_44").'--,--'.'UF_BX_PASSWORD--#--'.GetMessage("CQUIZ_SECT_45").'--,--'.'cedit1_csection4--#----AmoCRM--,--'.'UF_AMO_ON--#--'.GetMessage("CQUIZ_SECT_46").'--,--'.'UF_AMO_URL--#--'.GetMessage("CQUIZ_SECT_47").'--,--'.'UF_AMO_LOGIN--#--'.GetMessage("CQUIZ_SECT_44").'--,--'.'UF_AMO_HASH--#--'.GetMessage("CQUIZ_SECT_49").'--;--'.'cedit3--#--'.GetMessage("CQUIZ_SECT_50").'--,--'.'cedit3_csection1--#----'.GetMessage("CQUIZ_SECT_51").'--,--'.'UF_BORD_ON--#--'.GetMessage("CQUIZ_SECT_52").'--,--'.'UF_BORD_COLOR--#--'.GetMessage("CQUIZ_SECT_53").'--,--'.'UF_BORD_WIDTH--#--'.GetMessage("CQUIZ_SECT_54").'--,--'.'cedit3_csection2--#----'.GetMessage("CQUIZ_SECT_55").'--,--'.'UF_BLOCK_SHADOW--#--'.GetMessage("CQUIZ_SECT_56").'--,--'.'UF_BLOCK_ELIPS--#--'.GetMessage("CQUIZ_SECT_57").'--,--'.'UF_BLOCK_BG_OPACITY--#--'.GetMessage("CQUIZ_SECT_58").'--,--'.'UF_QUIZ_BG_TYPE--#--'.GetMessage("CQUIZ_SECT_59").'--;--';


    
    // edit form user oprions    
    CUserOptions::SetOption("form", "form_section_".$iblockID, array(
            "tabs" => $section_tabs
        ),
        true
    );


    
    // edit form user oprions

    $element_tabs = 'cedit1--#--'.GetMessage("CQUIZ_ELEM_51").'--,--'.'ACTIVE--#--'.GetMessage("CQUIZ_ELEM_52").'--,--'.'SORT--#--'.GetMessage("CQUIZ_ELEM_53").'--,--'.'NAME--#--'.GetMessage("CQUIZ_ELEM_54").'--,--'.'PROPERTY_'.$arProperty["TYPE_ELEMENT"].'--#--'.GetMessage("CQUIZ_ELEM_2").'--;--'.'cedit2--#--'.GetMessage("CQUIZ_ELEM_55").'--,--'.'PROPERTY_'.$arProperty["QUEST"].'--#--'.GetMessage("CQUIZ_ELEM_12").'--,--'.'PROPERTY_'.$arProperty["QUEST_SUBTITLE"].'--#--'.GetMessage("CQUIZ_ELEM_9").'--,--'.'PROPERTY_'.$arProperty["QUEST_TYPE"].'--#--'.GetMessage("CQUIZ_ELEM_10").'--,--'.'PROPERTY_'.$arProperty["QUEST_ANSWER"].'--#--'.GetMessage("CQUIZ_ELEM_11").'--,--'.'PROPERTY_'.$arProperty["QUEST_PICTURES"].'--#--'.GetMessage("CQUIZ_ELEM_3").'--,--'.'PROPERTY_'.$arProperty["QUEST_COUNT"].'--#--'.GetMessage("CQUIZ_ELEM_13").'--,--'.'PROPERTY_'.$arProperty["QUEST_ANSWER_USER"].'--#--'.GetMessage("CQUIZ_ELEM_6").'--,--'.'PROPERTY_'.$arProperty["QUEST_ANSWER_USER_DESC"].'--#--'.GetMessage("CQUIZ_ELEM_5").'--,--'.'PROPERTY_'.$arProperty["QUEST_SKIP"].'--#--'.GetMessage("CQUIZ_ELEM_14").'--,--'.'PROPERTY_'.$arProperty["ONE_CLICK_NEXT"].'--#--'.GetMessage("CQUIZ_ELEM_1").'--,--'.'cedit2_csection1--#----'.GetMessage("CQUIZ_ELEM_56").'--,--'.'PROPERTY_'.$arProperty["QUEST_COMMENT"].'--#--'.GetMessage("CQUIZ_ELEM_8").'--,--'.'PROPERTY_'.$arProperty["QUEST_VIDEO"].'--#--'.GetMessage("CQUIZ_ELEM_4").'--,--'.'PROPERTY_'.$arProperty["QUEST_VIDEO_PIC"].'--#--'.GetMessage("CQUIZ_ELEM_7").'--,--'.'PROPERTY_'.$arProperty["QUEST_VIDEO_DESC"].'--#--'.GetMessage("CQUIZ_ELEM_15").'--;--'.'cedit3--#--'.GetMessage("CQUIZ_ELEM_57").'--,--'.'cedit3_csection1--#----'.GetMessage("CQUIZ_ELEM_58").'--,--'.'PROPERTY_'.$arProperty["RESULT_MIN_VALUE"].'--#--'.GetMessage("CQUIZ_ELEM_16").'--,--'.'PROPERTY_'.$arProperty["RESULT_MAX_VALUE"].'--#--'.GetMessage("CQUIZ_ELEM_17").'--,--'.'PROPERTY_'.$arProperty["RESULT_SHOW_USERS_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_26").'--,--'.'cedit3_csection7--#----'.GetMessage("CQUIZ_ELEM_59").'--,--'.'PROPERTY_'.$arProperty["RESULT_SYMBOLS"].'--#--'.GetMessage("CQUIZ_ELEM_40").'--,--'.'cedit3_csection2--#----'.GetMessage("CQUIZ_ELEM_60").'--,--'.'PROPERTY_'.$arProperty["RESULT_MAIN_TITLE"].'--#--'.GetMessage("CQUIZ_ELEM_27").'--,--'.'PROPERTY_'.$arProperty["RESULT_MAIN_TEXT"].'--#--'.GetMessage("CQUIZ_ELEM_23").'--,--'.'PROPERTY_'.$arProperty["RESULT_VIDEO"].'--#--'.GetMessage("CQUIZ_ELEM_24").'--,--'.'PROPERTY_'.$arProperty["RESULT_PICTURE"].'--#--'.GetMessage("CQUIZ_ELEM_31").'--,--'.'PROPERTY_'.$arProperty["RESULT_PICTURE_COVER"].'--#--'.GetMessage("CQUIZ_ELEM_32").'--,--'.'cedit3_csection3--#----'.GetMessage("CQUIZ_ELEM_61").'--,--'.'PROPERTY_'.$arProperty["SPECIAL_ONTITLE"].'--#--'.GetMessage("CQUIZ_ELEM_35").'--,--'.'PROPERTY_'.$arProperty["SPECIAL_TEXT"].'--#--'.GetMessage("CQUIZ_ELEM_37").'--,--'.'PROPERTY_'.$arProperty["SPECIAL_PICTURE"].'--#--'.GetMessage("CQUIZ_ELEM_36").'--,--'.'cedit3_csection4--#----'.GetMessage("CQUIZ_ELEM_62").'--,--'.'PROPERTY_'.$arProperty["RESULT_FORM_TITLE"].'--#--'.GetMessage("CQUIZ_ELEM_25").'--,--'.'PROPERTY_'.$arProperty["RESULT_FORM_SUBTITLE"].'--#--'.GetMessage("CQUIZ_ELEM_19").'--,--'.'PROPERTY_'.$arProperty["RESULT_INPUTS"].'--#--'.GetMessage("CQUIZ_ELEM_21").'--,--'.'PROPERTY_'.$arProperty["RESULT_INPUTS_REQ"].'--#--'.GetMessage("CQUIZ_ELEM_18").'--,--'.'PROPERTY_'.$arProperty["RESULT_FORM_ALL_INPUTS"].'--#--'.GetMessage("CQUIZ_ELEM_34").'--,--'.'PROPERTY_'.$arProperty["RESULT_BUTTON_NAME"].'--#--'.GetMessage("CQUIZ_ELEM_28").'--,--'.'PROPERTY_'.$arProperty["RESULT_TEXT_THANK"].'--#--'.GetMessage("CQUIZ_ELEM_22").'--,--'.'cedit3_csection5--#----'.GetMessage("CQUIZ_ELEM_63").'--,--'.'PROPERTY_'.$arProperty["RESULT_THEME"].'--#--'.GetMessage("CQUIZ_ELEM_30").'--,--'.'PROPERTY_'.$arProperty["RESULT_TEXT"].'--#--'.GetMessage("CQUIZ_ELEM_20").'--,--'.'PROPERTY_'.$arProperty["RESULT_FILES"].'--#--'.GetMessage("CQUIZ_ELEM_33").'--,--'.'PROPERTY_'.$arProperty["RESULT_REDIRECT_USER"].'--#--'.GetMessage("CQUIZ_ELEM_29").'--,--'.'cedit3_csection6--#----'.GetMessage("CQUIZ_ELEM_64").'--,--'.'PROPERTY_'.$arProperty["GOAL_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_38").'--,--'.'PROPERTY_'.$arProperty["GOOGLE_GOAL_CATEGORY_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_41").'--,--'.'PROPERTY_'.$arProperty["GOOGLE_GOAL_ACTION_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_43").'--,--'.'PROPERTY_'.$arProperty["GTM_GOAL_CATEGORY_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_42").'--,--'.'PROPERTY_'.$arProperty["GTM_GOAL_ACTION_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_44").'--,--'.'PROPERTY_'.$arProperty["GTM_EVENT_RESULT"].'--#--'.GetMessage("CQUIZ_ELEM_45").'--,--'.'cedit3_csection8--#----'.GetMessage("CQUIZ_ELEM_65").'--,--'.'PROPERTY_'.$arProperty["GOAL_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_39").'--,--'.'PROPERTY_'.$arProperty["GOOGLE_GOAL_CATEGORY_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_46").'--,--'.'PROPERTY_'.$arProperty["GOOGLE_GOAL_ACTION_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_47").'--,--'.'PROPERTY_'.$arProperty["GTM_GOAL_CATEGORY_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_48").'--,--'.'PROPERTY_'.$arProperty["GTM_GOAL_ACTION_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_49").'--,--'.'PROPERTY_'.$arProperty["GTM_EVENT_SEND"].'--#--'.GetMessage("CQUIZ_ELEM_50").'--;--'.'cedit5--#--'.GetMessage("CQUIZ_ELEM_66").'--,--'.'XML_ID--#--'.GetMessage("CQUIZ_ELEM_67").'--;--';



    CUserOptions::SetOption("form", "form_element_".$iblockID, array(
        "tabs" => $element_tabs
    ),true);


    foreach($arUF as $key=>$UFid)
    {
        $oUserTypeEntity = new CUserTypeEntity();;
        $oUserTypeEntity->Update($UFid, 
            array(
                'EDIT_FORM_LABEL' => array('ru' => GetMessage("CQUIZ_SECT_NAME_$key")),
                'HELP_MESSAGE'=> array('ru' => GetMessage("CQUIZ_SECT_HELP_$key"))
            ) 
        );
    }
    
    foreach($arProperty as $key=>$propID)
    {        
        if(strlen(GetMessage("CQUIZ_ELEM_HINT_$key")) > 0)
        {
            $arFields = Array("HINT"=>GetMessage("CQUIZ_ELEM_HINT_$key"));
            $ibp = new CIBlockProperty;
            $ibp->Update($propID, $arFields);
        }
    }



//REQUESTS

$iblockXMLFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/install/services/iblock/xml/ru/requests.xml';
$iblockCode = "concept_quiz_site_requests";


    $permissions = Array(
            "1" => "X",
            "2" => "R"
        );
    $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
    if($arGroup = $dbGroup -> Fetch())
    {
        $permissions[$arGroup["ID"]] = 'W';
    };
    $iblockID = WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockCode,
        $iblockType,
        $arSites,
        $permissions
    );

    if ($iblockID < 1)
        return;

    //IBlock fields
    $iblock = new CIBlock;
    $arFields = Array(
        "ACTIVE" => "Y",
        "FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
        "CODE" => $iblockCode, 
        "XML_ID" => $iblockCode,
        "NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
    );
    
    $iblock->Update($iblockID, $arFields);


    // iblock user fields

    $arProperty = array();
    $dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
    while($arProp = $dbProperty->Fetch())
        $arProperty[$arProp["CODE"]] = $arProp["ID"];

    $arUF = array();
    $rsData = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID"=>"IBLOCK_".$iblockID."_SECTION"));
    while($arRes = $rsData->Fetch())
        $arUF[$arRes["FIELD_NAME"]] = $arRes["ID"];


    $section_tabs = 'edit1--#----,--'.'ID--#--ID--,--'.'NAME--#--'.GetMessage("CQUIZ_SECT_REQ_1").'--,--'.'UF_QUIZ_LAND_ID--#--'.GetMessage("CQUIZ_SECT_REQ_2").'--;--';


    
    // edit form user oprions    
    CUserOptions::SetOption("form", "form_section_".$iblockID, array(
            "tabs" => $section_tabs
        ),
        true
    );


    
    // edit form user oprions

    $element_tabs = 'edit1--#--'.GetMessage("CQUIZ_ELEM_REQ_5").'--,--'.'DATE_CREATE--#--'.GetMessage("CQUIZ_ELEM_REQ_6").'--,--'.'NAME--#--'.GetMessage("CQUIZ_ELEM_REQ_7").'--,--'.'PROPERTY_'.$arProperty["EMAIL"].'--#--'.GetMessage("CQUIZ_ELEM_REQ_2").'--,--'.'PROPERTY_'.$arProperty["NAME"].'--#--'.GetMessage("CQUIZ_ELEM_REQ_3").'--,--'.'PROPERTY_'.$arProperty["PHONE"].'--#--'.GetMessage("CQUIZ_ELEM_REQ_1").'--,--'.'PROPERTY_'.$arProperty["URL"].'--#--'.GetMessage("CQUIZ_ELEM_REQ_4").'--,--'.'PREVIEW_TEXT--#--'.GetMessage("CQUIZ_ELEM_REQ_8").'--;--'.'cedit1--#--'.GetMessage("CQUIZ_ELEM_REQ_9").'--,--'.'XML_ID--#--'.GetMessage("CQUIZ_ELEM_REQ_10").'--;--';



    CUserOptions::SetOption("form", "form_element_".$iblockID, array(
        "tabs" => $element_tabs
    ),true);




    foreach($arUF as $key=>$UFid)
    {
        $oUserTypeEntity = new CUserTypeEntity();;
        $oUserTypeEntity->Update($UFid, 
            array(
                'EDIT_FORM_LABEL' => array('ru' => GetMessage("CQUIZ_SECT_REQ_NAME_$key")),
                'HELP_MESSAGE'=> array('ru' => GetMessage("CQUIZ_SECT_REQ_HELP_$key"))
            ) 
        );
    }

    if($arUF["UF_QUIZ_LAND_ID"] > 0)
    {
        $oUserTypeEntity = new CUserTypeEntity();;
        $oUserTypeEntity->Update($arUF["UF_QUIZ_LAND_ID"], 
            array(
                "SETTINGS" => Array
                (
                    "LIST_HEIGHT" => 1,
                    "IBLOCK_ID" => $quiz_IB,
                )
            ) 
        );
    }



?>
