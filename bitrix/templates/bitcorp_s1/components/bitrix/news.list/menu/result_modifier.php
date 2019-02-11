<?
//Bitrix\Main\Diag\Debug::dumpToFile($arResult);
//return $arCustomFields;
foreach ($arResult['ITEMS'] as $key => $value) {
	$arResult[] = 	$value['NAME'];			
}
?>