<?
//�������� �������� module.name1, module.name2, module.name3 � ��� ����� �� ������ ������� ������ ������ �� �������� ��������
$arModules = array(
'module.name1',
'module.name2',
'module.name3'
);

foreach($arModules as $val){
    if(isset($arClientModules[$val])) unset($arClientModules[$val]);
}
?>