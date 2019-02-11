<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<tr>						
	<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		<td>
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
		    	<span><?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?></span>
			<?else:?>
		    	<span><?=$arProperty["DISPLAY_VALUE"];?></span>
			<?endif?>
		</td>
	<?endforeach;?>
</tr>