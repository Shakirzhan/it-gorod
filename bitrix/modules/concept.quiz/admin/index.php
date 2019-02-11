<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Converter;
use \Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

Main\Loader::includeModule('concept.quiz');
$moduleID = "concept.quiz";
global $APPLICATION;
global $siteId;
global $CQUIZ_TEMPLATE_ARRAY;

if($GLOBALS["APPLICATION"]->GetGroupRight($moduleID) < "R")
    LocalRedirect("/bitrix/");




$APPLICATION->SetTitle(Loc::getMessage("MODULE_PAGE_TITLE"));

$siteId = isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : '';

$arCurrentSite = array();
$arDefaultSite = array();
$arSites = array();

$dbSites = Bitrix\Main\SiteTable::getList(
	array(
		'order' => array('DEF' => 'DESC', 'NAME' => 'ASC'),
		'select' => array('LID', 'NAME', 'DEF', 'DIR', 'DOC_ROOT', 'SERVER_NAME')
	)
);


while($arRes = $dbSites->fetch(Converter::getHtmlConverter()))
{
	if($arRes['DOC_ROOT'] == '')
	{
		$arRes['DOC_ROOT'] = Converter::getHtmlConverter()->encode(
			Main\SiteTable::getDocumentRoot($arRes['LID'])
		);
	}

	if($arRes['DEF'] == 'Y')
	{
		$arDefaultSite = $arRes;
	}

	$arSites[$arRes['LID']] = $arRes;
}

$arCurrentSite = isset($arSites[$siteId]) ? $arSites[$siteId] : $arDefaultSite;
$siteId = $arCurrentSite['LID'];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

CConceptWqec::ConceptQuizOptions($siteId);
$aMenu = array();

$arDDMenu = array();

$arDDMenu[] = array(
	"TEXT" => "<b>".Loc::getMessage("MODULE_CHOOSE_SITE")."</b>",
	"ACTION" => false
);

foreach($arSites as $arRes)
{
	$arDDMenu[] = array(
		"TEXT" => "[".$arRes["LID"]."] ".$arRes["NAME"],
		"LINK" => "concept_quiz.php?lang=".LANGUAGE_ID."&site_id=".$arRes['LID']
	);
}

$aContext = array();
$aContext[] = array(
	"TEXT"	=> $arCurrentSite['NAME'],
	"MENU" => $arDDMenu
);

$context = new CAdminContextMenu($aContext);
$context->Show();

function addListRadioCheck($type, $title, $value, $option, $hint=''){?>

	<?global $siteId;?>

	<tr>

		<td width="40%" valign="top">
			<?if(strlen($hint)>0) ShowJSHint($hint);?>
		
			<?=$title?>:


			
		</td>
		
		<td width="60%">
			<?foreach($value as $k => $val):?>

				<?
				if($type == "checkbox")
					$cmp = $option[$k];
				else
					$cmp = $option;
				?>

				<?=InputType($type, "$val[0]", $val[2], $cmp, false, $val[1])?> <br>

			<?endforeach;?>

		</td>

	</tr>

<?}

function addRowTextInput($name, $title, $rows, $size, $options, $hint='')
	{?>
	
	<?
		global $siteId;
		global $APPLICATION; 
	?>

	<tr>

		<td width="40%" valign="top">
			<?if(strlen($hint)>0) ShowJSHint($hint);?>
            <label for="ya_id"><?=$title?></label>:
        </td>

	
        <td width="60%" valign="top">
            <?
         		echo CUserTypeString::GetEditFormHTML(array("SETTINGS"=> array('SIZE' => $size, 'ROWS' => $rows,), 'EDIT_IN_LIST' => 'Y'), array('NAME' => $name, "VALUE" => $options));
			?> 

    	</td>
	</tr>

	

 

<?}?>

<?CJSCore::Init(array("jquery"));?>
<?if(strlen($_REQUEST["tab"])>0):?>
	
	<script type="text/javascript">

		jQuery(document).ready(function($) {

			var value = "<?=$_REQUEST["tab"]?>".replace("tab_cont_", "");

			$("#tabControl_tabs").find("span").removeClass('adm-detail-tab-active');
			$("#tabControl_tabs").find("span#<?=$_REQUEST["tab"]?>").addClass('adm-detail-tab-active');
			$("div.adm-detail-content-wrap").find("div.adm-detail-content").hide();
			$("div.adm-detail-content-wrap").find("div#"+value).show();
		});

		
	</script>
<?endif;?>

<script>
	$(document).on('click', '.adm-detail-tab', 
	function() 
	{
		$("input[name='tab']").val($(this).attr("id"));
	});
</script>

<?
// _____________________________ POST part _____________________________ //
if(!empty($_REQUEST["save"]))
{

		CConceptWqec::optionDelete($CQUIZ_TEMPLATE_ARRAY['CHECK_OPTIONS'], $siteId);
		foreach($_REQUEST as $key => $val)
		{
			if(strpos($key, "wqec_") == 0)
	        	Option::set("concept.quiz", $key, trim($val), $siteId);
		}



		BXClearCache(true);
		LocalRedirect($APPLICATION->GetCurUri("saved=1&tab=".$_REQUEST["tab"]));
}
// _____________________________ POST part _____________________________ //

$aTabs = array(
	array(
		"DIV" => "maintab",
		"TAB" => Loc::getMessage("MODULE_MAINTAM_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("MODULE_MAINTAM_DESCRIPTION")
	),
	array(
		"DIV" => "wqec_bx24",
		"TAB" => Loc::getMessage("MODULE_WQEC_BX24_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("MODULE_WQEC_BX24_DESCRIPTION")
	),
	array(
		"DIV" => "wqec_amo",
		"TAB" => Loc::getMessage("MODULE_WQEC_AMO_NAME"),
		"ICON" => "main_user_edit",
		"TITLE" => GetMessage("MODULE_WQEC_AMO_DESCRIPTION")
	),
	

);

$tabControl = new CAdminTabControl("tabControl", $aTabs);



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($_REQUEST["saved"] == 1)
{
	echo CAdminMessage::ShowNote(Loc::getMessage("MODULE_NOTE_MSG1") );
}
?>

<form id="quiz" method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&site_id=<?=$siteId?>" ENCTYPE="multipart/form-data" name="landquiz">
	<input name="tab" type="hidden" value="<?=$_REQUEST["tab"]?>">
<?

echo bitrix_sessid_post();

$tabControl->Begin();


$tabControl->BeginNextTab();


$width_xs = 20;
$height_xs = 1;

$width_sm = 40;
$height_sm = 4;
?>
	
	<?addListRadioCheck('checkbox', $CQUIZ_TEMPLATE_ARRAY['CQUIZ_JQ_ON']['NAME'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_JQ_ON']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_JQ_ON']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_JQ_ON']['HINT'])?>

	<?addListRadioCheck('checkbox', $CQUIZ_TEMPLATE_ARRAY['CQUIZ_IC_ON']['NAME'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_IC_ON']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_IC_ON']['VALUE'])?>


	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_HIDE_PAGES']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_HIDE_PAGES']['NAME'], $height_sm, $width_sm, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_HIDE_PAGES']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_HIDE_PAGES']['HINT']);?>

	<tr class="heading">
		<td colspan="2">
			<?=$CQUIZ_TEMPLATE_ARRAY['CQUIZ_TITLE_MAIL']['NAME'];?>
		</td>
	</tr>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_FROM']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_FROM']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_FROM']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_FROM']['HINT']);?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_TO']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_TO']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_TO']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_MAIL_TO']['HINT']);?>

	<tr class="heading">
		<td colspan="2">
			<?=$CQUIZ_TEMPLATE_ARRAY['CQUIZ_TITLE_COPYRIGHT']['NAME'];?>
		</td>
	</tr>

	<?addListRadioCheck('checkbox', $CQUIZ_TEMPLATE_ARRAY['CQUIZ_COPYRIGHT']['NAME'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_COPYRIGHT']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_COPYRIGHT']['VALUE'])?>




<?
$tabControl->BeginNextTab();
?>
	
	<?addListRadioCheck('checkbox', $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_ON']['NAME'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_ON']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_ON']['VALUE'])?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_URL']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_URL']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_URL']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_URL']['HINT']);?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_LOGIN']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_LOGIN']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_LOGIN']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_LOGIN']['HINT']);?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_PASSWORD']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_PASSWORD']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_PASSWORD']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_BX_PASSWORD']['HINT']);?>
	

<?
$tabControl->BeginNextTab();
?>

	<?addListRadioCheck('checkbox', $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_ON']['NAME'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_ON']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_ON']['VALUE'])?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_URL']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_URL']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_URL']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_URL']['HINT']);?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_LOGIN']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_LOGIN']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_LOGIN']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_LOGIN']['HINT']);?>

	<?addRowTextInput($CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_HASH']['VALUE_ID'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_HASH']['NAME'], $height_xs, $width_xs, $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_HASH']['VALUE'], $CQUIZ_TEMPLATE_ARRAY['CQUIZ_AMO_HASH']['HINT']);?>

<?


$tabControl->Buttons();?>
<?if($GLOBALS["APPLICATION"]->GetGroupRight($moduleID) > "R"):?>
<input type="submit" name="save" value="<?=GetMessage("MODULE_BUTTON_SAVE_VALUE")?>" title="<?=GetMessage("MODULE_BUTTON_SAVE_TITLE")?>" />
<?endif;?>
<?


$tabControl->End();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>