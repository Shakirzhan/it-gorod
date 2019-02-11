<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//echo "<pre>arParams: "; print_r($arParams);echo "</pre>";
//echo "<pre>arResult: "; print_r($arResult);echo "</pre>";

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}


$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>


<div class="infospice-search-pager">
	<?
	if ($arResult["NavPageNomer"] > 1)
	{
		if($arResult["bSavePage"])
		{
			?><a class="infospice-search-prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&nbsp;</a><?
		}
		else 
		{
			if ( $arResult["NavPageNomer"] > 2 )
			{
				?><a class="infospice-search-prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&nbsp;</a><?
			}
			else 
			{
				?><a class="infospice-search-prev" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">&nbsp;</a><?
			}
		}
	}
	else 
	{
		?><a class="infospice-search-prev" href="">&nbsp;</a><?
	}
	?>
	<ul>
		<?
		while($arResult["nStartPage"] <= $arResult["nEndPage"])
		{
			if ($arResult["nStartPage"] == $arResult["NavPageNomer"])
			{
				?><li><a class="active" href=""><?=$arResult["nStartPage"]?></a></li><?
			}
			elseif( $arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false )
			{
				?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li><?	
			}
			else 
			{
				?><li><a href="?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li><?	
			}
			$arResult["nStartPage"]++;
		}
		?>
	</ul>
	<?
	if($arResult["NavPageNomer"] < $arResult["NavPageCount"])
	{
		?><a class="infospice-search-next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">&nbsp;</a><?
	}
/*	if ($arResult["bShowAll"])
	{
		if ($arResult["NavShowAll"])
		{
			?><a class="infospice-search-next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow">&nbsp;</a><?
		}
		else
		{
			?><a class="infospice-search-next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow">&nbsp;</a><?
		}
	}
	*/
	?>

</div>

<? return; ?>











			
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

	<?if ( !empty( $arResult["NavTitle"])):?>
		<span class="title"><?=$arResult["NavTitle"]?>:</span>
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