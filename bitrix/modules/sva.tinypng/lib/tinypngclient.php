<?php
/**
 * Created by PhpStorm.
 * User: WebDev
 * Date: 01.10.2016
 * Time: 13:31
 */

namespace Sva\TinyPng;

use Bitrix\Main;

class TinyPNGClient{

    static $module_id = "sva.tinypng";

    function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt){

        $boolCompress = \COption::GetOptionString(self::$module_id, "tiny_png_compress_all_files");

        if($boolCompress == 'Y') {
            if ((!isset($arFile["MODULE_ID"]) || $arFile["MODULE_ID"] != "iblock")){
                $key = \COption::GetOptionString(self::$module_id, "tiny_png_api_key");
                if (strlen($key) > 0) {
                    if ($arFile["type"] == "image/jpeg" || $arFile["type"] == "image/png") {
                        Tinify\Tinify::setKey($key);
                        $source = Tinify\Source::fromFile($arFile["tmp_name"]);
                        $source->toFile($arFile["tmp_name"]);
                        $arFile["size"] = filesize($arFile["tmp_name"]);
                    }
                }
            }
        }
    }

    function OnAfterIBlockSectionUpdate(&$arFields){

        $boolCompress = \COption::GetOptionString(self::$module_id, "tiny_png_compress_iblock_section");
        if($boolCompress != 'Y')
            return;

        if($arFields["RESULT"]){

            if((is_array($arFields["PICTURE"]) && isset($arFields["PICTURE"]['type'])) && (($arFields["PICTURE"]['type'] == 'image/png' || $arFields["PICTURE"]['type'] == 'image/jpeg'))){

                $rsSection = \CIBlockSection::GetByID($arFields["ID"]);
                $arSection = $rsSection->GetNext();
                self::CompressImageByID($arSection['PICTURE']);

            }

	    if((is_array($arFields["PICTURE"]) && isset($arFields["PICTURE"]['type'])) && (($arFields["PICTURE"]['type'] == 'image/png' || $arFields["PICTURE"]['type'] == 'image/jpeg'))){

                $rsSection = \CIBlockSection::GetByID($arFields["ID"]);
                $arSection = $rsSection->GetNext();
                self::CompressImageByID($arSection['DETAIL_PICTURE']);

            }

        }

    }

    function OnAfterIBlockElementUpdate(&$arFields){

        $boolCompress = \COption::GetOptionString(self::$module_id, "tiny_png_compress_iblock_element");
        if($boolCompress != 'Y')
            return;

        if(isset($arFields["PREVIEW_PICTURE_ID"]) && intval($arFields["PREVIEW_PICTURE_ID"]) > 0){
            self::CompressImageByID($arFields["PREVIEW_PICTURE_ID"]);
        }

        if(isset($arFields["DETAIL_PICTURE_ID"]) && intval($arFields["DETAIL_PICTURE_ID"]) > 0){
            self::CompressImageByID($arFields["DETAIL_PICTURE_ID"]);
        }

        $arEl = false;

        foreach($arFields["PROPERTY_VALUES"] as $key => $values){

            if(!is_array(!$values)) continue;

            foreach($values as $k => $v){

                if(is_array(!$v)) continue;

                if($v['VALUE']['type'] == 'image/png' || $v['VALUE']['type'] == 'image/jpeg'){

                    if(!$arEl){
                        $rsEl = \CIBlockElement::GetByID($arFields["ID"]);
                        if($rsEl){
                            $obEl = $rsEl->GetNextElement();
                            $arEl = $obEl->GetFields();
                            $arEl["PROPERTIES"] = $obEl->GetProperties();
                        }
                    }

                    foreach($arEl["PROPERTIES"] as $strPropCode => $arProp){
                        if($arProp["ID"] == $key){
                            if($arProp["MULTIPLE"]){
                                foreach($arProp["VALUE"] as $intFileID){
                                    self::CompressImageByID($intFileID);
                                }
                            } else {
                                self::CompressImageByID($arProp["VALUE"]);
                            }
                        }
                    }

                }
            }
        }
    }

    public static function CompressImage($strSourcePath, $strDestPath){

        $key = \COption::GetOptionString(self::$module_id, "tiny_png_api_key");

        if(strlen($key) <= 0) return false;

        Tinify\Tinify::setKey($key);

        $source = Tinify\Source::fromFile($strSourcePath);
        return $source->toFile($strDestPath);
        
    }

    public static function CompressImageByID($intFileID){

        global $DB;

        $rsFile  = \CFile::GetByID($intFileID);
        $arFile = $rsFile->GetNext();

		if($arFile["CONTENT_TYPE"] != 'image/jpeg' && $arFile["CONTENT_TYPE"] != 'image/png'){
			return;
		}

        $strFilePath = $_SERVER["DOCUMENT_ROOT"] . \CFile::GetPath($intFileID);

        if(file_exists($strFilePath)){

            $oldSize = filesize($strFilePath);

            if($newSize = \Sva\TinyPng\TinyPNGClient::CompressImage($strFilePath, $strFilePath)){

                $DB->Query("UPDATE b_file SET FILE_SIZE='".$DB->ForSql($newSize, 255)."' WHERE ID=".intval($intFileID));

                $arFields = Array(
                    'FILE_ID' => $intFileID,
                    'SIZE_BEFORE' => $oldSize,
                    'SIZE_AFTER' => $newSize,
                );

                $rs = TinyPngFileTable::getById($intFileID);

                if($rs->getSelectedRowsCount() <= 0){

                    $el =  new TinyPngFileTable();
                    $result = $el->add($arFields);

                } else {

                    $result = TinyPngFileTable::update($intFileID, $arFields);

                }

            } else {
//                throw new Main\ArgumentException("TinyPNG key is not set");
            }
        } else {
//            throw new Main\ArgumentException("File not found");
        }

    }

    public static function GetFileList($arOrder = array(), $arFilter = array()){
        global $DB;
        $arSqlSearch = array();
        $arSqlOrder = array();
        $strSqlSearch = $strSqlOrder = "";

        if(is_array($arFilter))
        {
            foreach($arFilter as $key => $val)
            {
                $key = strtoupper($key);

                $strOperation = '';
                if(substr($key, 0, 1)=="@")
                {
                    $key = substr($key, 1);
                    $strOperation = "IN";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } elseif(substr($val, 0, 1) == ">"){
                    $val = substr($val, 1);
                    $strOperation = ">";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } elseif(substr($val, 0, 1) == "<"){
                    $val = substr($val, 1);
                    $strOperation = "<";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } else {
                    $val = $DB->ForSql($val);
                }

                if($val == '')
                    continue;

                switch($key)
                {
                    case "MODULE_ID":
                    case "ID":
                    case "EXTERNAL_ID":
                    case "SUBDIR":
                    case "FILE_NAME":
                    case "FILE_SIZE":
                    case "ORIGINAL_NAME":
                    case "CONTENT_TYPE":
                        if ($strOperation == "IN")
                            $arSqlSearch[] = "f.".$key." IN (".$val.")";
                        elseif($strOperation == ">")
                            $arSqlSearch[] = "f.".$key." > ".$val."";
                        elseif($strOperation == "<")
                            $arSqlSearch[] = "f.".$key." < ".$val."";
                        else
                            $arSqlSearch[] = "f.".$key." = '".$val."'";
                        break;
                    case "COMRESSED":
                        if($val == "Y")
                            $arSqlSearch[] = "tf.FILE_ID > 0";
                        else
                            $arSqlSearch[] = "tf.FILE_ID is NULL";

                        break;
                }
            }
        }

        if(!empty($arSqlSearch))
            $strSqlSearch = " WHERE (".implode(") AND (", $arSqlSearch).")";

        if(is_array($arOrder))
        {
            static $aCols = array(
                "ID" => 1,
                "TIMESTAMP_X" => 1,
                "MODULE_ID" => 1,
                "HEIGHT" => 1,
                "WIDTH" => 1,
                "FILE_SIZE" => 1,
                "CONTENT_TYPE" => 1,
                "SUBDIR" => 1,
                "FILE_NAME" => 1,
                "ORIGINAL_NAME" => 1,
                "EXTERNAL_ID" => 1,
            );
            foreach($arOrder as $by => $ord)
            {
                $by = strtoupper($by);
                if(array_key_exists($by, $aCols))
                    $arSqlOrder[] = "f.".$by." ".(strtoupper($ord) == "DESC"? "DESC":"ASC");
            }
        }
        if(empty($arSqlOrder))
            $arSqlOrder[] = "f.ID ASC";
        $strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

        $strSql =
            "SELECT f.*, ".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X, tf.* " .
            "FROM b_file f ".
            "LEFT JOIN b_sva_tinypng_files as tf ON f.ID = tf.FILE_ID".
            $strSqlSearch.
            $strSqlOrder;

        $res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

        return $res;
    }

}