<?php
global $DBType;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'sva.tinypng',
    array(
        'CSVAAdminList' => 'classes/admin/admin_list.php',
    )
);