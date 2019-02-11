<?
IncludeModuleLangFile(__FILE__);
$module_id='skyweb24.alreadygoing';
CJSCore::Init(array("jquery"));
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.fancybox.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/'.$module_id.'/jquery.fancybox.css');

$skinArr=array(
	't_default'=>GetMessage("SKWB24_AG_CSS_SKIN_DEFAULT"),
	't_flat'=>GetMessage("SKWB24_AG_CSS_SKIN_FLAT")
);
$skinColorArr=array(
	'c_green'=>GetMessage("SKWB24_AG_CSS_COLOR_GREEN"),
	'c_red'=>GetMessage("SKWB24_AG_CSS_COLOR_RED"),
	'c_blue'=>GetMessage("SKWB24_AG_CSS_COLOR_BLUE"),
	'c_dark'=>GetMessage("SKWB24_AG_CSS_COLOR_DARK")
);

foreach($skinColorArr as $key=>$nextSkin){
	$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/".$module_id."/".$key.".css");
}
foreach($skinArr as $key=>$nextSkin){
	$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/".$module_id."/".$key.".css");
}

$modify='';
//s - строка, sn - строка(не бывает пустой), b - истина ложь, n - число
if(!empty($_POST['going'])){
	$postArr=array("active" => "b", "jquery" => "b", "fancybox" => "b", "cookie" => "n", "header" => "sn", "header_ext" => "s", "link_value" => "s", "link_name" => "sn", "content" => "s", "skin"=>"s", "skin_color"=>"s");
	foreach($postArr as $key=>$nextPost){
		$setVal='';
		switch($nextPost){
			case 'b':
				$setVal=($_REQUEST[$key]=='Y')?'Y':'N';
				break;
			case 'n':
				$setVal=(intval($_REQUEST[$key])>0)?intval($_REQUEST[$key]):0;
				break;
			case 's':
				$setVal=trim($_REQUEST[$key]);
				break;
			case 'sn':
				$setVal=(empty($_REQUEST[$key]))?COption::GetOptionString($module_id, $key):trim($_REQUEST[$key]);
				break;
		}
		COption::SetOptionString($module_id, $key, $setVal);
	}
	$modify=GetMessage("SKWB24_AG_SAVE_CHANGE");
}


$aTabs = array(
	array("DIV" => "sw24_aq_settings", "TAB" => GetMessage("SKWB24_AG_TAB1_NAME"), "ICON" => "", "TITLE" => GetMessage("SKWB24_AG_TITLE1")),
	array("DIV" => "sw24_aq_stats", "TAB" => GetMessage("SKWB24_AG_TAB2_NAME"), "ICON" => "", "TITLE" => GetMessage("SKWB24_AG_TITLE2")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<tr><td>
<form class="alreadygoing_edit_block" method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
<table>
<input type="hidden" name="going" value="Y" />
<tr>
	<td colspan="2">
		<p class="status" id="status_line"><?=$modify?></p>
	</td>
</tr>
<tr class="heading">
	<td colspan="2">
		<b><?=GetMessage("SKWB24_AG_UPLOAD_PARAMS")?></b>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><b><?=GetMessage("SKWB24_AG_ACTIVE")?></b></span>
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<input type="checkbox" name="active" value="Y"<?=(COption::GetOptionString($module_id, "active")=='Y')?' checked="checked"':''?>/>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_INCLUDE_JQUERY")?></span>
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<input type="checkbox" name="jquery" value="Y"<?=(COption::GetOptionString($module_id, "jquery")=='Y')?' checked="checked"':''?>/>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_INCLUDE_FANCYBOX")?></span>
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<input type="checkbox" name="fancybox" value="Y"<?=(COption::GetOptionString($module_id, "fancybox")=='Y')?' checked="checked"':''?>/>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span>
			<?=GetMessage("SKWB24_AG_COOCKIE_LONG")?>
			<sup>
				<span class="required">1</span>
			</sup>
		</span>
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<input type="number" name="cookie" value="<?=intval(COption::GetOptionString($module_id, "cookie"));?>" />
	</td>
</tr>

<tr class="heading">
	<td colspan="2">
		<b><?=GetMessage("SKWB24_AG_UPLOAD_CONTENTS")?></b>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_SKIN")?></span>
		<select name="skin">
		<?
			foreach($skinArr as $key=>$nextSkin){
				$selected=($key==COption::GetOptionString($module_id, "skin"))?' selected="selected"':'';
				echo '<option value="'.$key.'"'.$selected.'>'.$nextSkin.'</option>';
			}
		?>
		</select>
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_SKIN_COLOR")?></span>
		<select name="skin_color">
		<?
			foreach($skinColorArr as $key=>$nextSkin){
				$selected=($key==COption::GetOptionString($module_id, "skin_color"))?' selected="selected"':'';
				echo '<option value="'.$key.'"'.$selected.'>'.$nextSkin.'</option>';
			}
		?>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2" class="_algo-preview">
		<div class="alreadygoing <?=COption::GetOptionString($module_id, "skin")?> <?=COption::GetOptionString($module_id, "skin_color")?>">
			<img id="img_going" src="<?=COption::GetOptionString($module_id, "img_path")?>" alt="<?=GetMessage("SKWB24_AG_BANNER_IMAGE")?>">
			<div class="text">
				<h2><?=COption::GetOptionString($module_id, "header")?></h2>
				<div class="info"><?=COption::GetOptionString($module_id, "content")?></div>
				<h3><?=COption::GetOptionString($module_id, "header_ext")?></h3>
				<a href="<?=COption::GetOptionString($module_id, "link_value")?>" class="going_link"><?=COption::GetOptionString($module_id, "link_name")?></a>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo-preview-btn" width="50%">
		<input type="button" value="<?=GetMessage("SKWB24_AG_UPDATE")?>" />
	</td>
	<td class="adm-detail-content-cell-r _algo-preview-btn" width="50%">
		<input type="button" class="preview_button" value="<?=GetMessage("SKWB24_AG_PREVIEW")?>" />
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_HEADER")?></span>
		<input type="text" name="header" value="<?=COption::GetOptionString($module_id, "header");?>" />
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_HEADER_EXT")?></span>
		<input type="text" name="header_ext" value="<?=COption::GetOptionString($module_id, "header_ext");?>" />
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_LINK_VALUE")?></span>
		<input type="text" name="link_value" value="<?=COption::GetOptionString($module_id, "link_value");?>" />
	</td>
	<td class="adm-detail-content-cell-r _algo" width="50%">
		<span><?=GetMessage("SKWB24_AG_UPLOAD_LINK_TEXT")?></span>
		<input type="text" name="link_name" value="<?=COption::GetOptionString($module_id, "link_name");?>" />
	</td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l _algo-area" width="50%">
		<div>
			<span>
				<?=GetMessage("SKWB24_AG_UPLOAD_CONTENT_TEXT")?>
				<sup>
					<span class="required">2</span>
				</sup>
			</span>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:fileman.light_editor","",Array(
			"CONTENT" => COption::GetOptionString($module_id, "content"),
			"INPUT_NAME" => "content",
			"INPUT_ID" => "",
			"WIDTH" => "100%",
			"HEIGHT" => "200px",
			"RESIZABLE" => "Y",
			"AUTO_RESIZE" => "Y",
			"VIDEO_ALLOW_VIDEO" => "N",
			"VIDEO_MAX_WIDTH" => "640",
			"VIDEO_MAX_HEIGHT" => "480",
			"VIDEO_BUFFER" => "20",
			"VIDEO_LOGO" => "",
			"VIDEO_WMODE" => "transparent",
			"VIDEO_WINDOWLESS" => "Y",
			"VIDEO_SKIN" => "/bitrix/components/bitrix/player/mediaplayer/skins/bitrix.swf",
			"USE_FILE_DIALOGS" => "Y",
			"ID" => "",
			"JS_OBJ_NAME" => ""
			)
		);?>
	</td>
	<td class="adm-detail-content-cell-r _algo-area" width="50%">
		<div>
			<?=GetMessage("SKWB24_AG_UPLOAD_IMAGE")?>
			<sup>
				<span class="required">3</span>
			</sup>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
		   array(
			  "INPUT_NAME"=>"going_file",
			  "MULTIPLE"=>"N",
			  "MODULE_ID"=>$module_id,
			  "MAX_FILE_SIZE"=>"3000000",
			  "ALLOW_UPLOAD"=>"I",
			  "ALLOW_UPLOAD_EXT"=>""
		   ),
		   false
		);?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input class="adm-btn-save" type="submit" title="" value="<?=GetMessage("SKWB24_AG_SAVE")?>">
	</td>
</tr>
</table>
</form>
</td></tr>

<?
$tabControl->BeginNextTab();
echo '<tr><td class="centered"><div class="pyramid"><div class="pyramid _second"></div><div class="pyramid _third"><div class="pyramid__inner">'.GetMessage("SKWB24_AG_STAT_STAT_GOTOLINK").' '.COption::GetOptionString('skyweb24.alreadygoing', 'stat_gotolink').'</div></div><div class="pyramid__inner">'.GetMessage("SKWB24_AG_STAT_SHOW_BANNER").' '.COption::GetOptionString('skyweb24.alreadygoing', 'stat_showbanner').'</div></div></td></tr>';
$tabControl->End();
?>

<div class="adm-info-message-wrap">
	<div class="adm-info-message">
		<span class="required">1</span>
			<?=GetMessage("SKWB24_AG_COMMENT")?>
		</br>
		<span class="required">2</span>
			<?=GetMessage("SKWB24_AG_COMMENT_CONTENT")?>
		</br>
		<span class="required">3</span>
			<?=GetMessage("SKWB24_AG_COMMENT_IMAGE")?>
		</br>
	</div>
</div>

<script>
var aPath='/bitrix/admin/skyweb24_alreadygoing.php',
	infoChange='<?=GetMessage("SKWB24_AG_SAVE_CHANGE")?>';

function removeNode(o){
	if(BX(o)){
		BX.cleanNode(BX(o), true);
		
		BX('status_line').innerHTML=infoChange;
	}else{
		setTimeout(function(){removeNode(o);}, 100);
	}
}

BX.addCustomEvent('uploadFinish', function(result){
	
	if(result.error!='undefined'){
		BX.ajax({
			url: aPath,
			data: {IMAGE_ID:result.element_id, MODULE_ID:'<?=$module_id?>'},
			method: 'POST',
			dataType: 'json',
			timeout: 30,
			async: true,
			onsuccess: function(data){
				BX('img_going').src=data.path;
				removeNode("wd-doc"+result.element_id);
			},
			onfailure: function(erdata){
				//console.log(erdata);
			}
		});
	}

});


$(document).ready(function(){
	var previewBlock=$('.alreadygoing'),
		cForm=$('form.alreadygoing_edit_block');
	$('#sw24_aq_settings').on('click change keyup', 'input, select, button[type=button]', function(){
		previewBlock.find('h2').html(cForm.find('input[name=header]').val());
		previewBlock.find('h3').html(cForm.find('input[name=header_ext]').val());
		previewBlock.find('a.going_link').text(cForm.find('input[name=link_name]').val());
		previewBlock.find('a.going_link').attr('href', cForm.find('input[name=link_value]').val());
		previewBlock.find('div.info').html(cForm.find('input[name=content]').val());
		previewBlock.removeClass().addClass('alreadygoing '+cForm.find('select[name=skin]').val()+' '+cForm.find('select[name=skin_color]').val());
	});
	
	$('.alreadygoing_edit_block .preview_button').click(function(){
		$.fancybox({content:$('.alreadygoing')[0].outerHTML, padding:'0'});
	});
	
	$('a.file-selectdialog-switcher').trigger('click');
	
})

</script>