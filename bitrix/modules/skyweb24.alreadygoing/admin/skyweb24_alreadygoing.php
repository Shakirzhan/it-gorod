<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$cInfo='';
if(!empty($_REQUEST['IMAGE_ID'])){
	CModule::IncludeModule("iblock");
	$cInfo=array('path'=>'N');
	$res = CFile::GetList(array(""), array("MODULE_ID"=>htmlspecialchars($_REQUEST['MODULE_ID'])));
	while($res_arr = $res->GetNext()){
		if($res_arr['ID']!=$_REQUEST['IMAGE_ID']){
			CFile::Delete($res_arr["ID"]);
		}else{
			$cInfo=array('path'=>'/upload/'.$res_arr['SUBDIR'].'/'.$res_arr['FILE_NAME']);
			$arFile = CFile::MakeFileArray($res_arr['ID']);
			$arNewFile = CIBlock::ResizePicture($arFile, array("WIDTH" => 600, "HEIGHT" => 600));
			COption::SetOptionString(htmlspecialchars($_REQUEST['MODULE_ID']), "img_path", $cInfo['path']);
		}
	}
	
}elseif(!empty($_REQUEST['STAT_SW24_AG'])){
	if($_REQUEST['STAT_SW24_AG']=='stat_showbanner' && $_REQUEST['STAT_SW24_AG']=='stat_closebanner' && $_REQUEST['STAT_SW24_AG']=='stat_gotolink'){
		$cCount=intval(COption::GetOptionString('skyweb24.alreadygoing', $_REQUEST['STAT_SW24_AG']))+1;
		COption::SetOptionString('skyweb24.alreadygoing', $_REQUEST['STAT_SW24_AG'], $cCount);
		$cInfo='success';
	}
}

echo json_encode($cInfo);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>