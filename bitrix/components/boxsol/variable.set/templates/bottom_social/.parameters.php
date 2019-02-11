<?php
$set = array(
	"LINK_VK" => GetMessage('MD_VS_LINK_VK'),
	"LINK_FB" => GetMessage('MD_VS_LINK_FB'),
	"LINK_TWITTER" => GetMessage('MD_VS_LINK_TWITTER'),
	"LINK_INSTAGRAM" => GetMessage('MD_VS_LINK_INSTAGRAM'),
	"LINK_YOUTUBE" => GetMessage('MD_VS_LINK_YOUTUBE'),
	"LINK_ODNIKLASSNIKI" => GetMessage('MD_VS_LINK_ODNIKLASSNIKI'),
	"LINK_GOOGLEPLUS" => GetMessage('MD_VS_LINK_GOOGLEPLUS'),
);

$arTemplateParameters = array();
foreach ($set as $k => $val) {
	$arTemplateParameters[$k] = array(
		"NAME" => $val,
		"COLS" => 20,
		"ROWS" => 1
	);
}