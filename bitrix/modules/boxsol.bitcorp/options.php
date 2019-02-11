<?
if (!$USER->IsAdmin())
    return;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if($RIGHT >= "R"){
    Loc::loadMessages(__FILE__);
    Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
    CModule::IncludeModule(BITCORP_MODULE_NAME);

    $moduleClass = "CBitcorp";
    $moduleID = "boxsol.bitcorp";

    $arSites = array();
    $arSiteList = array('');
    $dbSites = CSite::GetList($b = "sort", $o = "asc", array("ACTIVE" => "Y"));
    while ($arSite = $dbSites->Fetch()) {
        $arSites[] = $arSite;
        $arSiteList[] = $arSite['ID'];
    }       

    $arTabs = array();
    foreach($arSites as $key => $arSite){
        $arBackParametrs = $moduleClass::GetBackParametrsValues($arSite["ID"], false);       
        $arTabs[] = array(
            "DIV" => "edit_" . $arSite["ID"],           
            "TAB" => Loc::getMessage("BITCORP_SITE_SETTINGS", array("#NAME#" => $arSite["NAME"], "#ID#" => $arSite["ID"])),
            "ICON" => "settings_" . $arSite["ID"], 
            "TITLE" => Loc::getMessage("BITCORP_SITE_SETTINGS", array("#NAME#" => $arSite["NAME"], "#ID#" => $arSite["ID"])),           
            "SITE_ID" => $arSite["ID"],
            "OPTIONS" => $arBackParametrs,
        );  
    }    

    $tabControl = new CAdminTabControl("tabControl", $arTabs);

    if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) && check_bitrix_sessid()) {
        
        global $APPLICATION;
        if(strlen($RestoreDefaults)){            
            Option::delete($moduleID);
            $APPLICATION->DelGroupRight($moduleID);
        } else {
            COption::RemoveOption($moduleID, "sid");    
            foreach($arTabs as $key => $arTab) {                
                $optionsSiteID = $arTab["SITE_ID"];
                $arBackParametrs = $moduleClass::GetBackParametrsValues($optionsSiteID, false);
                foreach($moduleClass::getParametrs() as $blockCode => $arBlock) {                    
                    foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {                                             

                        if($arOption["TYPE"] == "file") {                           
                            $arPICTURE = $_FILES[$optionCode."_".$optionsSiteID];                           
                            $arPICTURE["del"] = ${$optionCode."_".$optionsSiteID."_del"};
                            $arPICTURE["MODULE_ID"] = $moduleID;                            
                            if($arPICTURE["size"] > 0) {
                                $_REQUEST[$optionCode."_".$optionsSiteID] = CFile::SaveFile($arPICTURE, $moduleID);
                            } elseif($arPICTURE["del"] == "Y") {                                                                
                                CFile::Delete($arBackParametrs[$optionCode]);                                
                                $_REQUEST[$optionCode."_".$optionsSiteID] = "";                         
                            } else {
                                $_REQUEST[$optionCode."_".$optionsSiteID] = $arBackParametrs[$optionCode];
                            }
                        }
                        
                        $newVal = $_REQUEST[$optionCode."_".$optionsSiteID];
                        
                        if($arOption["TYPE"] == "checkbox") {
                            if(!strlen($newVal) || $newVal != "Y") {
                                $newVal = "N";
                            }
                        } elseif($arOption["TYPE"] == "multiselectbox") {
                            if(!is_array($newVal))
                                $newVal = array();
                        } elseif($arOption["TYPE"] == "includefile"){
                            continue;
                        }
                        $arTab["OPTIONS"][$optionCode] = $newVal;                         
                    }
                } 

                $var = COption::SetOptionString($moduleID, "OPTIONS", serialize((array)$arTab["OPTIONS"]), "", $arTab["SITE_ID"]);
                $arTabs[$key] = $arTab;
            }
        }
        
        if(CHTMLPagesCache::isOn()) {
            $staticHtmlCache = \Bitrix\Main\Data\StaticHtmlCache::getInstance();
            $staticHtmlCache->deleteAll();
        }
        
        foreach($arSites as $key => $arSite) {
            BXClearCache(true, "/".$arSite["ID"]."/boxsol/forms/");
            BXClearCache(true, "/".$arSite["ID"]."/news/projects/detail/");
            BXClearCache(true, "/".$arSite["ID"]."/news/services/detail/");                       
        }
        
        $APPLICATION->RestartBuffer();
    }

    CModule::IncludeModule("fileman");
    CJSCore::Init(array("jquery"));
    CAjax::Init();

    $tabControl->Begin();?> 
    
    <form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>" enctype="multipart/form-data">        
        <?=bitrix_sessid_post();
        foreach($arTabs as $key => $arTab) {
            $tabControl->BeginNextTab();
            if($arTab["SITE_ID"]) {
                $optionsSiteID = $arTab["SITE_ID"];
                foreach($moduleClass::getParametrs() as $blockCode => $arBlock) {?>                    
                    <tr class="heading">
                        <td colspan="2"><?=$arBlock["TITLE"]?></td>
                    </tr>
                    <?foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {
                        if(isset($arTab["OPTIONS"][$optionCode])) {
                            $arControllerOption = CControllerClient::GetInstalledOptions($module_id);
                            if(isset($arOption["NOTE"])) {?>
                                <tr>
                                    <td colspan="2" align="center">
                                        <?=BeginNote('align="center"');?>
                                        <?=$arOption["NOTE"]?>
                                        <?=EndNote();?>
                                    </td>
                                </tr>
                            <?} else {
                                $optionName = $arOption["TITLE"];
                                $optionHint = $arOption["HINT"];
                                $optionType = $arOption["TYPE"];
                                $optionList = $arOption["LIST"];
                                $optionDefault = $arOption["DEFAULT"];
                                $optionVal = $arTab["OPTIONS"][$optionCode];
                                $optionSize = $arOption["SIZE"];
                                $optionCols = $arOption["COLS"];
                                $optionRows = $arOption["ROWS"];
                                $optionChecked = $optionVal == "Y" ? "checked" : "";
                                $optionDisabled = isset($arControllerOption[$optionCode]) || array_key_exists("DISABLED", $arOption) && $arOption["DISABLED"] == "Y" ? "disabled" : "";
                                $optionSup_text = array_key_exists("SUP", $arOption) ? $arOption["SUP"] : "";
                                $optionController = isset($arControllerOption[$optionCode]) ? "title='".GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT")."'" : "";?>
                                <tr>
                                    <td class="<?=(in_array($optionType, array("multiselectbox", "textarea", "statictext", "statichtml")) ? "adm-detail-valign-top" : "")?>" width="50%">
                                        <?if($optionType == "checkbox"):?>
                                            <label for="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>"><?=$optionName?></label><br>
                                            <small style="color: #757575;"><?=$optionHint?></small>
                                        <?else:?>
                                            <?=$optionName?><br>
                                            <small style="color: #757575;"><?=$optionHint?></small>
                                        <?endif;?>
                                        <?if(strlen($optionSup_text)):?>
                                            <span class="required"><sup><?=$optionSup_text?></sup></span>
                                        <?endif;?>
                                    </td>
                                    <td width="50%">                                        
                                        <?if($optionType == "checkbox"):?>
                                            <input type="checkbox" <?=$optionController?> id="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" value="Y" <?=$optionChecked?> <?=$optionDisabled?> <?=(strlen($optionDefault) ? $optionDefault : "")?> />
                                        <?elseif($optionType == "text" || $optionType == "password"):?>
                                            <input type="<?=$optionType?>" <?=$optionController?> size="<?=$optionSize?>" maxlength="255" value="<?=htmlspecialcharsbx($optionVal)?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" <?=$optionDisabled?> <?=($optionCode == "password" ? "autocomplete='off'" : "")?> />
                                        <?elseif($optionType == "number"):?>
                                            <input type="<?=$optionType?>" <?=$optionController?> size="<?=$optionSize?>" value="<?=htmlspecialcharsbx($optionVal)?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" <?=$optionDisabled?> step="any" />
                                        <?elseif($optionType == "selectbox"):?>
                                            <?if(!is_array($optionList)) $optionList = (array)$optionList;
                                            $arr_keys = array_keys($optionList);?>
                                            <select name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" <?=$optionController?> <?=$optionDisabled?>>
                                                <?for($j = 0, $c = count($arr_keys); $j < $c; ++$j):?>
                                                    <option value="<?=$arr_keys[$j]?>" <?if($optionVal == $arr_keys[$j]) echo "selected"?>><?=htmlspecialcharsbx((is_array($optionList[$arr_keys[$j]]) ? $optionList[$arr_keys[$j]]["TITLE"] : $optionList[$arr_keys[$j]]))?></option>
                                                <?endfor;?>
                                            </select>
                                        <?elseif($optionType == "multiselectbox"):?>
                                            <?if(!is_array($optionList)) $optionList = (array)$optionList;
                                            $arr_keys = array_keys($optionList);
                                            if(!is_array($optionVal)) $optionVal = (array)$optionVal;?>
                                            <select size="<?=$optionSize?>" <?=$optionController?> <?=$optionDisabled?> multiple name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>[]" >
                                                <?for($j = 0, $c = count($arr_keys); $j < $c; ++$j):?>
                                                    <option value="<?=$arr_keys[$j]?>" <?if(in_array($arr_keys[$j], $optionVal)) echo "selected"?>><?=htmlspecialcharsbx((is_array($optionList[$arr_keys[$j]]) ? $optionList[$arr_keys[$j]]["TITLE"] : $optionList[$arr_keys[$j]]))?></option>
                                                <?endfor;?>
                                            </select>
                                        <?elseif($optionType == "textarea"):?>
                                            <textarea <?=$optionController?> <?=$optionDisabled?> rows="<?=$optionRows?>" cols="<?=$optionCols?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>"><?=htmlspecialcharsbx($optionVal)?></textarea>
                                        <?elseif($optionType == "file"):
                                            echo CFileInput::Show(htmlspecialcharsbx($optionCode)."_".$optionsSiteID, htmlspecialcharsbx($optionVal),
                                                array(
                                                    "IMAGE" => "Y",
                                                    "PATH" => "Y",
                                                    "FILE_SIZE" => "Y",
                                                    "DIMENSIONS" => "Y",
                                                    "IMAGE_POPUP" => "Y",
                                                    "MAX_SIZE" => array(
                                                        "W" => COption::GetOptionString("iblock", "detail_image_size"),
                                                        "H" => COption::GetOptionString("iblock", "detail_image_size"),
                                                    ),
                                                ), array(
                                                    'upload' => true,
                                                    'medialib' => false,
                                                    'file_dialog' => false,
                                                    'cloud' => false,
                                                    'del' => true,
                                                    'description' => false,
                                                )
                                            );
                                        elseif($optionType == "statictext"):?>
                                            <?=htmlspecialcharsbx($optionVal)?>
                                        <?elseif($optionType == "statichtml"):?>
                                            <?=$optionVal?>
                                        <?elseif($optionType === 'includefile'):?>
                                            <?
                                            if(!is_array($arOption['INCLUDEFILE'])){
                                                $arOption['INCLUDEFILE'] = array($arOption['INCLUDEFILE']);
                                            }
                                            foreach($arOption['INCLUDEFILE'] as $includefile){
                                                $includefile = str_replace('//', '/', str_replace('#SITE_DIR#', $arTab['SITE_DIR'].'/', $includefile));
                                                if(strpos($includefile, '#') === false){
                                                    $template = (isset($arOption['TEMPLATE']) && strlen($arOption['TEMPLATE']) ? 'include_area.php' : $arOption['TEMPLATE']);
                                                    $href = (!strlen($includefile) ? "javascript:;" : "javascript: new BX.CAdminDialog({'content_url':'/bitrix/admin/public_file_edit.php?site=".$arTab['SITE_ID']."&bxpublic=Y&from=includefile&templateID=".TEMPLATE_NAME."&path=".$includefile."&lang=".LANGUAGE_ID."&template=".$template."&subdialog=Y&siteTemplateId=".TEMPLATE_NAME."','width':'1009','height':'503'}).Show();");
                                                    ?><a class="adm-btn" href="<?=$href?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" title="<?=GetMessage('OPTIONS_EDIT_BUTTON_TITLE')?>"><?=GetMessage('OPTIONS_EDIT_BUTTON_TITLE')?></a>&nbsp;<?
                                                }
                                            }
                                            ?>                                       
                                        <?endif;?>
                                    </td>
                                </tr>
                            <?}
                        }
                    }
                }
            }
        }?>       

        <?if($REQUEST_METHOD == "POST" && strlen($Update) && check_bitrix_sessid()) {

            if(strlen($Update) && strlen($_REQUEST["back_url_settings"]))
                LocalRedirect($_REQUEST["back_url_settings"]);
            else
                LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());    
        }
        $tabControl->Buttons();?>        
        
        <input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">

        <?if(strlen($_REQUEST["back_url_settings"])):?>
            <input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
            <input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
        <?endif;?>

        <input type="submit" name="RestoreDefaults" title="<? echo Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           OnClick="return confirm('<? echo AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<? echo Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>">       
              
    </form>
    <style type="text/css">
        #wait_window_div{
            display:none !important;
        }
    </style>
    <?$tabControl->End();
} else {
    CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));
}?>