<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use \Bitrix\Main\Localization\Loc;
$bImage = ($arResult["MD_DETAIL_PICTURE"] ? true : false);
?>

<div class="row row-in">
	
	<div class="col-xs-12 col-md-4">
		<?if($bImage):?>
			<img class="img-responsiver" src="<?=$arResult["MD_DETAIL_PICTURE"]["PREVIEW"]["src"]?>" title="<?=$arResult["MD_DETAIL_PICTURE"]["TITLE"]?>" alt="<?=$arResult["MD_DETAIL_PICTURE"]["ALT"]?>" />
		<?else:?>
			<img class="img-responsiver" src="<?=$this->GetFolder().'/images/people-default.jpg'?>" title="<?=$arResult["NAME"]?>" alt="<?=$arResult["NAME"]?>" />
		<?endif;?>
	</div>	

	<div class="col-xs-12 col-md-8">

		<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
		<div class="h3"><?=$arResult["NAME"]?></div>
		<?endif;?>

		<?if($arResult['DISPLAY_PROPERTIES']['POST']['VALUE']):?>
			<p><?=$arResult['DISPLAY_PROPERTIES']['POST']['VALUE']?></p>
		<?endif;?>

		<?if(is_array($arResult['TEAM_FIELDS'])):?>	

			<div class="properties">
				<?foreach ($arResult['TEAM_FIELDS'] as $propCode => $propValue):?>
					<div class="inner-wrapper">
						<div class="property">
							<?if(strlen($propValue['DESCRIPTION'])):?>
								<i class="fa <?=$propValue['DESCRIPTION']?>"></i>
							<?endif;?>

							<?if($propCode == 'EMAIL'):?>
								<!-- noindex -->
								<a href="mailto:<?=$propValue['VALUE'];?>" rel="nofollow">
									<?=$propValue['VALUE']?>	
								</a>
								<!-- /noindex -->
							<?else:?>
								<?=$propValue['VALUE']?>
							<?endif;?>

						</div>
					</div>
				<?endforeach;?>					
			</div>

		<?endif;?>
	</div>
</div>

<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>			
		<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
			<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
				<p><?=$arResult['FIELDS']['DETAIL_TEXT'];?></p>
			<?else:?>
				<?=$arResult['FIELDS']['DETAIL_TEXT'];?>
			<?endif;?>
		<?endif;?>	
<?endif;?>