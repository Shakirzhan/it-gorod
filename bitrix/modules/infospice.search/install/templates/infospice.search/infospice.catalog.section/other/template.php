<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="result-items result-items2">
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<div class="item">
			<div class="image">
				<?if(!empty($arElement["PREVIEW_PICTURE"]["SRC"])):?>
					<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
				<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
					<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
				<?endif?>
			</div>
			<div class="description">
				<p><strong><a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></strong></p>
				<p><?=$arElement["PREVIEW_TEXT"]?></p>
			</div>
		</div>
	<?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>