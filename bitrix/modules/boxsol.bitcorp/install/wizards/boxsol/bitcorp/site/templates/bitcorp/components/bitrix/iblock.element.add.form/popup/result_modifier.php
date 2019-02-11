<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use \Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest(); 
CModule::IncludeModule(COSMOS_MODULE_NAME);

if ($arParams["IBLOCK_ID"] != Option::get(COSMOS_MODULE_NAME, "feedback_iblock_id", "", SITE_ID)) {
	Option::set(COSMOS_MODULE_NAME, "feedback_iblock_id", $arParams["IBLOCK_ID"], SITE_ID);
}
if ($arParams["EMAIL_TO"] != Option::get(COSMOS_MODULE_NAME, "email_feedback_section_id_" . $arParams["SECTION_ID"], "", SITE_ID)) {
	Option::set(COSMOS_MODULE_NAME, "email_feedback_section_id_" . $arParams["SECTION_ID"], $arParams["EMAIL_TO"], SITE_ID);
}


if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	$arResult['IS_AJAX'] = false;
} else {
	$arResult['IS_AJAX'] = true;
}

foreach ($arResult["PROPERTY_LIST_FULL"] as $keyProp => $arProp) {
	if ($arProp["PROPERTY_TYPE"] == "E") {

		$arSelect = Array("ID", "NAME", "CODE");
		$arFilter = Array("IBLOCK_ID"=>IntVal($arProp["LINK_IBLOCK_ID"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>100), $arSelect);
		while($ob = $res->GetNextElement())
		{
			$itemToAdd = array();
			$arFields = $ob->GetFields();
			$itemToAdd["ID"] = $arFields["ID"]; 
			$itemToAdd["VALUE"] = $arFields["NAME"];
			if ($arFields["CODE"] == $_GET["ELEMENT_CODE"]) {
				$itemToAdd["DEF"] = "Y";
			}
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["ENUM"][$itemToAdd["ID"]] = $itemToAdd;
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["PROPERTY_TYPE"] = "L";
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["MULTIPLE"] = "Y";
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["PROPERTY_TYPE_OLD"] = "E";
		}
	}

	if (0 === strpos(strtolower($arProp["CODE"]), 'utm')) {
		if (isset($_COOKIE[strtolower($arProp["CODE"])])) {
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["DEFAULT_VALUE"] = htmlspecialcharsbx($_COOKIE[strtolower($arProp["CODE"])]);
		} else {
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["DEFAULT_VALUE"] = htmlspecialcharsbx($request->getQuery(strtolower($arProp["CODE"])));
		}
	}

	if ($arProp["CODE"] == 'REF_PAGE') {
		if ($arResult['IS_AJAX'] == true) {
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["DEFAULT_VALUE"] = htmlspecialcharsbx($_SERVER["HTTP_REFERER"]);
		} else {
			$arResult["PROPERTY_LIST_FULL"][$keyProp]["DEFAULT_VALUE"] = htmlspecialcharsbx($_SERVER["REQUEST_SCHEME"]) . '://' . htmlspecialcharsbx($_SERVER["HTTP_HOST"]) .  htmlspecialcharsbx($_SERVER["REQUEST_URI"]);
		}
	}

}

if ($arParams["SECTION_ID"] > 0) {

	$res = CIBlockSection::GetByID($arParams["SECTION_ID"]);
	if($ar_res = $res->GetNext()) {
		$arResult["SECTION_DESCRIPTION"] = $ar_res["DESCRIPTION"];
	} else {
		$arResult["SECTION_DESCRIPTION"] = "";
	}
} else {
		$arResult["SECTION_DESCRIPTION"] = "";
}