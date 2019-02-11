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

final class Sinchronize extends \FrameWork\Common {
	
	private $microsecond=0;
	private $sinchronize=10000;
	private $debug=false;
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
		$this->get($PARAM);
	}
	
	public function get($PARAM=array()) {
		if (!empty($PARAM) && is_array($PARAM) && !empty($PARAM['debug'])) {
			$this->debug=true;
			unset($PARAM['debug']);
		}
		$debug_campaign=1;
		$debug_group=1;
		$debug_advert=1;
		$debug_keyword=1;
		if ($this->debug) echo date('H:i:s d.m.Y')." check and repair tables<br>\r\n";
		//Чиним сломавшиеся таблицы//
		$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		//Конфигурация//
		$CONFIG=$this->Framework->direct->model->config->CONFIG;
		//$this->microsecond=!empty($CONFIG['microsecond'])?$CONFIG['microsecond']:$this->microsecond;
		$this->sinchronize=is_numeric($this->Framework->direct->model->config->CONFIG['sinchronize'])&&$this->Framework->direct->model->config->CONFIG['sinchronize']>0?(int)$this->Framework->direct->model->config->CONFIG['sinchronize']:$this->sinchronize;
		//\Конфигурация//
		
		//Получаем курсы валют//
		$CURRENCY=$this->Framework->direct->model->currency->get();
		//\Получаем курсы валют//
		
		$DELETE=array('COMPANY'=>array(), 'BANNER'=>array(), 'PHRASE'=>array());
		
		$count_campaigns=0;
		$count_groups=0;
		$count_adverts=0;
		$count_keywords=0;
		$count_retargetings=0;
		
		//Получаем список аккаунтов//
		$this->Framework->db->set("UPDATE `".$this->Framework->user->model->model->TABLE[0]."` `t1` SET `t1`.`account`=`t1`.`id` WHERE `t1`.`parent`=0 AND `t1`.`account`!=`t1`.`id`");
		$GET=array(
			'group'=>4, 
			'status'=>1,
			'ID'=>(!empty($PARAM)?(is_array($PARAM) && !empty($PARAM[0])?$PARAM:(is_array($PARAM) && !empty($PARAM['account'])?array($PARAM['account']):array($PARAM))):0),
		);
		
		$USERS=$this->Framework->user->model->model->get($GET);
		$USERS_ID=!empty($USERS['ID'])?$USERS['ID']:array();
		$USERS=!empty($USERS['ELEMENT'])?$USERS['ELEMENT']:array();
		$USERS_STOP=$this->Framework->user->model->model->get(array('group'=>4, 'status'=>2));
		//\Получаем список аккаунтов//
		if ($this->debug) echo date('H:i:s d.m.Y')." get users<br>\r\n";
		foreach ($USERS as $LOGIN) {
			//Устанавливаем авторизационные данные//
			$this->Framework->direct->model->config->login=(string)$LOGIN['login'];
			$this->Framework->direct->model->config->token=(string)$LOGIN['token'];
			$this->Framework->api->yandex->direct->config->login=(string)$LOGIN['login'];//АПИ 5
			$this->Framework->api->yandex->direct->config->token=(string)$LOGIN['token'];//АПИ 5
			//\Устанавливаем авторизационные данные//
			
			//Синхронизируем клиентов//
			$CLIENT=$this->Framework->direct->model->client->get($LOGIN['login']);
			if (!empty($CLIENT[0]['Role']) && $CLIENT[0]['Role']!='Client') {
				$CLIENT=$this->Framework->direct->model->clients->get(); 
				$ID=array();
				foreach ($CLIENT as $key=>&$VALUE) {
					if ($VALUE['StatusArch']=='No' && !empty($VALUE['Login'])) {
						$USER=$this->Framework->user->model->model->get(array('login'=>$VALUE['Login']));
						$VALUE['timestamp']=!empty($USER['ELEMENT'][0]['timestamp'])?$USER['ELEMENT'][0]['timestamp']:'';
						$VALUE['unit']=!empty($USER['ELEMENT'][0]['unit'])?$USER['ELEMENT'][0]['unit']:'';
						$VALUE['unit_total']=!empty($USER['ELEMENT'][0]['unit_total'])?$USER['ELEMENT'][0]['unit_total']:'';
						$VALUE['unit_status']=!empty($USER['ELEMENT'][0]['unit_status'])?$USER['ELEMENT'][0]['unit_status']:'';
						$VALUE['unit_time']=!empty($USER['ELEMENT'][0]['unit_time'])?$USER['ELEMENT'][0]['unit_time']:'';
						$VALUE['id']=$this->Framework->user->model->model->set(array(
							'id'=>(!empty($USER['ELEMENT'][0]['id'])?$USER['ELEMENT'][0]['id']:null),
							'login'=>$VALUE['Login'], 
							'parent'=>$LOGIN['id'],
							'account'=>$LOGIN['id'],
							'group'=>2, 
							'name'=>$VALUE['FIO'], 
							'password'=>!empty($USER['ELEMENT'][0]['id'])?'':$this->Framework->library->lib->password(),
							'email'=>$VALUE['Email'],
							'phone'=>$VALUE['Phone'],
							'right'=>1,
							'status'=>1,
						));
						$ID[]=$VALUE['id'];
					} else
						unset($CLIENT[$key]);
				}
				//Отбираем субклиентов//
				$SUBID=array();
				if (!empty($CLIENT)) {
					if ($LOGIN['login']) {
						$MANAGER=$this->Framework->direct->model->subclient->get($LOGIN['login']);
						if (!empty($MANAGER)) {
							foreach ($MANAGER as &$VALUES) {
								if ($VALUES['Role']=='LimitedRepAgency') {
									$INFO=$this->Framework->direct->model->client->get($VALUES['Login']);
									if (!empty($INFO[0]) && $INFO[0]['StatusArch']=='No') {
										$INFO=array_shift($INFO);
										$USER=$this->Framework->user->model->model->get(array('login'=>$INFO['Login']));
										if (empty($USER['ELEMENT'])) {
											$INFO['id']=$this->Framework->user->model->model->set(array(
												'login'=>$INFO['Login'], 
												'parent'=>$LOGIN['id'],
												'account'=>$LOGIN['id'],
												'group'=>3, 
												'name'=>$INFO['FIO'], 
												'password'=>$this->Framework->library->lib->password(),
												'email'=>$INFO['Email'],
												'phone'=>$INFO['Phone'],
												'right'=>0,
												'status'=>1,
											));
										} else
											$INFO['id']=$USER['ELEMENT'][0]['id'];
										$SUBID[]=$INFO['id'];
										$SUBCLIENT=$this->Framework->direct->model->subclient->get($INFO['Login']);
										foreach ($SUBCLIENT as $SUB) {
											$USER=$this->Framework->user->model->model->get(array('login'=>$SUB['Login']));
											if (!empty($USER['ELEMENT'][0]['id'])) {
												$this->Framework->user->model->model->set(array(
													'id'=>$USER['ELEMENT'][0]['id'], 
													'parent'=>$INFO['id'], 
												));
											}
										}
									}
								}
							}
						}
					}
				}
				//\Отбираем субклиентов//
				
				//Удаляем заархивированных представителей и субклиентов//
				if ($this->Framework->library->error->count()==0) {
					if (!empty($ID)) {
						$this->Framework->db->set("DELETE FROM `".$this->Framework->user->model->model->TABLE[0]."` WHERE `id` NOT IN (".implode(',', $ID).") AND `parent` IN (".$LOGIN['id'].(!empty($SUBID)?','.implode(',', $SUBID):'').") AND `group` IN (2)");
						unset($ID);
					}
					if (!empty($SUBID)) {
						$this->Framework->db->set("DELETE FROM `".$this->Framework->user->model->model->TABLE[0]."` WHERE `id` NOT IN (".implode(',', $SUBID).") AND `parent`='".$LOGIN['id']."' AND `group` IN (3)");
						unset($SUBID);
					}
				}
				//\Удаляем заархивированных представителей и субклиентов//
				
			} else {
				$CLIENT=array(array('id'=>$LOGIN['id'], 'login'=>$LOGIN['login'], 'Login'=>$LOGIN['login'], 'timestamp'=>$LOGIN['timestamp'], 'unit'=>$LOGIN['unit'], 'unit_total'=>$LOGIN['unit_total'], 'unit_status'=>$LOGIN['unit_status'], 'unit_time'=>$LOGIN['unit_time']));	
			}
			
			if (!empty($USERS_STOP['ID'])) {
				$where_delete=" `t1`.`account` NOT IN (".implode(',',$USERS_STOP['ID']).") ";
				unset($USERS_STOP);
			}
			//\Синхронизируем клиентов//
			
			
			//Синхронизируем кампании клиентов//
			$COMPANY=array();
			$timestamp=0;
			foreach ($CLIENT as &$VALUE) {
				if (!empty($VALUE['Login'])) {
					//Проверяем лимиты//
					$this->Framework->api->yandex->direct->query->reset();
					if ( $VALUE['unit_status'] && !empty($VALUE['unit_time']) && strtotime($VALUE['unit_time'])>=mktime(0, $this->Framework->direct->model->config->time_yandex, 1, date('m'), date('d'), date('Y')) ) {
						if ($this->debug) echo 'LIMIT API5 for '.$VALUE['Login']."<br>\r\n";
						
						if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'], 'login'=>$VALUE['Login'], 'total'=>$VALUE['unit_total'], 'unit'=>$VALUE['unit'], 'percent'=>$CONFIG['api_percent'])))
							continue;
						elseif ($this->debug) print_r($VALUE, true);
							
					}
					//\Проверяем лимиты//
					
					$change_error=$this->Framework->library->error->count();
					//Устанавливаем авторизационные данные//
					$this->Framework->api->yandex->direct->config->login=(string)$VALUE['Login'];//АПИ 5
					//\Устанавливаем авторизационные данные//
					if ($this->debug) $time1=time();
					//Проверяем изменения//
					if ($this->debug) echo "Сheck company ".$VALUE['Login'].' '.$VALUE['timestamp']."<br>\r\n";
					$timestamp=$VALUE['timestamp'];
					$CHANGE=$this->Framework->api->yandex->direct->change->campaign($timestamp);
					//Проверяем лимиты//
					if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
						continue;
					//\Проверяем лимиты//
					//\Проверяем изменения//
					$CAMPAIGN_SELF=array();
					$CAMPAIGN_CHILDREN=array();
					if (!empty($CHANGE['Campaigns']))
						foreach ($CHANGE['Campaigns'] as $CAMPAIGN)
							if (!empty($CAMPAIGN['ChangesIn'])) {
								if (in_array('SELF', $CAMPAIGN['ChangesIn']))
									$CAMPAIGN_SELF[]=$CAMPAIGN['CampaignId'];
								if (in_array('CHILDREN', $CAMPAIGN['ChangesIn']))
									$CAMPAIGN_CHILDREN[$CAMPAIGN['CampaignId']]=$CAMPAIGN['CampaignId'];
							}
					//if ($this->debug) if ($VALUE['id']==532) {$CAMPAIGN_SELF[]=22606540; $CAMPAIGN_CHILDREN[22606540]=22606540; $timestamp=null;}//Диагностика	
					$timestamp_new=date('Y-m-d H:i:s', strtotime($CHANGE['Timestamp']));				
					unset($CHANGE);
					if ($this->debug) echo 'Count company self='.count($CAMPAIGN_SELF).', campaign children='.count($CAMPAIGN_CHILDREN)."<br>\r\n";
					if (!empty($CAMPAIGN_SELF)) {
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `delete`=1 WHERE (`id`='".implode("' OR `id`='", $CAMPAIGN_SELF)."') AND `user`='".(int)$VALUE['id']."'");
						$COMPANIES[$VALUE['id']]=$this->Framework->api->yandex->direct->campaign->get($CAMPAIGN_SELF);
						//Проверяем лимиты//
						if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
							continue;
						//\Проверяем лимиты//
						if ($this->debug) echo 'API5 campaign get '.$VALUE['Login'].', timestamp='.$timestamp_new.', time='.(time()-$time1).' сек.'."<br>\r\n";
						$count_campaign=0;
						if ($this->debug) $time1=time();		
						if (!empty($COMPANIES[$VALUE['id']]))
							foreach ($COMPANIES[$VALUE['id']] as $key=>&$COMP) {
								if ($this->debug) if ($debug_campaign) echo 'Campaign=<pre>'.print_r($COMP, true).'</pre>'."<br>\r\n";
								$COMP=array(
									'CampaignCurrency' => $COMP['Currency'],
									'Sum' => round((!empty($COMP['Funds']['CampaignFunds']['Sum'])?$COMP['Funds']['CampaignFunds']['Sum']:(!empty($COMP['Funds']['SharedAccountFunds']['Spend'])?$COMP['Funds']['SharedAccountFunds']['Spend']:0))/1000000, 2),
									//'AgencyName' => Интернет-коммуникации
									//'EnableRelatedKeywords' => Yes
									//'SumAvailableForTransfer' => 
									'IsActive' => $COMP['State']=='ON'?'Yes':'No',
									'Login' => $VALUE['Login'],
									'Shows' => $COMP['Statistics']['Impressions'],
									'CampaignID' => $COMP['Id'],
									'StrategyName' => $COMP['TextCampaign']['BiddingStrategy']['Search']['BiddingStrategyType'],
									//'DayBudgetEnabled' => Yes
									//'StatusActivating' => 'Yes',
									'Name' => $COMP['Name'],
									'Type' => $COMP['Type'],
									'StatusModerate' => $COMP['Status']=='ACCEPTED'?'Yes':'No',
									//'ExtendedAdTitleEnabled' => Yes
									'Currency' => $COMP['Currency'],
									'Clicks' => $COMP['Statistics']['Clicks'],
									'StatusArchive' => $COMP['State']=='ARCHIVED'?'Yes':'No',
									'StatusShow' => $COMP['State']=='ON'?'Yes':'No',
									'State' => $COMP['State'],
									'StatusClarification' => $COMP['StatusClarification'],
									//'ManagerName' => 
									'ContextStrategyName' => $COMP['TextCampaign']['BiddingStrategy']['Network']['BiddingStrategyType'],
									'StartDate' => $COMP['StartDate'],
									'Rest' => round((!empty($COMP['Funds']['CampaignFunds']['Balance'])?$COMP['Funds']['CampaignFunds']['Balance']:0)/1000000, 2),
									'Shared' => !empty($COMP['Funds']['Mode'])&&$COMP['Funds']['Mode']=='SHARED_ACCOUNT_FUNDS'?1:0,
								);
							
								if (empty($COMP['CampaignCurrency']))
									$COMP['CampaignCurrency']='';
								$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
									'id'=>$COMP['CampaignID'],
									'account'=>(int)$LOGIN['id'], 
									'user'=>(int)$VALUE['id'], 
									'name'=>$COMP['Name'],
									'state'=>$COMP['StatusClarification'],
									'strategy_name'=>$COMP['StrategyName'],
									'context_strategy_name'=>$COMP['ContextStrategyName'],
									'types'=>$COMP['Type'],
									'price'=>$COMP['Rest'],
									'show28'=>(int)$COMP['Shows'],
									'click28'=>(int)$COMP['Clicks'],
									'ctr28'=>round(100*(int)$COMP['Clicks']/((int)$COMP['Shows']>0?(int)$COMP['Shows']:1),2),
									'currency'=>(!empty($COMP['CampaignCurrency'])?(!empty($CURRENCY['KEY'][$COMP['CampaignCurrency']]['id'])?$CURRENCY['KEY'][$COMP['CampaignCurrency']]['id']:0):0),
									'delete'=>0,
									'status'=>(!empty($COMP['State'])&&$COMP['State']=='ON')||(!empty($COMP['StatusClarification']) && (mb_ereg_match('.*Идут показы.*', $COMP['StatusClarification'], 'i') || mb_ereg_match('.*Показы приостановлены по дневному ограничению.*', $COMP['StatusClarification'], 'i') || mb_ereg_match('.*Показы начнутся.*', $COMP['StatusClarification'], 'i') || mb_ereg_match('.*Средства на счете закончились.*', $COMP['StatusClarification'], 'i')))?1:2,
									'date'=>$COMP['StartDate'],
									//'time'=>date('Y-m-d H:i:s'),
								));
								$count_campaign++;
								$count_campaigns++;
								$COMP['user']=(int)$VALUE['id'];
								$COMP['login']=$VALUE['Login'];
								//$COMPANY[$COMP['CampaignID']]=$COMP;
							
							}
						if ($this->debug) echo 'Campaign save '.$count_campaign.', time='.(time()-$time1).' сек., memory='.memory_get_usage(true)."<br>\r\n";
						if (empty($COMPANIES[$VALUE['id']]))
							unset($COMPANIES[$VALUE['id']]);
					}
					$campaign_self_error=$this->Framework->library->error->count()-$change_error;
					
					//Получаем баланс для текстово-графических кампаний через АПИ4//
					$result_campaign=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `user`='".(int)$VALUE['id']."' AND `types`='TEXT_CAMPAIGN'");
					$CAMPAIGN_TEXT_CAMPAIGN=array();
					while($CAMPAIGN_ROW=$this->Framework->db->get($result_campaign)) 
						if (!empty($CAMPAIGN_ROW['id']))
							$CAMPAIGN_TEXT_CAMPAIGN[]=$CAMPAIGN_ROW['id'];
					unset($CAMPAIGN_ROW, $result_campaign);
					if (!empty($CAMPAIGN_TEXT_CAMPAIGN)) {
						$CAMPAIGN_BALANCES=$this->Framework->direct->model->company->balance($CAMPAIGN_TEXT_CAMPAIGN);
						if (!empty($CAMPAIGN_BALANCES))
							foreach ($CAMPAIGN_BALANCES as $CAMPAIGN_BALANCE)
								if (!empty($CAMPAIGN_BALANCE['CampaignID']))
									$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['currency']."` `t2` ON (`t2`.`id`=`t1`.`currency` AND `t1`.`currency`>0) SET `t1`.`price`=IF(`t1`.`currency`>0,ROUND(".round($CAMPAIGN_BALANCE['Rest'], 2)."*`t2`.`value`/(1+`t2`.`tax`/100),2), ROUND(".round($CAMPAIGN_BALANCE['Rest'], 2).")) WHERE `t1`.`user`='".(int)$VALUE['id']."' AND `t1`.`id`='".(int)$CAMPAIGN_BALANCE['CampaignID']."'");
					}
					//\Получаем баланс для текстово-графических кампаний через АПИ4//
					if ($this->debug) echo 'Campaigns='.print_r($CAMPAIGN_CHILDREN, true)."<br>\r\n";
					if (!empty($CAMPAIGN_CHILDREN))
						$CAMPAIGN_TEXT_CAMPAIGN=$CAMPAIGN_CHILDREN=array_values(array_intersect($CAMPAIGN_CHILDREN, $CAMPAIGN_TEXT_CAMPAIGN));
					//Отбираем уже синхронизированные кампании//
					$result_campaign=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `user`='".(int)$VALUE['id']."' AND `types`='TEXT_CAMPAIGN' AND `time` IS NOT NULL AND `time`>'".$timestamp."'");
					$CAMPAIGN_DIFF=array();
					while($CAMPAIGN_ROW=$this->Framework->db->get($result_campaign)) 
						if (!empty($CAMPAIGN_ROW['id']))
							$CAMPAIGN_DIFF[$CAMPAIGN_ROW['id']]=$CAMPAIGN_ROW['id'];
					if (!empty($CAMPAIGN_DIFF))
						$CAMPAIGN_CHILDREN=array_values(array_diff($CAMPAIGN_CHILDREN, $CAMPAIGN_DIFF));
					//\Отбираем уже синхронизированные кампании//
					//Отбираем не синхронизированные кампании//
					$result_campaign=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `user`='".(int)$VALUE['id']."' AND `types`='TEXT_CAMPAIGN' AND `time` IS NULL");
					$CAMPAIGN_ADD=array();
					while($CAMPAIGN_ROW=$this->Framework->db->get($result_campaign)) 
						if (!empty($CAMPAIGN_ROW['id']))
							$CAMPAIGN_ADD[$CAMPAIGN_ROW['id']]=$CAMPAIGN_ROW['id'];
					if (!empty($CAMPAIGN_ADD))
						$CAMPAIGN_CHILDREN=array_unique(array_values(array_merge($CAMPAIGN_CHILDREN, $CAMPAIGN_ADD)));
					if ($this->debug) echo 'Added campaigns='.print_r($CAMPAIGN_ADD, true)."<br>\r\n";
					//\Отбираем не синхронизированные кампании//
					if ($this->debug) echo 'CAMPAIGN_CHILDREN='.print_r($CAMPAIGN_CHILDREN, true)."<br>\r\n";
					if ($this->debug) echo 'Campaign children count='.count($CAMPAIGN_CHILDREN)."<br>\r\n";
					$campaign_children_error=0;
					if (!empty($CAMPAIGN_CHILDREN)) {
						
						//Метки//
						if (!empty($CAMPAIGN_TEXT_CAMPAIGN)) {
							$TAGS=$this->Framework->direct->model->company->tag(array('id'=>$CAMPAIGN_TEXT_CAMPAIGN));
							if (!empty($TAGS)) {
								foreach ($TAGS as &$TAG) {
									$TAG_DELETE=array();
									foreach ($TAG['Tags'] as &$VAL) {
										if ($this->microsecond>0)
											usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
										$TAG_DELETE[]=(int)$VAL['TagID'];
										$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['tag'], array(
											'id'=>$VAL['TagID'],
											'company'=>$TAG['CampaignID'],
											'name'=>$VAL['Tag'],
											'datetime'=>date('Y-m-d H:i:s'),
										));
									}
									if (!empty($TAG_DELETE)) {
										$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag']."` WHERE `company`='".$TAG['CampaignID']."' AND `id` NOT IN (".implode(',', $TAG_DELETE).")");
										unset($TAG_DELETE);
									}
								}
							}
							unset($TAGS); 
							
							$CAMPAIGN_CHILDREN_CHUNK=array_chunk($CAMPAIGN_TEXT_CAMPAIGN, 10);
							foreach ($CAMPAIGN_CHILDREN_CHUNK as &$CAMPAIGN_CHILDREN_CHUNK_VALUE) {
								$TAGS=$this->Framework->direct->model->banner->tag(array('company'=>$CAMPAIGN_CHILDREN_CHUNK_VALUE));
								if (!empty($TAGS)) {
									foreach ($TAGS as &$TAG) {
										$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag_banner']."` WHERE `banner`='".(int)$TAG['BannerID']."'");
										foreach ($TAG['TagIDS'] as &$val) {
											if ($this->microsecond>0)
												usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
											$this->Framework->db->set("REPLACE `".$this->Framework->direct->model->config->TABLE['tag_banner']."` SET 
												`id`='".(int)$val."',
												`banner`='".(int)$TAG['BannerID']."'"
											);
										}
										$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`group`=`t2`.`group` WHERE `t1`.`banner`='".(int)$TAG['BannerID']."'");
									}
								}
								unset($TAGS);
							}
							unset($CAMPAIGN_TEXT_CAMPAIGN);
						}
						//\Метки//
						
						$campaign_children_error=$this->Framework->library->error->count();
						foreach($CAMPAIGN_CHILDREN as $CAMPAIGN_ONE) {
							$campaign_error=$this->Framework->library->error->count();
							//Проверяем изменения у групп и объявлений//
							$CAMPAIGN_ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array('id'=>$CAMPAIGN_ONE), array(), array('currency', 'time'));
							if (is_null($CAMPAIGN_ROW[0]['time']))
								$timestamp_campaign=$CAMPAIGN_ROW[0]['time'];
							else
								$timestamp_campaign=$timestamp;
							if ($this->debug) {echo 'Change get campaign='.$CAMPAIGN_ONE.', timestamp_campaign='.$timestamp_campaign; $time1=time();}
							$CHANGE=$this->Framework->api->yandex->direct->change->get(array('company'=>$CAMPAIGN_ONE, 'time'=>$timestamp_campaign));//$CHANGE['Unprocessed']['CampaignIds']							
							$timestamp_campaign_new=date('Y-m-d H:i:s', strtotime($CHANGE['Timestamp']));
							//Проверяем лимиты//
							if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
								continue;
							//\Проверяем лимиты//
							//file_put_contents('log'.time().'.txt', print_r($CAMPAIGN_ONE, true).print_r($CHANGE, true));
							if ($this->debug) if (!empty($CHANGE['Unprocessed']['CampaignIds'])) echo ' WARNING UNPROCESSED Campaigns '.print_r($CHANGE['Unprocessed']['CampaignIds'], true)."\r\n";
							if ($this->debug) if ($this->debug) echo ', time='.(time()-$time1).' сек., memory='.memory_get_usage(true)."<br>\r\n";
							//\Проверяем изменения у групп и объявлений//
							
							//Объявления//
							
							if ($this->debug) {echo 'Adverts modified '.(!empty($CHANGE['Modified']['AdIds'])?count($CHANGE['Modified']['AdIds']):0).''; $time1=time();}
							$GROUP_ADVERT=array();
							$RETARGETING_ADVERTS=array();
							$count_advert=0;
							if (!empty($CHANGE['Modified']['AdIds'])) {
								//Отбираем уже синхронизированные объявления//
								if ($this->debug) print_r($CHANGE['Modified']['AdIds']);
								$result_advert=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `company`='".(int)$CAMPAIGN_ONE."' AND `time` IS NOT NULL AND `time`>'".$timestamp_campaign."'");
								$ADVERT_DIFF=array();
								while($ADVERT_ROW=$this->Framework->db->get($result_advert)) 
									if (!empty($ADVERT_ROW['id']))
										$ADVERT_DIFF[$ADVERT_ROW['id']]=$ADVERT_ROW['id'];
								if (!empty($ADVERT_DIFF))
									$CHANGE['Modified']['AdIds']=array_values(array_diff($CHANGE['Modified']['AdIds'], $ADVERT_DIFF));
								if ($this->debug) print_r($CHANGE['Modified']['AdIds']);
								//\Отбираем уже синхронизированные объявления//
								$ADVERTS_CHUNK=array_chunk($CHANGE['Modified']['AdIds'], $this->sinchronize);
								if ($this->debug) {$time_api=0; $time_sql=0;}
								foreach ($ADVERTS_CHUNK as &$ADVERT_CHUNK) {
									//Помечаем на удаление//
									$delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET `delete`=1 WHERE `user`='".(int)$VALUE['id']."'".' AND (';
									$delete_sql='';
									foreach($ADVERT_CHUNK as &$value) {
										$value=preg_replace('[^0-9]', '', (string)$value);
										$delete_sql_postfix=(empty($delete_sql)?$delete_sql_prefix:" OR ")."`id`='".(string)$value."'";
										if (strlen($delete_sql.$delete_sql_postfix)>49999) {
											$delete_sql.=')';
											$this->Framework->db->set($delete_sql);
											$delete_sql=$delete_sql_prefix."`id`='".(string)$value."'";
										} else
											$delete_sql.=$delete_sql_postfix;
									}
									if ($delete_sql) {
										$delete_sql.=')';
										$this->Framework->db->set($delete_sql);
									}
									//\Помечаем на удаление//
									if ($this->debug) $time2=microtime(true);
									$ADVERTS=$this->Framework->api->yandex->direct->advert->get($ADVERT_CHUNK);
									if ($this->debug) $time_api+=microtime(true)-$time2;
									//Проверяем лимиты//
									if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
										continue;
									//\Проверяем лимиты//
									//echo '<br>$ADVERTS=<pre>'.print_r($ADVERTS, true).'</pre>';
									if ($this->debug) $time3=microtime(true);
									foreach ($ADVERTS as &$ADVERT) {
										$ADVERT['Id']=(string)$ADVERT['Id'];
										$ADVERT['AdGroupId']=(string)$ADVERT['AdGroupId'];
										$RETARGETING_ADVERTS[]=$ADVERT['Id'];
										$name=!empty($ADVERT['TextAd']['Title'])?$ADVERT['TextAd']['Title']:(!empty($ADVERT['TextImageAd']['AdImageHash'])?$ADVERT['TextImageAd']['AdImageHash']:(!empty($ADVERT['MobileAppImageAd']['AdImageHash'])?$ADVERT['MobileAppImageAd']['AdImageHash']:(!empty($ADVERT['MobileAppAd']['Title'])?$ADVERT['MobileAppAd']['Title']:(!empty($ADVERT['DynamicTextAd']['Text'])?$ADVERT['DynamicTextAd']['Text']:'Неизвестное объявление'))));
										$body=!empty($ADVERT['TextAd']['Text'])?$ADVERT['TextAd']['Text']:(!empty($ADVERT['TextImageAd']['AdImageHash'])?'':(!empty($ADVERT['MobileAppImageAd']['AdImageHash'])?'':(!empty($ADVERT['MobileAppAd']['Text'])?$ADVERT['MobileAppAd']['Text']:(!empty($ADVERT['DynamicTextAd']['Text'])?$ADVERT['DynamicTextAd']['Text']:''))));
										$url=!empty($ADVERT['TextAd']['Href'])?$ADVERT['TextAd']['Href']:(!empty($ADVERT['TextImageAd']['Href'])?$ADVERT['TextImageAd']['Href']:(!empty($ADVERT['MobileAppImageAd']['TrackingUrl'])?$ADVERT['MobileAppImageAd']['TrackingUrl']:(!empty($ADVERT['MobileAppAd']['TrackingUrl'])?$ADVERT['MobileAppAd']['TrackingUrl']:'')));
										if (!isset($GROUP_ADVERT[$ADVERT['AdGroupId']]) || $ADVERT['State']=='ON')
											$GROUP_ADVERT[$ADVERT['AdGroupId']]=array('id'=>$ADVERT['Id'], 'domain'=>$ADVERT['TextAd']['DisplayDomain'], 'name'=>$name, 'body'=>$body, 'url'=>$url, 'status'=>($ADVERT['State'] == 'ON'?1:2));					
										$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array(
											'id'=>$ADVERT['Id'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>(int)$ADVERT['CampaignId'], 
											'group'=>$ADVERT['AdGroupId'],
											'name'=>$name,
											'body'=>$body,
											'url'=>$url,
											'domain'=>!empty($ADVERT['TextAd']['DisplayDomain'])?$ADVERT['TextAd']['DisplayDomain']:'',
											'delete'=>0,
											'variant'=>$ADVERT['Type'],
											'status'=>($ADVERT['State'] == 'SUSPENDED'?2:1),
											'time'=>$timestamp_campaign_new,
										));
										if ($ADVERT['State'] == 'ON') 
											$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."`SET `status`=1 WHERE `id`='".$ADVERT['AdGroupId']."'");
										//else
											//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id` AND `t2`.`status`=1) SET `t1`.`status`=IF(`t2`.`status`=1, 1, 2) WHERE `t1`.`id`='".$ADVERT['AdGroupId']."'");
										if ($this->debug) if ($debug_advert) echo 'ADVERT=<pre>'.print_r($ADVERT, true).'</pre>'."<br>\r\n";
										$count_advert++;
										$count_adverts++;
									}
									if ($this->debug) $time_sql+=microtime(true)-$time3;
								}
								unset($ADVERTS, $ADVERT, $ADVERTS_CHUNK, $ADVERT_CHUNK, $CHANGE['Modified']['AdIds']);
							}
							if ($this->debug) echo ', save '.$count_advert.', timestamp_campaign='.$timestamp_campaign_new.', time='.(time()-$time1).' сек., time api='.round($time_api, 4).' сек., time sql='.round($time_sql, 4).', memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
							//\Объявления//
							
							//Фразы//
							if ($this->debug) $time1=time();
							$count_keyword=0;
							$GROUP_KEYWORD=array();
							if (!empty($CHANGE['Modified']['AdGroupIds'])) {
								//Отбираем уже синхронизированные группы//
								if ($this->debug) print_r($CHANGE['Modified']['AdGroupIds']);
								$result_group=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['group']."` WHERE `company`='".(int)$CAMPAIGN_ONE."' AND `time` IS NOT NULL AND `time`>'".$timestamp_campaign."'");
								$GROUP_DIFF=array();
								while($GROUP_ROW=$this->Framework->db->get($result_group)) 
									if (!empty($GROUP_ROW['id']))
										$GROUP_DIFF[$GROUP_ROW['id']]=$GROUP_ROW['id'];
								if (!empty($GROUP_DIFF))
									$CHANGE['Modified']['AdGroupIds']=array_values(array_diff($CHANGE['Modified']['AdGroupIds'], $GROUP_DIFF));
								if ($this->debug) 'Кол-во групп в запросе фраз='.$this->Framework->api->yandex->direct->keyword->group;
								//\Отбираем уже синхронизированные группы//
								$GROUPS_CHUNK=array_chunk($CHANGE['Modified']['AdGroupIds'], min($this->sinchronize, $this->Framework->api->yandex->direct->keyword->group));
								if ($this->debug) {$time_api=0; $time_sql=0;}
								foreach ($GROUPS_CHUNK as &$GROUP_CHUNK) {
									//Помечаем на удаление//
									/*$delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `delete`=1 WHERE `user`='".(int)$VALUE['id']."'".' AND (';
									$delete_sql='';
									foreach($GROUP_CHUNK as &$value) {
										$value=preg_replace('[^0-9]', '', (string)$value);
										$delete_sql_postfix=(empty($delete_sql)?$delete_sql_prefix:" OR ")."`group`='".(string)$value."'";
										if (strlen($delete_sql.$delete_sql_postfix)>49999) {
											$delete_sql.=')';
											$this->Framework->db->set($delete_sql);
											$delete_sql=$delete_sql_prefix."`id`='".(string)$value."'";
										} else
											$delete_sql.=$delete_sql_postfix;
									}
									if ($delete_sql) {
										$delete_sql.=')';
										$this->Framework->db->set($delete_sql);
									}*/
									//\Помечаем на удаление//
									//Помечаем на удаление//
									$delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `delete`=1 WHERE `user`='".(int)$VALUE['id']."'".' AND (';
									$delete_sql='';
									foreach($GROUP_CHUNK as &$value) {
										$value=preg_replace('[^0-9]', '', (string)$value);
										$delete_sql_postfix=(empty($delete_sql)?$delete_sql_prefix:" OR ")."`id`='".(string)$value."'";
										if (strlen($delete_sql.$delete_sql_postfix)>49999) {
											$delete_sql.=')';
											$this->Framework->db->set($delete_sql);
											$delete_sql=$delete_sql_prefix."`id`='".(string)$value."'";
										} else
											$delete_sql.=$delete_sql_postfix;
									}
									if ($delete_sql) {
										$delete_sql.=')';
										$this->Framework->db->set($delete_sql);
									}
									//\Помечаем на удаление//
									if ($this->debug) $time2=microtime(true);
									$page=0;
									while (true) {
										$KEYWORDS=$this->Framework->api->yandex->direct->keyword->get(array('group'=>$GROUP_CHUNK, 'page'=>$page, 'limit'=>$this->sinchronize,'time'=>$timestamp_campaign));
										if (empty($KEYWORDS)) 
											break;
										if ($this->debug) $time_api+=microtime(true)-$time2;
										//Проверяем лимиты//
										if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
											break;
										//\Проверяем лимиты//
										if ($this->debug) echo '<br>$KEYWORDS=<pre>'.print_r($KEYWORDS, true).'</pre>';
										if ($this->debug) $time3=microtime(true);
										if (!empty($KEYWORDS))
											foreach ($KEYWORDS as &$KEYWORD) {
												$KEYWORD['Id']=(string)$KEYWORD['Id'];
												$KEYWORD['AdGroupId']=(string)$KEYWORD['AdGroupId'];
												$GROUP_KEYWORD[$KEYWORD['AdGroupId']]=isset($GROUP_KEYWORD[$KEYWORD['AdGroupId']])?$GROUP_KEYWORD[$KEYWORD['AdGroupId']]+1:1;
											
												$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
													'id'=>$KEYWORD['Id'],
													'account'=>(int)$LOGIN['id'],
													'user'=>(int)$VALUE['id'], 
													'company'=>(int)$KEYWORD['CampaignId'], 
													'group'=>$KEYWORD['AdGroupId'],
													'banner'=>!empty($GROUP_ADVERT[$KEYWORD['AdGroupId']]['id'])?$GROUP_ADVERT[$KEYWORD['AdGroupId']]['id']:0, 
													'name'=>array_shift(explode(' -', $KEYWORD['Keyword'])),
													'price'=>round($KEYWORD['Bid']/1000000,2),
													'currency'=>!empty($CAMPAIGN_ROW[0]['currency'])?$CAMPAIGN_ROW[0]['currency']:0,
													'context_price'=>round($KEYWORD['ContextBid']/1000000,2),
													'delete'=>0,
													'status'=>($KEYWORD['State'] == 'SUSPENDED'?2:1),
													'datetime'=>date('Y-m-d H:i:s'),
												));
												if ($this->debug) if ($debug_keyword) echo 'KEYWORD=<pre>'.print_r($KEYWORD, true).'</pre>'."<br>\r\n";
												$count_keyword++;
												$count_keywords++;
											}
										if ($this->debug) $time_sql+=microtime(true)-$time3;
										$page++;
										if ($page>=1000) break;
									}
									unset($KEYWORDS);
								}
								unset($GROUPS_CHUNK, $GROUP_CHUNK);
								if ($this->debug) echo 'Keywords save '.$count_keyword.', time='.(time()-$time1).' сек., time api='.round($time_api, 4).' сек., time sql='.round($time_sql, 4).', memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
							}
							//\Фразы//
							
							//Группы//
							$count_group=0;
							if (!empty($CHANGE['Modified']['AdGroupIds'])) {
								if ($this->debug) {echo 'Groups modified '.(!empty($CHANGE['Modified']['AdGroupIds'])?count($CHANGE['Modified']['AdGroupIds']):0).''; $time1=time();}
								//$GROUPS_KEYWORD=array_keys($GROUP_KEYWORD);
								$GROUPS_CHUNK=array_chunk($CHANGE['Modified']['AdGroupIds'], $this->sinchronize);
								if ($this->debug) {$time_api=0; $time_sql=0;}
								foreach ($GROUPS_CHUNK as &$GROUP_CHUNK) {
									if ($this->debug) $time2=microtime(true);
									$GROUPS=$this->Framework->api->yandex->direct->group->get($GROUP_CHUNK);
									if ($this->debug) $time_api+=microtime(true)-$time2;
									//Проверяем лимиты//
									if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
										continue;
									//\Проверяем лимиты//
									//echo '<br>$GROUPS=<pre>'.print_r($GROUPS, true).'</pre>';
									if ($this->debug) $time3=microtime(true);
									foreach ($GROUPS as &$GROUP) {
										$GROUP['Id']=(string)$GROUP['Id'];
										$group_keyword_count=!empty($GROUP_KEYWORD[$GROUP['Id']])?(empty($timestamp_campaign)?$GROUP_KEYWORD[$GROUP['Id']]:($GROUP_KEYWORD[$GROUP['Id']]>1?$GROUP_KEYWORD[$GROUP['Id']]:0)):0;
										/*if ($group_keyword_count<=1) {
											$this->Framework->db->set("SELECT COUNT(*) as `count` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `group`='".$GROUP['Id']."'");
											$GROUP_KEYWORD_ROW=$this->Framework->db->get();
											if (!empty($GROUP_KEYWORD_ROW['count']))
												$group_keyword_count=$GROUP_KEYWORD_ROW['count'];
										}*/
										if ($this->debug) if ($debug_group) echo 'GROUP=<pre>'.print_r($GROUP, true).'</pre>'."<br>\r\n";
										//if (!empty($GROUP_KEYWORD[$GROUP['Id']])) {
										$SAVE=array(
											'id'=>$GROUP['Id'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>(int)$GROUP['CampaignId'],
											'name'=>$GROUP['Name'],
											'currency'=>!empty($CAMPAIGN_ROW[0]['currency'])?$CAMPAIGN_ROW[0]['currency']:0,
											'rarely'=>!empty($GROUP['ServingStatus'])&&$GROUP['ServingStatus']=='RARELY_SERVED'?1:0,
											'delete'=>0,
											'time'=>$timestamp_campaign_new,
										);
										if (!empty($group_keyword_count))
											$SAVE['count']=$group_keyword_count;
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['id']))
											$SAVE['banner']=$GROUP_ADVERT[$GROUP['Id']]['id'];
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['domain']))
											$SAVE['domain']=$GROUP_ADVERT[$GROUP['Id']]['domain'];
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['name']))
											$SAVE['title']=$GROUP_ADVERT[$GROUP['Id']]['name'];
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['body']))
											$SAVE['body']=$GROUP_ADVERT[$GROUP['Id']]['body'];
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['url']))
											$SAVE['url']=$GROUP_ADVERT[$GROUP['Id']]['url'];
										if (!empty($GROUP_ADVERT[$GROUP['Id']]['status']))
											$SAVE['status']=$GROUP_ADVERT[$GROUP['Id']]['status'];
										$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], $SAVE);
										$count_group++;
										$count_groups++;
										//}
										if (isset($GROUP_ADVERT[$GROUP['Id']]))
											unset($GROUP_ADVERT[$GROUP['Id']]);
										if (isset($GROUP_KEYWORD[$GROUP['Id']]))
											unset($GROUP_KEYWORD[$GROUP['Id']]);
									}
									if ($this->debug) $time_sql+=microtime(true)-$time3;
								}
								unset($GROUPS, $GROUPS_CHUNK, $GROUP_CHUNK, $CHANGE['Modified']['AdGroupIds']);
								if ($this->debug) echo ', save='.$count_group.', time='.(time()-$time1).' сек., time api='.round($time_api, 4).' сек., time sql='.round($time_sql, 4).', memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
							}
							//\Группы//
							
							//Ретаргетинг//
							if (!empty($RETARGETING_ADVERTS)) {//if ($this->debug) echo 'RETARGETING_ADVERTS=<pre>'.print_r($RETARGETING_ADVERTS, true).'</pre>';
								//Помечаем на удаление//
								$delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['retargeting']."` SET `delete`=1 WHERE `user`='".(int)$VALUE['id']."'".' AND (';
								$delete_sql='';
								foreach($RETARGETING_ADVERTS as &$value) {
									$value=preg_replace('[^0-9]', '', (string)$value);
									$delete_sql_postfix=(empty($delete_sql)?$delete_sql_prefix:" OR ")."`banner`='".(string)$value."'";
									if (strlen($delete_sql.$delete_sql_postfix)>49999) {
										$delete_sql.=')';
										$this->Framework->db->set($delete_sql);
										$delete_sql=$delete_sql_prefix."`id`='".(string)$value."'";
									} else
										$delete_sql.=$delete_sql_postfix;
								}
								if ($delete_sql) {
									$delete_sql.=')';
									$this->Framework->db->set($delete_sql);
								}
								//\Помечаем на удаление//
								$RETARGETINGS_CHUNK=array_chunk($RETARGETING_ADVERTS, $this->Framework->direct->model->retargeting->limit);
								foreach ($RETARGETINGS_CHUNK as $RETARGETING_CHUNK) {
								
									$RETARGETINGS=$this->Framework->direct->model->retargeting->get(array('login'=>$VALUE['Login'], 'banner'=>$RETARGETING_CHUNK, 'currency'=>(!empty($VALUE['CampaignCurrency'])?$VALUE['CampaignCurrency']:0)));
									if (!empty($this->debug)) if (!empty($RETARGETINGS)) echo 'retargeting get '.count($RETARGETINGS).', memory='.memory_get_usage(true).', time='.date('H:i:s d.m.Y');
			
									if (!empty($RETARGETINGS)) {
										foreach ($RETARGETINGS as $RETARGETING) {
											$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['retargeting'], array(
												'id'=>$RETARGETING['RetargetingID'],
												'account'=>(int)$LOGIN['id'],
												'user'=>(int)$VALUE['id'], 
												'company'=>$CAMPAIGN_ONE, 
												'group'=>(!empty($RETARGETING['AdGroupID'])?$RETARGETING['AdGroupID']:0),
												'banner'=>(!empty($RETARGETING['AdID'])?$RETARGETING['AdID']:0),
												'name'=>'Ретаргетинг: '.$RETARGETING['RetargetingID'],
												'delete'=>0,
												'status'=>($RETARGETING['StatusPaused'] == 'No'?1:2),
												'time'=>date('Y-m-d H:i:s'),
											));
											$count_retargetings++;
										}
									}
									unset($RETARGETINGS);
								}
								unset($RETARGETING_ADVERTS, $RETARGETINGS_CHUNK, $RETARGETING_CHUNK);
								if ($this->debug) echo ', save='.$count_retargetings.', currency='.(!empty($VALUE['CampaignCurrency'])?$VALUE['CampaignCurrency']:0)."<br>\r\n";
							}
							//\Ретаргетинг//
							if ($this->debug) echo $CAMPAIGN_ONE.' campaign_error='.$campaign_error.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
							
							$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id` AND `t2`.`status`=1) SET `t1`.`banner`=IF(`t2`.`id`>0, `t2`.`id`, 0), `t1`.`status`=IF(`t2`.`status`=1, 1, 2) WHERE `t1`.`company`='".(int)$CAMPAIGN_ONE."'");
							
							if ($this->Framework->library->error->count()==$campaign_error) {
								if ($this->debug) $time1=time();
								$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`='".date('Y-m-d H:i:s', strtotime($timestamp_new))."' WHERE `id`='".(int)$CAMPAIGN_ONE."'");
								//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `delete`=0, `time`='".date('Y-m-d H:i:s', strtotime($timestamp_new))."' WHERE `id`='".(int)$CAMPAIGN_ONE."'");						
								$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['group']."` WHERE (`delete`>0 AND `time` IS NOT NULL) AND `user`='".(int)$VALUE['id']."' AND `company`='".(int)$CAMPAIGN_ONE."'");
								$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE (`delete`>0 AND `time` IS NOT NULL) AND `user`='".(int)$VALUE['id']."' AND `company`='".(int)$CAMPAIGN_ONE."'");
								$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."' AND `company`='".(int)$CAMPAIGN_ONE."'");
								$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."' AND `company`='".(int)$CAMPAIGN_ONE."'");
								if ($this->debug) echo 'Update company data "'.$CAMPAIGN_ONE.'", timestamp='.$CHANGE['Timestamp'].', time='.(time()-$time1).' сек., memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
							}
						}//\foreach
					}
					
					//Записываем время последних изменений в кампании//
					$campaign_children_error=$this->Framework->library->error->count()-$campaign_children_error;
					if (!empty($timestamp_new) && !$campaign_children_error) {
						$time=strtotime($timestamp_new);
						if ($this->debug) $time1=time();
						$this->Framework->db->set("UPDATE `".$this->Framework->user->model->model->TABLE[0]."` SET `timestamp`='".date('Y-m-d H:i:s', $time)."' WHERE `id`='".(int)$VALUE['id']."'");		
						$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE (`delete`>0 AND `time` IS NOT NULL) AND `user`='".(int)$VALUE['id']."'");
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) SET `t1`.`banner`=`t2`.`id` WHERE `t1`.`banner`=0 AND `t1`.`user`='".(int)$VALUE['id']."'");
						$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) WHERE ((`t1`.`banner`=0 OR `t2`.`group` is NULL) AND `t1`.`time` IS NOT NULL) AND `t1`.`user`='".(int)$VALUE['id']."'");
						$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE (`t2`.`id` is NULL) AND `t1`.`user`='".(int)$VALUE['id']."'");
						if ($this->debug) echo 'Update user data "'.$VALUE['Login'].'", timestamp='.$timestamp_new.', time='.(time()-$time1).' сек., memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
					} elseif ($this->debug) echo 'Campaign children Error='.$campaign_children_error.', timestamp='.$timestamp_new.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
					//\Записываем время последних изменений в кампании//
					unset($CHANGE, $GROUP_ADVERT, $GROUP_KEYWORD, $RETARGETING_ADVERTS);
					//Записываем лимиты//
					$this->Framework->direct->limit->set(array('id'=>$VALUE['id'], 'login'=>$VALUE['Login'], 'total'=>$this->Framework->api->yandex->direct->config->daily, 'unit'=>$this->Framework->api->yandex->direct->config->limit, 'percent'=>$CONFIG['api_percent']));
					//\Записываем лимиты//
				}
			}
			//\Синхронизируем кампании клиентов//
		}
		
		if ($this->debug) $time1=time();
		//Удаляем неактуальные данные//
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`) LEFT JOIN `".$this->Framework->direct->model->config->TABLE['retargeting']."` `t3` ON (`t3`.`company`=`t1`.`id`) WHERE (`t1`.`id` IS NOT NULL AND `t2`.`id` is NULL AND `t3`.`id` is NULL)");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`group`=`t1`.`id`) LEFT JOIN `".$this->Framework->direct->model->config->TABLE['retargeting']."` `t3` ON (`t3`.`group`=`t1`.`id`) WHERE (`t1`.`time` IS NOT NULL AND `t2`.`id` IS NULL AND `t3`.`id` is NULL)");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) WHERE `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL");
		
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` WHERE `t1`.`group`=0 OR `t1`.`company`=0");//`t1`.`banner`=0 OR 
		
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) WHERE `t2`.`id` is NULL");
	
		//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`) SET `t1`.`time`=NULL WHERE `t2`.`id` is NULL");	
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) SET `t1`.`plan`=0 WHERE `t2`.`status`!=1 OR `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->user->model->model->TABLE[0]."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`parent`) WHERE `t1`.`parent`>0 AND `t2`.`id` is NULL");
		//\Удаляем неактуальные данные//
		
		//Объединям теги для баннеров с группами//
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`group`=`t2`.`group`");
		$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag_banner']."` WHERE `group`=0");
		//\Объединям теги для баннеров с группами//
		
		//Вычисляем заранее данные//
		$this->Framework->db->set("UPDATE `".$this->Framework->user->model->param->TABLE[0]."` `t1`, (SELECT `t2`.`account` as `account`, SUM(`t1`.`unit`) as `unit`, MIN(`t1`.`unit_status`) as `unit_status`, SUM(`t1`.`unit_total`) as `unit_total` FROM `".$this->Framework->user->model->param->TABLE[0]."` `t1` INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) WHERE `t2`.`group`=2 GROUP BY `t2`.`account`) `t2` SET `t1`.`unit`=`t2`.`unit`, `t1`.`unit_total`=`t2`.`unit_total`, `t1`.`unit_status`=`t2`.`unit_status` WHERE `t1`.`user`=`t2`.`account`");
		$this->Framework->db->set("UPDATE `".$this->Framework->user->model->param->TABLE[0]."` `t1`, (SELECT `t2`.`parent` as `parent`, SUM(`t1`.`unit`) as `unit`, SUM(`t1`.`unit_total`) as `unit_total` FROM `".$this->Framework->user->model->param->TABLE[0]."` `t1` INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) WHERE `t2`.`group`=2 GROUP BY `t2`.`parent`) `t2` SET `t1`.`unit`=`t2`.`unit`, `t1`.`unit_total`=`t2`.`unit_total` WHERE `t1`.`user`=`t2`.`parent`");
		//\Вычисляем заранее данные//
		
		if ($this->debug) echo 'Delete time='.(time()-$time1).' сек.'."<br>\r\n";
		
		$LIMIT=$this->Framework->api->yandex->direct->query->limit();
		if ($this->debug) echo 'Sinchronize campaign='.$count_campaigns.', group='.$count_groups.', advert='.$count_adverts.', keyword='.$count_keywords.', retargeting='.$count_retargetings.', LIMIT unit='.$LIMIT['unit'].', limit='.$LIMIT['limit'].', daily='.$LIMIT['daily'].', время запросов к АПИ4+5='.($this->Framework->direct->model->api->time()+$this->Framework->api->yandex->direct->query->time()).', время запросов SQL='.$this->Framework->db->time().', memory='.$this->Framework->library->lib->mb(memory_get_peak_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
		//Определяем позицию через Яндекс.XML//
		$this->Framework->direct->model->position->get();
		//\Определяем позицию через Яндекс.XML//
		
		if ($this->debug) if ((int)$this->Framework->CONFIG['DEBUG']['all']>0) $this->Framework->library->error->set('Sinchronize campaign='.$count_campaigns.', group='.$count_groups.', advert='.$count_adverts.', keyword='.$count_keywords.', retargeting='.$count_retargetings.', LIMIT unit='.$LIMIT['unit'].', limit='.$LIMIT['limit'].', daily='.$LIMIT['daily'].', время запросов к АПИ4+5='.($this->Framework->direct->model->api->time()+$this->Framework->api->yandex->direct->query->time()).', время запросов SQL='.$this->Framework->db->time().', memory='.$this->Framework->library->lib->mb(memory_get_peak_usage(true)).'Mb', '', '', '', '', '');
		
		//Удаляем старые ошибки//
		$this->Framework->model->error->delete(array('datetime'=>array('<='=>$this->Framework->library->time->day(!empty($this->Framework->model->config->CONFIG['error_day'])?-$this->Framework->model->config->CONFIG['error_day']:-1))));
		$this->Framework->model->error->delete(array(), array('id'), !empty($this->Framework->model->config->CONFIG['error_number'])?-$this->Framework->model->config->CONFIG['error_number']:-10000);
		//\Удаляем старые ошибки//
		return null;
	}
	
	
	
}//\class
?>