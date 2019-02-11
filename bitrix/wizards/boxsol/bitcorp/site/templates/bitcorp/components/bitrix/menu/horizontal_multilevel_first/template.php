<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;
if (!empty($arResult)):?>
    <?
    CModule::IncludeModule(BITCORP_MODULE_NAME);
    $this->setFrameMode(true);   
    $isIndex = CSite::inDir(SITE_DIR."index.php");
    ?>
    <nav class="nav-main" role="navigation">
        <ul class="main-menu flex">
            <?
            $previousLevel = 0;
            foreach($arResult as $key => $arItem):?>

                <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
                <?endif?>

                <?if ($arItem["IS_PARENT"]):?>

                    <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                        <li class="main-menu--item main-menu--item_drop <?if ($arItem["SELECTED"]):?>main-menu--item_active<?endif?>">
                            <a href="<?=$arItem["LINK"]?>" class="main-menu--link"><span><?=$arItem["TEXT"]?></span></a>
                            <ul class="dropdown-menu">
                    <?else:?>
                        <li class="main-menu--item main-menu--item_drop <?if ($arItem["SELECTED"]):?>main-menu--item_active<?endif?>">
                            <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu-lev2">
                    <?endif?>

                <?else:?>

                    <?if ($arItem["PERMISSION"] > "D"):?>

                        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                            <li class="main-menu--item <?= ($arItem["SELECTED"]) ? 'main-menu--item_active' : (!$isIndex && $key == 0 ? 'first-menu-item' : '')?>">
                                <a href="<?=$arItem["LINK"]?>" class="main-menu--link"><span><?=$arItem["TEXT"]?></span></a>
                            </li>
                        <?else:?>
                            <li<?if ($arItem["SELECTED"]):?> class="dropdown-menu-active"<?endif?>>
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

            <!-- more -->
            <li class="more main-menu--item main-menu--item_drop" data-width="110" style="display: none;">
                <a href="#" class="main-menu--link">
                  <span><?=Loc::getMessage("MENU_SHOW_MORE")?></span>
                </a>
                <ul class="more-dropdown-menu dropdown-menu dropdown-menu_pull-right"></ul>
            </li>
            <!-- /more -->

        </ul>
    </nav>   
<? endif ?>
       
