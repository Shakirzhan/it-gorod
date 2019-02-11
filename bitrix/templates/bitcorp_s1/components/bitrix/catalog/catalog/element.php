<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);
?>
<div class="row row-in">
	<div class="col-md-3 col-sm-3 hide-sm hide-md">
		<aside class="sidebar">
			<?$APPLICATION->IncludeComponent(
				"bitrix:menu",
				"left",
				array(
					"ROOT_MENU_TYPE" => "left",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "3600000",
					"MENU_CACHE_USE_GROUPS" => "N",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "3",
					"CHILD_MENU_TYPE" => "left",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "Y",
					"COMPONENT_TEMPLATE" => "left"
				),
				false
			);?>
		</aside>
	</div>
	<div class="col-md-9">
		<?$ElementID = $APPLICATION->IncludeComponent(
			"bitrix:catalog.element",
			"",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
				"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
				"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ''),
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
				"MESSAGE_404" => $arParams["MESSAGE_404"],
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"SHOW_404" => $arParams["SHOW_404"],
				"FILE_404" => $arParams["FILE_404"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
				"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
				"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
				"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
				"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
				"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

				"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
				"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
				'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['CURRENCY_ID'],
				'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
				'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
				'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],

				'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
				'LABEL_PROP' => $arParams['LABEL_PROP'],
				'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
				'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
				'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
				'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
				'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
				'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
				'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
				'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
				'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
				'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
				'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
				'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
				'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
				'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
				'BLOG_EMAIL_NOTIFY' => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),
				'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
				'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
				'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
				'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
				'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
				'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
				'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
				'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
				'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
				"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
				"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
				"DISPLAY_PREVIEW_TEXT_MODE" => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
				"DETAIL_PICTURE_MODE" => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
				'ADD_TO_BASKET_ACTION' => $basketAction,
				'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
				'DISPLAY_COMPARE' => (isset($arParams['USE_COMPARE']) ? $arParams['USE_COMPARE'] : ''),
				'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
				'SHOW_BASIS_PRICE' => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),
				'BACKGROUND_IMAGE' => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
				'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
				'SET_VIEWED_IN_COMPONENT' => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),

				"USE_GIFTS_DETAIL" => $arParams['USE_GIFTS_DETAIL']?: 'Y',
				"USE_GIFTS_MAIN_PR_SECTION_LIST" => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST']?: 'Y',
				"GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
				"GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
				"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
				"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
				"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
				"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
				"GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
				"GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
				"GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

				"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
				"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

				"MESS_BTN_ORDER" => $arParams["MESS_BTN_ORDER"],
				"MESS_BTN_QUESTION" => $arParams["MESS_BTN_QUESTION"],
				"MESS_TAB_DESCRIPTION" => $arParams["MESS_TAB_DESCRIPTION"],
				"MESS_TAB_PROPS" => $arParams["MESS_TAB_PROPS"],
				"MESS_TAB_FILES" => $arParams["MESS_TAB_FILES"],
			),
			$component
		);?>

		<?	
			//Link elements
			$arLinkElements = array();
			$linkElementsCacheID = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ELEMENT_ID" => $ElementID);
			$obCache = new CPHPCache();
			if($obCache->InitCache($arParams["CACHE_TIME"], serialize($linkElementsCacheID), "/".SITE_ID."/catalog/element/detail")) {
				$arLinkElements = $obCache->GetVars();	
			} elseif($obCache->StartDataCache()) {

				$arItemFilter = CBitcorp::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);
				$arSelect = array("ID","IBLOCK_ID","NAME");
				$arSort = array("SORT"=>"ASC");

				$rsItems = CIBlockElement::GetList($arSort, $arItemFilter, $arSelect);
				while ($arItem = $rsItems->GetNextElement()){			
					$arLinkElements = $arItem->GetProperties();	
					$arLinkElements["ELEMENT"] = $arItem->GetFields();					
				}

				$obCache->EndDataCache($arLinkElements);
			}	
		?>
		

		<?if(in_array('LINK_GOODS', $arParams['DETAIL_PROPERTY_CODE']) && $arLinkElements['LINK_GOODS']['VALUE'] && $arLinkElements['LINK_GOODS']['LINK_IBLOCK_ID']):?>
			<?global $arCatalogDetailFilter; $arCatalogDetailFilter = array('ID' => $arLinkElements['LINK_GOODS']['VALUE']);?>
				<div class="row">
					<div class="col-md-8 mt30">
						<div class="h2"><?= strlen($arParams["MESS_OTHER_ITEMS"]) ? $arParams["MESS_OTHER_ITEMS"] : GetMessage('CATALOG_ELEMENT_MESS_OTHER_ITEMS');?></div>
					</div>
				</div>
				<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"inner_catalog", 
				array(
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"ADD_SECTIONS_CHAIN" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"DISPLAY_DATE" => "N",
					"DISPLAY_NAME" => "Y",
					"DISPLAY_PICTURE" => "Y",
					"DISPLAY_PREVIEW_TEXT" => "Y",
					"DISPLAY_TOP_PAGER" => "N",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "",
					),
					"FILTER_NAME" => "arCatalogDetailFilter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
					"IBLOCK_ID" => $arLinkElements['LINK_GOODS']['LINK_IBLOCK_ID'],					
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "Y",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "99",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => ".default",
					"PAGER_TITLE" => "Товары на главной",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"PREVIEW_TRUNCATE_LEN" => "",
					"PROPERTY_CODE" => array(
						0 => "PRICE",
						1 => "OLD_PRICE",
						2 => "CURRENCY",
						3 => "SHOW_FRONT_PAGE",
						4 => "HIT",
						5 => "STATUS",
						6 => "",
					),
					"SET_BROWSER_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_STATUS_404" => "N",
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SHOW_ALL_TITLE" => "Все товары",
					"SHOW_ALL_TITLE_BLOCK" => "Y",
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_BY2" => "SORT",
					"SORT_ORDER1" => "DESC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N",
					"COMPONENT_TEMPLATE" => "inner_catalog",
					"USE_SEARCH" => "N",
					"USE_RSS" => "N",
					"USE_RATING" => "N",
					"USE_CATEGORIES" => "N",
					"USE_FILTER" => "N",
					"ADD_ELEMENT_CHAIN" => "N",
					"USE_PERMISSIONS" => "N",
					"USE_SHARE" => "N",
					"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
					"LIST_FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "",
					),
					"LIST_PROPERTY_CODE" => array(
						0 => "SHOW_FRONT_PAGE",
						1 => "SERVICE_TYPE",
						2 => "ICON_NAME",
						3 => "",
					),
					"META_KEYWORDS" => "-",
					"META_DESCRIPTION" => "-",
					"BROWSER_TITLE" => "-",
					"DETAIL_SET_CANONICAL_URL" => "N",
					"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
					"DETAIL_FIELD_CODE" => array(
						0 => "",						
					),
					"DETAIL_PROPERTY_CODE" => array(
						0 => "",						
					),
					"DETAIL_DISPLAY_TOP_PAGER" => "N",
					"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
					"DETAIL_PAGER_TITLE" => "Страница",
					"DETAIL_PAGER_TEMPLATE" => "",
					"DETAIL_PAGER_SHOW_ALL" => "N",
					"CATALOG_BLOCK_TITLE" => "Популярные товары",
					"CATALOG_BLOCK_DESCRIPTION" => ""
				),
				false
			);?>
		<?endif;?>
	</div>
</div>
