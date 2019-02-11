<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<script type="text/javascript">
$(function()
{ 
	$items = $(".result-items-boxes2 .item");
	countItems = $items.size();
	var i = 0;
	while( i < countItems )
	{
		min = i; 
		max = i + 4;

	    if ( max > countItems ) 
	    	max = countItems; 

	    var maxHeightImage = 0;
	    var maxHeightName  = 0; 
	    var maxHeightPrice = 0;
	    
	    $resizeItems = $(".result-items-boxes2 .item").slice( min, max );
	    $resizeItems.each( function()
	    {
		    var heightImage, heightName, heightPrice; heightImage = $("[align=image]", this).height();
		    heightName     = $(".description", this).height();
		    heightPrice    = $(".price-label", this).height();
		    maxHeightImage = Math.max(maxHeightImage, heightImage);
		    maxHeightName  = Math.max(maxHeightName, (heightName)); 
		    maxHeightPrice = Math.max(maxHeightPrice, heightPrice); 
		})

		$("[align=image]", $resizeItems).height( maxHeightImage    ); 
        $(".description",  $resizeItems).height( maxHeightName + 5 );
        $(".price-label",  $resizeItems).height( maxHeightPrice    ); 
        i += 4; 
    }
})
</script>

<?
	// $iblockId    = ( !empty( $_GET[ "iblock" ]  )) ? ( int )$_GET[ "iblock" ]  : 0;
	// $sectionId   = ( !empty( $_GET[ "section" ] )) ? ( int )$_GET[ "section" ] : 0;
	
	// $res = CIBlock::GetByID( $iblockId );
	// if ( $ar_res = $res->GetNext())
 //  		$iblockName = $ar_res['NAME'];

$userQuery   = ( !empty( $_GET[ "q" ] ))       ? trim( $_GET[ "q" ])       : "";
?>

<div class="infospice-search-content">
	<h2 class="infospice-search-content-title">Результаты поиска по строке «<?echo $userQuery;?>»: </h2> 

<?
	if ( $arParams[ "DISPLAY_TOP_PAGER" ])
		echo $arResult[ "NAV_STRING" ];
	
	//echo '<pre>'; print_r( $arResult ); echo '</pre>';

	$numColumns  = intval( $arParams["LINE_ELEMENT_COUNT"] );
	$columnIndex = 1;
	foreach( $arResult[ "ITEMS" ] as $cell => $arElement )
	{
		if ( $columnIndex == 1 )
		{
			//
			// Начало колонки, первый элемент
			//
			?><div class="infospice-search-products"><?
		}
		?>

		<?
		// Картинка
		?>
		<div class="infospice-search-product">
			<?
			if( !empty( $arElement["PREVIEW_PICTURE"]["SRC"]))
			{
				//
				// Все необходимые проверки и преобразования делаются в result_modifier
				//
				$width  = "";
				$height = "120px";      // принудительно указываем высоту (и только её), чтобы не 
				                        // расползались ячейки с элементами в дизайне

				$src   = $arElement["PREVIEW_PICTURE"]["SRC"];
				$title = $alt = $arElement["NAME"];
				?>
				<a href="<?=$arElement["~DETAIL_PAGE_URL"]?>">
					<img border="0" src="<?=$src?>" title="<?=$title?>" width="<?=$width?>" height="<?=$height?>" alt="<?=$alt?>">
				</a>
				<?
			}
			?>
			<p><a href="<?=$arElement["~DETAIL_PAGE_URL"]?>"><?=$arElement[ "NAME" ]?></a></p>
			

			<?
			//
			// Если есть какие-то цены на товар или элемент, то отображаем их 
			// и рисуем кнопку "Купить"
			//
			if ( !empty( $arElement["PRICES"])) 
			{
				?>
				<div class="infospice-search-product-prices">
					<div class="infospice-search-product-price">
					<?
					//
					// Цена:
					//
					foreach($arElement["PRICES"] as $code=>$arPrice) 
					{
						if($arPrice["CAN_ACCESS"])
						{
							if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
							{
								?>
								<div class="infospice-search-product-price-old">
									<strong><?=$arPrice["PRINT_VALUE"]?></strong>
									<!-- <span class="infospice-search-rouble">Р</span> -->
								</div>
								<div class="infospice-search-product-price-new infospice-search-product-price-red">
									<strong><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></strong>
									<!-- <span class="infospice-search-rouble">Р</span> -->
								</div>
								<?
							}
							else
							{
								?>
								<div class="infospice-search-product-price-new">
									<strong><?=$arPrice["PRINT_VALUE"]?></strong>
									<!-- <span class="infospice-search-rouble">Р</span> -->
								</div>
								<?
							}
						}
					}
					?>
					</div>
					<?
					//
					// Кнопка "Купить" (если в настройках компоненты, она включена)
					//
					if ( $arParams["ADD_TO_BASKET_BUTTON"] == "Y" )
					{
						if ( $arElement["ALREADY_ON_BASKET"] == "Y" ) 
						{
							echo '<strong class="infospice-search-product-incart">'.$arParams["ALREADY_ON_BASKET_TITLE"].'</strong>';
						}
						else
						{
							$btnStyle = $arParams[ "ADD_TO_BASKET_BUTTON_COLOR" ];
							if ( empty( $btnStyle )) 
								$btnStyle = "red";

							echo '<a href="'.$arElement["ADD_URL"].'" class="infospice-search-product-btn '.$btnStyle.'"><strong>'.$arParams["ADD_TO_BASKET_BUTTON_TITLE"].'</strong></a>';
						}
					}
					?>
				</div>
				<?
			}


			//
			// Всякие характеристики продукта (заданые в настройках компоненты)
			//
			if ( count( $arElement[ "PROPERTIES" ]) > 0 )
			{
				?><ul class="infospice-search-product-info"><?
				foreach ( $arElement[ "PROPERTIES" ] as $arProperty ) 
				{
					?>
					<li>
						<span class="infospice-search-product-param"><?=$arProperty[ "NAME" ];?></span>
						<span class="infospice-search-product-value"><?=$arProperty[ "VALUE" ];?></span>
						<span class="infospice-search-product-dots"></span>
					</li>
					<?
				}
				?></ul><?
			}
			?>
		</div>
		<?
		//
		// Закрытие тэгов колонки элементов
		//
		if ( $columnIndex >= $numColumns )
		{
			$columnIndex = 1;
			?></div><?
		}
		else 
		{
			$columnIndex++;
		}
	}

	if ( $arParams[ "DISPLAY_BOTTOM_PAGER" ])
		echo $arResult["NAV_STRING"];
?>
</div>


<? return; ?>
























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
