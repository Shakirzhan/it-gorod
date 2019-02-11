<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
CModule::IncludeModule('iblock');
CModule::IncludeModule('concept.quiz');

// 
$cwizType = trim($_REQUEST["cwizType"]);
$cwizIblockId = trim($_REQUEST["cwizIblockId"]);
$cwizSectionId = trim($_REQUEST["cwizSectionId"]);
$cwizSoc = trim($_REQUEST["wqec-soc"]);
$site_id = trim($_REQUEST["cwizsite_id"]);
// 
$cwizMaxResult = trim($_REQUEST["cwizMaxResult"]);



// 
$x = 0;

    $arVal = array();
    


    $arItem = array();
    $arFilter = Array('IBLOCK_ID' => $cwizIblockId, 'ID' => $cwizSectionId);
    $db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, array("ID","IBLOCK_ID", "IBLOCK_TYPE_ID", "NAME", "DETAIL_PICTURE", "UF_TYPE_CALC"));


    while($ar_result = $db_list->GetNext())
        $arResult["SECTION_INFO"] = $ar_result;


    if(strlen($arResult["SECTION_INFO"]["UF_TYPE_CALC"]) > 0)
    {
        $arResult["SECTION_INFO"]["UF_TYPE_CALC_ENUM"] = CUserFieldEnum::GetList(array(), array(
            "ID" => $arResult["SECTION_INFO"]["UF_TYPE_CALC"],
        ))->GetNext();
    }



    if($arResult["SECTION_INFO"]["UF_TYPE_CALC_ENUM"]["XML_ID"] == "symbols")
    {
        $arVal = CConceptWqec::QuizTotal($_REQUEST, "symbols");

        $arFilter = Array("IBLOCK_ID" => $cwizIblockId, "SECTION_ID" => $cwizSectionId, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'PROPERTY_RESULT_SYMBOLS' => $arVal["TOTAL_POINTS"]);
    }
    else
    {
        $arVal = CConceptWqec::QuizTotal($_REQUEST, "points");

        $arFilter = Array("IBLOCK_ID" => $cwizIblockId, "SECTION_ID" => $cwizSectionId, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", '<=PROPERTY_RESULT_MIN_VALUE' => $arVal["TOTAL_POINTS"], '>=PROPERTY_RESULT_MAX_VALUE' => $arVal["TOTAL_POINTS"]);
    }



    $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false);
    while(($ob = $res->GetNextElement()) && ($x < 1)){ 
        $arItem = $ob->GetFields();  
        $arItem["PROPERTIES"] = $ob->GetProperties();
        if($arItem["PROPERTIES"]["TYPE_ELEMENT"]["VALUE_XML_ID"] != 'result')
            continue;

        $x++;
    }

/*
<input type="hidden" class= "cwiz-yaGoalResult" value="<?=$arItem['PROPERTIES']['GOAL_RESULT']['VALUE']?>">
<input type="hidden" class= "cwiz-yaGoalSend" value="<?=$arItem['PROPERTIES']['GOAL_SEND']['VALUE']?>">*/
?>


<input type="hidden" class= "cwiz-res-id" value="<?=$arItem['ID']?>">
<?if($cwizType == "second"):?>
    <div class="wqec-result-wrap">
        
        <div class="wqec-result-val">
            <?if($arItem["PROPERTIES"]["RESULT_SHOW_USERS_RESULT"]["VALUE"] == "Y"):?>
                <?=loc::GetMessage("CWIZ_RESULT_USER");?><?=$arVal["TOTAL_POINTS"]?><?=loc::GetMessage("CWIZ_RESULT_MAX");?><?=$cwizMaxResult?><?=loc::GetMessage("CWIZ_RESULT_POINTS");?>
            <?endif;?>
            <?if(strlen($arItem["PROPERTIES"]["RESULT_MAIN_TITLE"]["VALUE"])>0):?>
                <?=$arItem['PROPERTIES']['RESULT_MAIN_TITLE']['~VALUE']?>
            <?endif;?>
            <!-- <div class="wqec-restart wqec-hide"></div> -->
            
        </div>
        <div class="wrap-result-inner <?if($USER->isAdmin()):?>cquiz-edit-parent<?endif;?>">
            
            <?if($x > 0):?>

                <?

                    $two_cols = false;

                    if(strlen($arItem['PROPERTIES']["SPECIAL_ONTITLE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_TEXT"]["VALUE"]["TEXT"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["RESULT_FORM_TITLE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["RESULT_FORM_SUBTITLE"]["VALUE"])>0 || (is_array($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"]) && !empty($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"])))
                        $two_cols = true;
                

                    $left_col = "wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12";
                    $right_col = "";

                    if($two_cols)
                    {
                        $left_col = "wqec-col-lg-7 wqec-col-md-7 wqec-col-sm-7 wqec-col-xs-12";
                        $right_col = "wqec-col-lg-5 wqec-col-md-5 wqec-col-sm-5 wqec-col-xs-12";
                    }
                ?>
                
                <div class="wqec-col <?=$left_col?>">

                    <?if(strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"])>0 ):?>

                        <?$bg = '';?>

                        <?if(strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0):?>
                            <?$img = CFile::ResizeImageGet($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"], array('width'=>800, 'height'=>600), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, false, false, 85);?>

                            <?$bg = "style='background-image: url(".$img['src'].");'";?>

                        <?endif;?>

                        <?if(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"])>0):?>
                            <?preg_match("/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/", $arItem["PROPERTIES"]["RESULT_VIDEO"]["VALUE"], $out);?>
                        <?endif;?>
                        

                        <div class="wqec-container-video-pic<?if($arItem['PROPERTIES']["RESULT_PICTURE_COVER"]["VALUE"] == "Y" || strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0):?> wqec-cover<?endif;?>" <?=$bg?>>
                            <?if(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0 && strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0):?>
                                <a class="wqec-link-video" data-wqec-video="<?=$out[1]?>"></a>

                            <?elseif(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0):?>
                                <iframe allowfullscreen="" frameborder="0" height="100%" src="https://www.youtube.com/embed/<?=$out[1]?>?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=1" width="100%"></iframe>
                            <?endif;?>
                        </div>

                    <?endif;?>

                    <?if(strlen($arItem["PROPERTIES"]["RESULT_MAIN_TEXT"]["VALUE"]['TEXT'])>0):?>
                        <div class="wqec-result-desc"><?=$arItem['PROPERTIES']['RESULT_MAIN_TEXT']['~VALUE']['TEXT']?></div>
                    <?endif;?>

                      
                </div>

                <?if($two_cols):?>

                    <div class="wqec-col <?=$right_col?>">
                        <table class="wqec-wrap-act">
                            <tr>
                                <td>
                                    <div class="wqec-questions wqec-active wqec-clear">
                                        <?if(strlen($arItem['PROPERTIES']["SPECIAL_ONTITLE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_TEXT"]["VALUE"]["TEXT"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?>

                                            <div class="wqec-special-wrap<?if(strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?> wqec-pic<?endif;?>">

                                                <?if(strlen($arItem['PROPERTIES']["SPECIAL_ONTITLE"]["VALUE"])>0):?>
                                                    <div class="wqec-spec-ontitle"><?=$arItem['PROPERTIES']["SPECIAL_ONTITLE"]["~VALUE"]?></div>
                                                <?endif;?>

                                                <?if(strlen($arItem['PROPERTIES']["SPECIAL_TEXT"]["VALUE"]["TEXT"])>0):?>
                                                    <div class="wqec-spec-text"><?=$arItem['PROPERTIES']["SPECIAL_TEXT"]["~VALUE"]["TEXT"]?></div>
                                                <?endif;?>


                                                <?if(strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?>

                                                <?$img = CFile::ResizeImageGet($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"], array('width'=>200, 'height'=>200), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, false, false, 85);?>

                                                <img src="<?=$img["src"]?>" alt="specimage">

                                                <?endif;?>
                                            </div>

                                        <?endif;?>

                                        <?if(strlen($arItem['PROPERTIES']["RESULT_FORM_TITLE"]["VALUE"])>0):?>
                                            <div class="wqec-form-title"><?=$arItem['PROPERTIES']["RESULT_FORM_TITLE"]["~VALUE"]?></div>
                                        <?endif;?>

                                        <?if(strlen($arItem['PROPERTIES']["RESULT_FORM_SUBTITLE"]["VALUE"])>0):?>
                                            <div class="wqec-form-subtitle"><?=$arItem['PROPERTIES']["RESULT_FORM_SUBTITLE"]["~VALUE"]?></div>
                                        <?endif;?>

                                        <?if(is_array($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"]) && !empty($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"])):?>
                                            <input type="hidden" name="wqec_element" value="<?=$arItem["ID"]?>">
                                            <input type="hidden" name="cwisSectionId" value="<?=$cwizSectionId?>">


                                            <?
                                            $show_inputs = false;
                                            if($arItem['PROPERTIES']["RESULT_FORM_ALL_INPUTS"]["VALUE"] != 'Y')
                                                $show_inputs = true;
                                            ?>

                                       

                                            <?foreach($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"] as $k=>$arInput):?>

                                                <?if($arInput == "email"):?>
                                                    <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                        <div class="wqec-input">
                                                            <div class="wqec-inp-bg"></div>

                                                            <span class="wqec-inp-desc"><?=GetMessage('CWIZ_EMAIL')?></span>
                                                            <input class="input-text wqec-in-focus-anim wqec-email<?if(in_array("email", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?> require<?endif;?>" name="wqec-email" type="text">
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                <?endif;?>

                                                <?if($arInput == "name"):?>
                                                    <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                        <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                            <div class="wqec-inp-bg"></div>
                                                            <span class="wqec-inp-desc"><?=GetMessage('CWIZ_NAME')?></span>
                                                            <input class='input-text wqec-in-focus-anim <?if(in_array("name", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?> wqec-name' name="wqec-name" type="text">
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                <?endif;?>


                                                <?if($arInput == "phone"):?>
                                                    
                                                    <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                        <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                            <div class="wqec-inp-bg"></div>

                                                            <input class="input-text wqec-phone <?if(in_array("phone", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?>" data-placeholder="<?=GetMessage('CWIZ_PHONE')?>" name="wqec-phone" placeholder="<?=GetMessage('CWIZ_PHONE')?>" type="text">
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                <?endif;?>
                                                                                          

                                                <?if($arInput == "file"):?>
            
                                                    <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                        <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                            <div class="load-file">
                                                                <label><span class="text-file-style file-none area-file"><?=GetMessage('CWIZ_FILE')?></span> 

                                                                <input class="wqec-hide <?if(in_array("file", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?>" name="cquiz_userfile" type="file">

                                                                <?if(in_array("file", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?><span class="star-req"></span><?endif;?>

                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?endif;?>

                                                
                                            <?endforeach;?>

                                            <?if(count($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"])>1 && $show_inputs):?>
                                                <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                    <div class="wqec-input-btn">
                                                        <a class="wqec-button-def wqec-blue wqec-show-inputs">
                                                            <?if(strlen($arItem['PROPERTIES']['RESULT_BUTTON_NAME']['VALUE']) > 0):?>

                                                                <?=$arItem['PROPERTIES']['RESULT_BUTTON_NAME']['~VALUE']?>
                                                        
                                                            <?else:?>

                                                                <?=GetMessage('CWIZ_BUTTON')?>

                                                            <?endif;?>
                                                        </a>
                                                    </div>
                                                </div>

                                            <?endif;?>

                                            <div class="wrap-btn-submit<?if(count($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"]) > 1 && $show_inputs):?> wqec-hide<?endif;?>">
                                                <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                    <div class="wqec-input-btn ">
                                                        <div class="wqec-load">
                                                            <div class="wqec-form-preload">
                                                                <div class="wqec-audio-wave">
                                                                <span></span><span></span><span></span><span></span><span></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button class="wqec-form-submit wqec-button-def wqec-blue wqec-active" type="submit" <?if(strlen($arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]) > 0):?> data-link='<?=$arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]?>'<?endif;?>>
                                                            <?if(strlen($arItem['PROPERTIES']['RESULT_BUTTON_NAME']['VALUE']) > 0):?>

                                                                <?=$arItem['PROPERTIES']['RESULT_BUTTON_NAME']['~VALUE']?>
                                                        
                                                            <?else:?>

                                                                <?=GetMessage('CWIZ_BUTTON')?>

                                                            <?endif;?>

                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">

                                                    <div class="wrap-agree">

                                                        <label>
                                                            <input type="checkbox" checked id="agreecheck" name="checkboxAgree" value="agree">
                                                            <span></span>   
                                                        </label>   

                                                        <div class="wrap-desc">                                                                    
                                                            <span class="wqec-text"><?=GetMessage('CWIZ_PERSONAL_POLITIC')?><a class="wqec-agree" data-agreemodal="wqec-agree<?=$cwizSectionId?>"><?=GetMessage('CWIZ_PERSONAL_POLITIC_CALL')?></a></span>
                                                            
                                                        </div>
                                                    </div>

                                                </div>
                                                
                                            </div>

                                        <?endif;?>

                                    </div>



                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 wqec-thank">
                                        <?if(strlen($arItem['PROPERTIES']['RESULT_TEXT_THANK']['~VALUE']['TEXT']) > 0):?>
                                            <?=$arItem['PROPERTIES']['RESULT_TEXT_THANK']['~VALUE']['TEXT']?>
                                        <?else:?>
                                            <?=GetMessage('CWIZ_THANK')?>
                                        <?endif;?>

                                        <?if($cwizSoc && strlen($arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]) == 0):?>
                                    

                                            <div class="wqec-soc">
                                                <div class="desc"><?=GetMessage('CWIZ_SHARE_TEXT')?></div>
                                                <a class="wqec-share wqec-soc quiz_ic-vk" data-wqec-name="vk" data-wqec-title="<?=$arResult['SECTION_INFO']['~NAME']?>" data-wqec-image="<?=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult["SECTION_INFO"]["DETAIL_PICTURE"])?>"></a>
                                                <a class="wqec-share wqec-soc quiz_ic-tw" data-wqec-name="tw" data-wqec-title="<?=$arResult['SECTION_INFO']['~NAME']?>"></a>
                                                <a class="wqec-share wqec-soc quiz_ic-fb" data-wqec-name="fb" data-wqec-image="<?=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult["SECTION_INFO"]["DETAIL_PICTURE"])?>"></a>
                                                <a class="wqec-share wqec-soc quiz_ic-ok" data-wqec-name="ok"></a>
                                                <a class="wqec-share wqec-soc quiz_ic-gp" data-wqec-name="gp"></a>
                                            </div>
                                      

                                        <?endif;?>
                                        
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </div>

                <?endif;?>
                <div class="wqec-clear"></div>

                <?if($USER->isAdmin()):?>
                    <a target = "_blank" class="cquiz-edit" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=$arItem["IBLOCK_ID"]?>&amp;type=<?=$arItem["IBLOCK_TYPE_ID"]?>&amp;ID=<?=$arItem["ID"]?>&amp;find_section_section=<?=$arItem["IBLOCK_SECTION_ID"]?>"><span><?=GetMessage("CQUIZ_RES_EDIT_TEXT");?></span></a>
                <?endif;?>

                
                
            <?else:?>

                <div class="wqec-no-res"><?=loc::GetMessage("CWIZ_EMPTY_RESULT");?></div>

                <?if($USER->isAdmin()):?>
                    <div class="cquiz-alert-message">

                        <?if($arResult["SECTION_INFO"]["UF_TYPE_CALC_ENUM"]["XML_ID"] == "symbols"):?>
                            <?=GetMessage("CQUIZ_RES_ALERT_MES_SYMBOLS");?><?=$arVal["TOTAL_POINTS"]?>
                        <?else:?>
                            <?=GetMessage("CQUIZ_RES_ALERT_MES_POINTS");?><?=$arVal["TOTAL_POINTS"]?>
                        <?endif;?>

                    </div>


                    <a target = "_blank" class="cquiz-add-btn" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=$arResult["SECTION_INFO"]["IBLOCK_ID"]?>&type=<?=$arResult["SECTION_INFO"]["IBLOCK_TYPE_ID"]?>&ID=0&lang=ru&IBLOCK_SECTION_ID=<?=$arResult["SECTION_INFO"]["ID"]?>&find_section_section=<?=$arResult["SECTION_INFO"]["ID"]?>"><?=GetMessage("CQUIZ_RES_ADD");?></a>
                <?endif;?>

            <?endif;?>
        </div>
    </div>
<?else:?>

    <?if($x > 0):?>
        <div class="wqec-row wqec-clear">

            <?if($arItem["PROPERTIES"]["RESULT_SHOW_USERS_RESULT"]["VALUE"] == "Y"):?>
                <div class="wqec-result-val"><?=loc::GetMessage("CWIZ_RESULT_USER");?><?=$arVal["TOTAL_POINTS"]?><?=loc::GetMessage("CWIZ_RESULT_MAX");?><?=$cwizMaxResult?><?=loc::GetMessage("CWIZ_RESULT_POINTS");?></div>
            <?endif;?>
            
            <div class="wqec-col wqec-col-lg-7 wqec-col-md-7 wqec-col-sm-12 wqec-col-xs-12">
                <div class="wqec-result-wrap <?if($USER->isAdmin()):?>cquiz-edit-parent<?endif;?>">
                    

                    <?if(strlen($arItem["PROPERTIES"]["RESULT_MAIN_TITLE"]["VALUE"])>0):?>
                        <div class="wqec-result-title"><?=$arItem['PROPERTIES']['RESULT_MAIN_TITLE']['~VALUE']?></div>
                    <?endif;?>

                    
                    <?if(strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"])>0 ):?>

                        <?$bg = '';?>

                        <?if(strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0):?>
                            <?$img = CFile::ResizeImageGet($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"], array('width'=>800, 'height'=>600), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, false, false, 85);?>

                            <?$bg = "style='background-image: url(".$img['src'].");'";?>

                        <?endif;?>

                        <?if(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"])>0):?>
                            <?preg_match("/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/", $arItem["PROPERTIES"]["RESULT_VIDEO"]["VALUE"], $out);?>
                        <?endif;?>
                        

                        <div class="wqec-container-video-pic<?if($arItem['PROPERTIES']["RESULT_PICTURE_COVER"]["VALUE"] == "Y" || strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0):?> wqec-cover<?endif;?>" <?=$bg?>>
                            <?if(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0 && strlen($arItem['PROPERTIES']["RESULT_PICTURE"]["VALUE"])>0):?>
                                <a class="wqec-link-video" data-wqec-video="<?=$out[1]?>"></a>

                            <?elseif(strlen($arItem['PROPERTIES']["RESULT_VIDEO"]["VALUE"]) > 0):?>
                                <iframe allowfullscreen="" frameborder="0" height="100%" src="https://www.youtube.com/embed/<?=$out[1]?>?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=1" width="100%"></iframe>
                            <?endif;?>
                        </div>

                    <?endif;?>

                    <?if(strlen($arItem["PROPERTIES"]["RESULT_MAIN_TEXT"]["VALUE"]['TEXT'])>0):?>
                        <div class="wqec-result-desc"><?=$arItem['PROPERTIES']['RESULT_MAIN_TEXT']['~VALUE']['TEXT']?></div>
                    <?endif;?>

                    <?if($USER->isAdmin()):?>
                        <a target = "_blank" class="cquiz-edit" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=$arItem["IBLOCK_ID"]?>&amp;type=<?=$arItem["IBLOCK_TYPE_ID"]?>&amp;ID=<?=$arItem["ID"]?>&amp;find_section_section=<?=$arItem["IBLOCK_SECTION_ID"]?>"><span><?=GetMessage("CQUIZ_RES_EDIT_TEXT");?></span></a>
                    <?endif;?>

                </div>
            </div>

            <div class="wqec-col wqec-col-lg-5 wqec-col-md-5 wqec-col-sm-12 wqec-col-xs-12">
                <table class="wqec-wrap-act">
                    <tr>
                        <td>
                            <div class="wqec-questions wqec-active">
                                <?if(strlen($arItem['PROPERTIES']["SPECIAL_ONTITLE"]["VALUE"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_TEXT"]["VALUE"]["TEXT"])>0 || strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?>

                                    <div class="wqec-special-wrap <?if(strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?> wqec-pic<?endif;?>">

                                        <?if(strlen($arItem['PROPERTIES']["SPECIAL_ONTITLE"]["VALUE"])>0):?>
                                            <div class="wqec-spec-ontitle"><?=$arItem['PROPERTIES']["SPECIAL_ONTITLE"]["~VALUE"]?></div>
                                        <?endif;?>

                                        <?if(strlen($arItem['PROPERTIES']["SPECIAL_TEXT"]["VALUE"]["TEXT"])>0):?>
                                            <div class="wqec-spec-text"><?=$arItem['PROPERTIES']["SPECIAL_TEXT"]["~VALUE"]["TEXT"]?></div>
                                        <?endif;?>


                                        <?if(strlen($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"])>0):?>

                                        <?$img = CFile::ResizeImageGet($arItem['PROPERTIES']["SPECIAL_PICTURE"]["VALUE"], array('width'=>200, 'height'=>200), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, false, false, 85);?>

                                        <img src="<?=$img["src"]?>" alt="specimage">

                                        <?endif;?>
                                    </div>

                                <?endif;?>

                                <?if(strlen($arItem['PROPERTIES']["RESULT_FORM_TITLE"]["VALUE"])>0):?>
                                    <div class="wqec-form-title"><?=$arItem['PROPERTIES']["RESULT_FORM_TITLE"]["~VALUE"]?></div>
                                <?endif;?>

                                <?if(strlen($arItem['PROPERTIES']["RESULT_FORM_SUBTITLE"]["VALUE"])>0):?>
                                    <div class="wqec-form-subtitle"><?=$arItem['PROPERTIES']["RESULT_FORM_SUBTITLE"]["~VALUE"]?></div>
                                <?endif;?>

                                <?if(is_array($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"]) && !empty($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"])):?>
                                    <input type="hidden" name="wqec_element" value="<?=$arItem["ID"]?>">
                                    <input type="hidden" name="cwisSectionId" value="<?=$cwizSectionId?>">
                                    <?
                                        $show_inputs = false;
                                        if($arItem['PROPERTIES']["RESULT_FORM_ALL_INPUTS"]["VALUE"] != 'Y')
                                            $show_inputs = true;
                                    ?>

                                    <?foreach($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"] as $k=>$arInput):?>

                                        <?if($arInput == "email"):?>
                                            <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                <div class="wqec-input">
                                                    <div class="wqec-inp-bg"></div>

                                                    <span class="wqec-inp-desc"><?=GetMessage('CWIZ_EMAIL')?></span>
                                                    <input class="input-text wqec-in-focus-anim wqec-email<?if(in_array("email", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?> require<?endif;?>" name="wqec-email" type="text">
                                                    <span></span>
                                                </div>
                                            </div>
                                        <?endif;?>

                                        <?if($arInput == "name"):?>
                                            <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                    <div class="wqec-inp-bg"></div>
                                                    <span class="wqec-inp-desc"><?=GetMessage('CWIZ_NAME')?></span>
                                                    <input class='input-text wqec-in-focus-anim <?if(in_array("name", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?> wqec-name' name="wqec-name" type="text">
                                                    <span></span>
                                                </div>
                                            </div>
                                        <?endif;?>


                                        <?if($arInput == "phone"):?>
                                            <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                    <div class="wqec-inp-bg"></div>

                                                    <input class="input-text wqec-phone <?if(in_array("phone", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?>" data-placeholder="<?=GetMessage('CWIZ_PHONE')?>" name="wqec-phone" placeholder="<?=GetMessage('CWIZ_PHONE')?>" type="text">
                                                    <span></span>
                                                </div>
                                            </div>
                                        <?endif;?>

                                        

                                        


                                        <?if($arInput == "file"):?>
        
                                            <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                                <div class="wqec-input <?if($k != 0 && $show_inputs):?>wqec-hide<?endif;?>">
                                                    <div class="load-file">
                                                        <label><span class="text-file-style file-none area-file"><?=GetMessage('CWIZ_FILE')?></span> 

                                                        <input class="wqec-hide <?if(in_array("file", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?>require<?endif;?>" name="cquiz_userfile" type="file">

                                                        <?if(in_array("file", $arItem["PROPERTIES"]["RESULT_INPUTS_REQ"]["VALUE_XML_ID"])):?><span class="star-req"></span><?endif;?>

                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                        <?endif;?>

                                        
                                    <?endforeach;?>

                                    <?if(count($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"])>1 && $show_inputs):?>
                                        <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                            <div class="wqec-input-btn">
                                                <a class="wqec-button-def wqec-blue wqec-show-inputs">
                                                    <?if(strlen($arItem['PROPERTIES']['RESULT_BUTTON_NAME']['VALUE']) > 0):?>

                                                        <?=$arItem['PROPERTIES']['RESULT_BUTTON_NAME']['~VALUE']?>
                                                
                                                    <?else:?>

                                                        <?=GetMessage('CWIZ_BUTTON')?>

                                                    <?endif;?>
                                                </a>
                                            </div>
                                        </div>

                                    <?endif;?>

                                    <div class="wrap-btn-submit<?if(count($arItem["PROPERTIES"]["RESULT_INPUTS"]["VALUE_XML_ID"]) > 1 && $show_inputs):?> wqec-hide<?endif;?>">
                                        <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">
                                            <div class="wqec-input-btn ">
                                                <div class="wqec-load">
                                                    <div class="wqec-form-preload">
                                                        <div class="wqec-audio-wave">
                                                        <span></span><span></span><span></span><span></span><span></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button class="wqec-form-submit wqec-button-def wqec-blue wqec-active" type="submit" <?if(strlen($arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]) > 0):?> data-link='<?=$arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]?>'<?endif;?>>
                                                    <?if(strlen($arItem['PROPERTIES']['RESULT_BUTTON_NAME']['VALUE']) > 0):?>

                                                        <?=$arItem['PROPERTIES']['RESULT_BUTTON_NAME']['~VALUE']?>
                                                
                                                    <?else:?>

                                                        <?=GetMessage('CWIZ_BUTTON')?>

                                                    <?endif;?>

                                                </button>
                                            </div>
                                        </div>
                                        <div class="wqec-col-lg-12 wqec-col-md-12 wqec-col-sm-12 wqec-col-xs-12">

                                            <div class="wrap-agree">

                                                <label>
                                                    <input type="checkbox" checked id="agreecheck" name="checkboxAgree" value="agree">
                                                    <span></span>   
                                                </label>   

                                                <div class="wrap-desc">                                                                    
                                                    <span class="wqec-text"><?=GetMessage('CWIZ_PERSONAL_POLITIC')?><a class="wqec-agree" data-agreemodal="wqec-agree<?=$cwizSectionId?>"><?=GetMessage('CWIZ_PERSONAL_POLITIC_CALL')?></a></span>
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        
                                    </div>

                                <?endif;?>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 wqec-thank">
                                <?if(strlen($arItem['PROPERTIES']['RESULT_TEXT_THANK']['~VALUE']['TEXT']) > 0):?>
                                    <?=$arItem['PROPERTIES']['RESULT_TEXT_THANK']['~VALUE']['TEXT']?>
                                <?else:?>
                                    <?=GetMessage('CWIZ_THANK')?>
                                <?endif;?>

                                <?if($cwizSoc && strlen($arItem["PROPERTIES"]["RESULT_REDIRECT_USER"]["VALUE"]) == 0):?>

                                    <div class="wqec-soc">
                                        <div class="desc"><?=GetMessage('CWIZ_SHARE_TEXT')?></div>
                                        <a class="wqec-share wqec-soc quiz_ic-vk" data-wqec-name="vk" data-wqec-title="<?=$arResult['SECTION_INFO']['~NAME']?>" data-wqec-image="<?=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult["SECTION_INFO"]["DETAIL_PICTURE"])?>"></a>
                                        <a class="wqec-share wqec-soc quiz_ic-tw" data-wqec-name="tw" data-wqec-title="<?=$arResult['SECTION_INFO']['~NAME']?>"></a>
                                        <a class="wqec-share wqec-soc quiz_ic-fb" data-wqec-name="fb" data-wqec-image="<?=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult["SECTION_INFO"]["DETAIL_PICTURE"])?>"></a>
                                        <a class="wqec-share wqec-soc quiz_ic-ok" data-wqec-name="ok"></a>
                                        <a class="wqec-share wqec-soc quiz_ic-gp" data-wqec-name="gp"></a>
                                    </div>
                              

                                <?endif;?>
                                
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    <?else:?>

        <div class="wqec-no-res"><?=loc::GetMessage("CWIZ_EMPTY_RESULT");?></div>

        <?if($USER->isAdmin()):?>
            <div class="cquiz-alert-message">
                <?if($arResult["SECTION_INFO"]["UF_TYPE_CALC_ENUM"]["XML_ID"] == "symbols"):?>
                    <?=GetMessage("CQUIZ_RES_ALERT_MES_SYMBOLS");?><?=$arVal["TOTAL_POINTS"]?>
                <?else:?>
                    <?=GetMessage("CQUIZ_RES_ALERT_MES_POINTS");?><?=$arVal["TOTAL_POINTS"]?>
                <?endif;?>
                    
            </div>
            <a target = "_blank" class="cquiz-add-btn" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=$arResult["SECTION_INFO"]["IBLOCK_ID"]?>&type=<?=$arResult["SECTION_INFO"]["IBLOCK_TYPE_ID"]?>&ID=0&lang=ru&IBLOCK_SECTION_ID=<?=$arResult["SECTION_INFO"]["ID"]?>&find_section_section=<?=$arResult["SECTION_INFO"]["ID"]?>"><?=GetMessage("CQUIZ_RES_ADD");?></a>
        <?endif;?>



    <?endif;?>
<?endif;?>
<!--  /.wqec-form-->



<?
if(trim($_REQUEST["cquiz-send_res"]) == "send_res")
{
    $_REQUEST["wqec-send"] = "Y";
    $_REQUEST["cwisSectionId"] = trim($cwizSectionId);
    $_REQUEST["wqec_element"] = trim($arItem["ID"]);
    SendWqecForm($_REQUEST, $site_id, "send_res");
}
    
?>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>