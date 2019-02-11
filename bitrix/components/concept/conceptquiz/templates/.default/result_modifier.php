<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
// 
if(!empty($arResult["ITEMS"]))
{
	$emptyAnswers = 0;
	$cwizMaxResult = 0;
	foreach($arResult["ITEMS"] as $arItem)
	{
		// 
		foreach($arItem["PROPERTIES"]['QUEST_ANSWER']['DESCRIPTION'] as $val)
		{
			if(strlen($val) == 0 || $val <= 0) 
				$val = 0;
			$cwizMaxResult += $val;
		}
		foreach($arItem["PROPERTIES"]['QUEST_ANSWER']['~VALUE'] as $check_val)
		{
			if($check_val == ' ')
				$emptyAnswers++;		
		}
	}
	$arResult['EMPTY_ANSWERS'] = $emptyAnswers;
	$arResult["CWIZ_MAX_RESULT"] = $cwizMaxResult;
}

if($arParams["TYPE"] == "quiz_block")
{
	$arResult["SECTION_INFO"]["UF_CWIZ_TYPE_VIEW"]["XML_ID"] = 'second';
	
	$arResult["STYLE_QUIZ_BLOCK"] = "";

	if($arResult["SECTION_INFO"]["UF_BLOCK_SHADOW"])
		$arResult["STYLE_QUIZ_BLOCK"] .= "quiz_shadow_block ";

	if($arResult["SECTION_INFO"]["UF_BORD_ON"])
		$arResult["STYLE_QUIZ_BLOCK"] .= "quiz_border_on ";

	if(!empty($arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"]) && strlen($arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"]["XML_ID"])>0 && strlen($arResult["SECTION_INFO"]["UF_BLOCK_BG_OPACITY"])<=0)
		$arResult["STYLE_QUIZ_BLOCK"] .= "quiz_bg_color_".$arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"]["XML_ID"];


	


	function quiz_hex2rgb($hex) 
	{
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}



	
	$style_attr = "";

	if(strlen($arResult["SECTION_INFO"]["UF_BLOCK_BG_OPACITY"])>0)
	{
		$arColor = "#f5f5f5";

		if($arResult["SECTION_INFO"]["UF_QUIZ_BG_TYPE"]["XML_ID"] == "white")
			$arColor = "#fff";

		$percent = 1;

	    $arColor = quiz_hex2rgb($arColor);
	    $arColor = implode(',', $arColor);
	        
	    if(strlen($arResult["SECTION_INFO"]["UF_BLOCK_BG_OPACITY"])>0)
	        $percent = (100 - $arResult["SECTION_INFO"]["UF_BLOCK_BG_OPACITY"])/100;
	        
		$style_attr .= "background-color: rgba(".$arColor.", ".$percent."); ";

	}

	if($arResult["SECTION_INFO"]["UF_BORD_WIDTH"])
		$style_attr .= "border: ".$arResult["SECTION_INFO"]["UF_BORD_WIDTH"]."px solid #bdbdbd; ";

	if($arResult["SECTION_INFO"]["UF_BORD_COLOR"])
		$style_attr .= "border-color: ".$arResult["SECTION_INFO"]["UF_BORD_COLOR"]."; ";

	if($arResult["SECTION_INFO"]["UF_BLOCK_ELIPS"])
		$style_attr .= "-webkit-border-radius: ".$arResult["SECTION_INFO"]["UF_BLOCK_ELIPS"]."px; -moz-border-radius: ".$arResult["SECTION_INFO"]["UF_BLOCK_ELIPS"]."px; border-radius: ".$arResult["SECTION_INFO"]["UF_BLOCK_ELIPS"]."px; ";


	if(strlen($style_attr)>0)
		$arResult["STYLE_ATTR_QUIZ_BLOCK"] = "style='".$style_attr."'";
}

?>

