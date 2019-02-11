<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {error} function plugin
 *
 * Type:     function<br>
 * Name:     error<br>
 * Purpose:  print php errors
 *
 * @author Stanislav Kononenko <info@cloud-automate.ru>
 * @param bool                    $html   parameters
 * @return string|null
 */
function smarty_function_error($html=false)
{
$error=\FrameWork\Framework::singleton()->library->error()->get();
if (!$html)
	$error=str_replace("\r\n", '\n', addslashes(strip_tags($error)));
return $error;    
}

?>