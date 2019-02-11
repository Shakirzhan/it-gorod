<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
	<?$this->setFrameMode(true);?>
		<div class="f-menu">
			<?foreach($arResult as $arItem):?>
				<a class="f-menu--item" href="<?=$arItem["LINK"]?>">
					<span><?=$arItem["TEXT"]?></span>
				</a>
				
			<?endforeach;?>
		</div>
<?endif;?>