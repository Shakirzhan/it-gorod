<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

//echo "<pre>"; print_r($arResult);echo "</pre>";

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>


			
<div class="pages-navigation">
	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["bSavePage"]):?>
			<span class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&larr;</a></span>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<span class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&larr;</a></span>
			<?else:?>
				<span class="prev"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">&larr;</a></span>
			<?endif?>
		<?endif?>

	<?else:?>
		<span class="prev"><a href="">&larr;</a></span>
	<?endif?>
	<ul>
		<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
	
			<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
				<li><strong><?=$arResult["nStartPage"]?></strong></li>
			<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
				<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
			<?else:?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
			<?endif?>
			<?$arResult["nStartPage"]++?>
		<?endwhile?>
	</ul>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<span class="next"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">&rarr;</a></span>
	<?endif?>
	
	<?if ($arResult["bShowAll"]):?>
		<?if ($arResult["NavShowAll"]):?>
			<span class="view-all"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow"><?=GetMessage('nav_paged')?></a></span>
		<?else:?>
			<span class="view-all"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow"><?=GetMessage('nav_all')?></a></span>
		<?endif?>
	<?endif?>
</div>