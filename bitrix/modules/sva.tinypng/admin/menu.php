<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    'parent_menu' => 'global_menu_services',
    'section' => 'sva.timypng',
    'sort' => 85,
    'text' => 'TinyPNG',
    'title' => GetMessage('SVA_TINYPNG_FILES'),
    'url' => 'sva_tinypng_files.php?lang='.LANGUAGE_ID,
    'icon' => 'sva_tinypng_menu_icon',
    'page_icon' => 'sva_tinypng_files_icon',
    'items_id' => 'menu_sva_tinypng',
);

if (!empty($aMenu)) {
    return $aMenu;
} else {
    return false;
}