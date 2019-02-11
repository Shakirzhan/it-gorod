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
//https://tech.yandex.ru/direct/doc/dg-v4/live/CreateNewReport-docpage/
final class Report extends \FrameWork\Common {
	private $debug=false;
	
	private $day=366;
	private $limit=5;
	
	public function __construct () {
		parent::__construct();
		$this->day=$this->Framework->direct->model->config->statistic_conversion>1 && $this->Framework->direct->model->config->statistic_conversion<$this->day? $this->Framework->direct->model->config->statistic_conversion:$this->day;
	}
		
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function set($PARAM=array()) {
		$REQUEST=array(
			'GroupByColumns' => array(
				'clBanner',
				'clPhrase',
				'clStatGoals',
				'clGoalConversionsNum',
				//'clAveragePosition',
				'clROI',
			),		   
			//'Limit' => 5000,
			//'Offset' => 30000,
			//'GroupByDate' => 'month',
			'OrderBy' => array('clPhrase'),
			'TypeResultReport' => 'xml',
			//'CompressReport' => 1,
		   );
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id'])) {
				$REQUEST['CampaignID'] = (int)$PARAM['id'];
				
				if (!empty($PARAM['start']))
					$PARAM['start']=strtotime($PARAM['start']);
				else
					$PARAM['start']=mktime(0, 0, 0, date('m'), date('d')-$this->day, date('Y'));
				$REQUEST['StartDate']=date('Y-m-d', $PARAM['start']);
				
				if (!empty($PARAM['end']))
					$PARAM['end']=strtotime($PARAM['end']);
				else
					$PARAM['end']=time();
				$REQUEST['EndDate']=date('Y-m-d', $PARAM['end']);
				
				if (!empty($PARAM['goal'])) {
					$PARAM['goal']=preg_replace('/[^0-9]/', '',$PARAM['goal']);
					if (!empty($PARAM['goal']))
						$REQUEST['Filter']['StatGoals']=array((int)$PARAM['goal']);
				}
				
				if (!empty($PARAM['currency']))
					$REQUEST['Currency']= strtoupper((string)$PARAM['currency']);
				
				$Result = $this->Framework->direct->model->api->get ( 'CreateNewReport', $REQUEST );
			}
		}

		
		
		if (!empty($Result->data))
			return (int)$Result->data;
		elseif (!empty($Result->error_code) && $Result->error_code == 2)
			return $Result->error_code;
		else
			$this->Framework->library->error->set('Ошибка создания отчета: '.$REQUEST['CampaignID'].' ('.print_r($Result, true).')', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);

		return 0;
	}
	
	public function get($PARAM=array()) {
		$DATA=array('ELEMENT'=>array(), 'COMPANY'=>array(), 'count'=>0);
		/*
		[bannerID] => 500078963
		[phrase_id] => 2524886388
		[phraseID] => 792536094
		[sum_search] => 0.78
		[sum_context] => 0
		[shows_search] => 5
		[shows_context] => 4
		[clicks_search] => 2
		[clicks_context] => 0
		[session_depth] => 3.00
		[goal_id] => 0
		[goal_conversion] => 50.00
		[goal_cost] => 0.78
		[goal_conversions_num] => 1
		[sum] => 0.78
		[shows] => 9
		[clicks] => 2
		*/
		$REPORT=$this->Framework->direct->model->report->data();
		$DATA['count']=$this->limit-$REPORT['number'];
		if (count($REPORT['ELEMENT'])>0) {
			foreach ($REPORT['ELEMENT'] as &$VALUE) {
				$xml=$this->Framework->library->http->get($VALUE['url']);
				$xml=preg_replace("/<phrasesDict>.+<\/phrasesDict>/isU", '', $xml);
				$Xml=$this->Framework->library->xml->get($xml);					
				if (!empty($Xml->stat)) {
					$DATA['COMPANY'][]=(int)$Xml->campaignID;
					foreach ($Xml->stat->row as $Value) {
						$Value=(array)$Value;
						if (!empty($Value['@attributes']['phrase_id']))
							if (!isset($DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']])) {
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]=$Value['@attributes'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['count']=1;
							}
							else {
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['sum_search']+=$Value['@attributes']['sum_search'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['sum_context']+=$Value['@attributes']['sum_context'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['shows_search']+=$Value['@attributes']['shows_search'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['shows_context']+=$Value['@attributes']['shows_context'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['clicks_search']+=$Value['@attributes']['clicks_search'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['clicks_context']+=$Value['@attributes']['clicks_context'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['session_depth']+=$Value['@attributes']['session_depth'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['goal_conversion']+=$Value['@attributes']['goal_conversion'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['goal_cost']+=$Value['@attributes']['goal_cost'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['goal_conversions_num']+=$Value['@attributes']['goal_conversions_num'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['revenue']+=$Value['@attributes']['revenue'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['roi']+=$Value['@attributes']['roi'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['sum']+=$Value['@attributes']['sum'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['shows']+=$Value['@attributes']['shows'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['clicks']+=$Value['@attributes']['clicks'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['sum_search']+=$Value['@attributes']['sum_search'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['sum_search']+=$Value['@attributes']['sum_search'];
								$DATA['ELEMENT'][(string)$Value['@attributes']['phrase_id']]['count']++;
							}
					}
				}
				unset($Xml);
				foreach ($DATA['ELEMENT'] as &$ELEMENT) {
					if (!empty($ELEMENT['count']) && $ELEMENT['count']>1) {
						$ELEMENT['session_depth']=round($ELEMENT['session_depth']/$ELEMENT['count'], 2);
						$ELEMENT['goal_conversion']=round($ELEMENT['goal_conversion']/$ELEMENT['count'], 2);
						$ELEMENT['goal_cost']=round($ELEMENT['goal_cost']/$ELEMENT['count'], 2);
						$ELEMENT['roi']=round($ELEMENT['roi']/$ELEMENT['count'], 2);
					}
				}
					
				$this->Framework->direct->model->report->delete($VALUE['id']);
			}
		}
		

		return $DATA;
	}
	
	public function delete($PARAM=array()) {
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array($PARAM);
		foreach ($PARAM as $value)
			if ($value)
				$Result = $this->Framework->direct->model->api->get ( 'DeleteReport', (int)$value );
		}
		if (!empty($Result->data))
			return (int)$Result->data;
		else
			$this->Framework->library->error->set('Ошибка создания отчета: '.$REQUEST['CampaignID'], __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);

		return 0;
	}
	
	public function data() {
		$DATA=array('ELEMENT'=>array(), 'count'=>0, 'number'=>0);
		$Result = $this->Framework->direct->model->api->get ( 'GetReportList', array() );
		$count=0;
		$number=0;
		if (!empty($Result->data)) {
			
			foreach ($Result->data as $Value) { 
				$count++;
				if ($Value->Url && $Value->StatusReport=='Done') {
					$number++;
					$DATA['ELEMENT'][]=array(
						'id'=>$Value->ReportID,
						'url'=>$Value->Url,
						'status'=>($Value->StatusReport=='Done'?1:0),
					);
				}
			}
		}
		$DATA['count']=$count;
		$DATA['number']=$count-$number;
		return $DATA;
	}
	
}//\class
?>