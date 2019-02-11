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
		if ($this->debug) echo date('H:i:s d.m.Y')." check and repair tables<br>\r\n";
		//Чиним сломавшиеся таблицы//
		$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		//Конфигурация//
		$CONFIG=$this->Framework->direct->model->config->CONFIG;
		$this->microsecond=!empty($CONFIG['microsecond'])?$CONFIG['microsecond']:$this->microsecond;
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
				//Проверяем лимиты//
				if ( $VALUE['unit_status'] && !empty($VALUE['unit_time']) && strtotime($VALUE['unit_time'])>=mktime(0, $this->Framework->direct->model->config->time_yandex, 1, date('m'), date('d'), date('Y')) ) {
					if ($this->debug) echo 'LIMIT API5';
					
					if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'], 'login'=>$VALUE['Login'], 'total'=>$VALUE['unit_total'], 'unit'=>$VALUE['unit'], 'percent'=>$CONFIG['api_percent'])))
						continue;
				}
				//\Проверяем лимиты//
				
				//Создаем временные таблицы//
				$table_sinchronize_advert=$this->Framework->direct->model->config->TABLE['sinchronize_advert'].(int)$VALUE['id'];
				$this->Framework->db->set("DROP TABLE IF EXISTS `".$this->Framework->direct->model->config->TABLE['sinchronize_advert'].(int)$VALUE['id']."`");
				$this->Framework->db->set("CREATE TABLE `".$table_sinchronize_advert."` LIKE `".$this->Framework->direct->model->config->TABLE['sinchronize_advert']."`");
				$table_sinchronize_keyword=$this->Framework->direct->model->config->TABLE['sinchronize_keyword'].(int)$VALUE['id'];
				$this->Framework->db->set("DROP TABLE IF EXISTS `".$this->Framework->direct->model->config->TABLE['sinchronize_keyword'].(int)$VALUE['id']."`");
				$this->Framework->db->set("CREATE TABLE `".$table_sinchronize_keyword."` LIKE `".$this->Framework->direct->model->config->TABLE['sinchronize_keyword']."`");
				$table_sinchronize_group=$this->Framework->direct->model->config->TABLE['sinchronize_group'].(int)$VALUE['id'];
				$this->Framework->db->set("DROP TABLE IF EXISTS `".$this->Framework->direct->model->config->TABLE['sinchronize_group'].(int)$VALUE['id']."`");
				$this->Framework->db->set("CREATE TABLE `".$table_sinchronize_group."` LIKE `".$this->Framework->direct->model->config->TABLE['sinchronize_group']."`");
				//\Создаем временные таблицы//
				
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
								$CAMPAIGN_CHILDREN[]=$CAMPAIGN['CampaignId'];
						}
						
				//$CAMPAIGN_SELF=$CAMPAIGN_CHILDREN=array(21840674);$timestamp=null;//Диагностика
				$VALUE['timestamp']=date('Y-m-d H:i:s', strtotime($CHANGE['Timestamp']));
				unset($CHANGE);
				if (!empty($CAMPAIGN_SELF)) {
					$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `delete`=1 WHERE (`id`='".implode("' OR `id`='", $CAMPAIGN_SELF)."') AND `user`='".(int)$VALUE['id']."'");
					$COMPANIES[$VALUE['id']]=$this->Framework->api->yandex->direct->campaign->get($CAMPAIGN_SELF);
					//Проверяем лимиты//
					if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
						continue;
					//\Проверяем лимиты//
					if ($this->debug) echo 'API5 campaign get '.$VALUE['Login'].', timestamp='.$VALUE['timestamp'].', time='.(time()-$time1).' сек.'."<br>\r\n";
					$count_campaign=0;
					if ($this->debug) $time1=time();		
					if (!empty($COMPANIES[$VALUE['id']]))
						foreach ($COMPANIES[$VALUE['id']] as $key=>&$COMP) {
							
							$COMP=array(
								'CampaignCurrency' => $COMP['Currency'],
								'Sum' => round((!empty($COMP['Funds']['CampaignFunds']['Sum'])?$COMP['Funds']['CampaignFunds']['Sum']:(!empty($COMP['Funds']['SharedAccountFunds']['Spend'])?$COMP['Funds']['SharedAccountFunds']['Spend']:0))/1000000, 2),
								//'AgencyName' => Интернет-коммуникации
								//'EnableRelatedKeywords' => Yes
								//'SumAvailableForTransfer' => 
								'IsActive' => $COMP['State']=='ON'?'Yes':'No',
								'Login' => $VALUE['Login'],
								'Shows' => $COMP['Statistics']['Impressions'],
								'Status' => $COMP['StatusClarification'],
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
								//'ManagerName' => 
								'ContextStrategyName' => $COMP['TextCampaign']['BiddingStrategy']['Network']['BiddingStrategyType'],
								'StartDate' => $COMP['StartDate'],
								'Rest' => round((!empty($COMP['Funds']['CampaignFunds']['Balance'])?$COMP['Funds']['CampaignFunds']['Balance']:0)/1000000, 2),
							);
							
							if (empty($COMP['CampaignCurrency']))
								$COMP['CampaignCurrency']='';
								
							$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
								'id'=>$COMP['CampaignID'],
								'account'=>(int)$LOGIN['id'], 
								'user'=>(int)$VALUE['id'], 
								'name'=>$COMP['Name'],
								'strategy_name'=>$COMP['StrategyName'],
								'context_strategy_name'=>$COMP['ContextStrategyName'],
								'types'=>$COMP['Type'],
								'price'=>$COMP['Rest'],
								'show28'=>(int)$COMP['Shows'],
								'click28'=>(int)$COMP['Clicks'],
								'ctr28'=>round(100*(int)$COMP['Clicks']/((int)$COMP['Shows']>0?(int)$COMP['Shows']:1),2),
								'currency'=>(!empty($COMP['CampaignCurrency'])?(!empty($CURRENCY['KEY'][$COMP['CampaignCurrency']]['id'])?$CURRENCY['KEY'][$COMP['CampaignCurrency']]['id']:0):0),
								'delete'=>0,
								'status'=>1,//$COMP['IsActive']=='Yes' && mb_ereg_match('.*Идут показы.*', $COMP['Status'], 'i')?1:2,
								'date'=>$COMP['StartDate'],
								'time'=>date('Y-m-d H:i:s'),
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
								$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['currency']."` `t2` ON (`t2`.`id`=`t1`.`currency` AND `t1`.`currency`>0) SET `t1`.`price`=ROUND(".round($CAMPAIGN_BALANCE['Rest'], 2)."*`t2`.`value`/(1+`t2`.`tax`/100),2) WHERE `t1`.`user`='".(int)$VALUE['id']."' AND `t1`.`id`='".(int)$CAMPAIGN_BALANCE['CampaignID']."'");
				}
				//\Получаем баланс для текстово-графических кампаний через АПИ4//
				if (!empty($CAMPAIGN_CHILDREN))
					$CAMPAIGN_TEXT_CAMPAIGN=$CAMPAIGN_CHILDREN=array_values(array_intersect($CAMPAIGN_CHILDREN, $CAMPAIGN_TEXT_CAMPAIGN));
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
					
					
					foreach($CAMPAIGN_CHILDREN as $CAMPAIGN_ONE) {
						//Проверяем изменения у групп и объявлений//
						if ($this->debug) {echo 'Change get campaign='.$CAMPAIGN_ONE.''; $time1=time(); $time0=time();}
						$CHANGE=$this->Framework->api->yandex->direct->change->get(array('company'=>$CAMPAIGN_ONE, 'time'=>$timestamp));//$CHANGE['Unprocessed']['CampaignIds']
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
							$ADVERTS_CHUNK=array_chunk($CHANGE['Modified']['AdIds'], $this->sinchronize);
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
								
								$ADVERTS=$this->Framework->api->yandex->direct->advert->get($ADVERT_CHUNK);
								//Проверяем лимиты//
								if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
									continue;
								//\Проверяем лимиты//
								//echo '<br>$ADVERTS=<pre>'.print_r($ADVERTS, true).'</pre>';
								foreach ($ADVERTS as &$ADVERT) {
									$ADVERT['Id']=(string)$ADVERT['Id'];
									$ADVERT['AdGroupId']=(string)$ADVERT['AdGroupId'];
									$RETARGETING_ADVERTS[]=$ADVERT['Id'];
									$name=!empty($ADVERT['TextAd']['Title'])?$ADVERT['TextAd']['Title']:(!empty($ADVERT['TextImageAd']['AdImageHash'])?$ADVERT['TextImageAd']['AdImageHash']:(!empty($ADVERT['MobileAppImageAd']['AdImageHash'])?$ADVERT['MobileAppImageAd']['AdImageHash']:(!empty($ADVERT['MobileAppAd']['Title'])?$ADVERT['MobileAppAd']['Title']:(!empty($ADVERT['DynamicTextAd']['Text'])?$ADVERT['DynamicTextAd']['Text']:'Неизвестное объявление'))));
									$body=!empty($ADVERT['TextAd']['Text'])?$ADVERT['TextAd']['Text']:(!empty($ADVERT['TextImageAd']['AdImageHash'])?'':(!empty($ADVERT['MobileAppImageAd']['AdImageHash'])?'':(!empty($ADVERT['MobileAppAd']['Text'])?$ADVERT['MobileAppAd']['Text']:(!empty($ADVERT['DynamicTextAd']['Text'])?$ADVERT['DynamicTextAd']['Text']:''))));
									$url=!empty($ADVERT['TextAd']['Href'])?$ADVERT['TextAd']['Href']:(!empty($ADVERT['TextImageAd']['Href'])?$ADVERT['TextImageAd']['Href']:(!empty($ADVERT['MobileAppImageAd']['TrackingUrl'])?$ADVERT['MobileAppImageAd']['TrackingUrl']:(!empty($ADVERT['MobileAppAd']['TrackingUrl'])?$ADVERT['MobileAppAd']['TrackingUrl']:'')));
									if (!isset($GROUP_ADVERT[$ADVERT['AdGroupId']]) || $ADVERT['State']=='ON')
										$GROUP_ADVERT[$ADVERT['AdGroupId']]=array('id'=>$ADVERT['Id'], 'domain'=>$ADVERT['TextAd']['DisplayDomain'], 'name'=>$name, 'body'=>$body, 'url'=>$url, 'status'=>($ADVERT['State'] == 'ON'?1:2));					
									$id=$this->Framework->library->model->set($table_sinchronize_advert, array(
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
										'status'=>($ADVERT['State'] == 'ON'?1:2),
										'time'=>date('Y-m-d H:i:s'),
									), '', false, true);
									//if ($ADVERT['State'] == 'ON') 
										//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."`SET `status`=1 WHERE `id`='".$ADVERT['AdGroupId']."'");
									//else
										//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id` AND `t2`.`status`=1) SET `t1`.`status`=IF(`t2`.`status`=1, 1, 2) WHERE `t1`.`id`='".$ADVERT['AdGroupId']."'");
									$count_advert++;
									$count_adverts++;
								}
							}
							unset($ADVERTS, $ADVERT, $ADVERTS_CHUNK, $ADVERT_CHUNK, $CHANGE['Modified']['AdIds']);
						}
						if ($this->debug) echo ', save '.$count_advert.', time='.(time()-$time1).' сек., memory='.memory_get_usage(true)."<br>\r\n";
						//\Объявления//
						
						//Фразы//
						if ($this->debug) $time1=time();
						$count_keyword=0;
						$GROUP_KEYWORD=array();
						if (!empty($CHANGE['Modified']['AdGroupIds'])) {
							$GROUPS_CHUNK=array_chunk($CHANGE['Modified']['AdGroupIds'], min($this->sinchronize, $this->Framework->api->yandex->direct->keyword->group));
							
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
								$KEYWORDS=$this->Framework->api->yandex->direct->keyword->get(array('group'=>$GROUP_CHUNK, 'time'=>$timestamp));
								if ($this->debug) $time2=round(microtime(true)-$time2, 4);
								//Проверяем лимиты//
								if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
									continue;
								//\Проверяем лимиты//
								//echo '<br>$KEYWORDS=<pre>'.print_r($KEYWORDS, true).'</pre>';
								if ($this->debug) $time3=microtime(true);
								if (!empty($KEYWORDS))
									foreach ($KEYWORDS as &$KEYWORD) {
										$KEYWORD['Id']=(string)$KEYWORD['Id'];
										$KEYWORD['AdGroupId']=(string)$KEYWORD['AdGroupId'];
										$GROUP_KEYWORD[$KEYWORD['AdGroupId']]=isset($GROUP_KEYWORD[$KEYWORD['AdGroupId']])?$GROUP_KEYWORD[$KEYWORD['AdGroupId']]+1:1;
									
										$id=$this->Framework->library->model->set($table_sinchronize_keyword, array(
											'id'=>$KEYWORD['Id'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>(int)$KEYWORD['CampaignId'], 
											'group'=>$KEYWORD['AdGroupId'],
											'banner'=>!empty($GROUP_ADVERT[$KEYWORD['AdGroupId']]['id'])?$GROUP_ADVERT[$KEYWORD['AdGroupId']]['id']:0, 
											'name'=>$KEYWORD['Keyword'],
											'price'=>round($KEYWORD['Bid']/1000000,2),
											'context_price'=>round($KEYWORD['ContextBid']/1000000,2),
											'delete'=>0,
											'status'=>($KEYWORD['State'] == 'ON'?1:2),
											'datetime'=>date('Y-m-d H:i:s'),
										), '', false, true);
										$count_keyword++;
										$count_keywords++;
									}
								if ($this->debug) $time3=round(microtime(true)-$time3, 4);
							}
							unset($GROUPS_CHUNK, $GROUP_CHUNK, $KEYWORDS, $CHANGE['Modified']['AdGroupIds']);
							if ($this->debug) echo 'Keywords save '.$count_keyword.', time='.(time()-$time1).' сек., time api='.($time2).' сек., time sql='.($time3).', memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
						}
						//\Фразы//
						
						//Группы//
						$count_group=0;
						if (!empty($GROUP_KEYWORD)) {if ($this->debug) {echo 'Groups modified '.(!empty($GROUP_KEYWORD)?count($GROUP_KEYWORD):0).''; $time1=time();}
							$GROUPS_KEYWORD=array_keys($GROUP_KEYWORD);
							$GROUPS_CHUNK=array_chunk($GROUPS_KEYWORD, $this->sinchronize);
							foreach ($GROUPS_CHUNK as &$GROUP_CHUNK) {
								
								$GROUPS=$this->Framework->api->yandex->direct->group->get($GROUP_CHUNK);
								//Проверяем лимиты//
								if ($this->Framework->direct->limit->get(array('id'=>$VALUE['id'])))
									continue;
								//\Проверяем лимиты//
								//echo '<br>$GROUPS=<pre>'.print_r($GROUPS, true).'</pre>';
								foreach ($GROUPS as &$GROUP) {
									$GROUP['Id']=(string)$GROUP['Id'];
									if (!empty($GROUP_KEYWORD[$GROUP['Id']])) {
										$SAVE=array(
											'id'=>$GROUP['Id'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>(int)$GROUP['CampaignId'],
											'name'=>$GROUP['Name'],
											'count'=>!empty($GROUP_KEYWORD[$GROUP['Id']])?$GROUP_KEYWORD[$GROUP['Id']]:0,
											'delete'=>0,
											'time'=>date('Y-m-d H:i:s'),
										);
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
										$id=$this->Framework->library->model->set($table_sinchronize_group, $SAVE, '', false, true);
										$count_group++;
										$count_groups++;
									}
									if (isset($GROUP_ADVERT[$GROUP['Id']]))
										unset($GROUP_ADVERT[$GROUP['Id']]);
									if (isset($GROUP_KEYWORD[$GROUP['Id']]))
										unset($GROUP_KEYWORD[$GROUP['Id']]);
								}
							}
							unset($GROUPS, $GROUPS_CHUNK, $GROUP_CHUNK);
							if ($this->debug) echo ', save='.$count_group.', time='.(time()-$time1).' сек., memory='.memory_get_usage(true)."<br>\r\n";
						}
						//\Группы//
						
						//Ретаргетинг//
						if (!empty($RETARGETING_ADVERTS)) {
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
							
								$RETARGETINGS=$this->Framework->direct->model->retargeting->get(array('login'=>$VALUE['Login'], 'banner'=>$RETARGETING_CHUNK, 'currency'=>$VALUE['CampaignCurrency']));
								if (!empty($this->debug)) if (!empty($RETARGETINGS)) echo 'retargeting get '.count($RETARGETINGS).', memory='.memory_get_usage(true).' - '.date('H:i:s')."<br>\r\n";
		
								if (!empty($RETARGETINGS)) {
									foreach ($RETARGETINGS as $RETARGETING) {
										$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['retargeting'], array(
											'id'=>$RETARGETING['RetargetingID'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>!empty($RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']])?$RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']]:0, 
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
						}
						//\Ретаргетинг//
						
						//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id` AND `t2`.`status`=1) SET `t1`.`status`=IF(`t2`.`status`=1, 1, 2) WHERE `t1`.`company`='".(int)$CAMPAIGN_ONE."'");
						if ($this->debug) echo 'End sinchronize campaign '.$CAMPAIGN_ONE.', time='.(time()-$time0).' сек., memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
					}//\foreach
				}
				
				//Копируем данные из временных таблиц//
				if ($this->debug) $time1=time();
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` INNER JOIN `".$table_sinchronize_advert."` `t2` ON (`t2`.`id`=`t1`.`id`) SET `t1`.`account`=`t2`.`account`, `t1`.`user`=`t2`.`user`, `t1`.`company`=`t2`.`company`, `t1`.`group`=`t2`.`group`, `t1`.`name`=`t2`.`name`, `t1`.`body`=`t2`.`body`, `t1`.`url`=`t2`.`url`, `t1`.`domain`=`t2`.`domain`, `t1`.`delete`=`t2`.`delete`, `t1`.`status`=`t2`.`status`, `t1`.`variant`=`t2`.`variant`, `t1`.`time`=`t2`.`time`");				
				$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['banner']."` (`id`, `account`, `user`, `company`, `group`, `name`, `body`, `url`, `domain`, `delete`, `status`, `variant`, `time`) SELECT `id`, `account`, `user`, `company`, `group`, `name`, `body`, `url`, `domain`, `delete`, `status`, `variant`, `time` FROM `".$table_sinchronize_advert."` ON DUPLICATE KEY UPDATE `delete`=0");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` INNER JOIN `".$table_sinchronize_keyword."` `t2` ON (`t2`.`id`=`t1`.`id`) SET `t1`.`account`=`t2`.`account`, `t1`.`user`=`t2`.`user`, `t1`.`company`=`t2`.`company`, `t1`.`group`=`t2`.`group`, `t1`.`banner`=`t2`.`banner`, `t1`.`name`=`t2`.`name`, `t1`.`price`=`t2`.`price`, `t1`.`context_price`=`t2`.`context_price`, `t1`.`delete`=`t2`.`delete`, `t1`.`status`=`t2`.`status`, `t1`.`datetime`=`t2`.`datetime`");				
				$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['phrase']."` (`id`, `account`, `user`, `company`, `group`, `banner`, `name`, `price`, `context_price`, `delete`, `status`, `datetime`) SELECT `id`, `account`, `user`, `company`, `group`, `banner`, `name`, `price`, `context_price`, `delete`, `status`, `datetime` FROM `".$table_sinchronize_keyword."` ON DUPLICATE KEY UPDATE `delete`=0");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` INNER JOIN `".$table_sinchronize_group."` `t2` ON (`t2`.`id`=`t1`.`id`) SET `t1`.`account`=`t2`.`account`, `t1`.`user`=`t2`.`user`, `t1`.`company`=`t2`.`company`, `t1`.`banner`=`t2`.`banner`, `t1`.`name`=`t2`.`name`, `t1`.`domain`=`t2`.`domain`, `t1`.`url`=`t2`.`url`, `t1`.`title`=`t2`.`title`, `t1`.`body`=`t2`.`body`, `t1`.`count`=`t2`.`count`, `t1`.`delete`=`t2`.`delete`, `t1`.`status`=`t2`.`status`, `t1`.`time`=`t2`.`time`");				
				$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['group']."` (`id`, `account`, `user`, `company`, `banner`, `name`, `domain`, `url`, `title`, `body`, `count`, `delete`, `status`, `time`) SELECT `id`, `account`, `user`, `company`, `banner`, `name`, `domain`, `url`, `title`, `body`, `count`, `delete`, `status`, `time` FROM `".$table_sinchronize_group."` ON DUPLICATE KEY UPDATE `delete`=0");
				//$this->Framework->db->set("DROP TABLE IF EXISTS `".$this->Framework->direct->model->config->TABLE['sinchronize_advert'].(int)$VALUE['id']."`");
				
				if ($this->debug) echo 'Copy temporary data, time='.(time()-$time1).' сек., memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
				//\Копируем данные из временных таблиц//
				
				//Записываем время последних изменений в кампании//
				if (!empty($CHANGE['Timestamp']) && $this->Framework->library->error->count()==$change_error) {
					if ($this->debug) $time1=time();
					$time=strtotime($CHANGE['Timestamp']);
					$this->Framework->db->set("UPDATE `".$this->Framework->user->model->model->TABLE[0]."` SET `timestamp`='".date('Y-m-d H:i:s', $time)."' WHERE `id`='".(int)$VALUE['id']."'");
					//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`='".date('Y-m-d H:i:s', $time)."' WHERE `account`='".(int)$LOGIN['id']."'".(!empty($CAMPAIGN_CHILDREN)?" AND (`id`='".implode("' OR `id`='", $CAMPAIGN_CHILDREN)."') ":''));
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['group']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` WHERE `delete`>0 AND `user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) WHERE (`t1`.`banner`=0 OR `t2`.`group` is NULL) AND `t1`.`user`='".(int)$VALUE['id']."'");
					$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL AND `t1`.`user`='".(int)$VALUE['id']."'");
					if ($this->debug) echo 'Delete user data "'.$VALUE['Login'].'", timestamp='.$CHANGE['Timestamp'].', time='.(time()-$time1).' сек., memory='.$this->Framework->library->lib->mb(memory_get_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
				}
				//\Записываем время последних изменений в кампании//
				unset($CHANGE, $GROUP_ADVERT, $GROUP_KEYWORD, $RETARGETING_ADVERTS);
				//Записываем лимиты//
				$this->Framework->direct->limit->set(array('id'=>$VALUE['id'], 'login'=>$VALUE['Login'], 'total'=>$this->Framework->api->yandex->direct->config->daily, 'unit'=>$this->Framework->api->yandex->direct->config->limit, 'percent'=>$CONFIG['api_percent']));
				//\Записываем лимиты//
			}
			//\Синхронизируем кампании клиентов//
		}
		
		if ($this->debug) $time1=time();
		//Удаляем неактуальные данные//
		if ($this->Framework->library->error->count()==0) {

			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`) WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL");

		}
		
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`) SET `t1`.`time`=NULL WHERE `t2`.`id` is NULL");	
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) SET `t1`.`plan`=0 WHERE `t2`.`status`!=1 OR `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->user->model->model->TABLE[0]."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`parent`) WHERE `t1`.`parent`>0 AND `t2`.`id` is NULL");
		
		//\Удаляем неактуальные данные//
		
		//Объединям теги для баннеров с группами//
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`group`=`t2`.`group`");
		$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag_banner']."` WHERE `group`=0");
		//\Объединям теги для баннеров с группами//
		
		//Вычисляем заранее данные//
		$this->Framework->db->set("UPDATE `".$this->Framework->user->model->param->TABLE[0]."` `t1`, (SELECT `t2`.`account` as `account`, SUM(`t1`.`unit`) as `unit`, SUM(`t1`.`unit_total`) as `unit_total` FROM `".$this->Framework->user->model->param->TABLE[0]."` `t1` INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) WHERE `t2`.`group`=2 GROUP BY `t2`.`account`) `t2` SET `t1`.`unit`=`t2`.`unit`, `t1`.`unit_total`=`t2`.`unit_total` WHERE `t1`.`user`=`t2`.`account`");
		$this->Framework->db->set("UPDATE `".$this->Framework->user->model->param->TABLE[0]."` `t1`, (SELECT `t2`.`parent` as `parent`, SUM(`t1`.`unit`) as `unit`, SUM(`t1`.`unit_total`) as `unit_total` FROM `".$this->Framework->user->model->param->TABLE[0]."` `t1` INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) WHERE `t2`.`group`=2 GROUP BY `t2`.`parent`) `t2` SET `t1`.`unit`=`t2`.`unit`, `t1`.`unit_total`=`t2`.`unit_total` WHERE `t1`.`user`=`t2`.`parent`");
		//\Вычисляем заранее данные//
		
		if ($this->debug) echo 'Delete time='.(time()-$time1).' сек.'."<br>\r\n";
		
		$LIMIT=$this->Framework->api->yandex->direct->query->limit();
		if ($this->debug) echo 'Sinchronize campaign='.$count_campaigns.', group='.$count_groups.', advert='.$count_adverts.', keyword='.$count_keywords.', retargeting='.$count_retargetings.', LIMIT unit='.$LIMIT['unit'].', limit='.$LIMIT['limit'].', daily='.$LIMIT['daily'].', errors='.$this->Framework->library->error->count().', время запросов к АПИ4+5='.($this->Framework->direct->model->api->time()+$this->Framework->api->yandex->direct->query->time()).', время запросов SQL='.$this->Framework->db->time().', memory='.$this->Framework->library->lib->mb(memory_get_peak_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
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