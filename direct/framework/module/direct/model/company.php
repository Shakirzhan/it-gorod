<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\model;

final class Company extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	//https://tech.yandex.ru/direct/doc/dg-v4/live/CreateOrUpdateCampaign-docpage/
	public function set($PARAM=array()) {
		/*
		array(
   "Login" => "agrom",
   "CampaignID" => 3193279,
   "Name" => "Promotion of home appliances",
   "FIO" => "Alex Gromov",
   "Strategy" => array(
      "StrategyName" => "RightBlockHighest"
   ),
   "ContextStrategy" => array(
      "StrategyName" => "Default",
      "ContextLimit" => "Limited",
      "ContextLimitSum" => 30,
      "ContextPricePercent" => 90
   ),
   "TimeTarget" => array(
      "TimeZone" => "Europe/Moscow",
      "DaysHours" => array(
         array(
            "Hours" => array(1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23),
            "Days" => array(1,2,3,4,5)
         ),
         array(
            "Hours" => array(10,11,12,13,14,15,16,17,18,19,20),
            "Days" => array(6,7),
            "BidCoefs" => array(40,40,40,50,50,60,100,60,60,20,10)
         )
      ),
      "ShowOnHolidays" => "Yes",
      "WorkingHolidays" => "No"
   ),
   "StatusBehavior" => "Yes",
   "StatusContextStop" => "No",
   "AutoOptimization" => "Yes",
   "StatusMetricaControl" => "Yes",
   "DisabledDomains" => "domain1.ru,domain2.ru",
   "DisabledIps" => "64.234.23.21",
   "StatusOpenStat" => "No",
   "ConsiderTimeTarget" => "Yes",
   "AddRelevantPhrases" => "No",
   "RelevantPhrasesBudgetLimit" => 100,
   "MinusKeywords" => array(),
   "SmsNotification" => array(
      "SmsTimeFrom" => "09:00",
      "MoneyInSms" => "Yes",
      "SmsTimeTo" => "21:00",
      "MoneyOutSms" => "Yes",
      "ModerateResultSms" => "Yes",
      "MetricaSms" => "Yes"
   ),
   "EmailNotification" => array(
      "MoneyWarningValue" => 20,
      "SendAccNews" => "Yes",
      "WarnPlaceInterval" => 60,
      "SendWarn" => "Yes",
      "Email" => "agrom@yandex.ru"
   )
)
		
		
		$Result = $this->Framework->direct->model->api->get ( 'GetCampaignsListFilter', $REQUEST );
		if (!empty($Result->data[0]))
			return $this->Framework->library->lib->objectToArray($Result->data);
		elseif (!empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить список компаний ('.print_r($REQUEST, true).print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		*/
		return array();
	}
	
	//http://api.yandex.ru/direct/doc/live/GetCampaignsListFilter.xml
	public function get($PARAM=array()) {
		$REQUEST = array();
		if (!empty($PARAM)) 
			if (!is_array($PARAM))
				$REQUEST['Logins']=array($PARAM);
			else
				$REQUEST['Logins']=$PARAM;
		
		$REQUEST['Filter']['StatusArchive']=array('No');
		$REQUEST['CurrencySupported']='Yes';
		
		$Result = $this->Framework->direct->model->api->get ( 'GetCampaignsListFilter', $REQUEST );
		if (!empty($Result->data[0]))
			return $this->Framework->library->lib->objectToArray($Result->data);
		elseif (!empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить список компаний ('.print_r($REQUEST, true).print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return array();
	}
	
	public function start($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array('CampaignID'=>$PARAM['id']);
			$Result = $this->Framework->direct->model->api->get ( 'ResumeCampaign', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			elseif (!empty($Result))
				$this->Framework->library->error->set('Не удалось запустить компанию №'.$PARAM['id'].' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	public function stop($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array('CampaignID'=>$PARAM['id']);
			$Result = $this->Framework->direct->model->api->get ( 'StopCampaign', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			else
				$this->Framework->library->error->set('Не удалось остановить компанию '.$PARAM['id'].' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/reference/ArchiveCampaign-docpage/
	public function Archive($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array('CampaignID'=>$PARAM['id']);
			$Result = $this->Framework->direct->model->api->get ( 'ArchiveCampaign', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			else
				$this->Framework->library->error->set('Не удалось заархивировать компанию '.$PARAM['id'].' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}	
	
	//https://tech.yandex.ru/direct/doc/dg-v4/reference/UnArchiveCampaign-docpage/
	public function UnArchive($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array('CampaignID'=>$PARAM['id']);
			$Result = $this->Framework->direct->model->api->get ( 'UnArchiveCampaign', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			else
				$this->Framework->library->error->set('Не удалось разархивировать компанию '.$PARAM['id'].' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/GetBalance-docpage/
	public function balance($PARAM) {
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM['id']) && is_array($PARAM['id']))
				$REQUEST=$PARAM['id'];
			elseif (!empty($PARAM['id']))
				$REQUEST=array($PARAM['id']);
			elseif (is_array($PARAM))
				$REQUEST=$PARAM;
			else
				$REQUEST=array($PARAM);
			if (!empty($REQUEST)) {
				$Result = $this->Framework->direct->model->api->get ( 'GetBalance', $REQUEST );
					if (!empty($Result->data[0]))
						return $this->Framework->library->lib->objectToArray($Result->data);
					elseif (!empty($Result->error_str) && (empty($Result->error_code) || $Result->error_code!=2))
						$this->Framework->library->error->set('Не удалось получить баланс кампании ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			}
		}		
	}
	
	//http://api.yandex.ru/direct/doc/live/GetSummaryStat.xml
	public function statistic($PARAM) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['date']))
			$PARAM['start']=date('Y-m-d', strtotime($PARAM['date']));
		if (empty($PARAM['start']))
			$PARAM['start']=date('Y-m-d');
		else
			$PARAM['start']=date('Y-m-d', strtotime($PARAM['start']));
		if (empty($PARAM['end']))
			$PARAM['end']=$PARAM['start'];
		else
			$PARAM['end']=date('Y-m-d', strtotime($PARAM['end']));
			
		if (!empty($PARAM) && !empty($PARAM['id'])) {
		
			$REQUEST=array(
			   'CampaignIDS' => (is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id'])),
			   'StartDate' => $PARAM['start'],
			   'EndDate' => $PARAM['end'],
			   'IncludeVAT' => 'No',
			);
			
			if (!empty($PARAM['currency']))
				$REQUEST['Currency']=$PARAM['currency'];
			
			$Result = $this->Framework->direct->model->api->get ( 'GetSummaryStat', $REQUEST );
			if (!empty($Result->data[0]))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_str) && (empty($Result->error_code) || $Result->error_code!=2))
				$this->Framework->library->error->set('Не удалось получить статистику кампании ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				
		}
		return array();
	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/GetCampaignsTags-docpage/
	public function tag($PARAM=array()) {
		$REQUEST=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$REQUEST['CampaignIDS']=array($PARAM);
		elseif (!empty($PARAM['id'])) {
			$REQUEST['CampaignIDS']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
		} elseif (!empty($PARAM) && is_array($PARAM))
			$REQUEST['CampaignIDS']=$PARAM;
	
		if (!empty($REQUEST) && is_array($REQUEST)) {		
			$Result = $this->Framework->direct->model->api->get ( 'GetCampaignsTags', $REQUEST );
			if (!empty($Result->data[0]))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_str))
				$this->Framework->library->error->set('Не удалось получить список тегов у баннеров ('.print_r($REQUEST, true).print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return array();
	}
	
}//\class
?>