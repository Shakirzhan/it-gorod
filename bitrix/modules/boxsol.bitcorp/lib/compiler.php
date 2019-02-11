<?php
namespace Marsd\Bitcorp;

use \Bitrix\Main\SystemException as SystemException;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Page\Asset;
use Bitrix\Main\IO\File;


if(!defined("BITCORP_MODULE_NAME"))
{
    define("BITCORP_MODULE_NAME", "boxsol.bitcorp");
}

class Compiler
{

    /**
     * Class compiler
     */
    private $compiler;
    private static $_instance = null;
    private $_arScssVars = array();
   
    const PATH_TO_FILES = "/scss/";        
    const SCSS_FILE = "main.scss";  
    const SCSS_COMPILE_POSTFIX = "scsscompile";
    const SCSS_CACHE_FOLDER = "/cache/bitcorpCache/scss/";
    const SHOW_ERRORS_IN_DISPLAY = 'Y';

    private function __construct()
    {
        // если публичка доступна, получаем настройки дл€ пользовател€ из сессии
        if ($this->isPublicSettingsAvailable())
        {
            //$this->initSettingsFromSession();
           
        }
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Check Required Modules
     * @throws Exception
     */
    protected function checkModule()
    {
        $moduleStatus = \Bitrix\Main\Loader::includeSharewareModule(BITCORP_MODULE_NAME);            
        if ($moduleStatus == 3) {
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_DEMO_EXPIRED"));
        } elseif ($moduleStatus == 0){
            die(\Bitrix\Main\Localization\Loc::getMessage("BITCORP_NOT_INSTALLED"));            
        }
    }    

    /**
     * @return \Marsd\Bitcorp\Compiler
     * @throws SystemException
     */
    protected function getCompiler()
    {
        if (!class_exists('\Marsd\Bitcorp\SCSSCompiler')) {
            throw new SystemException(sprintf('Class "%s" doesn\'t exist.', '\Marsd\Bitcorp\SCSSCompiler'));
        }

        $parser = new \Marsd\Bitcorp\SCSSCompiler;

        if (!($parser instanceof \Marsd\Bitcorp\Compiler)) {
            throw new SystemException(sprintf('Class "%s" is not a subclass of Marsd\Bitcorp\Compiler', '\Marsd\Bitcorp\SCSSCompiler'));
        }

        return $parser;
    }    

    /**
     * Clear cache composite site
     * @return void
     */
    public function clearAllCHTMLPagesCache()
    {
        \CHTMLPagesCache::CleanAll();
        \CHTMLPagesCache::writeStatistic(0, 0, 0, 0, 0);
    }

    public function initScss(){

        $this->checkModule();  
        $this->_arScssVars['$siteTemplatePath'] = SITE_TEMPLATE_PATH;

        $arSetting = \CBitcorp::GetFrontParametrsValues(SITE_ID);
        $arColorParametrs = \CBitcorp::GetColorParametrs(SITE_ID);      
        
        //check need init cssc color from $_SESSION
        //if ($this->isPublicSettingsAvailable()){
        //    $this->initSettingsFromSession();
        //} else{
            if($arSetting['COLOR_SCHEME'] != 'CUSTOM'){
                $this->_arScssVars['$mainColor']  = $arColorParametrs['COLOR_FIRST'] ? $arColorParametrs['COLOR_FIRST'] : '#0c4da2';
                $this->_arScssVars['$accentColor']  = $arColorParametrs['COLOR_SECOND'] ? $arColorParametrs['COLOR_SECOND'] : '#ed1c24';
            } else {
                $this->_arScssVars['$mainColor']  = ($arSetting['COLOR_SCHEME_CUSTOM_FIRST'] && preg_match('/^#[a-f0-9]{6}$/i', trim($arSetting['COLOR_SCHEME_CUSTOM_FIRST']))) ? trim($arSetting['COLOR_SCHEME_CUSTOM_FIRST']) : '#0c4da2';

                $this->_arScssVars['$accentColor']  = ($arSetting['COLOR_SCHEME_CUSTOM_SECOND'] && preg_match('/^#[a-f0-9]{6}$/i', trim($arSetting['COLOR_SCHEME_CUSTOM_SECOND']))) ? trim($arSetting['COLOR_SCHEME_CUSTOM_SECOND']) : '#ed1c24';              
            }   
        //}

       
        
    }

    
    public function compileScss()
    {
        $this->initScss();          
        try {
            
            $fileName = $this->getCompiledScssFileName();
            $cache_path = Application::getDocumentRoot() . $this->getScssCacheFolder();                       

            if (!File::isFileExists($cache_path . $fileName))
            {
                $styleLess = Application::getDocumentRoot() . SITE_TEMPLATE_PATH  . self::PATH_TO_FILES . self::SCSS_FILE;
                $parser = $this->getCompiler();

                $parser->setVariables($this->_arScssVars);
                $css = $parser->toCss($styleLess);                

                if (!empty($css)) {                    
                    File::putFileContents($cache_path . $fileName, $css);                    
                } 

                if (\CHTMLPagesCache::IsCompositeEnabled()) {
                    $this->clearAllCHTMLPagesCache();
                }             
            }            

        } catch (SystemException $e) {
            if (self::SHOW_ERRORS_IN_DISPLAY == 'Y') {
                ShowError($e->getMessage());
            } else {
                \Bitrix\Main\Diag\Debug::dumpToFile($e->getMessage());                
            }
        }

    }

    public function getCompiledScssFileName()
    {
        return md5(serialize($this->_arScssVars))
            . self::SCSS_COMPILE_POSTFIX
            . '.css';
    }

    public function getSCSSCacheFolder()
    {
        $md5 = $this->getCompiledScssFileName();
        return
            BX_PERSONAL_ROOT
            . self::SCSS_CACHE_FOLDER
            . SITE_ID
            . "/"
            . substr($md5, 0, 3)
            . "/";
    }

    public function addCss(){
        \Bitrix\Main\Page\Asset::getInstance()->addCss($this->getScssCacheFolder() . $this->getCompiledScssFileName());     
    }

    public function setScssVars($arVars)
    {
        $this->_arScssVars = $arVars;
    }

    public function getScssVars()
    {
        return $this->_arScssVars;
    }


    /**
     * ¬ключено ли отображение настроек в публичной части сайта
     *
     * @return bool
     */
    public function isPublicSettingsAvailable()
    {        
        global $USER;
        $arFrontParametrs = \CBitcorp::GetFrontParametrsValues(SITE_ID); 
        if ($arFrontParametrs['SHOW_SETTINGS_PANEL'] == 'Y'){
            return ($USER->IsAdmin());
        } else{
            return false;
        }        
    }

    private function initSettingsFromSession(){
        
    }
    

}