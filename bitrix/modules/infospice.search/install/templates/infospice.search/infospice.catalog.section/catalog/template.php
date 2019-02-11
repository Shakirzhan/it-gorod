<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
$(function(){ 
	$items = $(".result-items-boxes2 .item");
	countItems = $items.size();
	var i=0;
	while(i < countItems){
		min = i; max = i + 4;
	    if(max > countItems) max = countItems; 
	    $resizeItems = $(".result-items-boxes2 .item").slice(min, max);
	    var maxHeightImage = 0;
	    var maxHeightName = 0; 
	    var maxHeightPrice = 0;
	    $resizeItems.each(function(){
		     var heightImage, heightName, heightPrice; heightImage = $("[align=image]", this).height();
		     heightName = $(".description", this).height();
		     heightPrice = $(".price-label", this).height();
		     maxHeightImage = Math.max(maxHeightImage, heightImage);
		     maxHeightName = Math.max(maxHeightName, (heightName)); 
		     maxHeightPrice = Math.max(maxHeightPrice, heightPrice); }) 
		     $("[align=image]", $resizeItems).height(maxHeightImage); 
             $(".description", $resizeItems).height(maxHeightName+5);
             $(".price-label", $resizeItems).height(maxHeightPrice); i += 4; 
    	}
	})
</script>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
<div class="result-items result-items-boxes2">
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<div class="item">
			<div class="description">
				<p><strong><a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><?echo $arElement["NAME"]?></a></strong></p>
			</div>
			<div class="image">
				<?if(!empty($arElement["PREVIEW_PICTURE"]["SRC"])):?>
					<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
				<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
					<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
				<?else:?>
					<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><img src="<?=$templateFolder?>/images/none.png" alt="none"></a>
				<?endif?>
			</div>
			<? 
			if ( !empty( $arElement[ "PROPERTIES" ] ))
			{
				?>
				<div class="properties">
					<?
					foreach ( $arElement[ "PROPERTIES" ] as $arProperty ) 
						echo '<strong>'.$arProperty[ "NAME" ].":</strong> ".$arProperty[ "VALUE" ]."<br />";
					?>
					<br />
				</div>
				<?
			}
			?>
			<div class="price">
			<?
				//
				// add to basket button
				//
				if ( $arParams["ADD_TO_BASKET_BUTTON"] == "Y" )
				{
					if ( $arElement["ALREADY_ON_BASKET"] == "Y" ) 
						echo '<strong class="btn_buy incart">'.$arParams["ALREADY_ON_BASKET_TITLE"].'</strong>';
					else
					{
						$btnStyle = $arParams[ "ADD_TO_BASKET_BUTTON_COLOR" ];
						if ( empty( $btnStyle )) $btnStyle = "red";
						echo '<a href="'.$arElement["ADD_URL"].'" class="btn_buy '.$btnStyle.'">'.$arParams["ADD_TO_BASKET_BUTTON_TITLE"].'</a>';
					}
				}
				//
				// price label:
				//
				foreach($arElement["PRICES"] as $code=>$arPrice) 
				{
					if($arPrice["CAN_ACCESS"])
					{
						if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
						{
							?>
							<span class="discount-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br />
							<span class="old-price"><?=$arPrice["PRINT_VALUE"]?></span>
							<?
						}
						else
						{
							?><span class="regular-price"><?=$arPrice["PRINT_VALUE"]?></span><?
						}
					}
				}
			?>
			</div>
		</div>
	<?endforeach;?>
</div>



<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
