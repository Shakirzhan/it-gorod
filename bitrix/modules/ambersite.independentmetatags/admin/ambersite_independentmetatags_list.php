<? 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$RIGHT = $APPLICATION->GetGroupRight('ambersite.independentmetatags');
if ($RIGHT >= "R") :

IncludeModuleLangFile(__FILE__); 
global $DB; 
CModule::IncludeModule('ambersite.independentmetatags');

$sTableID = "tbl_ambersite_independentmetatags";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if($lAdmin->EditAction())
{
	foreach($FIELDS as $id=>$arField) {
		
	}
}

if(($arID = $lAdmin->GroupAction())) {
	foreach($arID as $ID) {
		$ID = IntVal($ID); if($ID <= 0) continue;
		if($_REQUEST['action']=='delete' && $RIGHT=='W') {
			IndependentMetaTags::DeleteItem($ID);
		}
	}
}

$lAdmin->InitFilter(array("find_id", "find_siteid", "find_url")); 
$arFilter = Array("ID" => $find_id, "SITEID" => $find_siteid, "URL" => $find_url, "DATEFROM" => $find_date_from, "DATETO" => $find_date_to);

$rsData = IndependentMetaTags::GetDataList($by, $order, $arFilter);
$rsData = new CAdminResult($rsData, $sTableID); 
$rsData->NavStart("20"); 
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ZAPISI"))); 

$lAdmin->AddHeaders(array( 
  array("id" => "ID", 
    "content" => "ID", 
    "sort" => "id", 
    "default" => true, 
  ), 
  array("id" => "SITEID", 
    "content" => GetMessage("SAJT"), 
    "sort" => "siteid", 
    "default" => true, 
  ),
  array("id" => "URL", 
    "content" => "URL", 
    "sort" => "url", 
    "default" =>true, 
  ),
  array("id" => "TITLE", 
    "content" => "Title", 
    "sort" => "title", 
    "default" => true, 
  ),
  array("id" => "DESCRIPTION", 
    "content" => "Description", 
    "sort" => "description", 
    "default" => true, 
  ),
  array("id" => "KEYWORDS", 
    "content" => "Keywords", 
    "sort" => "keywords", 
    "default" => true, 
  ),
  array("id" => "DATE", 
    "content" => GetMessage("DATA_IZMENENIYA"), 
    "sort" => "date", 
    "default" => true, 
  ),
)); 
$arActions = Array();
while($arRes = $rsData->NavNext(true, "f_")) { 
	if($arRes['DATE']) $arRes['DATE'] = $DB->FormatDate($arRes['DATE'], 'YYYY-MM-DD HH:MI:SS', 'DD.MM.YYYY HH:MI:SS');  
    $row =& $lAdmin->AddRow($f_ID, $arRes); 
	$row->AddField("ID", $f_ID);
	$row->AddViewField("ID", '<a href="ambersite_independentmetatags_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
	$arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage("PODROBNO"),
		"ACTION" => $lAdmin->ActionRedirect("ambersite_independentmetatags_edit.php?ID=".$f_ID),
		"DEFAULT" => true
	);
	if ($RIGHT=='W') $arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("UDALIT"),
		"ACTION" => "if(confirm('".GetMessage("DEJSTVITELNO_UDALIT_ZAPIS")."?')) ".$lAdmin->ActionDoGroup($f_ID, "delete"),
		"DEFAULT" => false
    );
	$row->AddActions($arActions); 
unset($arActions);}

if ($RIGHT=='W') $lAdmin->AddGroupActionTable(Array(
  "delete" => true,
)); 

$aContext = array(
  array(
    "TEXT" => GetMessage("DOBAVIT_ZAPIS"),
    "LINK" => "/bitrix/admin/ambersite_independentmetatags_edit.php?lang=".LANG,
    "TITLE" => GetMessage("DOBAVIT_ZAPIS"),
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode(); 
$APPLICATION->SetTitle(GetMessage("SPISOK_ZAPISEJ")); 

endif;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($RIGHT >= "R") :

$oFilter = new CAdminFilter($sTableID."_filter", array("ID", GetMessage("SAJT"), "URL", GetMessage("DATA_IZMENENIYA"))); 
?> 

<form name="find_form" method="get" action="<? $APPLICATION->GetCurPage();?>"> 
<? $oFilter->Begin();?> 
<tr> 
  <td>ID:</td> 
  <td> 
    <input type="text" name="find_id" size="47" value="<? htmlspecialcharsex($find_id)?>"> 
  </td> 
</tr>
<tr> 
  <td><?=GetMessage("SAJT")?>:</td> 
  <td> 
    <select name="find_siteid">
    	<option value=""><?=GetMessage("LJUBOJ")?></option>
    	<? $dbSites = CSite::GetList($by="def", $order="desc", array()); while ($arSite = $dbSites->Fetch()):?>
		<option value="<?=$arSite['LID']?>">[<?=$arSite['LID']?>] <?=$arSite['NAME']?></option>
		<? endwhile;?>
	</select> 
  </td> 
</tr>
<tr> 
  <td>URL:</td> 
  <td> 
    <input type="text" name="find_url" size="47" value="<? htmlspecialcharsex($find_url)?>"> 
  </td> 
</tr> 
<tr>
	<td nowrap><?=GetMessage("DATA_IZMENENIYA")?>:</td>
	<td nowrap><? echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form")?></td>
</tr>
<? 
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form")); 
$oFilter->End(); 
?> 
</form> 

<? $lAdmin->DisplayList();

echo BeginNote(); echo GetMessage("PODROBNO_O_MODULE_NEZAVISIMUE_META_TEGI").': <a href="http://marketplace.1c-bitrix.ru/solutions/ambersite.independentmetatags/" target="_blank">http://marketplace.1c-bitrix.ru/solutions/ambersite.independentmetatags/</a>'; echo EndNote();

endif; 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); 
?>