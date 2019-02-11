<?php
/**
 * Created by PhpStorm.
 * User: WebDev
 * Date: 01.10.2016
 * Time: 13:31
 */

namespace Sva\TinyPng;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class TinyPngFileTable extends Entity\DataManager{

    static $module_id = "sva.tinypng";

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_sva_tinypng_files';
    }

    public static function getTableTitle()
    {
        return Loc::getMessage('SVA_TINYPNG_REDIRECTS_TITLE');
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('SIZE_BEFORE'),
            new Entity\IntegerField('SIZE_AFTER'),
            new Entity\IntegerField('FILE_ID', array(
                'primary' => true,
            )),
            new Entity\ReferenceField(
                'FILE',
                'Bitrix\Main\FileTable',
                array(
                    '=this.FILE_ID' => 'ref.ID'
                )
            ),
//            'FILE_ID' => array(
//                'data_type' => 'integer',
//                'primary' => true,
//                'autocomplete' => true,
//                'admin_page' => true,
//                'admin_page_default' => true,
//                'title' => Loc::getMessage('SVA_TINYPNG_REDIRECTS_ENTITY_ID_FIELD'),
//            ),
//            'SIZE_BEFORE' => array(
//                'data_type' => 'integer',
//                'primary' => true,
//                'autocomplete' => true,
//                'admin_page' => true,
//                'admin_page_default' => true,
//                'title' => Loc::getMessage('SVA_TINYPNG_REDIRECTS_ENTITY_SIZE_BEFORE_FIELD'),
//            ),
//            'SIZE_AFTER' => array(
//                'data_type' => 'integer',
//                'primary' => true,
//                'autocomplete' => true,
//                'admin_page' => true,
//                'admin_page_default' => true,
//                'title' => Loc::getMessage('SVA_TINYPNG_REDIRECTS_ENTITY_SIZE_AFTER_FIELD'),
//            ),
        );
    }
}