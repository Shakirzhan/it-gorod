<?
use \Bitrix\Main\Config\Option as Option;
use Bitrix\Main\Localization\Loc;
IncludeModuleLangFile(__FILE__);



class CConceptWqec
{ 

    function ConceptQuizOptions($siteId)
    {

        global $CQUIZ_TEMPLATE_ARRAY;
        $CQUIZ_TEMPLATE_ARRAY = array(

            "CQUIZ_JQ_ON" => array(
                'NAME' => Loc::GetMessage("CQUIZ_JQ_ON"),
                'HINT' => Loc::GetMessage("CQUIZ_JQ_ON_HINT"),
                'VALUE_ID' =>  array(

                    array(
                        'wqec_jQ_active',
                        '',
                        'Y'
                    )
                ),
                'VALUE' => array(Option::get("concept.quiz", "wqec_jQ_active", false, $siteId))
            ),

            "CQUIZ_IC_ON" => array(
                'NAME' => Loc::GetMessage("CQUIZ_IC_ON"),
                'HINT' => Loc::GetMessage("CQUIZ_IC_ON_HINT"),
                'VALUE_ID' =>  array(

                    array(
                        'wqec_icon_active',
                        '',
                        'Y'
                    )
                ),
                'VALUE' => array(Option::get("concept.quiz", "wqec_icon_active", false, $siteId))
            ),

            "CQUIZ_TITLE_MAIL" => array(
                'NAME' => Loc::GetMessage("CQUIZ_TITLE_MAIL")
            ),

            "CQUIZ_MAIL_FROM" => array(
                'NAME' => Loc::GetMessage("CQUIZ_MAIL_FROM"),
                'HINT' => Loc::GetMessage("CQUIZ_MAIL_FROM_HINT"),
                'VALUE_ID' => 'wqec_mailfrom',
                'VALUE' => Option::get("concept.quiz", "wqec_mailfrom", false, $siteId)
            ),

            "CQUIZ_MAIL_TO" => array(
                'NAME' => Loc::GetMessage("CQUIZ_MAIL_TO"),
                'HINT' => Loc::GetMessage("CQUIZ_MAIL_TO_HINT"),
                'VALUE_ID' => 'wqec_mailto',
                'VALUE' => Option::get("concept.quiz", "wqec_mailto", false, $siteId)
            ),

            "CQUIZ_TITLE_COPYRIGHT" => array(
                'NAME' => Loc::GetMessage("CQUIZ_TITLE_COPYRIGHT")
            ),

            "CQUIZ_COPYRIGHT" => array(
                'NAME' => Loc::GetMessage("CQUIZ_COPYRIGHT"),
                'HINT' => Loc::GetMessage("CQUIZ_COPYRIGHT_HINT"),
                'VALUE_ID' =>  array(

                    array(
                        'wqec_copy_active',
                        '',
                        'Y'
                    )
                ),
                'VALUE' => array(Option::get("concept.quiz", "wqec_copy_active", false, $siteId))
            ),

            

            "CQUIZ_BX_ON" => array(
                'NAME' => Loc::GetMessage("CQUIZ_BX_ON"),
                'HINT' => Loc::GetMessage("CQUIZ_BX_ON_HINT"),
                'VALUE_ID' =>  array(

                    array(
                        'wqec_bx24_active',
                        '',
                        'Y'
                    )
                ),
                'VALUE' => array(Option::get("concept.quiz", "wqec_bx24_active", false, $siteId))
            ),

            "CQUIZ_BX_URL" => array(
                'NAME' => Loc::GetMessage("CQUIZ_BX_ADDRESS"),
                'HINT' => Loc::GetMessage("CQUIZ_BX_ADDRESS_HINT"),
                'VALUE_ID' => 'wqec_bx24_address',
                'VALUE' => Option::get("concept.quiz", "wqec_bx24_address", false, $siteId)
            ),

            "CQUIZ_BX_LOGIN" => array(
                'NAME' => Loc::GetMessage("CQUIZ_BX_LOGIN"),
                'HINT' => Loc::GetMessage("CQUIZ_BX_LOGIN_HINT"),
                'VALUE_ID' => 'wqec_bx24_login',
                'VALUE' => Option::get("concept.quiz", "wqec_bx24_login", false, $siteId)
            ),

            "CQUIZ_BX_PASSWORD" => array(
                'NAME' => Loc::GetMessage("CQUIZ_BX_PASSWORD"),
                'HINT' => Loc::GetMessage("CQUIZ_BX_PASSWORD_HINT"),
                'VALUE_ID' => 'wqec_bx24_password',
                'VALUE' => Option::get("concept.quiz", "wqec_bx24_password", false, $siteId)
            ),


            "CQUIZ_AMO_ON" => array(
                'NAME' => Loc::GetMessage("CQUIZ_AMO_ON"),
                'HINT' => Loc::GetMessage("CQUIZ_AMO_ON_HINT"),
                'VALUE_ID' =>  array(

                    array(
                        'wqec_amo_active',
                        '',
                        'Y'
                    )
                ),
                'VALUE' => array(Option::get("concept.quiz", "wqec_amo_active", false, $siteId))
            ),

            "CQUIZ_AMO_URL" => array(
                'NAME' => Loc::GetMessage("CQUIZ_AMO_ADDRESS"),
                'HINT' => Loc::GetMessage("CQUIZ_AMO_ADDRESS_HINT"),
                'VALUE_ID' => 'wqec_amo_address',
                'VALUE' => Option::get("concept.quiz", "wqec_amo_address", false, $siteId)
            ),

            "CQUIZ_AMO_LOGIN" => array(
                'NAME' => Loc::GetMessage("CQUIZ_AMO_LOGIN"),
                'HINT' => Loc::GetMessage("CQUIZ_AMO_LOGIN_HINT"),
                'VALUE_ID' => 'wqec_amo_login',
                'VALUE' => Option::get("concept.quiz", "wqec_amo_login", false, $siteId)
            ),

            "CQUIZ_AMO_HASH" => array(
                'NAME' => Loc::GetMessage("CQUIZ_AMO_PASSWORD"),
                'HINT' => Loc::GetMessage("CQUIZ_AMO_PASSWORD_HINT"),
                'VALUE_ID' => 'wqec_amo_password',
                'VALUE' => Option::get("concept.quiz", "wqec_amo_password", false, $siteId)
            ),

            "CQUIZ_HIDE_PAGES" => array(
                'NAME' => Loc::GetMessage("CQUIZ_CQUIZ_HIDE_PAGES"),
                'HINT' => Loc::GetMessage("CQUIZ_CQUIZ_HIDE_PAGEST_HINT"),
                'VALUE_ID' => 'wqec_hide_pages',
                'VALUE' => Option::get("concept.quiz", "wqec_hide_pages", false, $siteId)
            ),


        );

        $CQUIZ_TEMPLATE_ARRAY['CHECK_OPTIONS'] = array(
            array('wqec_jQ_active'),
            array('wqec_icon_active'),
            array('wqec_copy_active'),
            array('wqec_bx24_active'),
            array('wqec_amo_active')
        );

    }

    function optionDelete($arRes, $site_id)
    {

        if(!empty($arRes))
        {
            foreach($arRes as $val)
            {

                Option::delete("concept.quiz", array(
                    "name" => trim($val[0]),
                    "site_id" => $site_id
                    )
                );
            }

        }
        
    }

    private static function QuizInclude ()
    {
        global $APPLICATION;

        $host = $_SERVER["HTTP_HOST"];
        if(strlen($host)<=0){
            $host = $_SERVER["SERVER_NAME"];
        }

        $host = explode(":", $host);
        $host = $host[0];

        $hide_pages = COption::GetOptionString("concept.quiz", "wqec_hide_pages", false, SITE_ID);

        $arHide_pages = explode("\r\n", $hide_pages);

        $hide_page = false;

        
        foreach($arHide_pages as $arUrl)
        {

            if($hide_page)
                break;
            
            $search = array("http://", "https://", "/", "?", "=");
            $replace   = array("", "", "\/", "", "");
            $pattern = str_replace($search, $replace, $arUrl);

            $search = array(".php", ".html");
            $replace   = array("", "");
            $tmpUrl = str_replace($search, $replace, $arUrl);

            $arUrlExp = array();

            if(preg_match("/\./i", $tmpUrl))
            {
                $arUrlExp = explode("\/", $pattern);

                if(!preg_match("/^".$arUrlExp[0]."$/i", $host))
                    continue;
                
                if(count($arUrlExp) == 1 || (count($arUrlExp) == 2 && empty($arUrlExp[1])))
                {
                    if(!preg_match("/^".$arUrl."$/i", $host."/"))
                        $pattern = "\/";
                }
                else
                {
                    $arUrlExp[0] = "";
                    $pattern = implode("\/", $arUrlExp);
                }
            }

            if(preg_match("/\.php/i", $arUrl))
                $page = $APPLICATION->GetCurPage(true);
            
            else
                $page = $APPLICATION->GetCurPage(false);

            
            $search = array("*");
            $replace   = array(".*");
            $pattern = str_replace($search, $replace, $pattern);

            if(preg_match("/^".$pattern."$/i", $page))
                $hide_page = true;

        }

        $show_quiz = true;

        if(preg_match("/^\/bitrix/", $_SERVER['REQUEST_URI']) || preg_match("/^\/local/", $_SERVER['REQUEST_URI']) || $hide_page)
            $show_quiz = false;

        return $show_quiz;

    }
    
    function AddConceptWqec()
    {

        global $APPLICATION;
        global $USER;

        
        $show_quiz = CConceptWqec::QuizInclude ();

       
        if ($show_quiz && $GLOBALS["QUIZ_FIRSTCALL"] != "Y") 
        {
            $GLOBALS["QUIZ_FIRSTCALL"] = "Y";
            
            $wqec_icon_active = COption::GetOptionString("concept.quiz", "wqec_icon_active", false, SITE_ID);
            $wqec_jQ_active = COption::GetOptionString("concept.quiz", "wqec_jQ_active", false, SITE_ID);

            if(strlen($wqec_jQ_active)>0)
                $APPLICATION->AddHeadScript('/bitrix/js/concept.quiz/jquery-1.12.3.min.js');

            ?>
            <script type="text/javascript" src='/bitrix/js/concept.quiz/jquery.maskedinput-1.2.2.min.js'></script>
            <script type="text/javascript" src='/bitrix/js/concept.quiz/zero-clipboard.js'></script>
            <script type="text/javascript" src='/bitrix/js/concept.quiz/scripts.js'></script>
            <?

            $APPLICATION->SetAdditionalCSS("/bitrix/css/concept.quiz/template_styles.css");
            $APPLICATION->SetAdditionalCSS("/bitrix/css/concept.quiz/responsive.css");
            //$APPLICATION->AddHeadString('<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0" />');
          
            /*$url = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"];

            $URI = $_SERVER["REQUEST_URI"];

            $arUrl = explode('?', $URI);
            $url .= $arUrl[0];

           

            $quiz = DeleteParam(array("quiz")); 

            if(strlen($quiz)>0)
                $url .= '?'.$quiz;
            
            echo '<input type="hidden" name="wqec-url" value="">';*/


            if($USER->isAdmin() && $wqec_icon_active != "Y")
                $APPLICATION->IncludeComponent(
                    "concept:conceptquiz.list",
                    ".default",
                    Array(
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "IBLOCK_TYPE" => "news",
                        "IBLOCK_CODE" => "concept_quiz_questions",
                        "COMPOSITE_FRAME_MODE" => "Y",
                        "COMPOSITE_FRAME_TYPE" => "AUTO"
                    )
                );
          
        }

        return true;
    }

    public static function CConceptWqecUpdateHandler(&$arFields)
    {

        $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
        if($ar_res = $res->GetNext())
            $iBlock["IBLOCK_CODE"] = $ar_res['CODE'];


        if($_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php" && $iBlock["IBLOCK_CODE"] == "concept_quiz_questions")
        {
            $arResult = array();
            $arFilter = Array('IBLOCK_ID' => $arFields["IBLOCK_ID"], "ID" => $arFields["ID"]);
            $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false);
            while($ob = $res->GetNextElement()){ 
                $arResult = $ob->GetFields();  
                $arResult["PROPERTIES"] = $ob->GetProperties();
            }

            if(strlen($arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"]) == 0 && strlen($arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"]) > 0)
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], 1000000000, "RESULT_MAX_VALUE");
            
            elseif(strlen($arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"]) > 0 && strlen($arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"]) == 0)
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], 0, "RESULT_MIN_VALUE");
            
            
            elseif(strlen($arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"]) == 0 && strlen($arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"]) == 0)
            {
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], 0, "RESULT_MIN_VALUE");
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], 1000000000, "RESULT_MAX_VALUE");
            }
            

            if(($arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"] < $arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"]) && strlen($arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"]) > 0 && strlen($arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"]) > 0)
            {
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], $arResult["PROPERTIES"]["RESULT_MAX_VALUE"]["VALUE"], "RESULT_MIN_VALUE");
                CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], $arResult["PROPERTIES"]["RESULT_MIN_VALUE"]["VALUE"], "RESULT_MAX_VALUE");
            }
        }

    }

    function QuizTotal($arRes, $type)
    {
        CModule::IncludeModule("iblock");
        $arVal = array();

        foreach($arRes as $key => $val)
        {
            $key_new = "";


            if(strpos($key, "c_quiz") === 0)
            {

                $search = array("c_quiz");
                $replace   = array("");
                $key_new = str_replace($search, $replace, $key);
                $arVal["VAL"][$key_new]["MAIN"] = $val;
            }

            if(strpos($key, "usr_cquiz") === 0)
            {
                $search = array("usr_cquiz");
                $replace   = array("");
                $key_new = str_replace($search, $replace, $key);
                $arVal["VAL"][$key_new]["USR"] = $val;

                if(SITE_CHARSET == "windows-1251")
                    $arVal["VAL"][$key_new]["USR"] = utf8win1251(trim($val));
                
            }

            if(!in_array($key_new, $arVal["ID"]) && strlen($key_new) > 0)
                $arVal["ID"][] = $key_new;

        }

        $arVal["MESS"] = "";

        if($type == "symbols")
            $arVal["TOTAL_POINTS"] = "";
        else
            $arVal["TOTAL_POINTS"] = 0;

        $arSelect = Array("SORT", "ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = Array("IBLOCK_CODE"=>"concept_quiz_questions", "ID"=>$arVal["ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement()){ 
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();

            $value = $arVal["VAL"][$arFields["ID"]];
            $quest = $arProps["QUEST"]["VALUE"];
            $answer = "";
            $arVal["MESS"] .= "<div><b>".$arProps["QUEST"]["VALUE"]."</b></div>";

            $preText = "<b>".GetMessage("CWIZ_MESSAGE_USR")."</b>";
            if($arProps["QUEST_ANSWER_USER_DESC"]["VALUE"])
                $preText = "<b>".$arProps["QUEST_ANSWER_USER_DESC"]["VALUE"]."</b>: ";
            

            if(is_array($value["MAIN"]) && !empty($value["MAIN"]))
            {
                foreach($value["MAIN"] as $val)
                {

                    $answer .= "- ".$arProps["QUEST_ANSWER"]["VALUE"][$val]."<br/>";

                    if($type == "symbols")
                        $arVal["TOTAL_POINTS"] .= trim($arProps["QUEST_ANSWER"]["DESCRIPTION"][$val]);
                    else
                        $arVal["TOTAL_POINTS"] += floatval($arProps["QUEST_ANSWER"]["DESCRIPTION"][$val]);
                }

                if(strlen($value["USR"])>0)
                    $answer .= $preText.$value["USR"]."<br/>";
            }

            else
            {
                if(strlen($value["MAIN"])>0)
                {
                    $answer .= "- ".$arProps["QUEST_ANSWER"]["VALUE"][$value["MAIN"]]."<br/>";


                    if($type == "symbols")
                        $arVal["TOTAL_POINTS"] .= trim($arProps["QUEST_ANSWER"]["DESCRIPTION"][$value["MAIN"]]);
                    else
                        $arVal["TOTAL_POINTS"] += floatval($arProps["QUEST_ANSWER"]["DESCRIPTION"][$value["MAIN"]]);
                }

                if(strlen($value["USR"])>0)
                    $answer .= $preText.$value["USR"]."<br/>";
                
            }

            if(strlen($answer)<=0)
                $answer = GetMessage("CWIZ_MESSAGE_EMPTY_ANSWER")."<br/>";

            $arVal["MESS"] .= $answer."<br/>";


        }


       
        return $arVal;
    }


    public static function AddSiteCquizBuf(&$content)
    {

        $show_quiz = CConceptWqec::QuizInclude ();

        /*
        if($show_quiz){

            $newstring = '</html';
            $pos = strpos($content, $newstring);

            if($pos > 0)
            {

                $stringHTML = "";
                $i = 0;
                $cutHTML = false;

                while (!$cutHTML)
                {
                    
                    $rest = substr($content, $pos, 1);
                    $pos++;
                
                    $stringHTML .= $rest;

                    if($rest == ">")
                        $cutHTML = true;

                    $i++;

                    if($i > 100)
                        break;

                }

                if($cutHTML)
                    $content = str_replace($stringHTML, "", $content);
            }


            $newstring = '</body';
            $pos = strpos($content, $newstring);

            if($pos > 0)
            {

                $stringBODY = "";
                $i = 0;
                $cutBODY = false;

                while (!$cutBODY)
                {
                    
                    $rest = substr($content, $pos, 1);
                    $pos++;
                
                    $stringBODY .= $rest;

                    if($rest == ">")
                        $cutBODY = true;

                    $i++;

                    if($i > 100)
                        break;

                }
                
                if($cutBODY){
                    $content = str_replace($stringBODY, "", $content);

                }
            }


            if($stringBODY && $cutBODY)
                $content = $content.$stringBODY;
                
            if($stringHTML && $cutHTML)
                $content = $content.$stringHTML;
        }
        */


    }
}

class CConceptWqecSelectTab{ 
    
   public static function ConceptWqecSelectTab() 
   {

        if($_SERVER["SCRIPT_NAME"] == "/bitrix/admin/iblock_element_edit.php")
        {
            CModule::IncludeModule("iblock");

            $arSite_id = Array();
            $iBlock = Array();

            $res = CIBlock::GetByID($_REQUEST["IBLOCK_ID"]);
            if($ar_res = $res->GetNext())
                $iBlock["IBLOCK_CODE"] = $ar_res['CODE'];




            $resProp = CIBlock::GetProperties($_REQUEST["IBLOCK_ID"], Array(), Array("CODE"=>"TYPE_ELEMENT"));
            if($res_prop = $resProp->Fetch())
                $iBlock["PROP_ID"] = $res_prop["ID"];
            
                

            $rsSites = CSite::GetList($by="sort", $order="desc");
            $isIt = false;

            while ($arSite = $rsSites->Fetch())
            {

                if("concept_quiz_questions" == $iBlock["IBLOCK_CODE"] && !$isIt)
                    $isIt = true;
                
                if($isIt)
                    break;
            }

            if($isIt)
            {
                echo "<input type='hidden' name='prop_select' value='".$iBlock["PROP_ID"]."'>";

                CJSCore::Init(array('jquery'));
                echo "<script src= '/bitrix/js/concept.quiz/qtabs.js'></script>";
            
            }
           
        }

    
   }
}
?>