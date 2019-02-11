<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>


<?
//echo '<pre>////templates/infospice.search/infospice.blog.list/.default/template.php<br />'; 
//print_r( $arResult ); 
//echo '</pre>';

?>

<div class="infospice-search-products">
<?
foreach( $arResult["ITEMS"] as $arTopic ) 
{		
	?>
	<div class="infospice-search-product">
		<a href="#"><img src="<?=$this->__folder?>/images/forum-icon.png" height="128px" alt="image description" /></a>
		<p><a href="<?=$arTopic[ "URL" ]?>"><?=$arTopic[ "TITLE" ]?></a></p>
		<ul class="infospice-search-product-info">
			<li>
				<span class="infospice-search-product-param">Опубликовано</span>
				<span class="infospice-search-product-value"><?=$arTopic[ "START_DATE" ]?></span>
				<span class="infospice-search-product-dots"></span>
			</li>
			<li>
				<span class="infospice-search-product-param">Сообщений</span>
				<span class="infospice-search-product-value"><?=$arTopic[ "POSTS" ]?></span>
				<span class="infospice-search-product-dots"></span>
			</li>
		</ul>
	</div>
	<?
}
?>	
</div>

