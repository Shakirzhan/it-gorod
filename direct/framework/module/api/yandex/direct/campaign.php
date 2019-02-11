<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\direct;
//https://tech.yandex.ru/direct/doc/ref-v5/campaigns/campaigns-docpage/
final class Campaign extends \FrameWork\Common {
	
	private $limit=10000;
	private $campaign=1000;
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	//https://tech.yandex.ru/direct/doc/ref-v5/campaigns/get-docpage/
	public function get($PARAM=array()) {
		$DATA=array();
		//if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id']))
				$REQUEST['SelectionCriteria']['Ids']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			elseif (!empty($PARAM['company']))
				$REQUEST['SelectionCriteria']['CampaignIds']=is_array($PARAM['company'])?$PARAM['company']:array($PARAM['company']);
			if (empty($PARAM['type']))
				$REQUEST['SelectionCriteria']['Types']=array( 
					'TEXT_CAMPAIGN', 
					//'MOBILE_APP_CAMPAIGN', 
					//'DYNAMIC_TEXT_CAMPAIGN', 
				);
			
			if (empty($PARAM['state']))
				$REQUEST['SelectionCriteria']['States']=array( 'ENDED', 'OFF', 'ON', 'SUSPENDED' );// 'CONVERTED','ARCHIVED'
			if (empty($PARAM['status']))
				$REQUEST['SelectionCriteria']['Statuses']=array( 'MODERATION', 'ACCEPTED', 'REJECTED', 'DRAFT' );//
			if (empty($PARAM['payment']))
				$REQUEST['SelectionCriteria']['StatusesPayment']=array( 'DISALLOWED', 'ALLOWED' );//'DISALLOWED', 'ALLOWED'
			
			if (empty($PARAM['field']))
				$REQUEST['FieldNames']=array(
					'BlockedIps',
					'ExcludedSites',
					'Currency',
					'DailyBudget',
					'Notification',
					'EndDate',
					'Funds',
					'ClientInfo',
					'Id',
					'Name',
					'NegativeKeywords',
					'RepresentedBy',
					'StartDate',
					'Statistics',
					'State',
					'Status',
					'StatusPayment',
					'StatusClarification',
					'SourceId',
					'TimeTargeting',
					'TimeZone',
					'Type'
				);			
			if (empty($PARAM['textfield']))
				$REQUEST['TextCampaignFieldNames']=array(
					'CounterIds', 
					'RelevantKeywords', 
					'Settings', 
					'BiddingStrategy'
				);			
			if (empty($PARAM['mobilefield']))
				$REQUEST['MobileAppCampaignFieldNames']=array(
					'Settings', 
					'BiddingStrategy'
				);
			if (empty($PARAM['dynamicfield']))
				$REQUEST['DynamicTextCampaignFieldNames']=array(
					'CounterIds', 
					'Settings', 
					'BiddingStrategy'
				);
			
			if (empty($PARAM['limit']) || !$PARAM['limit']>0)			
				$REQUEST['Page']['Limit']=$this->limit;
			else	
				$REQUEST['Page']['Limit']=($PARAM['limit']>0 && $PARAM['limit']<=$this->limit)?(int)$PARAM['limit']:$this->limit;
			
			if (!empty($PARAM['page']) && $PARAM['page']>0)
				$REQUEST['Page']['Offset']=$REQUEST['Page']['Limit'] * (int)$PARAM['page'];
			
			if (empty($PARAM['offset']) || !$PARAM['offset']>0)
				$REQUEST['Page']['Offset']=0;
			else
				$REQUEST['Page']['Offset']=(int)$PARAM['offset'];

			$DATA=$this->Framework->api->yandex->direct->query->get('campaigns', 'get', $REQUEST);
			if (!empty($DATA['Campaigns']))
				$DATA=$DATA['Campaigns'];
			else
				$DATA=array();
		//}
/*Array
(
    [Campaigns] => Array
        (
            [0] => Array
                (
                    [NegativeKeywords] => Array
                        (
                            [Items] => Array
                                (
                                    [0] => !их
                                    [1] => !х
                                )

                        )

                    [SourceId] => 
                    [RepresentedBy] => Array
                        (
                            [Agency] => Интернет-коммуникации
                            [Manager] => 
                        )

                    [BlockedIps] => 
                    [TimeZone] => Europe/Moscow
                    [StatusPayment] => ALLOWED
                    [Funds] => Array
                        (
                            [Mode] => CAMPAIGN_FUNDS
                            [CampaignFunds] => Array
                                (
                                    [Sum] => 36438983050
                                    [SumAvailableForTransfer] => 6538657288
                                    [BalanceBonus] => 0
                                    [Balance] => 6838657288
                                )

                        )

                    [TextCampaign] => Array
                        (
                            [BiddingStrategy] => Array
                                (
                                    [Network] => Array
                                        (
                                            [BiddingStrategyType] => MAXIMUM_COVERAGE
                                        )

                                    [Search] => Array
                                        (
                                            [BiddingStrategyType] => LOWEST_COST_GUARANTEE
                                        )

                                )

                            [Settings] => Array
                                (
                                    [0] => Array
                                        (
                                            [Option] => ADD_TO_FAVORITES
                                            [Value] => NO
                                        )

                                    [1] => Array
                                        (
                                            [Value] => YES
                                            [Option] => ENABLE_BEHAVIORAL_TARGETING
                                        )

                                    [2] => Array
                                        (
                                            [Value] => NO
                                            [Option] => REQUIRE_SERVICING
                                        )

                                    [3] => Array
                                        (
                                            [Option] => SHARED_ACCOUNT_ENABLED
                                            [Value] => NO
                                        )

                                    [4] => Array
                                        (
                                            [Value] => NO
                                            [Option] => DAILY_BUDGET_ALLOWED
                                        )

                                    [5] => Array
                                        (
                                            [Option] => ENABLE_AUTOFOCUS
                                            [Value] => NO
                                        )

                                    [6] => Array
                                        (
                                            [Value] => YES
                                            [Option] => MAINTAIN_NETWORK_CPC
                                        )

                                    [7] => Array
                                        (
                                            [Option] => ENABLE_SITE_MONITORING
                                            [Value] => NO
                                        )

                                    [8] => Array
                                        (
                                            [Value] => YES
                                            [Option] => ADD_METRICA_TAG
                                        )

                                    [9] => Array
                                        (
                                            [Value] => NO
                                            [Option] => ADD_OPENSTAT_TAG
                                        )

                                    [10] => Array
                                        (
                                            [Option] => ENABLE_EXTENDED_AD_TITLE
                                            [Value] => NO
                                        )

                                    [11] => Array
                                        (
                                            [Value] => NO
                                            [Option] => ENABLE_RELATED_KEYWORDS
                                        )

                                    [12] => Array
                                        (
                                            [Value] => YES
                                            [Option] => EXCLUDE_PAUSED_COMPETING_ADS
                                        )

                                    [13] => Array
                                        (
                                            [Option] => ENABLE_AREA_OF_INTEREST_TARGETING
                                            [Value] => YES
                                        )

                                )

                            [CounterIds] => Array
                                (
                                    [Items] => Array
                                        (
                                            [0] => 25484177
                                        )

                                )

                            [RelevantKeywords] => 
                        )

                    [StatusClarification] => Идут показы
                     [TimeTargeting] => Array
						(
							[ConsiderWorkingWeekends] => YES
							[HolidaysSchedule] => Array
								(
									[BidPercent] => 110
									[EndHour] => 24
									[SuspendOnHolidays] => NO
									[StartHour] => 0
								)

							[Schedule] => Array
								(
									[Items] => Array
										(
											[0] => 1,100,100,100,100,100,100,100,100,100,110,110,110,110,110,110,110,110,110,100,100,100,100,100,100
											[1] => 2,100,100,100,100,100,100,100,100,100,110,110,110,110,110,110,110,110,110,100,100,100,100,100,100
											[2] => 3,100,100,100,100,100,100,100,100,100,110,110,110,110,110,110,110,110,110,100,100,100,100,100,100
											[3] => 4,100,100,100,100,100,100,100,100,100,110,110,110,110,110,110,110,110,110,100,100,100,100,100,100
											[4] => 5,100,100,100,100,100,100,100,100,100,110,110,110,110,110,110,110,110,110,100,100,100,100,100,100
											[5] => 6,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100
											[6] => 7,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100
										)

								)

						)

                    [ExcludedSites] => 
                    [Currency] => RUB
                    [Statistics] => Array
                        (
                            [Impressions] => 253052
                            [Clicks] => 4053
                        )

                    [Notification] => Array
                        (
                            [SmsSettings] => Array
                                (
                                    [TimeFrom] => 09:00
                                    [TimeTo] => 21:00
                                )

                            [EmailSettings] => Array
                                (
                                    [CheckPositionInterval] => 60
                                    [SendWarnings] => YES
                                    [Email] => info@direct-automate.ru
                                    [WarningBalance] => 20
                                    [SendAccountNews] => YES
                                )

                        )

                    [Type] => TEXT_CAMPAIGN
                    [State] => ON
                    [EndDate] => 
                    [Name] => Директ-автомат.рф
                    [DailyBudget] => 
                    [ClientInfo] => Станислав
                    [StartDate] => 2014-08-11
                    [Status] => ACCEPTED
                    [Id] => 10031819
                )

        )

)
*/
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		$DATA=array();
		
		
		return $DATA;
	}
	
	public function add ($PARAM=array()) {
		return $this->set($PARAM);
	}	
	
	public function resume($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('campaigns', 'resume', $REQUEST);
				if (!empty($DATA['ResumeResults']))
					$DATA=$DATA['ResumeResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}
	
	public function suspend($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('campaigns', 'suspend', $REQUEST);
				if (!empty($DATA['SuspendResults']))
					$DATA=$DATA['SuspendResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}	
	
	public function archive($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('campaigns', 'archive', $REQUEST);
				if (!empty($DATA['ArchiveResults']))
					$DATA=$DATA['ArchiveResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}
	
	public function unarchive($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('campaigns', 'unarchive', $REQUEST);
				if (!empty($DATA['UnarchiveResults']))
					$DATA=$DATA['UnarchiveResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}
	
}//\class
?>