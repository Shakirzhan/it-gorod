<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<?global $arSetting;?>	
	<?if (!empty($arResult)):?>
		<?$this->setFrameMode(true);?>		
		<?		
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/".strtolower($arSetting["SERVICES_TYPE"]["VALUE"]).".php")){
				include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/".strtolower($arSetting["SERVICES_TYPE"]["VALUE"]).".php");	
			}
		?>		
	<?endif;?>