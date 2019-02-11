<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
	<?$this->setFrameMode(true);?>
	<ul class="nav">
	<?
	$previousLevel = 0;
	$showChilds = false;
	foreach($arResult as $arItem):?>
		<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
			<?if($showChilds):?>
				<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
			<?else:?>
				<?=str_repeat("</li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
			<?endif;?>
		<?endif?>
		<?if ($arItem["IS_PARENT"]):?>
			<?if ($arItem["DEPTH_LEVEL"] == 1 && $arItem["SELECTED"]):?>
				<?$showChilds = true;?>
				<li class="nav--item nav--item_child <?=($arItem["SELECTED"] ? 'nav--item_active' : '')?>">
					<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
					<ul class="subnav">
			<?else:?>
				<?$showChilds = false;?>
				<li class="nav--item <?=($arItem["SELECTED"] ? 'nav--item_active' : '')?>">
					<a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>					
			<?endif?>
		<?else:?>
			<?if ($arItem["PERMISSION"] > "D"):?>
				<?if ($arItem["DEPTH_LEVEL"] == 1):?>
					<li class="nav--item <?=($arItem["SELECTED"] ? 'nav--item_active' : '')?>">
						<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
					</li>
				<?else:?>
					<?if($showChilds):?>
						<li class="subnav--item <?=($arItem["SELECTED"] ? 'subnav--item_active' : '')?>">
							<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
						</li>
					<?endif;?>
				<?endif?>
			<?else:?>
				<?if ($arItem["DEPTH_LEVEL"] == 1):?>
					<li class="nav--item <?=($arItem["SELECTED"] ? 'nav--item_active' : '')?>">
						<a href="javascript:;" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a>
					</li>
				<?else:?>
					<li class="subnav--item <?=($arItem["SELECTED"] ? 'subnav--item_active' : '')?>">
						<a href="javascript:;" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a>
					</li>
				<?endif?>
			<?endif?>
		<?endif?>
		<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
	<?endforeach?>
	<?if ($previousLevel > 1)://close last item tags?>
		<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
	<?endif?>
	</ul>

<?endif;?>