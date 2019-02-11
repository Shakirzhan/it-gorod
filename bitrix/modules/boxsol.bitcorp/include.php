<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL ^E_NOTICE);

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

//initialize module parametrs list and default values
include_once __DIR__."/parametrs.php";

if(!defined("BITCORP_MODULE_NAME"))
{
    define("BITCORP_MODULE_NAME", "boxsol.bitcorp");
}

\Bitrix\Main\Loader::registerAutoLoadClasses(
    BITCORP_MODULE_NAME,
    array(
        "CMarsHelper" => "classes/general/mdhelper.php",
        "\Marsd\Bitcorp\Compiler" => "lib/compiler.php", 
        "\Marsd\Bitcorp\SCSSCompiler" => "lib/scsscompiler.php",           
    )
);

class CBitcorp
{
    const MODULE_ID = 'boxsol.bitcorp';
    const PARTNER_NAME = 'boxsol';
    const SOLUTION_NAME = 'bitcorp';

    private static $_instance = null;
    private static $_isDemoMode = false;
    protected $_siteId = SITE_ID;
    static $arParametrsList = array();


    private function __construct()
    {
           
    }

    static public function initParametrs($arParams){
        self::$arParametrsList = $arParams;
    }

    static public function getParametrs(){
        return self::$arParametrsList;
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == SOLUTION_NAME) {
            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "HREF" => "/bitrix/admin/wizard_install.php?lang=" . LANGUAGE_ID . "&wizardName=".self::PARTNER_NAME.":".self::SOLUTION_NAME."&wizardSiteID=" . SITE_ID . "&" . bitrix_sessid_get(),
                "ID" => "bitcorp_wizard",
                "ICON" => "bx-panel-site-wizard-icon",
                "MAIN_SORT" => 2500,
                "fileTYPE" => "BIG",
                "SORT" => 10,
                "ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
                "TEXT" => GetMessage("SCOM_BUTTON_NAME"),
                "MENU" => array(),
            ));
        }
    }

    static public function setDemoMode(){
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);            
        if ($moduleStatus == 3) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        } elseif ($moduleStatus == 0){
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_NOT_INSTALLED"));            
        }

        if (self::$_isDemoMode) return;

        self::$_isDemoMode = true;
    }

    static public function isDemoMode(){
        return self::$_isDemoMode;
    }    

    function checkDemo()
    {
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if($moduleStatus == 1 || $moduleStatus == 2){
            return false;
        }
        else{
            return true;
        }
    }

    static public function init()
    {        
        
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);            
        if ($moduleStatus == 3) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        } elseif ($moduleStatus == 0){
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_NOT_INSTALLED"));            
        }        
       
        self::initCSS();
        self::SetJSOptions();    
        self::initJS(); 
                       

        if (isset($_GET['utm_source']) && !isset($_COOKIE['utm_source']))
        {
            foreach ($_GET as $key => $val)
            {
                if (0 === strpos($key, 'utm'))
                {
                    setcookie(htmlspecialcharsbx(strtolower($key)), htmlspecialcharsbx(strtolower($val)), time() + 60 * 60 * 24 * 30, "/");
                }
            }
        }         

    }    

    
    /**
     *  Инициализация стилей для работы шаблона
     */
    static public  function initCSS()
    {
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if ($moduleStatus == (3+5-4-1)) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        }         

        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/owlcarousel/owl.carousel.min.css");
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/fontawesome/font-awesome.min.css");
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/jquery-ui-custom/jquery-ui.min.css");
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/spectrum/spectrum.css");
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/animate.css");
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/jquery.fancybox.min.css");
        //\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/main.css");            
    }

    static public  function setColorCSS()
    {
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if ($moduleStatus == (3+5-4-1)) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        } 

        //compile scss files to css               
        $compiler = \Marsd\Bitcorp\Compiler::getInstance();
        $compiler->compileScss(); 
        $compiler->addCss(); 
        \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/custom.css");              
    }

    /**
     *  Инициализация скриптов для работы шаблона
     */
    static public function initJS()
    {
       
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if ($moduleStatus == (1 + 2 + 5 - 4 -1)) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        }
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/float-labels/fluid-labels.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/owlcarousel/owl.carousel.min.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jqModal/jqModal_bitcorp.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.fancybox.min.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/spectrum/spectrum.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.cookie.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.smooth-scroll.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.inputmask.bundle.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.validate.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery-ui-custom/jquery-ui.min.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/Sticky/jquery.sticky.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/jquery.matchHeight-min.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/main.js");
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/custom.js");

            
    }

    function SetJSOptions()
    {
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);            
        if ($moduleStatus == 3) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        } elseif ($moduleStatus == 0){
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_NOT_INSTALLED"));            
        }

        $arFrontParametrs = self::GetFrontParametrsValues(SITE_ID);
        $arFrontParametrs["SITE_DIR"] =  SITE_DIR;
        $arFrontParametrs["SITE_ID"] =  SITE_ID; 
        $arFrontParametrs["SITE_TEMPLATE_PATH"] =  SITE_TEMPLATE_PATH; 
        ?>
        <script type='text/javascript'>
            var arJsFrontParametrs = {};
            arJsFrontParametrs = <?=CUtil::PhpToJSObject($arFrontParametrs, false)?>;
        </script>
        <?
    }

    public function getSiteId()
    {
        return self::$_siteId;
    }

    function GetBackParametrsValues($SITE_ID, $bStatic = false)
    {
        global $USER;
        if($bStatic){
            static $arValues;            
        }
        
        if($bStatic && $arValues === NULL || !$bStatic){
            $arDefaultValues = $arValues = array();
            if(self::$arParametrsList && is_array(self::$arParametrsList)){
                foreach(self::$arParametrsList as $blockCode => $arBlock){
                    if($arBlock["OPTIONS"] && is_array($arBlock["OPTIONS"])){
                        foreach($arBlock["OPTIONS"] as $optionCode => $arOption){
                            $arDefaultValues[$optionCode] = $arOption["DEFAULT"];
                        }
                    }
                }
            }
            if(self::isDemoMode() && !$USER->IsAdmin()){
                //try to get OPTIONS from session for demoMode only 
                if(isset($_SESSION["BITCORP_OPTIONS"][$SITE_ID])){
                    $arValues = unserialize($_SESSION["BITCORP_OPTIONS"][$SITE_ID]);      
                }  else{
                    $arValues = unserialize(COption::GetOptionString(self::MODULE_ID, "OPTIONS", serialize(array()), $SITE_ID));    
                }                  
            } else{                
                $arValues = unserialize(COption::GetOptionString(self::MODULE_ID, "OPTIONS", serialize(array()), $SITE_ID));
            }

            if($arValues && is_array($arValues)){
                foreach($arValues as $optionCode => $arOption){
                    if(!isset($arDefaultValues[$optionCode])){
                        unset($arValues[$optionCode]);
                    }
                }
            }
            if($arDefaultValues && is_array($arDefaultValues)){
                foreach($arDefaultValues as $optionCode => $arOption){
                    if(!isset($arValues[$optionCode])){
                        $arValues[$optionCode] = $arOption;
                    }
                }
            }
        }

        return $arValues;
    }

    function GetColorParametrs($SITE_ID){
        if(!strlen($SITE_ID)) $SITE_ID = SITE_ID; 
        $arBackParametrs = self::$arParametrsList; 
        $arSetting = \CBitcorp::GetBackParametrsValues($SITE_ID, false);
        $selectedColor = $arSetting["COLOR_SCHEME"];

        return $arBackParametrs["MAIN"]["OPTIONS"]["COLOR_SCHEME"]["LIST"][$selectedColor];  
    }

    function GetFrontParametrsValues($SITE_ID)
    {
        if(!strlen($SITE_ID)) $SITE_ID = SITE_ID;
        $arBackParametrs = self::GetBackParametrsValues($SITE_ID, false);      
        $arValues = (array)$arBackParametrs;
        return $arValues;
    }

    function CheckColor($strColor){     
        if(strlen($strColor) > 0) {
            $strColor = str_replace("#", "", $strColor);
            if(strlen($strColor) < 3) {
                for($i = 0, $l = 6 - strlen($strColor); $i < $l; ++$i) {
                    $strColor = $strColor."0";
                }                   
            }
        } else {
            $strColor = "fde037";
        }
        $strColor = "#".$strColor;
        return $strColor;
    }

    static function ShowLogoImg(){        
        global $arSite;
        $arFrontParametrs = self::GetFrontParametrsValues(SITE_ID);             

        if(strlen($arFrontParametrs["LOGO_IMAGE"])){
            return '<img class="'.($arFrontParametrs["LOGO_WITHBG"] == 'Y' ? 'logo--img' : '').'" src="'.CFile::GetPath($arFrontParametrs["LOGO_IMAGE"]).'" alt="'.$arSite["SITE_NAME"].'" title="'.$arSite["SITE_NAME"].'" />';   
        } else {
            return '<img class="'.($arFrontParametrs["LOGO_WITHBG"] == 'Y' ? 'logo--img' : '').'" src="'.SITE_TEMPLATE_PATH.'/img/logo.png" alt="'.$arSite["SITE_NAME"].'" title="'.$arSite["SITE_NAME"].'" />'; 
        }
    }

    static function ShowFaviconImg(){        
        $arFrontParametrs = self::GetFrontParametrsValues(SITE_ID);             

        if(strlen($arFrontParametrs['FAVICON_IMAGE'])){
            Asset::getInstance()->addString('<link rel="shortcut icon" href="'.CFile::GetPath($arFrontParametrs['FAVICON_IMAGE']).'" type="image/x-icon" />', true);             
        } else {            
            Asset::getInstance()->addString('<link rel="shortcut icon" href="'.SITE_DIR.'favicon.ico" type="image/x-icon" />', true); 
        }
    }

    static function getFileInfo($fileID)
    {
        $file = CFile::GetFileArray($fileID);       

        $position = strrpos($file['FILE_NAME'], '.');
        $file['FILE_NAME'] = substr($file['FILE_NAME'], $position);

        if(!$file['FILE_SIZE']){            
            $file['FILE_SIZE'] = filesize($_SERVER['DOCUMENT_ROOT'].$file['SRC']);
        }
        $file['FILE_SIZE'] = self::getStrFileSize($file['FILE_SIZE']);

        $fileFormat = explode('.', $file['FILE_NAME']);
        $fileFormat = $fileFormat[1];
        
        if($fileFormat == 'doc' || $fileFormat == 'docx'){
            $fileType = 'doc';
        }
        elseif($fileFormat == 'xls' || $fileFormat == 'xlsx'){
            $fileType = 'xls';
        }
        elseif($fileFormat == 'jpg' || $fileFormat == 'jpeg'){
            $fileType = 'jpg';
        }
        elseif($fileFormat == 'png'){
            $fileType = 'png';
        }
        elseif($fileFormat == 'ppt'){
            $fileType = 'ppt';
        }
        elseif($fileFormat == 'tif'){
            $fileType = 'tif';
        }
        elseif($fileFormat == 'txt'){
            $fileType = 'txt';
        }
        else{
            $fileType = 'pdf';
        }
        return $arr = array('FILE_TYPE' => $fileType, 'FILE_SIZE' => $file['FILE_SIZE'], 'SRC' => $file['SRC'], 'DESCRIPTION' => $file['DESCRIPTION'], 'ORIGINAL_NAME' => $file['ORIGINAL_NAME']);
    }

    static function getStrFileSize($filesize){
        $formats = array(GetMessage('MD_NAME_b'), GetMessage('MD_NAME_KB'), GetMessage('MD_NAME_MB'), GetMessage('MD_NAME_GB'), GetMessage('MD_NAME_TB'));
        $format = 0;
        while($filesize > 1024 && count($formats) != ++$format){
            $filesize = round($filesize / 1024, 1);
        }
        $formats[] = GetMessage('MD_NAME_TB');
        return $filesize.' '.$formats[$format];
    }

    static function GetCurrentElementFilter(&$arVariables, &$arParams){
        
        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if ($moduleStatus == (3+5-4-1)) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        }

        $arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'INCLUDE_SUBSECTIONS' => 'Y');
        if($arParams['CHECK_DATES'] == 'Y'){
            $arFilter = array_merge($arFilter, array('ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'));
        }
        if($arVariables['ELEMENT_ID']){
            $arFilter['ID'] = $arVariables['ELEMENT_ID'];
        }
        elseif(strlen($arVariables['ELEMENT_CODE'])){
            $arFilter['CODE'] = $arVariables['ELEMENT_CODE'];
        }
        if($arVariables['SECTION_ID']){
            $arFilter['SECTION_ID'] = ($arVariables['SECTION_ID'] ? $arVariables['SECTION_ID'] : false);
        }
        if($arVariables['SECTION_CODE']){
            $arFilter['SECTION_CODE'] = ($arVariables['SECTION_CODE'] ? $arVariables['SECTION_CODE'] : false);
        }
        if(!$arFilter['SECTION_ID'] && !$arFilter['SECTION_CODE']){
            unset($arFilter['SECTION_GLOBAL_ACTIVE']);
        }
        return $arFilter;     
    }

    static function ShowTopBanner($arResult){

        $moduleStatus = CModule::IncludeModuleEx(self::MODULE_ID);
        if ($moduleStatus == (3+5-4-1)) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        }
       
        $bShowServiceOrder = ($arResult['PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'Y');
        $bannerBgSrc = ((isset($arResult['PROPERTIES']['BANNER_BG']) && $arResult['PROPERTIES']['BANNER_BG']['VALUE']) ? CFile::GetPath($arResult['PROPERTIES']['BANNER_BG']['VALUE']) : '');
        $bShowBg = (isset($arResult['PROPERTIES']['BANNER_IMAGE']) && $arResult['PROPERTIES']['BANNER_IMAGE']['VALUE']);
        $bannerSrc = CFile::GetPath($arResult['PROPERTIES']['BANNER_IMAGE']['VALUE']);  
        $bannerTitle = ($arResult['IPROPERTY_VALUES'] && strlen($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['~NAME']);
        $bBannerText = (isset($arResult['PROPERTIES']['BANNER_TEXT']) && isset($arResult['PROPERTIES']['BANNER_TEXT']['VALUE']['TEXT']));
        $textColor = ((isset($arResult['PROPERTIES']['BANNER_TEXT_COLOR']) && $arResult['PROPERTIES']['BANNER_TEXT_COLOR']['VALUE_XML_ID'] == "WHITE") ? 'page-head_inverse' : ''); 
        $bannerButtonText = (isset($arResult['PROPERTIES']['BANNER_BUTTON_TEXT']) && $arResult['PROPERTIES']['BANNER_BUTTON_TEXT']['VALUE'] ? $arResult['PROPERTIES']['BANNER_BUTTON_TEXT']['VALUE'] : Loc::getMessage("BITCORP_ORDER_SERVICE"));
        $bannerBgColor = (isset($arResult['PROPERTIES']['BANNER_BG_COLOR']) && $arResult['PROPERTIES']['BANNER_BG_COLOR']['VALUE']) ? $arResult['PROPERTIES']['BANNER_BG_COLOR']['VALUE'] : '';
               
        ?>
        
        <div class="page-head <?=$textColor?> <?= $bannerSrc ? '' : 'page-head_noimage'?>" style="background-image: url(<?=$bannerBgSrc?>); background-color: <?=$bannerBgColor?>">
            <div class="container">
                <div class="row page-head-row">
                    <div class="<?= $bShowBg ? 'col-xs-12 col-sm-6 col-md-7' : 'col-xs-12 col-sm-12 col-md-6'?>">
                        <h1 <?= (!$bBannerText && !$bShowServiceOrder && !$bShowBg) ? 'class="only-banner-title"' : ''?>><?=$bannerTitle?></h1>
                        <?if($bBannerText || $bShowServiceOrder):?>
                            <div class="page-head--text">

                                <?if($bBannerText):?>
                                    <span <?=$textColor?>>
                                        <?=$arResult['PROPERTIES']['BANNER_TEXT']['~VALUE']['TEXT']?>
                                    </span>
                                <?endif;?>

                                <?if($bShowServiceOrder && $bannerButtonText):?>
                                    <div class="mt40"><a class="btn btn-default order-button-anchor" href="#service-order-form"><?=$bannerButtonText?></a></div>
                                <?endif;?>

                            </div>
                        <?endif?> 
                    </div>
                    <?if($bShowBg):?>
                        <div class="hide-xs col-sm-6 col-md-5 page-head--image page-head--image_bottom">
                            <img class="img-responsiver" src="<?=$bannerSrc?>"/>
                        </div>
                    <?endif;?>
                </div>
            </div>

            <?if($arResult['INFO_PROPS']):?>
                <div class="proporties-line">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <?foreach ($arResult['INFO_PROPS'] as $code => $arProp):?>
                                    <span>
                                        <?=$arProp['NAME']?>:
                                        <?if(is_array($arProp['DISPLAY_VALUE'])):?>
                                            <?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
                                                <?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
                                                    <?=$value.'&nbsp;/ '?>
                                                <?else:?>
                                                    <?=$value?>
                                                <?endif;?>
                                            <?endforeach;?>
                                        <?else:?>
                                            <?=$arProp['DISPLAY_VALUE']?>
                                        <?endif;?> 
                                    </span>                             
                                <?endforeach;?>
                            </div>                          
                        </div>
                    </div>
                </div>
            <?endif;?>

        </div>
    <?}

}
?>