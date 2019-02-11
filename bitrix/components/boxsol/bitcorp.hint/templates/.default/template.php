<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
?>
<?$frame = $this->createFrame()->begin();?>
	<?if($arResult["SHOW_HINT"] != "N"):?>
	<!--noindex-->
		<div class="row info-row" style="background:#FFC107;">
			<div class="container tac">
				<strong>Мир! Труд! Май!</strong> – наши центры не работают 30 Апреля, 1, 8 и 9 Мая.
			</div>
			<a class="info-row--close" href="javascript:void(0)" onclick="bitcorpHintClose(this.parentNode);">&#215;</a>
		</div>
	<!--/noindex-->
	<?endif;?>
	<script>
		function bitcorpHintClose(hint)
		{
			BX.ajax.post(
				'<?=POST_FORM_ACTION_URI?>',
				{
					sessid: BX.bitrix_sessid(),
					action: 'bitcorpHintClose'
				},
				function(result)
				{
					hint.style.display = "none";				
				}
			);
		}
	</script>
<?$frame->end();?>