<?
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
global $DB, $APPLICATION, $MESS, $DBType;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "concept.quiz",
    array(
        'CConceptWqec' => 'classes/general/addwqec.php',
        'CConceptWqecSelectTab' => 'classes/general/addwqec.php',
    )
);




function SendWqecForm($request, $site_id, $type = "")
{

    $arRes = array();
    $arRes["OK"] = "N";
    
    if($request["wqec-send"] == "Y")
    {
        CModule::IncludeModule('iblock');
        CModule::IncludeModule('concept.quiz');
        CConceptWqec::ConceptQuizOptions($site_id);
        global $CQUIZ_TEMPLATE_ARRAY;

        $arSection = Array();


        $element_id = trim($request["wqec_element"]);
        $section_id = trim($request["cwisSectionId"]);


        $arIBlockElement = GetIBlockElement($element_id);
        $res = CIBlockSection::GetByID($section_id);

        if($ar_res = $res->GetNext())
            $iblock = $ar_res["IBLOCK_ID"];

        $rsResult = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblock, "ID" => $section_id), false, array("UF_*")); 

        $arSection = $rsResult->GetNext();
            

        $rsSites = CSite::GetByID($site_id);
        $arSite = $rsSites->Fetch();


        $email_from = $arSection["~UF_CWIZ_FROM_MAIL"];
        $email_to = $arSection["~UF_CWIZ_ADMIN_MAIL"];

        if(strlen($email_from) <= 0)
            $email_from = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_MAIL_FROM"]["VALUE"];

        if(strlen($email_from) <= 0)
            $email_from = $arSite['EMAIL'];


        if(strlen($email_to) <= 0)
            $email_to = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_MAIL_TO"]["VALUE"];

        if(strlen($email_to) <= 0)
            $email_to = $arSite['EMAIL'];

       

            $phone = trim($request["wqec-phone"]);
            $email = trim($request["wqec-email"]);
            $name = trim($request["wqec-name"]);
            $url = trim($request["cwizUrl"]);
            $cwizMaxResult = trim($request["cwizMaxResult"]);
            // 
            $x = 0;

            if(SITE_CHARSET == "windows-1251")
            {
                $name = utf8win1251(trim($request["wqec-name"]));
                $url = utf8win1251(trim($request["cwizUrl"]));
            }

            $url = urldecode($url);


            $message = '';
            $arFiles = array();

            if(strlen($name) > 0)
                $message .= "<b>".GetMessage("CWIZ_MESSAGE_NAME")."</b>".$name."<br>";
            

            if(strlen($phone) > 0)
                $message .= "<b>".GetMessage("CWIZ_MESSAGE_PHONE")."</b>".$phone."<br>";
            
            if(strlen($email) > 0)
                $message .= "<b>".GetMessage("CWIZ_MESSAGE_EMAIL")."</b>".$email."<br>"; 




            

            $arVal = array();
            $mes_points = "";

            if(strlen($arSection["UF_TYPE_CALC"]) > 0)
            {
                $arSection["UF_TYPE_CALC_ENUM"] = CUserFieldEnum::GetList(array(), array(
                    "ID" => $arSection["UF_TYPE_CALC"],
                ))->GetNext();
            }

            if($arSection["UF_TYPE_CALC_ENUM"]["XML_ID"] == "symbols")
            {
                $arVal = CConceptWqec::QuizTotal($request, "sym");
            }
            else
            {
                $arVal = CConceptWqec::QuizTotal($request, "points");
                $mes_points = "<br><div><b>".GetMessage("CWIZ_MESSAGE_RESULT").$arVal["TOTAL_POINTS"].GetMessage("CWIZ_POINTS_P").$cwizMaxResult."</b></div><br><b>";
            }
            
            if(strlen($arVal["MESS"]) > 0)
            {
                
                $message .= "<br/>".$mes_points.GetMessage("CWIZ_MESSAGE_RESULT_MESSAGE")."\"".$arIBlockElement["NAME"]."\"".'</b>'."<br><br><br><div style='font-size: 18px; line-height: 22px'><b>".GetMessage("CWIZ_MESSAGE_RESULT_USER")."</b></div><br>".$arVal["MESS"]."<br>"; 
            }
    

            //bx24 and Amo

            $mess = "";
            if(strlen($url)>0)
                $mess .= GetMessage("CWIZ_B24_URL").$url;

            if(strlen($arVal["MESS"]) > 0)
                $mess .= "<br><div><b>".GetMessage("CWIZ_MESSAGE_RESULT").$arVal["TOTAL_POINTS"].GetMessage("CWIZ_POINTS_P").$cwizMaxResult.'</b></div><br><b>'.GetMessage("CWIZ_MESSAGE_RESULT_MESSAGE")."\"".$arIBlockElement["NAME"]."\"".'</b>'."<br><br><br><div style='font-size: 18px; line-height: 22px'><b>".GetMessage("CWIZ_MESSAGE_RESULT_USER")."</b></div><br>".$arVal["MESS"]."<br>"; 


            

            $arEventFields = array(
                "MESSAGE" => $message,
                "EMAIL_TO" => $email_to,
                "EMAIL_FROM" => $email_from,
                "EMAIL" => $email,
                "NAME"  => $name,
                "PHONE" => $phone,
                "URL" => $url,
                "PAGE_NAME" => $arSection["NAME"]
            );


            if(!empty($_FILES["cquiz_userfile"]) && $_FILES["cquiz_userfile"]["error"] == 0 || !empty($arFiles))
            {
                
                $filename = basename($_FILES['cquiz_userfile']['name']);
        
                $newname = $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/concept.quiz/cquiz_tmp_file/'.$filename;
                if (!file_exists($newname)) 
                {
                    move_uploaded_file($_FILES['cquiz_userfile']['tmp_name'], $newname);
                }

                $arFiles = Array($newname);
             
                

                if(CEvent::Send("CONCEPT_UNIVERSAL_USER_INFO", $site_id, $arEventFields, "Y", "", $arFiles))
                    $arRes["OK"] = "Y";

                if (file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/concept.quiz/cquiz_tmp_file/'))
                    foreach (glob($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/concept.quiz/cquiz_tmp_file/*') as $file)
                        unlink($file);
            }

            else
            {
                if(CEvent::Send("CONCEPT_UNIVERSAL_USER_INFO", $site_id, $arEventFields, "Y", ""))
                $arRes["OK"] = "Y";
            }


            
                

            if(strlen($arIBlockElement["PROPERTIES"]['RESULT_TEXT']["~VALUE"]["TEXT"]) > 0 || !empty($arIBlockElement['PROPERTIES']['RESULT_FILES']['VALUE']) || strlen($arIBlockElement["PROPERTIES"]['RESULT_THEME']["VALUE"]) > 0)
            {
                $arEventFields2 = array(
                    "EMAIL_FROM" => $email_from,
                    "EMAIL_TO" => $email,
                    "MESSAGE_FOR_USER" => $arIBlockElement["PROPERTIES"]['RESULT_TEXT']["~VALUE"]["TEXT"],
                    "THEME" => $arIBlockElement["PROPERTIES"]['RESULT_THEME']["~VALUE"]
                );

                if(!empty($arIBlockElement['PROPERTIES']['RESULT_FILES']['VALUE']))
                {
                    $files = $arIBlockElement['PROPERTIES']['RESULT_FILES']['VALUE'];

                    if(CEvent::Send("CONCEPT_UNIVERSAL_FOR_USER", $site_id, $arEventFields2, "Y", "", $files))
                        $arRes["OK"] = "Y";
                }
                else
                {
                    if(CEvent::Send("CONCEPT_UNIVERSAL_FOR_USER", $site_id, $arEventFields2))
                        $arRes["OK"] = "Y";
                }
            }
                   
        

        
            if($type != "send_res")
            {
                $arRes = json_encode($arRes);
                echo $arRes;
            }



        //infoblock
        if($arSection["UF_SAVE_IN_IB"])
        {
            
            CModule::IncludeModule("iblock");
            
            $request_iblock_code = "concept_quiz_site_requests";
            
            $res = CIBlock::GetList(Array(), Array("CODE"=>$request_iblock_code), false);
            
            while($ar_res = $res->GetNext())
                $request_iblock_id = $ar_res["ID"];
            
            
            $arFilter = Array('IBLOCK_ID'=>$request_iblock_id, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y', "UF_QUIZ_LAND_ID" => $section_id);
            $db_list = CIBlockSection::GetList(Array(), $arFilter, true);
            
            if($db_list->SelectedRowsCount() <= 0)
            {
                
                $bs = new CIBlockSection;
                $arFields = Array(
                  "ACTIVE" => "Y",
                  "IBLOCK_SECTION_ID" => false,
                  "IBLOCK_ID" => $request_iblock_id,
                  "NAME" => $arSection["NAME"],
                  "UF_QUIZ_LAND_ID" => $section_id
                );
                
                
                $sect_id = $bs->Add($arFields);
  
            }
            else
            {
                $ar_res = $db_list->GetNext();
                $sect_id = $ar_res["ID"];
            }
            
            
            $el = new CIBlockElement;
            
            $request_message = str_replace(Array("<b>","</b>","<br>","<br/>","<div>","</div>"), Array("", "", "\r\n", "\r\n", "", "\r\n"), $message);
            
            $request_text = "";
            
            //$request_text .= GetMessage("CQUIZ_MESSAGE_TEXT1")."\r\n";
            $request_text .= GetMessage("CQUIZ_MESSAGE_TEXT1").$arSection["NAME"]."\r\n";
            $request_text .= GetMessage("CQUIZ_MESSAGE_TEXT2").$url."\r\n\r\n";
            $request_text .= GetMessage("CQUIZ_MESSAGE_TEXT3")."\r\n\r\n";
            $request_text .= $request_message;




            $arLoadProductArray = Array(
              "IBLOCK_SECTION_ID" => $sect_id,          
              "IBLOCK_ID"      => $request_iblock_id,
              "NAME"           => GetMessage("INFOBLOCK_TITLE").date("d.m.Y H:i:s"),
              "ACTIVE"         => "Y",            
              "PREVIEW_TEXT"   => strip_tags(html_entity_decode($request_text)),
              "PROPERTY_VALUES" => array(
                   "NAME" =>$name,
                   "EMAIL" =>$email, 
                   "URL" =>$url,
                   "PHONE" =>$phone 
                   )
            );
            
            $el->Add($arLoadProductArray);
            
        }
            

        //bitrix24

        if(strlen($arSection["UF_BX_ON"])>0){

            $bx_options = CUserFieldEnum::GetList(array(), array(
                "ID" => $arSection["UF_BX_ON"],
            ));
            $arSection["UF_BX_ON_ENUM"] = $bx_options->GetNext();
        }



        if($arSection["UF_BX_ON_ENUM"]["XML_ID"] == "parent")
        {
            $wqec_bx24_active = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_BX_ON"]["VALUE"][0];
            $wqec_bx24_address = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_BX_URL"]["VALUE"];
            $wqec_bx24_login = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_BX_LOGIN"]["VALUE"];
            $wqec_bx24_password = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_BX_PASSWORD"]["VALUE"];
        }

        if($arSection["UF_BX_ON_ENUM"]["XML_ID"] == "my")
        {
            $wqec_bx24_active = "Y";
            $wqec_bx24_address = $arSection["UF_BX_URL"];
            $wqec_bx24_login = $arSection["UF_BX_LOGIN"];
            $wqec_bx24_password = $arSection["UF_BX_PASSWORD"];
        }


        if($wqec_bx24_active == "Y" && strlen($wqec_bx24_address) > 0 && strlen($wqec_bx24_login) > 0 && strlen($wqec_bx24_password) > 0)
        {
            

            $crmUrl = "https://".trim($wqec_bx24_address)."/"; // https://mycrm.bitrix24.ru/
            $login = trim($wqec_bx24_login);
            $password = trim($wqec_bx24_password);
            
            
            $title = GetMessage("CWIZ_B24_TITLE").$arSection["NAME"];
            
            $mess = "";
            
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT1")."</b>".$arSection["NAME"]."<br/>";
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT2")."</b>".$url."<br/><br/>";
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT3")."</b>"."<br/><br/>";
            
            $mess .= $message;          
            

            $namebx = $name;
            $phonebx = $phone;
            $emailbx = $email;
            
            if(SITE_CHARSET == "windows-1251")
            {
                $title = iconv('windows-1251', 'utf-8', $title);
                $namebx = iconv('windows-1251', 'utf-8', $namebx);
                $phonebx = iconv('windows-1251', 'utf-8', $phonebx);
                $emailbx = iconv('windows-1251', 'utf-8', $emailbx);
                $mess = iconv('windows-1251', 'utf-8', $mess);
            }
          
            $arParams = array(
                'LOGIN' => $login, 
                'PASSWORD' => $password, 
                'TITLE' => $title
            );
             
            if(strlen($namebx) > 0)
                $arParams['NAME'] = $namebx;

            if(strlen($phone) > 0)
                $arParams['PHONE_WORK'] = $phonebx;
                
            if(strlen($email) > 0)
                $arParams['EMAIL_WORK'] = $emailbx;
                
            if(strlen($mess) > 0)
                $arParams['COMMENTS'] = $mess;
                
                 
            
            $obHttp = new CHTTP();
            $result = $obHttp->Post($crmUrl.'crm/configs/import/lead.php', $arParams);
            //$result = json_decode(str_replace('\'', '"', $result), true);
            //$arRes["ER"] = '['.$result['error'].'] '.$result['error_message'];

            
        }


        //amocrm

        if(strlen($arSection["UF_AMO_ON"])>0){

            $bx_options = CUserFieldEnum::GetList(array(), array(
                "ID" => $arSection["UF_AMO_ON"],
            ));
            $arSection["UF_AMO_ON_ENUM"] = $bx_options->GetNext();
        }

        if($arSection["UF_AMO_ON_ENUM"]["XML_ID"] == "parent")
        {
            $wqec_amo_active = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_AMO_ON"]["VALUE"][0];
            $wqec_amo_address = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_AMO_URL"]["VALUE"];
            $wqec_amo_login = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_AMO_LOGIN"]["VALUE"];
            $wqec_amo_hash = $CQUIZ_TEMPLATE_ARRAY["CQUIZ_AMO_HASH"]["VALUE"];
        }

        if($arSection["UF_AMO_ON_ENUM"]["XML_ID"] == "my")
        {
            $wqec_amo_active = "Y";
            $wqec_amo_address = $arSection["UF_AMO_URL"];
            $wqec_amo_login = $arSection["UF_AMO_LOGIN"];
            $wqec_amo_hash = $arSection["UF_AMO_HASH"];
        }


        if($wqec_amo_active == "Y" && strlen($wqec_amo_address) > 0 && strlen($wqec_amo_login) > 0 && strlen($wqec_amo_hash) > 0)
        {
            
            
            
            $crmUrl = "https://".trim($wqec_amo_address)."/"; 
            $login = trim($wqec_amo_login);
            $hash = trim($wqec_amo_hash);
            
            $title = GetMessage("CWIZ_B24_TITLE").$arSection["NAME"];
            
            $mess = "";
            
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT1")."</b>".$arSection["NAME"]."\r\n";
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT2")."</b>".$url."\r\n\r\n";
            $mess .= "<b>".GetMessage("CQUIZ_MESSAGE_TEXT3")."</b>"."\r\n\r\n";
            
            $mess .= $message;          
            

            $nameamo = $name;
            $phoneamo = $phone;
            $emailamo = $email;
            
            if(SITE_CHARSET == "windows-1251")
            {
                $title = iconv('windows-1251', 'utf-8', $title);
                $nameamo = iconv('windows-1251', 'utf-8', $nameamo);
                $phoneamo = iconv('windows-1251', 'utf-8', $phoneamo);
                $emailamo = iconv('windows-1251', 'utf-8', $emailamo);
                $mess = iconv('windows-1251', 'utf-8', $mess);
            }
            
            $mess = str_replace(Array("<b>","</b>","<br>","<br/>","<div>","</div>"), Array("", "", "\r\n", "\r\n", "", "\r\n"), $mess);

            $mess = html_entity_decode(strip_tags($mess));
            
            require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/amocrm/add.php');
            
        }
        
        require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/concept.quiz/crm.php');

    }

    else
    {
        $arRes = json_encode($arRes);
        echo $arRes;
    }


}

?>