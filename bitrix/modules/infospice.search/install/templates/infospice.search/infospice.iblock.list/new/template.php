<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>
<?
$iblockId  = (!empty($_GET["iblock"])) ? ( int )($_GET["iblock"]) : 0;
$sectionId = (!empty($_GET["section"])) ? ( int )($_GET["section"]) : 0;
$userQuery = (!empty($_GET["q"])) ? trim( $_GET["q"] ) : "";
$pageType  = (!empty($_GET["page"])) ? trim( $_GET["page"] ) : "";
$isStatic  = ( boolean )($pageType == "static");


// $res = CIBlock::GetByID( $iblockId );
// if ( $ar_res = $res->GetNext())
//  		$iblockName = $ar_res['NAME'];

//echo '<pre>'; print_r( $arResult ); echo '</pre>';
?>


<div class="infospice-search-sidebar" >
    <h3 ><?= GetMessage( "INFOSPICE_SEARCH_REZULQTATY_POISKA_PO" ) ?></h3 >

    <?
    //
    // 1. Инфоблоки
    //
    ?>
    <ul class="infospice-search-sidebar-nav" >
        <?
        if(count($arResult["IBLOCK"]))
        {
            foreach ( $arResult["IBLOCK"] as $arIBlock )                                     // - Первый уровень
            {
                ?>
                <li >
                    <a href="<?= $arIBlock['URL'] ?>"
                       <? if (!isset($_REQUEST['section']) && $_REQUEST['iblock'] == $arIBlock['ID']){
                       ?>class="active"
                        <? }; ?> >
                        <?= $arIBlock['NAME'] ?>
                    </a >

                    <span class="arrow" ></span >
                    <?
                    if ( is_array( $arIBlock["SECTIONS"] ) && !empty($arIBlock["SECTIONS"]) )
                    {
                        ?>
                        <ul >
                            <?
                            $cnClose      = 0;
                            $arKeySection = array_keys( $arIBlock["SECTIONS"] );
                            $keySection   = 0;
                            foreach ( $arIBlock["SECTIONS"] as $arSection )                         // - Второй уровень
                            {
                                ?>
                                <li >
                                    <a href="<?= $arSection['URL'] ?>"
                                       <? if ($_REQUEST['section'] == $arSection['ID']){ ?>class="active" <? } ?>
                                        >
                                        <?= $arSection['NAME'] ?>
                                    </a >
                                    <?
                                    $nextKey = $keySection + 1;
                                    if ( $arIBlock["SECTIONS"][$arKeySection[$nextKey]]['DEPTH_LEVEL'] > $arSection['DEPTH_LEVEL'] )
                                    {
                                        $cnClose++;
                                        ?>
                                        <ul >
                                    <?
                                    }
                                    elseif ( $arIBlock["SECTIONS"][$arKeySection[$nextKey]]['DEPTH_LEVEL'] != $arSection['DEPTH_LEVEL'] )
                                    {
                                        for ( $in = 0 ; $in < $cnClose ; $in++ )
                                        {
                                            ?>
                                            </ul>
                                        <?
                                        }
                                        $cnClose = 0;
                                    }
                                    ?>
                                </li >
                                <?
                                $keySection++;
                            }
                            ?>
                        </ul >
                    <?
                    }
                    ?>
                </li >
                <?/**/
            }
        }

        //
        // 2. Блоги
        //
        if ( !empty($arResult["BLOG"]) )
        {
            $url = $APPLICATION->GetCurPage();
            $url .= "?q=" . $userQuery . "&blog=all";
            ?>
            <li >
                <a href="<?= $url ?>"
                    <?if($_REQUEST['blog'] == "all"){?>class="active"<?};?>
                 ><?= GetMessage( "INFOSPICE_SEARCH_BLOGI" ) ?></a >
                <span class="arrow" ></span >
                <ul >
                    <?
                    foreach ( $arResult["BLOG"] as $arBlog )
                    {
                        $url   = $arBlog['URL'];
                        $count = $arBlog['COUNT'];
                        $name  = $arBlog['NAME'];

                        ?>
                        <li >
                        <a href="<?= $url ?>"
                           <?if($_REQUEST['blog'] == $arBlog["ID"]){?>class="active"<?};?>
                         ><?= $name ?></a ></li ><?
                    }
                    ?>
                </ul >
            </li >
            <?/**/
        }

        //
        // 4. Форумы
        //
        if ( !empty($arResult["FORUM"]) )
        {
            $url = $APPLICATION->GetCurPage();
            $url .= "?q=" . $userQuery . "&forum=all";
            ?>
            <li >
                <a href="<?= $url ?>"
                   <?if($_REQUEST['forum'] == "all"){?>class="active"<?};?>
                 ><?= GetMessage( "INFOSPICE_SEARCH_FORUMY" ) ?></a >
                <span class="arrow" ></span >
                <ul >
                    <?
                    foreach ( $arResult["FORUM"] as $arForum )
                    {
                        $url   = $arForum['URL'];
                        $count = $arForum['COUNT'];
                        $name  = $arForum['NAME'];

                        ?>
                        <li >
                        <a href="<?= $url ?>"
                           <?if($_REQUEST['forum'] == $arForum["ID"]){?>class="active"<?};?>
                        ><?= $name ?></a ></li ><?
                    }
                    ?>
                </ul >
            </li >
            <?/**/
        }


        //
        // 5. Другие страницы
        //
        if ( !empty($arResult["STATIC"]) )
        {
            $url = $APPLICATION->GetCurPage();
            $url .= "?q=" . $userQuery . "&static=all";
            ?>
            <li >
                <a href="<?= $url ?>"
                   <?if($_REQUEST['static'] == "all"){?>class="active"<?};?>
                 ><?= GetMessage( "INFOSPICE_SEARCH_DRUGIE_STRANICY" ) ?></a >
                <span class="arrow" ></span >
            </li >
        <?
        }
        ?>
    </ul >
</div >


