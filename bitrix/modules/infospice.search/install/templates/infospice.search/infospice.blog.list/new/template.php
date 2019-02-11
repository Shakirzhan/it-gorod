<? if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die(); ?>


<?
//echo '<pre>////templates/infospice.search/infospice.blog.list/.default/template.php<br />'; 
//print_r( $arResult ); 
//echo '</pre>';

?>

<div class="infospice-search-products" >
    <?
    foreach ( $arResult["BLOG_POST"] as $arPost )
    {
        ?>
        <div class="infospice-search-product" >
            <a href="<?=$arPost['URL']?>" >
                <img src="<?= $this->__folder ?>/images/blog-icon.png" height="128px" alt="image description" />
            </a >
            <p >
                <a href="<?= $arPost["URL"] ?>" ><?= $arPost["TITLE"] ?></a >
            </p >
            <ul class="infospice-search-product-info" >
                <li >
                    <span class="infospice-search-product-param" ><?= GetMessage(
                            "INFOSPICE_SEARCH_OPUBLIKOVANO"
                        ) ?></span >
                    <span class="infospice-search-product-value" ><?= $arPost["DATE_PUBLISH"] ?></span >
                    <span class="infospice-search-product-dots" ></span >
                </li >
                <li >
                    <span class="infospice-search-product-param" ><?= GetMessage(
                            "INFOSPICE_SEARCH_KOMMENTARIEV"
                        ) ?></span >
                    <span class="infospice-search-product-value" ><?= $arPost["NUM_COMMENTS"] ?></span >
                    <span class="infospice-search-product-dots" ></span >
                </li >
            </ul >
        </div >
    <?
    }
    ?>
</div >

