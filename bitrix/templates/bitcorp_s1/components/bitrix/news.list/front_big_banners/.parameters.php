<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arAnimList = array(
	"" => "�� ���������",
	"bounce" => "bounce",
	"flash" => "flash",
	"pulse" => "pulse",
	"rubberBand" => "rubberBand",
	"shake" => "shake",
	"headShake" => "headShake",
	"swing" => "swing",
	"tada" => "tada",
	"wobble" => "wobble",
	"jello" => "jello",
	"bounceIn" => "bounceIn",
	"bounceInDown" => "bounceInDown",
	"bounceInLeft" => "bounceInLeft",
	"bounceInRight" => "bounceInRight",
	"bounceInUp" => "bounceInUp",
	"bounceOut" => "bounceOut",
	"bounceOutDown" => "bounceOutDown",
	"bounceOutLeft" => "bounceOutLeft",
	"bounceOutRight" => "bounceOutRight",
	"bounceOutUp" => "bounceOutUp",
	"fadeIn" => "fadeIn",
	"fadeInDown" => "fadeInDown",
	"fadeInDownBig" => "fadeInDownBig",
	"fadeInLeft" => "fadeInLeft",
	"fadeInLeftBig" => "fadeInLeftBig",
	"fadeInRight" => "fadeInRight",
	"fadeInRightBig" => "fadeInRightBig",
	"fadeInUp" => "fadeInUp",
	"fadeInUpBig" => "fadeInUpBig",
	"fadeOut" => "fadeOut",
	"fadeOutDown" => "fadeOutDown",
	"fadeOutDownBig" => "fadeOutDownBig",
	"fadeOutLeft" => "fadeOutLeft",
	"fadeOutLeftBig" => "fadeOutLeftBig",
	"fadeOutRight" => "fadeOutRight",
	"fadeOutRightBig" => "fadeOutRightBig",
	"fadeOutUp" => "fadeOutUp",
	"fadeOutUpBig" => "fadeOutUpBig",
	"flipInX" => "flipInX",
	"flipInY" => "flipInY",
	"flipOutX" => "flipOutX",
	"flipOutY" => "flipOutY",
	"lightSpeedIn" => "lightSpeedIn",
	"lightSpeedOut" => "lightSpeedOut",
	"rotateIn" => "rotateIn",
	"rotateInDownLeft" => "rotateInDownLeft",
	"rotateInDownRight" => "rotateInDownRight",
	"rotateInUpLeft" => "rotateInUpLeft",
	"rotateInUpRight" => "rotateInUpRight",
	"rotateOut" => "rotateOut",
	"rotateOutDownLeft" => "rotateOutDownLeft",
	"rotateOutDownRight" => "rotateOutDownRight",
	"rotateOutUpLeft" => "rotateOutUpLeft",
	"rotateOutUpRight" => "rotateOutUpRight",
	"hinge" => "hinge",
	"jackInTheBox" => "jackInTheBox",
	"rollIn" => "rollIn",
	"rollOut" => "rollOut",
	"zoomIn" => "zoomIn",
	"zoomInDown" => "zoomInDown",
	"zoomInLeft" => "zoomInLeft",
	"zoomInRight" => "zoomInRight",
	"zoomInUp" => "zoomInUp",
	"zoomOut" => "zoomOut",
	"zoomOutDown" => "zoomOutDown",
	"zoomOutLeft" => "zoomOutLeft",
	"zoomOutRight" => "zoomOutRight",
	"zoomOutUp" => "zoomOutUp",
	"slideInDown" => "slideInDown",
	"slideInLeft" => "slideInLeft",
	"slideInRight" => "slideInRight",
	"slideInUp" => "slideInUp",
	"slideOutDown" => "slideOutDown",
	"slideOutLeft" => "slideOutLeft",
	"slideOutRight" => "slideOutRight",
	"slideOutUp" => "slideOutUp",
	);

$arTemplateParameters = array(
	"AUTOPLAY_TIME" => array(
		"NAME"    => GetMessage("MD_OC_AUTOPLAY_TEXT"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MD_OC_AUTOPLAY_DEFAULT"),
	),
	"AUTOPLAY" => Array(
		"NAME" => GetMessage("MD_OC_AUTOPLAY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"ANIMATION_IN" => Array(
		"NAME" => GetMessage("MD_OC_ANIMATION_IN"),
		"TYPE" => "LIST",
		"DEFAULT" => "",
		"VALUES" => $arAnimList,
	),
	"ANIMATION_OUT" => Array(
		"NAME" => GetMessage("MD_OC_ANIMATION_OUT"),
		"TYPE" => "LIST",
		"DEFAULT" => "",
		"VALUES" => $arAnimList,
	),
	"ACTIVE_DATE_FORMAT" => Array(
		"HIDDEN" => 'Y',
	),
	"HIDE_LINK_WHEN_NO_DETAIL" => Array(
		"HIDDEN" => 'Y',
	),
	"ADD_SECTIONS_CHAIN" => Array(
		"HIDDEN" => 'Y',
	),
	"INCLUDE_IBLOCK_INTO_CHAIN" => Array(
		"HIDDEN" => 'Y',
	),
	"PREVIEW_TRUNCATE_LEN" => Array(
		"HIDDEN" => 'Y',
	),
	"SET_TITLE" => Array(
		"HIDDEN" => 'Y',
	),
	"SET_BROWSER_TITLE" => Array(
		"HIDDEN" => 'Y',
	),
	"STRICT_SECTION_CHECK" => Array(
		"HIDDEN" => 'Y',
	),
	"INCLUDE_SUBSECTIONS" => Array(
		"HIDDEN" => 'Y',
	),
	"SET_LAST_MODIFIED" => Array(
		"HIDDEN" => 'Y',
	),
	"SET_META_DESCRIPTION" => Array(
		"HIDDEN" => 'Y',
	),
	"SET_META_KEYWORDS" => Array(
		"HIDDEN" => 'Y',
	),
		
);
?>
