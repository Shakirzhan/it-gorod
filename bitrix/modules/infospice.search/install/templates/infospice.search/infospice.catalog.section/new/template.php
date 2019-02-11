<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>


<script type="text/javascript" >
    $(function () {
        jQuery(".infospice-search-products").each(function (i, arItems) {

            var maxHeightImage = 0;
            var maxHeightName = 0;
            var maxHeightPrice = 0;

            $resizeItems = jQuery(arItems).children(".infospice-search-product");
            var heightBlock = 0;
            $resizeItems.each(function () {
                heightBlock = $(this).height() > heightBlock ? $(this).height() : heightBlock;
            });

            $($resizeItems).css("min-height", heightBlock);
            $($resizeItems).parent().css("min-height", heightBlock);
        });
    })
</script >

<div class="infospice-search-content" >
    <?
    if ( $arParams["DISPLAY_TOP_PAGER"] )
    {
        echo $arResult["NAV_STRING"];
    }

    //echo '<pre>'; print_r( $arResult ); echo '</pre>';

    $numColumns  = intval( $arParams["LINE_ELEMENT_COUNT"] );
    $columnIndex = 0;
    ?>
    <div class="infospice-search-products" >
        <?
        foreach ($arResult["ITEMS"] as $cell => $arElement)
        {
        if ($columnIndex % $numColumns == 0 && $columnIndex != 0)
        {
        ?>
    </div >
    <div class="infospice-search-products" >
        <?
        }
        $columnIndex++;

        $this->AddEditAction(
            $arElement['ID'] ,
            $arElement['EDIT_LINK'] ,
            CIBlock::GetArrayByID(
                $arElement["IBLOCK_ID"] ,
                "ELEMENT_EDIT"
            )
        );
        $this->AddDeleteAction(
            $arElement['ID'] ,
            $arElement['DELETE_LINK'] ,
            CIBlock::GetArrayByID(
                $arElement["IBLOCK_ID"] ,
                "ELEMENT_DELETE"
            ) ,
            array( "CONFIRM" => GetMessage( 'CT_BNL_ELEMENT_DELETE_CONFIRM' ) )
        );
        $id = $this->GetEditAreaId( $arElement['ID'] );

        // Картинка
        ?>
        <div class="infospice-search-product" id="<?= $id ?>" >
            <?
            if ( !empty($arElement["PREVIEW_PICTURE"]["SRC"]) )
            {
                //
                // Все необходимые проверки и преобразования делаются в result_modifier

                $src   = $arElement["PREVIEW_PICTURE"]["SRC"];
                $title = $alt = $arElement["NAME"];
                ?>
                <a href="<?= $arElement["~DETAIL_PAGE_URL"] ?>" >
                    <img border="0" src="<?= $src ?>" title="<?= $title ?>"
                         width="<?= $arElement["PREVIEW_PICTURE"]["WIDTH"] ?>"
                         height="<?= $arElement["PREVIEW_PICTURE"]["HEIGHT"] ?>" alt="<?= $alt ?>" >
                </a >
            <?
            }
            ?>
            <p >
                <a href="<?= $arElement["~DETAIL_PAGE_URL"] ?>" ><?= $arElement["NAME"] ?></a >
            </p >


            <?
            //
            // Если есть какие-то цены на товар или элемент, то отображаем их
            // и рисуем кнопку "Купить"
            //
            if ( !empty($arElement["PRICES"]) )
            {
                ?>
                <div class="infospice-search-product-prices" >
                    <div class="infospice-search-product-price" >
                        <?
                        //
                        // Цена:
                        //
                        foreach ( $arElement["PRICES"] as $code => $arPrice )
                        {
                            if ( $arPrice["CAN_ACCESS"] )
                            {
                                if ( $arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"] )
                                {
                                    ?>
                                    <div class="infospice-search-product-price-old" >
                                        <strong ><?= $arPrice["PRINT_VALUE"] ?></strong >
                                    </div >
                                    <div class="infospice-search-product-price-new infospice-search-product-price-red" >
                                        <strong ><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></strong >
                                    </div >
                                <?
                                }
                                else
                                {
                                    ?>
                                    <div class="infospice-search-product-price-new" >
                                        <strong ><?= $arPrice["PRINT_VALUE"] ?></strong >
                                    </div >
                                <?
                                }
                            }
                        }
                        ?>
                    </div >
                    <?
                    //
                    // Кнопка "Купить" (если в настройках компоненты, она включена)
                    //
                    if ( $arParams["ADD_TO_BASKET_BUTTON"] == "Y" )
                    {
                        if ( $arElement["ALREADY_ON_BASKET"] == "Y" )
                        {
                            echo '<strong class="infospice-search-product-incart">' . $arParams["ALREADY_ON_BASKET_TITLE"] . '</strong>';
                        }
                        else
                        {
                            $btnStyle = $arParams["ADD_TO_BASKET_BUTTON_COLOR"];
                            if ( empty($btnStyle) )
                            {
                                $btnStyle = "red";
                            }

                            echo '<a href="' . $arElement["ADD_URL"] . '" class="infospice-search-product-btn ' . $btnStyle . '"><strong>' . $arParams["ADD_TO_BASKET_BUTTON_TITLE"] . '</strong></a>';
                        }
                    }
                    ?>
                </div >
            <?
            }


            //
            // Всякие характеристики продукта (заданые в настройках компоненты)
            //
            if ( count( $arElement["PROPERTIES"] ) > 0 )
            {
                ?>
                <ul class="infospice-search-product-info" ><?
                foreach ( $arElement["PROPERTIES"] as $arProperty )
                {
                    ?>
                    <li >
                        <span class="infospice-search-product-param" ><?= $arProperty["NAME"]; ?></span >
                        <span class="infospice-search-product-value" ><?= $arProperty["VALUE"]; ?></span >
                        <span class="infospice-search-product-dots" ></span >
                    </li >
                <?
                }
                ?></ul ><?
            }
            ?>
        </div >
        <?

        }
        ?>
    </div >
    <?

    if ( $arParams["DISPLAY_BOTTOM_PAGER"] )
    {
        echo $arResult["NAV_STRING"];
    }
    ?>
</div >
