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
$this->setFrameMode(true);
?>
<?$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"news_detail",
	Array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" => $arParams["USE_SHARE"],
		"SHARE_HIDE" => $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
		'STRICT_SECTION_CHECK' => (isset($arParams['STRICT_SECTION_CHECK']) ? $arParams['STRICT_SECTION_CHECK'] : ''),
	),
	$component
);?>

<?if($arParams['SHOW_OTHER_NEWS'] == 'Y'):?>
	<?$this->SetViewTarget('footer_other_news');?>
		<?		
		$GLOBALS['noCurElement'] = array('!ID' => $ElementID);
		?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list", 
			"front_news", 
			array(
				"COMPONENT_TEMPLATE" => "front_news",
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"NEWS_COUNT" => "3",
				"SORT_BY1" => 'ACTIVE_FROM',
				"SORT_ORDER1" => $arParams["SORT_ORDER1"],
				"SORT_BY2" => $arParams["SORT_BY2"],
				"SORT_ORDER2" => $arParams["SORT_ORDER2"],
				"FILTER_NAME" => "noCurElement",
				"FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
				"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
				"CHECK_DATES" => $arParams["CHECK_DATES"],
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => 'Y',
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "j M Y",
				"SET_TITLE" => "N",
				"SET_BROWSER_TITLE" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"STRICT_SECTION_CHECK" => "N",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"MESSAGE_404" => "",
				"DISPLAY_DATE" => "Y",
				"DISPLAY_NAME" => "Y",
				"DISPLAY_PICTURE" => "Y",
				"DISPLAY_PREVIEW_TEXT" => "Y",			
				"SHOW_NAME" => "Y",
				"SHOW_ALL_TITLE" => "",
				"NEWS_BLOCK_TITLE" => $arParams['OTHER_ITEMS_TITLE'],
			),
			false
		);?>   
	<?$this->EndViewTarget();?>
<?endif;?>

<div class="row row-in mt50">
	<div class="<?=$arParams['USE_SHARE'] == 'Y' ? 'col-md-6' : 'col-md-12'?>">
		<div class="g-news--link">
			<a href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"]?>">
				<i class="icon-rounded fa fa-angle-left"></i>
				<span class="link_underline"><?=GetMessage("T_NEWS_DETAIL_BACK")?></span>		
			</a>
		</div>
	</div>
	<?if($arParams['USE_SHARE'] == 'Y'):?>
		<div class="col-md-6">
			<div class="ya-share">
				<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
				   "AREA_FILE_SHOW" => "file",
				   "PATH" => SITE_DIR."include/ya-share.php",
				   "EDIT_TEMPLATE" => ""
				   ),
				   false,
				   array('HIDE_ICONS' => 'Y')
				);?>								
			</div>
		</div>
	<?endif;?>
</div>