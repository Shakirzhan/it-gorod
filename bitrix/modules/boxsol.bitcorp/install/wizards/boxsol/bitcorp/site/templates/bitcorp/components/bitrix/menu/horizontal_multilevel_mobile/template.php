<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!empty($arResult)):?>
    <?$this->setFrameMode(true);?>
    <div class="mobile-menu--block" style="display: none;"> 
        <div class="mobile-menu-contacts">
            <i class="fa fa-phone" aria-hidden="true"></i>
            <div class="mobile-menu-contacts--phone">
                <?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-phone.php", Array(), Array("MODE" => "html", "NAME" => "header-phone"));?>
            </div>
        </div>      
        <ul class="mobile-menu flex">
            <?
            $previousLevel = 0;
            foreach($arResult as $arItem):?>

                <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
                <?endif?>

                <?if ($arItem["IS_PARENT"]):?>

                    <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                        <li class="mobile-menu--item with-dropdown <?if ($arItem["SELECTED"]):?>active<?endif?>">
                            <a href="<?=$arItem["LINK"]?>" class="mobile-menu--link"><span><?=$arItem["TEXT"]?></span></a>
                            <div class="js-show-m-dropdown-menu show-m-dropdown-menu-btn">
                                <i class="fa fa-angle-down" aria-hidden="true"></i>
                            </div>
                            <ul class="m-dropdown-menu">
                    <?else:?>
                        <li class="mobile-menu--item with-dropdown <?if ($arItem["SELECTED"]):?>active<?endif?>">
                            <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a> 
                                <div class="js-show-m-dropdown-menu show-m-dropdown-menu-btn">
                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </div>                                   
                                <ul class="m-dropdown-menu">
                    <?endif?>

                <?else:?>

                    <?if ($arItem["PERMISSION"] > "D"):?>

                        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                            <li class="<?if ($arItem["SELECTED"]):?>active<?endif?>">
                                <a href="<?=$arItem["LINK"]?>" class=""><span><?=$arItem["TEXT"]?></span></a>
                            </li>
                        <?else:?>
                            <li class="<?if ($arItem["SELECTED"]):?> active<?endif?>">
                                <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
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
    </div> 
<? endif ?>