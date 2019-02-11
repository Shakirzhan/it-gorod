<?  
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
$module_id = "ambersite.independentmetatags";
$RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($RIGHT >= "R") :

IncludeModuleLangFile(__FILE__); 
global $DB;  
CModule::IncludeModule('ambersite.independentmetatags'); 
$strWarning=""; 

$dbSites = CSite::GetList($by="def", $order="desc", array()); while ($arSite = $dbSites->Fetch()) {$arSites[] = $arSite;}; 

foreach($arSites as $itemSite) { 
    $arAllOptions[] = array("ignored_parameters", GetMessage("AS_IND_IGNORIROVAT_SLEDUYUSHIE_PARAMETRU_URL"), "", array("textarea", "5"), $itemSite['LID']);
	$arAllOptions[] = array("ignore_index", GetMessage("AS_IND_IGNORIROVAT_INDEKSNUJ_FAIL"), "N", array("checkbox"), $itemSite['LID']);
	$arAllOptions[] = array("alternative_method", GetMessage("AS_IND_ALTERNATIVNUJ_METOD_VUVODA"), "N", array("checkbox"), $itemSite['LID']);
} 

foreach($arSites as $itemSite) {$aTabs[] = array("DIV" => "edit_".$itemSite['LID'], "TAB" => "[".$itemSite['LID']."] ".$itemSite['NAME'], "ICON" => "", "TITLE" => GetMessage("AS_IND_NASTROJKI_DLYA_SAJTA")." [".$itemSite['LID']."] ".$itemSite['NAME']);}
$aTabs[] = array("DIV" => "edit_rights", "TAB" => GetMessage("AS_IND_DOSTUP"), "ICON" => "", "TITLE" => GetMessage("AS_IND_UROVEN_DOSTUPA_K_MODULYU"));
$tabControl = new CAdminTabControl("tabControl", $aTabs); 

if($REQUEST_METHOD=="POST" && strlen($Apply.$RestoreDefaults)>0 && check_bitrix_sessid() && strlen($strWarning)==0 && $RIGHT=='W') { 
    if(strlen($RestoreDefaults)>0) COption::RemoveOption("ambersite.independentmetatags"); 
    elseif(strlen($Apply)>0) { 
        foreach($arAllOptions as $arOption){ 
            $name=$arOption[0]; 
            $val=$_REQUEST[$name.'_'.$arOption[4]]; 
            if($arOption[3][0]=="checkbox" && $val!="Y") $val="N"; 
            COption::SetOptionString("ambersite.independentmetatags", $name, $val, $arOption[1], $arOption[4]); 
        } unset($name, $val); 
    } 
	ob_start();
	$Update = $Update.$Apply;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();
	LocalRedirect($APPLICATION->GetCurPageParam($tabControl->ActiveTabParam(), array('tabControl_active_tab')));
} 

endif;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($RIGHT >= "R") : 

$APPLICATION->SetTitle(GetMessage("AS_IND_NASTRIJKI_MODULYA")." ".GetMessage("AS_IND_MODULE_NAME"));   

CAdminMessage::ShowOldStyleError($strWarning); 

$tabControl->Begin();?>  

<form method="POST" action="<?=$APPLICATION->GetCurPageParam()?>" ENCTYPE="multipart/form-data" name="post_form"> 
<?=bitrix_sessid_post();?> 
<? foreach($arSites as $itemSite):?> 
<? $tabControl->BeginNextTab();?> 
    <? 
    foreach($arAllOptions as $arOption): if($arOption[4]==$itemSite['LID']): 
        $val = COption::GetOptionString("ambersite.independentmetatags", $arOption[0], $arOption[2], $arOption[4]); 
        $type = $arOption[3]; 
    ?> 
    <tr> 
        <td width="40%" <? if($type[0]=="textarea") echo 'class="adm-detail-valign-top"'?>> 
            <label for="<?=htmlspecialcharsbx($arOption[0].'_'.$arOption[4])?>"><?=$arOption[1]?>:</label> 
        <td width="60%"> 
            <? if($type[0]=="checkbox"):?> 
                <input type="checkbox" id="<?=htmlspecialcharsbx($arOption[0].'_'.$arOption[4])?>" name="<?=htmlspecialcharsbx($arOption[0].'_'.$arOption[4])?>" value="Y"<? if($val=="Y")echo" checked";?>> 
            <? elseif($type[0]=="text"):?> 
                <input type="text" size="<?=$type[1]?>" maxlength="255" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($arOption[0].'_'.$arOption[4])?>"> 
            <? elseif($type[0]=="textarea"):?> 
                <textarea rows="<?=$type[1]?>" style="width:100%;" name="<?=htmlspecialcharsbx($arOption[0].'_'.$arOption[4])?>"><?=htmlspecialcharsbx($val)?></textarea> 
            <?endif?> 
        </td> 
    </tr>     
    <? endif; endforeach?> 
<? endforeach?> 
<? $tabControl->BeginNextTab();?>

<?
//$RIGHT = $APPLICATION->GetGroupRight($module_id);
//echo $RIGHT;
?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php"); ?>

<? $tabControl->Buttons();?> 
    <input type="submit" name="Apply" value="<?=GetMessage("AS_IND_PRIMENIT")?>" title="" class="adm-btn-save" <? if ($RIGHT < "W") echo "disabled" ?>> 
    <input type="submit" name="RestoreDefaults" title="" OnClick="return confirm('<?=AddSlashes(GetMessage("AS_IND_PO_UMOLCHANIJU_DANGER"))?>')" value="<?=GetMessage("AS_IND_PO_UMOLCHANIJU")?>" <? if ($RIGHT < "W") echo "disabled" ?>> 
<?  
$tabControl->End(); 
$tabControl->ShowWarnings("post_form", $message); 
?> 
</form>

<? endif; ?> 

<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>