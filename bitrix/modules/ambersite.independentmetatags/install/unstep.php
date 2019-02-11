<?php

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("UDALENIE_MODULYA_NEZAVISIMUE_META_TEGI")); 

?>
	<form action="<?=$APPLICATION->GetCurPage(); ?>">
		<?=bitrix_sessid_post(); ?>
		<input type="hidden" name="lang" value="<?=LANG; ?>">
        <input type="hidden" name="id" value="ambersite.independentmetatags">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
		<?=CAdminMessage::ShowMessage(GetMessage("VNIMANIE_MODUL_BUDET_UDALEN_IZ_SISTEMU")); ?>
		<?php /*?><p><?=GetMessage("VU_MOGETE_SOHRANIT_DANNUE_V_TABLICAH_BAZU_DANNUH")?>:</p><?php */?>
		<p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata"><?=GetMessage("SOHRANIT_TABLICU")?></label></p><br />
		<input type="submit" name="inst" value="<?=GetMessage("UDALIT_MODYL")?>">
	</form>
