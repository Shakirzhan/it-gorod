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
		
		//Чиним сломавшиеся таблицы//
		$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		//Получаем курсы валют//
		$CURRENCY=$this->Framework->direct->model->currency->get();
		//\Получаем курсы валют//
		
		$DELETE=array('COMPANY'=>array(), 'BANNER'=>array(), 'PHRASE'=>array());
		
		//Получаем список аккаунтов//
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
		
		foreach ($USERS as $LOGIN) {
			//Устанавливаем авторизационные данные//
			$this->Framework->direct->model->config->login=(string)$LOGIN['login'];//АПИ 4
			$this->Framework->direct->model->config->token=(string)$LOGIN['token'];//АПИ 4
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
						$VALUE['id']=$this->Framework->user->model->model->set(array(
							'id'=>(!empty($USER['ELEMENT'][0]['id'])?$USER['ELEMENT'][0]['id']:null),
							'login'=>$VALUE['Login'], 
							'parent'=>$LOGIN['id'],
							'group'=>2, 
							'right'=>0,
							'name'=>$VALUE['FIO'], 
							'password'=>$this->Framework->library->lib->password(),
							'email'=>$VALUE['Email'],
							'phone'=>$VALUE['Phone'],
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
											'group'=>3, 
											'right'=>1,
											'name'=>$INFO['FIO'], 
											'password'=>$this->Framework->library->lib->password(),
											'email'=>$INFO['Email'],
											'phone'=>$INFO['Phone'],
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
				$CLIENT=array(array('id'=>$LOGIN['id'], 'Login'=>''));	
			}
			
			if (!empty($USERS_STOP['ID'])) {
				$where_delete=" `account` NOT IN (".implode(',',$USERS_STOP['ID']).") ";
				unset($USERS_STOP);
			}
			
			if ($this->Framework->library->error->count()==0) {
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `delete`=1 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($where_delete)?' AND '.$where_delete:''));
			}
			//\Синхронизируем клиентов//
			if ($this->debug) echo "sinc company<br>\r\n";
			
			
			
			foreach ($CLIENT as &$VALUE) {
				//Устанавливаем авторизационные данные//
				$this->Framework->api->yandex->direct->config->login=(string)$VALUE['Login'];//АПИ 5
				//\Устанавливаем авторизационные данные//
				$COMPANY=array();
				//Синхронизируем кампании//
				$COMPANIES=$this->Framework->direct->model->company->get($VALUE['Login']);				
				foreach ($COMPANIES as $key=>&$COMP) {
					
					if ($COMP['StatusArchive'] == 'No') {
						if (empty($COMP['CampaignCurrency']))
							$COMP['CampaignCurrency']='';
							
						$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
							'id'=>$COMP['CampaignID'],
							'account'=>(int)$LOGIN['id'], 
							'user'=>(int)$VALUE['id'], 
							'name'=>$COMP['Name'],
							'strategy_name'=>$COMP['StrategyName'],
							'context_strategy_name'=>$COMP['ContextStrategyName'],
							'price'=>$COMP['Rest'],
							//'show_avg'=>(int)$COMP['Shows'],
							//'click_avg'=>(int)$COMP['Clicks'],
							//'ctr_avg'=>round(100*(int)$COMP['Clicks']/((int)$COMP['Shows']>0?(int)$COMP['Shows']:1),2),
							'currency'=>(!empty($COMP['CampaignCurrency'])?(!empty($CURRENCY['KEY'][$COMP['CampaignCurrency']]['id'])?$CURRENCY['KEY'][$COMP['CampaignCurrency']]['id']:0):0),
							'delete'=>0,
							'status'=>($COMP['IsActive'] == 'No'?2:1),
							'date'=>$COMP['StartDate'],
							//'time'=>date('Y-m-d H:i:s'),
						));
						$COMP['user']=(int)$VALUE['id'];
						$COMPANY[$COMP['CampaignID']]=$COMP;
						
					} else
						unset($COMPANIES[$key]);
				}
				if (empty($COMPANIES))
					unset($COMPANIES);
				//\Синхронизируем кампании//
				
				//Синхронизируем группы, объявления и фразы//
				$COMPANIES_CHUNK=array_chunk($COMPANY, $this->Framework->api->yandex->direct->change->company);
				$count_banner=0;
				$count_phrase=0;
				$limit=is_numeric($this->Framework->direct->model->config->CONFIG['sinchronize'])?$this->Framework->direct->model->config->CONFIG['sinchronize']:1;
				foreach ($COMPANIES_CHUNK as $COMPANY_CHUNK) {
					$COMPANY_CHANGE=array();
					foreach ($COMPANY_CHUNK as $VALUE) 
						$COMPANY_CHANGE[]=$VALUE['CampaignID'];
					
					//Метки//
					/*$TAGS=$this->Framework->direct->model->company->tag(array('id'=>$COMPANY_CHANGE));
					if (!empty($TAGS)) {
						foreach ($TAGS as &$TAG) {
							$TAG_DELETE=array();
							foreach ($TAG['Tags'] as &$VAL) {
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
					
					$COMPANY_CHANGE_CHUNK=array_chunk($COMPANY_CHANGE, 10);
					foreach ($COMPANY_CHANGE_CHUNK as &$COMPANY_CHANGE_CHUNK_VALUE) {
						$TAGS=$this->Framework->direct->model->banner->tag(array('company'=>$COMPANY_CHANGE_CHUNK_VALUE));
						if (!empty($TAGS)) {
							foreach ($TAGS as &$TAG) {
								$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag_banner']."` WHERE `banner`='".(int)$TAG['BannerID']."'");
								foreach ($TAG['TagIDS'] as &$val) {
									$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['tag_banner']."` SET 
										`id`='".(int)$val."',
										`banner`='".(int)$TAG['BannerID']."'"
									);
								}
							}
						}
						unset($TAGS);
					}*/
					//\Метки//
						
					$TIME=$this->Framework->direct->company->get(array('id'=>$COMPANY_CHANGE), array('time'), array('time'), array('page'=>0, 'number'=>1));
					
					if (!empty($TIME['ELEMENT'][0]['time']) && $TIME['ELEMENT'][0]['time']!='0000-00-00 00:00:00')
						$time=$TIME['ELEMENT'][0]['time'];
					else
						$time=0;
					
					$CHANGE=$this->Framework->api->yandex->direct->change->get(array('company'=>$COMPANY_CHANGE, 'time'=>$time));
					
					
					//Синхронизируем объявления//	
					//Удаляем отсутствующие объявления//
					$ADVERT_ALL=$this->Framework->direct->banner->get(array('company'=>$COMPANY_CHANGE), array(), array('id'));
					if (!empty($ADVERT_ALL['ID']) && is_array($ADVERT_ALL['ID'])) {
						$ADVERT_CHUNK=array_chunk($ADVERT_ALL['ID'], $this->Framework->api->yandex->direct->change->group);
						foreach ($ADVERT_CHUNK as &$ADVERTS) {
							$CHANGE_ADVERT=$this->Framework->api->yandex->direct->change->get(array('group'=>$ADVERTS, 'time'=>$time));
							if (!empty($CHANGE_ADVERT['Modified']['AdIds']))
								unset($CHANGE_ADVERT['Modified']['AdIds']);
							if (!empty($CHANGE_ADVERT['NotFound']['AdIds'])) {
								$this->Framework->model->api->model->delete($this->Framework->direct->model->config->TABLE['banner'], array('id'=>$CHANGE_ADVERT['NotFound']['AdIds']));
							}
						}
						
					}
					unset($ADVERT_CHUNK, $ADVERTS, $ADVERT);
				
					//\Удаляем отсутствующие объявления//
					
					//Сохраняем изменившиеся объявления//
					if (!empty($CHANGE['Modified']['AdIds'])) {
						$ADVERTS_CHANGE=array_chunk($CHANGE['Modified']['AdIds'], $this->Framework->api->yandex->direct->group->limit);

						//print_r($ADVERT);
						foreach ($ADVERTS_CHANGE as &$ADVERT_CHANGE) {
							$ADVERT_LIMIT=array_chunk($ADVERT_CHANGE, $limit);
							foreach ($ADVERT_LIMIT as &$ADVERTS) {
								//Объявления//
								$ADVERT=$this->Framework->api->yandex->direct->advert->get(array('id'=>$ADVERTS));
								foreach ($ADVERT as &$ROW) { 
									if ($ROW['State']!='ARCHIVED') {
										/*if (!empty($ADVERT_DOMAIN[$ROW['AdGroupId']])) 
											$ADVERT_DOMAIN[$ROW['AdGroupId']]++;
										else
											$ADVERT_DOMAIN[$ROW['AdGroupId']]=1;*/
										$advert=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array(
											'id'=>$ROW['Id'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$COMPANY[$ROW['CampaignId']]['user'], 
											'company'=>(int)$ROW['CampaignId'], 
											'group'=>(int)$ROW['AdGroupId'], 
											'name'=>!empty($ROW['TextAd']['Title'])?$ROW['TextAd']['Title']:'',
											'body'=>!empty($ROW['TextAd']['Text'])?$ROW['TextAd']['Text']:'',
											'url'=>!empty($ROW['TextAd']['Href'])?$ROW['TextAd']['Href']:'',
											'domain'=>!empty($ROW['TextAd']['DisplayDomain'])?$ROW['TextAd']['DisplayDomain']:'',
											'status'=>($ROW['Status'] == 'ACCEPTED' || $ROW['Status'] == 'PREACCEPTED'?1:2),
											'time'=>date('Y-m-d H:i:s'),
										));
									}
								}
								unset($ADVERT);
								//\Объявления//
							}
							
						}
						unset($ADVERTS_CHANGE);						
					}
					//\Сохраняем изменившиеся объявления//
					
					//\Синхронизируем объявления//
					
					//Синхронизируем группы//
					if ($this->debug) echo 'change '.$time."<br>\r\n";
					//Удаляем отсутствующие группы и фразы//
					$GROUP_ALL=$this->Framework->direct->group->get(array('company'=>$COMPANY_CHANGE), array(), array('id'));
					if (!empty($GROUP_ALL['ID']) && is_array($GROUP_ALL['ID'])) {
						$GROUP_CHUNK=array_chunk($GROUP_ALL['ID'], $this->Framework->api->yandex->direct->change->group);
						foreach ($GROUP_CHUNK as &$GROUPS) {
							$CHANGE_GROUP=$this->Framework->api->yandex->direct->change->get(array('group'=>$GROUPS, 'time'=>$time));
							if (!empty($CHANGE_GROUP['Modified']['AdGroupIds']))
								unset($CHANGE_GROUP['Modified']['AdGroupIds']);
							if (!empty($CHANGE_GROUP['NotFound']['AdGroupIds'])) {
								$this->Framework->model->api->model->delete($this->Framework->direct->model->config->TABLE['group'], array('id'=>$CHANGE_GROUP['NotFound']['AdGroupIds']));
								$this->Framework->model->api->model->delete($this->Framework->direct->model->config->TABLE['phrase'], array('group'=>$CHANGE_GROUP['NotFound']['AdGroupIds']));
								$this->Framework->model->api->model->delete($this->Framework->direct->model->config->TABLE['banner'], array('group'=>$CHANGE_GROUP['NotFound']['AdGroupIds']));
							}
						}
						
					}
					unset($GROUP_CHUNK, $GROUPS, $GROUP);
					//\Удаляем отсутствующие группы и фразы//
					
					//Сохраняем изменившиеся группы и фразы//
					if (!empty($CHANGE['Modified']['AdGroupIds'])) {
						$GROUPS_CHANGE=array_chunk($CHANGE['Modified']['AdGroupIds'], $this->Framework->api->yandex->direct->group->limit);

						//print_r($GROUP);
						foreach ($GROUPS_CHANGE as &$GROUP_CHANGE) {
							$GROUP_LIMIT=array_chunk($GROUP_CHANGE, $limit);
							foreach ($GROUP_LIMIT as &$GROUPS) {
								//Фразы//
								$KEYWORD=$this->Framework->api->yandex->direct->keyword->get(array('group'=>$GROUPS));
								$KEYWORD_COUNT=array();
								foreach ($KEYWORD as &$ROW) {
									
									if (!empty($KEYWORD_COUNT[$ROW['AdGroupId']])) 
										$KEYWORD_COUNT[$ROW['AdGroupId']]++;
									else
										$KEYWORD_COUNT[$ROW['AdGroupId']]=1;
									
									$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
										'id'=>$ROW['Id'],
										'account'=>(int)$LOGIN['id'],
										'user'=>(int)$COMPANY[$ROW['CampaignId']]['user'], 
										'company'=>(int)$ROW['CampaignId'], 
										'group'=>$ROW['AdGroupId'], 
										'name'=>$ROW['Keyword'],
										'price'=>$ROW['Bid'],
										'context_price'=>!empty($ROW['ContextBid'])?$ROW['ContextBid']:0,
										'productivity'=>!empty($ROW['Productivity']['Value'])?$ROW['Productivity']['Value']:0,
										'status'=>$ROW['State']=='ON'?1:($ROW['State']=='SUSPENDED'?2:0),
									));
								}
								unset($KEYWORD);
								//\Фразы//
								
								//Группы//
								$GROUP=$this->Framework->api->yandex->direct->group->get(array('id'=>$GROUPS));
								foreach ($GROUP as &$ROW) {
									$group=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], array(
										'id'=>$ROW['Id'],
										'account'=>(int)$LOGIN['id'],
										'user'=>(int)$COMPANY[$ROW['CampaignId']]['user'], 
										'company'=>(int)$ROW['CampaignId'], 
										'name'=>$ROW['Name'],
										//'domain'=>'',
										'count'=>!empty($KEYWORD_COUNT[$ROW['Id']])?$KEYWORD_COUNT[$ROW['Id']]:0,
										'status'=>($ROW['Status'] == 'ACCEPTED' || $ROW['Status'] == 'PREACCEPTED'?1:2),
										'time'=>date('Y-m-d H:i:s'),
									));
								}
								unset($GROUP, $KEYWORD_COUNT);
								//\Группы//
							}
							
						}
						unset($GROUPS_CHANGE);						
					}
					//\Сохраняем изменившиеся группы и фразы//
					
					//\Синхронизируем группы//
					
	
					//Записываем время последних изменений в кампании//
					if (!empty($CHANGE['Timestamp'])) {
						$time=strtotime($CHANGE['Timestamp']);
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`='".date('Y-m-d H:i:s', $time)."' WHERE `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`id`='".implode("' OR `id`='", $COMPANY_CHANGE)."') ":''));
					}
					unset($CHANGE);
					//\Записываем время последних изменений в кампании//
					
				}//\foreach
				//\Синхронизируем группы, объявления и фразы//
			}//\foreach CLIENT
		}//\foreach USERS
		
		//Удаляем неактуальные данные//
		if ($this->Framework->library->error->count()==0) {
			if ($this->debug) echo "delete<br>\r\n";
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL");
			//$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			//$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL");
			//$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
		}
		//\Удаляем неактуальные данные//
		
		
		
		
		//Определяем позицию через Яндекс.XML//
		$this->Framework->direct->model->position->get();
		//\Определяем позицию через Яндекс.XML//
		
		//Удаляем старые ошибки//
			$this->Framework->model->error->delete(array('datetime'=>array('<='=>$this->Framework->library->time->day(!empty($this->Framework->model->config->CONFIG['error_day'])?-$this->Framework->model->config->CONFIG['error_day']:-1))));
			$this->Framework->model->error->delete(array(), array('id'), -10000);
		//\Удаляем старые ошибки//
		return null;
	}
	
	
	
}//\class
?>