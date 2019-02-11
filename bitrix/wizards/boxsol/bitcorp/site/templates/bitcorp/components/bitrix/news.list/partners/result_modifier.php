<?
$arCustomFields = array("SHOW_FRONT_PAGE");

foreach ($arResult["ITEMS"] as $key => $arItem) {
	foreach ($arItem["DISPLAY_PROPERTIES"] as $propCode => $propValue) {
		if(!in_array($propCode, $arCustomFields)){			
			$arResult["ITEMS"][$key]["PARTNER_FIELDS"][$propCode] = $propValue;
		}	
	}	
}

//collect all sections IDs from all elements
foreach($arResult["ITEMS"] as $arItem){
	if($SID = $arItem["IBLOCK_SECTION_ID"]){
		$arSectionsIDs[] = $SID;
	}
}

if($arSectionsIDs){

	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE"	=> "Y",
		"GLOBAL_ACTIVE" => "Y",
		"DEPTH_LEVEL" => 1,
		"ID" => $arSectionsIDs,
		);

	$arSelect = array(
		"ID",
		"NAME",
		"SECTION_PAGE_URL",
		"DESCRIPTION"
		);
	$arSort = array(
		"SORT"=>"ASC"
		);

	$rsSect = CIBlockSection::GetList(
		$arSort,
		$arFilter,
		true,
		$arSelect
		);

	$arResult["SECTIONS"] = array();	
    while ($arSect = $rsSect->GetNext()) {
        $arResult["SECTIONS"][$arSect["ID"]] = $arSect;
    }    

    if(count($arResult["SECTIONS"])){
	    foreach ($arResult["ITEMS"] as $key => $arItem) {
	    	$elementGroupsList = CIBlockElement::GetElementGroups($arItem['ID'], false);    	
	    	if ($arParams["SHOW_INFOBLOCK_DESCRIPTION"] != "N" && $arElementSect = $elementGroupsList->Fetch()) {    		
	    		$arResult["SECTIONS"][$arElementSect["ID"]]["ITEMS"][] = $arItem;
	    	} else {
	    		$arResult["SECTIONS"]['NOSECT']["ITEMS"][] = $arItem;
	    	}
	    }
	}
}	
?>