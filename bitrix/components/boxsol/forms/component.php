<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
	Bitrix\Main\Text\Encoding,
	Bitrix\Iblock,	
	Bitrix\Main\Application,
	Bitrix\Main\Mail\Event,
	Bitrix\Main\Localization\Loc;
global $APPLICATION;
global $USER;

if(!Loader::includeModule("iblock"))
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

if( $arParams["IBLOCK_ID"] > 0 ){
	$arResult["FORM_RIGHT"] = CIBlock::GetPermission( $arParams["IBLOCK_ID"] );

	if( $arResult["FORM_RIGHT"] == "D" ){
		$arResult["ERROR_MESSAGE"][] = Loc::getMessage("FORM_ACCESS_DENIED")."<br />";	
	}
} else{
	$arResult["ERROR_MESSAGE"][] = Loc::getMessage("NO_IBLOCK_ID")."<br />";
}

$arParams["IS_AUTHORIZED"] = $USER->IsAuthorized() ? "Y" : "N";
$arSetting = CBitcorp::GetFrontParametrsValues(SITE_ID);
$arParams["USE_CAPTCHA"] = $arParams["IS_AUTHORIZED"] != "Y" && $arSetting["FORMS_USE_CAPTCHA"] == "Y" ? "Y" : "N";
$arParams["PHONE_MASK"] = $arSetting["FORMS_PHONE_MASK"];
$arParams["VALIDATE_PHONE_MASK"] = $arSetting["FORMS_VALIDATE_PHONE_MASK"];
$arParams["SHOW_PERSONAL_DATA"] = "Y";
$arParams["SHOW_PERSONAL_DATA"] = $arSetting["SHOW_PERSONAL_DATA"];
$arParams["TEXT_PERSONAL_DATA"] = $arSetting["TEXT_PERSONAL_DATA"];

//$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
$arResult["ERROR_MESSAGE"] = $arResult["SUCCESS_MESSAGE"] = array();
$request = Application::getInstance()->getContext()->getRequest();

	$arResult = array();
	$arResultCacheID = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ELEMENT_NAME" => $arParams["ELEMENT_NAME"]);
	$obCache = new CPHPCache();

	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arResultCacheID), "/".SITE_ID.$this->GetRelativePath())) {
		$arResult = $obCache->GetVars();		
	} elseif($obCache->StartDataCache()) {

		//IBLOCK//
		$arIblock = CIBlock::GetList(array("SORT" => "ASC"), array("ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y"))->Fetch();
		
		if(empty($arIblock)) {
			$this->abortResultCache();
			return;
		}
		
		$arResult["IBLOCK"]["ID"] = $arIblock["ID"];
		$arResult["IBLOCK"]["CODE"] = $arIblock["CODE"];
		$arResult["IBLOCK"]["NAME"] = $arIblock["NAME"];
		$arResult["IBLOCK"]["DESCRIPTION"] = $arIblock["DESCRIPTION"];
		$arResult["IBLOCK"]["DESCRIPTION_TYPE"] = $arIblock["DESCRIPTION_TYPE"];

		//ELEMENT_AREA_ID//
		$arResult["ELEMENT_AREA_ID"] = $arResult["IBLOCK"]["CODE"];
		
		//IBLOCK_PROPS//
		$rsProps = CIBlock::GetProperties($arIblock["ID"], array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y"));
		while($arProps = $rsProps->fetch()) {
			$arResult["IBLOCK"]["PROPERTIES"][] = $arProps;
		}
		
		foreach($arResult["IBLOCK"]["PROPERTIES"] as $key => $arProp){			
			if(is_array($arProp)){
							
				$required = $arProp["IS_REQUIRED"] == "Y" ? 'required' : '';
				$bRequired = $arProp["IS_REQUIRED"] == "Y" ? true : false;
				$phone = strpos( $arProp["CODE"], "PHONE" ) !== false ? 'phone' : '';
				$placeholder = $bRequired ? $arProp["NAME"].'*' : $arProp["NAME"];
				$labelPlaceholder = $required ? ''.$arProp["NAME"].'<span class="required-star">*</span>':''.$arProp["NAME"].'';
				$icon = ($arProp["CODE"] == "NAME") ? '<i class="fa fa-user-o"></i>' : (($arProp["CODE"] == "EMAIL") ? '<i class="fa fa-envelope-o"></i>' : (($arProp["CODE"] == "PHONE")? '<i class="fa fa-phone"></i>' : ''));
				$multiple = $arProp["MULTIPLE"] == "Y" ? ' multiple ' : '';
				$elementName = $arParams["ELEMENT_NAME"] ? $arParams["ELEMENT_NAME"] : "";			
				$value = $arProp["DEFAULT_VALUE"] ? $arProp["DEFAULT_VALUE"] : "";
				$readonly = "";
				
				if($arProp["CODE"] == "SERVICE" && strlen($elementName) > 0){
					$value = $elementName;
					$readonly = 'readonly="readonly"';
				}

				$html = '';
				if($arProp["PROPERTY_TYPE"] == "S"  && empty($arProp["USER_TYPE"]) && $arProp["CODE"] == "SEND_FROM"){
					//input=hidden
					$value = $APPLICATION->GetCurPage();
					$html = '<input value="'.$value.'" type="hidden" id="form_'.$arProp["CODE"].'" name="'.$arProp["CODE"].'"/>';
					$arResult["IBLOCK"]["PROPERTIES"][$key]["HTML_CODE"] = $html;
				}elseif($arProp["PROPERTY_TYPE"] == "S"  && empty($arProp["USER_TYPE"])){
					//input=text
					$html = '<input value="'.$value.'" type="'.($arProp["CODE"] == "EMAIL" ? "email" : "text").'" id="form_'.$arProp["CODE"].'" name="'.$arProp["CODE"].'" class="form-control '.$required.' '.$phone.'" placeholder="'.$placeholder.'" '.$readonly.' />'.'<label for="form-'.$arProp["CODE"].'">'.$labelPlaceholder.'</label>'.$icon;
					$arResult["IBLOCK"]["PROPERTIES"][$key]["HTML_CODE"] = $html;

				}elseif($arProp["PROPERTY_TYPE"] == "S" && !empty($arProp["USER_TYPE"]) && $arProp["USER_TYPE"] == "HTML" ){
					//textarea
					$value = (isset($value['TEXT']) ? $value['TEXT'] : $value);
					$html = '<textarea rows="3" id="form_'.$arProp["CODE"].'"  class="form-control '.$required.'"  name="'.$arProp["CODE"].'" placeholder="'.$placeholder.'">'.$value.'</textarea><label for="form-'.$arProp["CODE"].'">'.$labelPlaceholder.'</label><i class="fa fa-commenting-o "></i>';
					$arResult["IBLOCK"]["PROPERTIES"][$key]["HTML_CODE"] = $html;
				} elseif($arProp["PROPERTY_TYPE"] == "L" && $arProp["LIST_TYPE"] == "L"){
					//select
					$rsSelectValues = CIBlockProperty::GetPropertyEnum( $arProp["CODE"], array( "SORT" => "ASC", "ID" => "ASC" ), array("IBLOCK_ID" => $arParams["IBLOCK_ID"]));				

					$html = '<select id="form_'.$arProp["CODE"].'" name="'.$arProp["CODE"].($arProp["MULTIPLE"] == "Y" ? '[]' : '').'" class="form-control '.$required.'" '.$multiple.$placeholder.' '.$readonly.'>';
					while( $arSelectValue = $rsSelectValues->Fetch() ){
						$selected = '';
						if( !empty( $value ) && (!is_array($value) ? ($arSelectValue["ID"] == $value) : (in_array($arSelectValue["ID"], $value))) ){
							$selected = 'selected="selected"';
						}
						if( empty( $value ) && $arSelectValue["DEF"] == "Y" ){
							$selected = 'selected="selected"';
						}
						$html .= '<option '.$selected.' value="'.$arSelectValue["ID"].'">'.$arSelectValue["VALUE"].'</option>';

						$arResult["IBLOCK"]["PROPERTIES"][$key]["ENUMS"][$arSelectValue["ID"]] = $arSelectValue["VALUE"];
					}
					$html .= '</select>';

					$arResult["IBLOCK"]["PROPERTIES"][$key]["HTML_CODE"] = $html;
				}
			}
		}

		if(!isset($arResult["IBLOCK"]["PROPERTIES"]) || empty($arResult["IBLOCK"]["PROPERTIES"])) {
			$this->abortResultCache();
			return;
		}		
		$obCache->EndDataCache($arResult);
	}
	
	


$formSubmit = $request->getPost("form_submit");
$paramsHash = $request->getPost("PARAMS_HASH");
$bPost = $request->isPost();
$method = $request->getRequestMethod();

//process submit
if($bPost && $formSubmit <> ''){

	$captchaWord = $request->getPost("CAPTCHA_WORD");
	$captchaSid = $request->getPost("CAPTCHA_SID");

	//REQUARED//
	foreach($arResult["IBLOCK"]["PROPERTIES"] as $key => $arProp) {		
		if($arProp["IS_REQUIRED"] == "Y") {
			$arRequared[] = array(
				"CODE" => $arProp["CODE"],
				"NAME" => $arProp["NAME"]
			);
		}
		
	}	

	//CHECKS//
	if(isset($arRequared) && !empty($arRequared)) {
		foreach($arRequared as $arRequaredProp) {
			$arPropFromPost = $request->getPost($arRequaredProp["CODE"]);
			if(empty($arPropFromPost)){
				$arResult["ERROR_MESSAGE"][] = Loc::getMessage("FIELD_NOT_FILLED", array("#FIELD#" => $arRequaredProp["NAME"]))."<br />";
			}
		}
	}

	//VALIDATE PHONE_MASK//
	/*
	foreach($arResult["IBLOCK"]["PROPERTIES"] as $key => $arProp) {
		if($arProp["CODE"] == "PHONE") {
			$arPropFromPost = $request->getPost($arProp["CODE"]);
			if(!empty($arPropFromPost)) {
				if(!preg_match($arParams["VALIDATE_PHONE_MASK"], $arPropFromPost)) {
					$arResult["ERROR_MESSAGE"][] = Loc::getMessage("FIELD_INVALID", array("#FIELD#" => $arProp["NAME"]))."<br />";
				}
			}
		}
	}
	*/

	if(!empty($captchaSid) && !$APPLICATION->CaptchaCheckCode($captchaWord, $captchaSid)){
		$arResult["ERROR_MESSAGE"][] = Loc::getMessage("WRONG_CAPTCHA")."<br />";	
	}

	//PROCESS PROPERTIES//
	$arProps = array();//fields for event
	$arElementProps = array();//fields for new iblock element

	foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {			
		$arPropFromPost = $request->getPost($arProp["CODE"]);
							
		if(!empty($arPropFromPost)) {
			if($arProp["USER_TYPE"] == "HTML") {
				$arProps[$arProp["CODE"]] = array(
					"VALUE" => array(
						"TEXT" => \Bitrix\Main\Text\Encoding::convertEncodingToCurrent(strip_tags(trim($arPropFromPost))),
						"TYPE" => $arProp["DEFAULT_VALUE"]["TYPE"]
					)
				);
				$arElementProps[$arProp["CODE"]] = array(
					"VALUE" => array(
						"TEXT" => \Bitrix\Main\Text\Encoding::convertEncodingToCurrent(strip_tags(trim($arPropFromPost))),
						"TYPE" => $arProp["DEFAULT_VALUE"]["TYPE"]
					)
				);
			} elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["LIST_TYPE"] == "L"){
				if(is_array($arPropFromPost)){
					foreach ($arPropFromPost as $propValue) {
						$arTmp[] = $arProp["ENUMS"][$propValue];	
					}
					$arProps[$arProp["CODE"]] = implode(" / ", $arTmp);
				} else {
					$arProps[$arProp["CODE"]] = $arProp["ENUMS"][$arPropFromPost];
				}

				$arElementProps[$arProp["CODE"]] = $arPropFromPost;
			} else {				
				$arProps[$arProp["CODE"]] = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent(strip_tags(trim($arPropFromPost)));

				$arElementProps[$arProp["CODE"]] = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent(strip_tags(trim($arPropFromPost)));									
			}
		}		
	}
	
	//NEW_ELEMENT//
	if(count($arResult["ERROR_MESSAGE"]) <= 0){
		$el = new CIBlockElement;

		$arFields = array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"NAME" => Loc::getMessage("IBLOCK_ELEMENT_NAME").ConvertTimeStamp(time(), "FULL", SITE_ID),
			"PROPERTY_VALUES" => isset($arElementProps) && !empty($arElementProps) ? $arElementProps : array(),
		);

		if($el->Add($arFields)) {

			$arResult["SUCCESS_MESSAGE"][] = Loc::getMessage("SUCCESS_MESSAGE")."<br />";	

			//MAIL_EVENT//
			$eventName = "MD_FORM_".$arResult["IBLOCK"]["CODE"];

			$eventDesc = "";
			$messBody = "";

			foreach($arResult["IBLOCK"]["PROPERTIES"] as $key => $arProp) {
				$eventDesc .= "#".$arProp["CODE"]."# - ".$arProp["NAME"]."\n";
				$messBody .= $arProp["NAME"].": "."#".$arProp["CODE"]."#\n";
			}

			$eventDesc .= GetMessage("MAIL_EVENT_DESCRIPTION");

			//MAIL_EVENT_TYPE//
			$arEvent = CEventType::GetByID($eventName, LANGUAGE_ID)->Fetch();
			if(empty($arEvent)) {
				$et = new CEventType;
				$arEventFields = array(
					"LID" => LANGUAGE_ID,
					"EVENT_NAME" => $eventName,
					"NAME" => GetMessage("MAIL_EVENT_TYPE_NAME")." \"".$arResult["IBLOCK"]["NAME"]."\"",
					"DESCRIPTION" => $eventDesc
				);
				$et->Add($arEventFields);
			}

			//MAIL_EVENT_MESSAGE//
			$arMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE_ID" => $eventName))->Fetch();
			if(empty($arMess)) {
				$em = new CEventMessage;
				$arMess = array();
				$arMess["ID"] = $em->Add(
					array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => $eventName,
						"LID" => SITE_ID,
						"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
						"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
						"BCC" => "",
						"SUBJECT" => GetMessage("MAIL_EVENT_MESSAGE_SUBJECT"),
						"BODY_TYPE" => "text",
						"MESSAGE" => GetMessage("MAIL_EVENT_MESSAGE_MESSAGE_HEADER").$messBody.GetMessage("MAIL_EVENT_MESSAGE_MESSAGE_FOOTER")
					)
				);
			}

			//SEND_MAIL//
			$arProps["FORM_NAME"] = $arResult["IBLOCK"]["NAME"];

			Event::send(array(
				"EVENT_NAME" => $eventName,
				"LID" => SITE_ID,
				"C_FIELDS" => $arProps,
			));			

		} else {
			$arResult["ERROR_MESSAGE"][] = Loc::getMessage("ERROR_MESSAGE")."<br />".$el->LAST_ERROR;		
		}
	}

	//\Bitrix\Main\Diag\Debug::dumpToFile($_REQUEST);
	
}

$this->IncludeComponentTemplate();
?>
