<?php
$module = 'sva.tinypng';

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module.'/include.php');
IncludeModuleLangFile(__FILE__);

$arSites = array();
$rsSites = CSite::GetList($by1='sort', $order1='desc');
while ($arSite = $rsSites->GetNext()) {
	$arSites[$arSite['ID']] = '['.$arSite['ID'].'] '.$arSite['NAME'];
}

CModule::IncludeModule($module);
function getNiceFileSize($fileSize, $digits = 2){

	$sizes = array("TB", "GB", "MB", "KB", "B");
	$total = count($sizes);
	while ($total-- && $fileSize > 1024) {
		$fileSize /= 1024;
	}
	return round($fileSize, $digits) . " " . $sizes[$total];
}

$key = \COption::GetOptionString($module, "tiny_png_api_key");

if($_REQUEST["compress"]){

	if(intval($_REQUEST["compress"])){

		$intFileID = intval($_REQUEST["compress"]);

		\Sva\TinyPng\TinyPNGClient::CompressImageByID($intFileID);
	}

} elseif($_REQUEST["action"] == "compress"){

	$arIDs = $_REQUEST["ID"];

	if(is_array($arIDs) && count($arIDs)){

		set_time_limit(0);

		\Sva\TinyPng\Tinify\Tinify::setKey($key);

		foreach($arIDs as $intFileID){

			\Sva\TinyPng\TinyPNGClient::CompressImageByID($intFileID);
		}
	}
}


/********************************FILTER****************************************/
$list = new CSVAAdminList($module);
$list->generalKey = 'ID';
$list->SetRights();
$list->SetTitle(GetMessage('SVA_TINYPNG_TITLE'));
$list->SetGroupAction(array(
	'compress' => function($hash) {},
));
$list->SetContextMenu(false);
$list->SetHeaders(array(
	'ID' => "ID",
	'MODULE_ID' => GetMessage('SVA_TINYPNG_MODULE_ID'),
	'CONTENT_TYPE' => GetMessage("SVA_TINYPNG_CONTENT_TYPE"),
	'FILE_NAME' => GetMessage("SVA_TINYPNG_FILE_NAME"),
	'ORIGINAL_NAME' => GetMessage("SVA_TINYPNG_ORIGINAL_NAME"),
	'DESCRIPTION' => GetMessage("SVA_TINYPNG_DESCRIPTION"),
	'FILE_SIZE' => GetMessage("SVA_TINYPNG_FILE_SIZE"),
	'IMAGE' => GetMessage("SVA_TINYPNG_IMAGE"),
	'COMPRESS' => GetMessage("SVA_TINYPNG_COMPRESS"),
	'SIZE_BEFORE' => GetMessage("SVA_TINYPNG_SIZE_BEFORE"),
	'SIZE_AFTER' => GetMessage("SVA_TINYPNG_SIZE_AFTER"),
));
$list->SetFilter(array(
	'id' => array('TITLE' => GetMessage('SVA_TINYPNG_FILTER_ID'), 'OPER' => ''),
	'file_size' => array(
		'TITLE' => GetMessage('SVA_TINYPNG_FILTER_FILE_SIZE'),
	),
	'comressed' => array(
		'TITLE' => GetMessage('SVA_TINYPNG_FILTER_COMRESSED'),
		'TYPE' => 'select',
		'VARIANTS' => Array(
			"Y" => GetMessage('SVA_TINYPNG_FILTER_COMRESSED_Y'),
			"N" => GetMessage('SVA_TINYPNG_FILTER_COMRESSED_N'),
		)
	),
	'module_id' => array('TITLE' => GetMessage('SVA_TINYPNG_FILTER_MODULE_ID')),
	'original_name' => array('TITLE' => GetMessage('SVA_TINYPNG_FILTER_ORIGINAL_NAME'), 'OPER' => '?'),
	'file_name' => array('TITLE' => GetMessage('SVA_TINYPNG_FILTER_FILE_NAME'), 'OPER' => '?'),
	'content_type' => array(
		'TITLE' => GetMessage('SVA_TINYPNG_FILTER_FILE_TYPE'),
		'TYPE' => 'select',
		'OPER' => '@',
		'VARIANTS' => array(
			'image/png' => GetMessage('SVA_TINYPNG_MIME_PNG'),
			'image/jpeg' => GetMessage('SVA_TINYPNG_MIME_JPG')
		)
	),
));
if (!isset($by))
	$by = 'ID';
if (!isset($order))
	$order = 'ASC';

$rsFiles = Sva\TinyPng\TinyPNGClient::GetFileList(Array($by => $order), $list->MakeFilter());

$list->SetList(
	$rsFiles,
	array(
		'IMAGE' => function($val, $arRec){
			$strFilePath = CFile::GetPath($arRec["ID"]);
			return "<img style='max-width: 200px; height: auto;' src='" . $strFilePath . "'>";
		},
		'COMPRESS' => function($val, $arRec){

			if(intval($arRec['FILE_ID']) <= 0){
				return "<button value='".$arRec["ID"]."' name='compress' data-image-id='"  . $arRec["ID"] . "' href='#'>" . GetMessage("SVA_TINYPNG_COMPRESS") . "</button>";
			} else {
				return GetMessage('SVA_TINYPNG_COMRESSED');
			}

		},
		'SIZE_BEFORE' => function($val, $arRec){
			return getNiceFileSize($arRec["SIZE_BEFORE"]);
		},
		'SIZE_AFTER' => function($val, $arRec){
			return getNiceFileSize($arRec["SIZE_AFTER"]);
		},
		'FILE_SIZE' => function($val, $arRec){
			return getNiceFileSize($arRec["FILE_SIZE"]);
		}
	),
	false
);
$list->SetFooter(array(
	'compress' => GetMessage('SVA_TINYPNG_COMPRESS'),
));
$list->Output();