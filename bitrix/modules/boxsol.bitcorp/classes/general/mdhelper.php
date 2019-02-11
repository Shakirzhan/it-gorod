<?php

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Localization\Loc;
use \Boxsol\Bitcorp\Template;

class CMarsHelper
{

	static function isIncFileExists11($fileSuffix)
	{
		$sRealFilePath = $_SERVER["REAL_FILE_PATH"];
		// if page in SEF mode check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos + 1);
			$sFileName = substr($sRealFilePath, $slash_pos + 1);
			$sFileName = substr($sFileName, 0, strlen($sFileName) - 4) . "_" . $fileSuffix . ".php";
		}
		// otherwise use current
		else
		{
			$sFilePath = $GLOBALS['APPLICATION']->GetCurDir();
			$sFileName = substr($GLOBALS['APPLICATION']->GetCurPage(true), 0, strlen($GLOBALS['APPLICATION']->GetCurPage(true)) - 4) . "_" . $fileSuffix . ".php";
			$sFileName = substr($sFileName, strlen($sFilePath));
		}

		$io = CBXVirtualIo::GetInstance();
		return $io->FileExists($_SERVER['DOCUMENT_ROOT'] . $sFilePath . $sFileName);
	}

	static function changeSiteColor($color, $siteId, $addTmpStyle)
	{

	}

	static function checkPhone($phoneNumber)
	{
		if(preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $phoneNumber))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	static function getIconByPropCode($propCode)
	{
		switch ($propCode) {
			case 'LINK':
				return '<i class="icon-external-link"></i>';
				break;
			case 'PHONE':
				return '<i class="icon-phone3"></i>';
				break;
			case 'EMAIL':
				return '<i class="icon-mail"></i>';
				break;
			default:
				return false;
				break;
		}
	}

	static function getLinkByPropCode($propString, $propCode)
	{
		switch ($propCode) {
			case 'EMAIL':
				return '<a href="mailto:' . $propString . '">' . $propString . '</a>';
				break;
			default:
				return $propString;
				break;
		}
	}

	static function convertRequest(&$arToConvert)
    {
        /*
         * TODO: update function to use D7
        $context = Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();

        $post = $request->getPostList()->toArray();

        $post = Main\Text\Encoding::convertEncoding($post, "UTF-8", $context->getCulture()->getCharset());
        */


		if (SITE_CHARSET == 'utf-8')
			return;

		if (!isset($arToConvert["IBLOCK_ID"]) || Option::get(BITCORP_MODULE_NAME, "feedback_iblock_id", "", SITE_ID) != $arToConvert["IBLOCK_ID"])  {
			return;
		}

		array_walk_recursive($arToConvert, function(&$value,$key){
		   $value=iconv('utf-8', SITE_CHARSET, $value);
		});
	}

	static function sendInfoToEmail(&$arFields){
		if (!isset($arFields["IBLOCK_ID"]) || Option::get(BITCORP_MODULE_NAME, "feedback_iblock_id", "", SITE_ID) != $arFields["IBLOCK_ID"])  {
			return;
		}

		if (Option::get(BITCORP_MODULE_NAME, "b24_use", "N") == "Y") {
            $integrationB24 = new IntegrationBitrix24();
            $integrationB24->sendInfoToB24($arFields);
        }

		$EVENT_NAME = "COSMOS_CONTACT_IBLOCK";

        $arSend = array();

		if (Option::get(BITCORP_MODULE_NAME, "email_feedback_section_id_" . $arFields["IBLOCK_SECTION"][0], "", SITE_ID)) {
			$arSend["EMAIL_TO"] = Option::get(BITCORP_MODULE_NAME, "email_feedback_section_id_" . $arFields["IBLOCK_SECTION"][0], "", SITE_ID);

            if ($arSend["EMAIL_TO"] == "example@example.com" || $arSend["EMAIL_TO"] == "") {
                $arSend["EMAIL_TO"] = Option::get("main", 'email_from', '', SITE_ID);
            }
		}
		if (isset($arSend["EMAIL_TO"]) && strlen($arSend["EMAIL_TO"]) >0) {
        	$arSend["MESSAGE_TYPE"] = $arFields["NAME"];
        	$arSend["MESSAGE"] = "";

            $utmString = '';
        	foreach ($arFields["PROPERTY_VALUES"] as $keyProp => $valueProp) {
				$resIblockProp = CIBlockProperty::GetByID($keyProp);
				if($arIblockProp = $resIblockProp->GetNext()) {

                    if (strpos(strtoupper($arIblockProp["CODE"]), 'UTM_') !== false) {
                        $utmString .= $arIblockProp['NAME'] . ": " . $valueProp . "
";
                        continue;
                    }


                    $arSend["MESSAGE"] .= $arIblockProp['NAME'] . ": 
	";

					if ($arIblockProp["LINK_IBLOCK_ID"] > 0) {
						$resIblockElement = CIBlockElement::GetByID($valueProp);
						if($arElement = $resIblockElement->GetNext())
							$valueProp = $arElement['NAME'];
					}

                    if ($arIblockProp["PROPERTY_TYPE"] == "L") {
                        $propEnumById = CIBlockPropertyEnum::GetByID($valueProp);
                        $valueProp = $propEnumById["VALUE"];
                    }

                    if (is_array($valueProp)) {
                        $tmpString = '';
                        foreach ($valueProp as $itemVal) {
                            $tmpString = $itemVal . "
    ";
                        }
                        $valueProp = $tmpString;
                    }

					$arSend["MESSAGE"] .= $valueProp . '
';
				}
        	}
        	$arSend["MESSAGE"] .= $utmString;
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $isMakeOrder = $request->getPost("MAKE_ORDER");
            if ($isMakeOrder == "Y") {
                CBitrixComponent::includeComponentClass("boxsol:basket.product.list");
                $basket = new CBoxsolProductList();
                $strProductsList = $basket->getListForEmail();
                $arSend["MESSAGE"] =  $strProductsList. $arSend["MESSAGE"];
                $arFields["PREVIEW_TEXT"] = $strProductsList;
            }

            CEvent::Send($EVENT_NAME, SITE_ID, $arSend);
        }
	}


	static function checkHexColor($color) {
		return preg_match('/^#[a-f0-9]{6}$/i', $color);
	}

	static function saveSettings() {
		global $USER;
		if (!$USER->IsAdmin()) return;
		foreach ($_COOKIE as $keyCookie => $valCookie) {
			if (stripos($keyCookie, "marsd_") !== false) {
				$settingValue = str_replace("marsd_", "", $keyCookie);
				if ($settingValue == "cosmos_color") {
					$settingValue = "site_theme_color";
					$valCookie = "#".$valCookie;
					CMarsHelper::changeSiteColor($valCookie, SITE_ID);
				}
				if ($settingValue == "cosmos_font") {

					$valCookie = str_replace("+", " ", $valCookie);
					//CMarsHelper::changeSiteFont($valCookie, SITE_ID);
				}
				Option::set(BITCORP_MODULE_NAME, $settingValue, $valCookie, SITE_ID);
			}
		}
		global $APPLICATION;
		LocalRedirect($APPLICATION->GetCurPageParam("", array("marsd_save_settings")));
		return true;
	}

	static function getColInfo($num)
	{
		if ($num == 6) {
		    $colInfo["CLASS"] = 'col_one_sixth';
		    $colInfo["NUM"] = 6;
		}
		if ($num == 5) {
		    $colInfo["CLASS"] = 'col_one_fifth';
		    $colInfo["NUM"] = 5;
		}
		if ($num == 4) {
		    $colInfo["CLASS"] = 'col_one_fourth';
		    $colInfo["NUM"] = 4;
		}
		if ($num == 3) {
		    $colInfo["CLASS"] = 'col_one_third';
		    $colInfo["NUM"] = 3;
		}
		if ($num == 2) {
		    $colInfo["CLASS"] = 'col_half';
		    $colInfo["NUM"] = 2;
		}
		if ($num == 1) {
		    $colInfo["CLASS"] = 'col_full';
		    $colInfo["NUM"] = 1;
		}
		return $colInfo;
	}

	static function getDoublePicturesForItem(&$item, $propertyCode, $resizeData = false)
    {
		//$resizeData = width, height, resize_type
		$result = array(
			'PICT' => false,
			'SECOND_PICT' => false
		);
		$result = CIBlockPriceTools::getDoublePicturesForItem($item, $propertyCode);


		if (is_array($resizeData)) {
		    foreach ($result as $keyPicture => $picture) {
		        $pictureId = intval($picture['ID']);
		        if ($pictureId == 0) return;
		        $arFileTmp = CFile::ResizeImageGet(
		            $pictureId,
		            $resizeData,
		            $resizeData["resize_type"],
		            true, flase, false, 100
		        );
		        $result[$keyPicture]["ORIGINAL_SRC"] = $picture['SRC'];
		        $result[$keyPicture]["SRC"] = $arFileTmp["src"];
		        $result[$keyPicture]["WIDTH"] = $arFileTmp["width"];
		        $result[$keyPicture]["HEIGHT"] = $arFileTmp["height"];
				/*
		        $arFileTmpThumb = CFile::ResizeImageGet(
		            $pictureId,
		            $resizeDataThumb,
		            $resizeDataThumb["resize_type"],
		            true, flase, false, 100
		        );
		        $productSlider[$keyPicture]["THUMB_SRC"] = $arFileTmpThumb['src'];
		        */
		    }
		}
		return $result;
	}

    static function createLeadAMOCRM($data){

    }

    static function getMdColInfo($itemsCnt){
     	if($itemsCnt > 3){
     		return 3;
     	} elseif($itemsCnt > 2){
     		return 4;
     	} elseif($itemsCnt > 1){
     		return 6;
     	} else{
     		return 12;
     	}
    }

    static function getSmColInfo($itemsCnt){
     	if($itemsCnt > 2){
     		return 6;
     	} else{
     		return 12;
     	}     	
    }

}