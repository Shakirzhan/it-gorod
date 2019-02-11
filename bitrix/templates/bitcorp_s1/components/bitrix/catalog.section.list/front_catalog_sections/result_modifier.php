<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//process section sections (for depth level 2 catalog only now)
foreach ($arResult["SECTIONS"] as $key => $arSection){
	//process section user fields
	if($arSection["UF_SHOW_ON_INDEX"] > 0) {
		$arSection["UF_SHOW_ON_INDEX"] = true;
	};
	if($arSection["UF_TITLE_BG"] > 0) {
		$arSection["UF_TITLE_BG"] = true;
	};
	if($arSection["UF_SECTION_SIZE"] > 0) {
		$UserField = CUserFieldEnum::GetList(array(), array("ID" => $arSection["UF_SECTION_SIZE"]));
		if($arUserField = $UserField->Fetch()) {
			$arSection["UF_SECTION_SIZE"] = $arUserField["XML_ID"];
		}
	};	

	//find depth level 2 subsections for level 1 section
	if(!empty($arSection["IBLOCK_SECTION_ID"]) && $arSection["RELATIVE_DEPTH_LEVEL"] == 2){
		if($arResult["RESULT_SECTIONS"][$arSection["IBLOCK_SECTION_ID"]]){
			$arResult["RESULT_SECTIONS"][$arSection["IBLOCK_SECTION_ID"]]["SUBSECTIONS"][] = $arSection;	
		}
	} else{
		//find depth level 1 sections
		$arResult["RESULT_SECTIONS"][$arSection["ID"]] = $arSection;
	}
}
?>