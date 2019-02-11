<?php

namespace Marsd\Bitcorp;

require __DIR__ . '/scssphp/scss.inc.php';

class SCSSCompiler extends Compiler
{

    /**
     * @var \Leafo\ScssPhp\Compiler $compiler
     */
    private $compiler;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compiler = new \Leafo\ScssPhp\Compiler();

        $this->compiler->setFormatter('\Leafo\ScssPhp\Formatter\Expanded');        
    }

    /**
     * Set colors by setting compiler scss variables
     */    public function setVariables($vars)
    {
        $this->compiler->setVariables($vars);                            
    }

    /**
     * Parse a scssc file to CSS
     * @param string $file path to file
     * @return string CSS
     */
    public function toCss($file)
    {
        $this->compiler->addImportPath(dirname($file));

        return ($css = @ file_get_contents($file)) !== false ? $this->compiler->compile($css) : '';
    }

    public static function getExtension()
    {
        return 'scss';
    }

}
