<?
class alreadygoing{
	function __construct(){
		
	}
	public function insertAGBlock(){
		global $APPLICATION;
		$module_id='skyweb24.alreadygoing';
		if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true && COption::GetOptionString($module_id, "active")=='Y'){			
			$default_option = array("img_path", "cookie", "header", "header_ext", "link_value", "link_name", "content", "skin", "skin_color", "active");
			$scriptOut='<script type="text/javascript">var alreadyGoingObj={';
			foreach($default_option as $val){
				$scriptOut.=$val.':\''.COption::GetOptionString($module_id, $val).'\',';
			}
			$scriptOut.='};</script>';
			$APPLICATION->AddHeadString($scriptOut, true);
			if(COption::GetOptionString($module_id, 'jquery')=='Y'){
				CJSCore::Init(array("jquery"));
			}
			if(COption::GetOptionString($module_id, 'fancybox')=='Y'){
				$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.fancybox.pack.js');
				$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/'.$module_id.'/jquery.fancybox.css');
			}
			$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/'.$module_id.'/'.COption::GetOptionString($module_id, "skin").'.css');
			$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/'.$module_id.'/'.COption::GetOptionString($module_id, "skin_color").'.css');
			$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/script.js');
		}/*else{
			$APPLICATION->AddHeadString("<script type='text/javascript'>console.log('admin');</script>", true);
		}*/
	}
	public function setStatistic(){
		if(!empty($_REQUEST['STAT_SW24_AG'])){
			if($_REQUEST['STAT_SW24_AG']=='stat_showbanner' || $_REQUEST['STAT_SW24_AG']=='stat_closebanner' || $_REQUEST['STAT_SW24_AG']=='stat_gotolink'){
				$module_id='skyweb24.alreadygoing';
				$cCount=COption::GetOptionString($module_id, $_REQUEST['STAT_SW24_AG']);
				COption::SetOptionString($module_id, $_REQUEST['STAT_SW24_AG'], $cCount+1);
				echo json_encode('successs');
				die();
			}
		}
	}
}
?>