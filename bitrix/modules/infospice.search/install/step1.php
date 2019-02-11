<?if(!check_bitrix_sessid()) return;?>
<style>
#license_block {
	height: 200px;
	overflow: auto;
}
</style>

<form name="form1" action="<?=$APPLICATION->GetCurPageParam()?>">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>"/>
<input type="hidden" name="id" value="infospice.search"/>
<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<?include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/infospice.search/lang/ru/description.php");?>

<br style="clear:both;" />
<div id="license_block">
<?include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/infospice.search/lang/ru/license.php");?>
</div>
<br />
<p><label><input type="checkbox" name="search_install" value="Y" checked="checked" /> <?=GetMessage("YES_LICENSE")?></label></p>

<input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL")?>"/>

</form>