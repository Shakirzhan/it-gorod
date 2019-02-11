<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Заказать сайт в набережных челнах в  It-Gorod-большое портфолио.");
$APPLICATION->SetPageProperty("description", "Наши проекты интернет магазинов, корпоративных сайтов а также информационных порталов и форумов.");
$APPLICATION->SetTitle("Проекты");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news",
	"projects",
	Array(
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "Y",
		"AJAX_OPTION_JUMP" => "Y",
		"AJAX_OPTION_SHADOW" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPONENT_TEMPLATE" => "projects",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(0=>"NAME",1=>"DETAIL_TEXT",2=>"DETAIL_PICTURE",3=>"DATE_ACTIVE_FROM",4=>"",),
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DETAIL_PAGER_TEMPLATE" => "modern",
		"DETAIL_PAGER_TITLE" => "Новости",
		"DETAIL_PROPERTY_CODE" => array(0=>"SHOW_FRONT_PAGE",1=>"BANNER_SIZE",2=>"FORM_ORDER",3=>"DOCUMENTS_TITLE",4=>"GALLERY_TITLE",5=>"LINK_FAQ",6=>"LINK_REVIEWS",7=>"LINK_STAFF",8=>"LINK_PROJECTS",9=>"BANNER_VISIBLE",10=>"BANNER_BUTTON_TEXT",11=>"BANNER_TEXT_COLOR",12=>"BANNER_BG_COLOR",13=>"INFO_SITE",14=>"INFO_SCOPE",15=>"INFO_DATE",16=>"INFO_CLIENT",17=>"BANNER_TEXT_CODE",18=>"DOCUMENTS",19=>"MORE_PHOTO",20=>"",),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PANEL" => "N",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
		"IBLOCK_ID" => "14",
		"IBLOCK_TYPE" => "marsd_bitcorp_s1",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(0=>"NAME",1=>"PREVIEW_PICTURE",2=>"DATE_ACTIVE_FROM",3=>"",),
		"LIST_PROPERTY_CODE" => array(0=>"SHOW_FRONT_PAGE",1=>"BANNER_SIZE",2=>"",),
		"MESSAGE_404" => "",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"NEWS_COUNT" => "20",
		"NUM_DAYS" => "30",
		"NUM_NEWS" => "3",
		"OTHER_ITEMS_TITLE" => "Другие новости",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "modern",
		"PAGER_TITLE" => "Проекты",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROJECTS_BLOCK_DESCRIPTION" => "Наша компания оказывает полный спектр услуг. У нашей компании прекрасная репутация, и мы уже успели завоевать доверие и расположение своих клиентов. Во многом это связано с широким спектром услуг, предоставляемых нашей компанией, а также высоким качеством обслуживания.",
		"PROJECTS_BLOCK_TITLE" => "Наши проекты",
		"PROJECTS_SHOW_DESCRIPTION" => "Y",
		"SEF_FOLDER" => "/projects/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => array("news"=>"","section"=>"","detail"=>"#ELEMENT_CODE#/",),
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SHOW_OTHER_NEWS" => "Y",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "N",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N",
		"USE_SHARE" => "Y",
		"YANDEX" => "N"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>