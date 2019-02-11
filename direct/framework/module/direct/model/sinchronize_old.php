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
		
		//Получаем курсы валют//
		$CURRENCY=$this->Framework->direct->model->currency->get();
		//\Получаем курсы валют//
		
		$CONFIG=$this->Framework->direct->model->config->CONFIG;
		$this->microsecond=!empty($CONFIG['microsecond'])?$CONFIG['microsecond']:$this->microsecond;
		
		$DELETE=array('COMPANY'=>array(), 'BANNER'=>array(), 'PHRASE'=>array());
		$count_banners=0;
		$count_phrases=0;
		$count_banners_unknown=0;
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
		if ($this->debug) echo date('H:i:s d.m.Y')." get users<br>\r\n";
		foreach ($USERS as $LOGIN) {
			if ($this->microsecond>0)
				usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
			//Устанавливаем авторизационные данные//
			$this->Framework->direct->model->config->login=(string)$LOGIN['login'];
			$this->Framework->direct->model->config->token=(string)$LOGIN['token'];
			//\Устанавливаем авторизационные данные//
			
			//Синхронизируем клиентов//
			$CLIENT=$this->Framework->direct->model->client->get($LOGIN['login']); 
			if (!empty($CLIENT[0]['Role']) && $CLIENT[0]['Role']!='Client') {
				$CLIENT=$this->Framework->direct->model->clients->get(); 
				$ID=array();
				foreach ($CLIENT as $key=>&$VALUE) {
					if ($VALUE['StatusArch']=='No' && !empty($VALUE['Login'])) {
						if ($this->microsecond>0)
							usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
						$USER=$this->Framework->user->model->model->get(array('login'=>$VALUE['Login']));
						$VALUE['id']=$this->Framework->user->model->model->set(array(
							'id'=>(!empty($USER['ELEMENT'][0]['id'])?$USER['ELEMENT'][0]['id']:null),
							'login'=>$VALUE['Login'], 
							'parent'=>$LOGIN['id'],
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
									if ($this->microsecond>0)
										usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
									$INFO=array_shift($INFO);
									$USER=$this->Framework->user->model->model->get(array('login'=>$INFO['Login']));
									if (empty($USER['ELEMENT'])) {
										$INFO['id']=$this->Framework->user->model->model->set(array(
											'login'=>$INFO['Login'], 
											'parent'=>$LOGIN['id'],
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
			if ($this->debug) echo "sinc company ".memory_get_usage(true)."<br>\r\n";
			//Синхронизируем кампании клиентов//
			$COMPANY=array();
			
			foreach ($CLIENT as &$VALUE) {
				//Получаем не поддерживающиеся баннеры//
				$result_banner=$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `user`='".(int)$VALUE['id']."' AND `variant`='UNKNOWN'");
				$BANNER_UNKNOWN=array();
				while($BANNER_ROW=$this->Framework->db->get($result_banner)) 
					if (!empty($BANNER_ROW['id']))
						$BANNER_UNKNOWN[]=$BANNER_ROW['id'];
				//\Получаем не поддерживающиеся баннеры//
				if ($this->debug) $time1=time();
				$COMPANIES[$VALUE['id']]=$this->Framework->direct->model->company->get($VALUE['Login']);
				if ($this->debug) echo 'API company get '.$VALUE['Login'].' '.(time()-$time1).' сек.'."<br>\r\n";
				
				if ($this->debug) $time1=time();		
				foreach ($COMPANIES[$VALUE['id']] as $key=>&$COMP) {
					
					if ($COMP['StatusArchive'] == 'No' && $COMP['StatusModerate']!='New') {
						if ($this->microsecond>0)
							usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
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
							'show28'=>(int)$COMP['Shows'],
							'click28'=>(int)$COMP['Clicks'],
							'ctr28'=>round(100*(int)$COMP['Clicks']/((int)$COMP['Shows']>0?(int)$COMP['Shows']:1),2),
							'currency'=>(!empty($COMP['CampaignCurrency'])?(!empty($CURRENCY['KEY'][$COMP['CampaignCurrency']]['id'])?$CURRENCY['KEY'][$COMP['CampaignCurrency']]['id']:0):0),
							'delete'=>0,
							'status'=>$COMP['IsActive']=='Yes' && mb_ereg_match('.*Идут показы.*', $COMP['Status'], 'i')?1:2,
							'date'=>$COMP['StartDate'],
							//'time'=>date('Y-m-d H:i:s'),
						));
						$COMP['user']=(int)$VALUE['id'];
						$COMP['login']=$VALUE['Login'];
						$COMPANY[$COMP['CampaignID']]=$COMP;
						//if ($COMP['CampaignID']==10031819) $this->Framework->library->error->set('Диагностика: '.print_r($COMP, true).'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						
					} else
						unset($COMPANIES[$VALUE['id']][$key]);
				}
				if ($this->debug) echo 'SQL company save '.$VALUE['Login'].' '.(time()-$time1).' сек. '.memory_get_usage(true)."<br>\r\n";
				if (empty($COMPANIES[$VALUE['id']]))
					unset($COMPANIES[$VALUE['id']]);
			}

			//\Синхронизируем кампании клиентов//	
			if ($this->debug) echo "sinc banners<br>\r\n";
			
			//Синхронизируем объявления компаний//
			$COMPANIES_CHUNK=array_chunk($COMPANY, 100);
			$count_banner=0;
			$count_phrase=0;
			$limit=is_numeric($this->Framework->direct->model->config->CONFIG['sinchronize'])&&$this->Framework->direct->model->config->CONFIG['sinchronize']>0?(int)$this->Framework->direct->model->config->CONFIG['sinchronize']:2000;
			foreach ($COMPANIES_CHUNK as $COMPANY_CHUNK) {
				$COMPANY_CHANGE=array();
				$company_change_error=$this->Framework->library->error->count();
				foreach ($COMPANY_CHUNK as $CHUNK) 
					$COMPANY_CHANGE[]=$CHUNK['CampaignID'];
				
				//Метки//
				$TAGS=$this->Framework->direct->model->company->tag(array('id'=>$COMPANY_CHANGE));
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
				
				$COMPANY_CHANGE_CHUNK=array_chunk($COMPANY_CHANGE, 10);
				foreach ($COMPANY_CHANGE_CHUNK as &$COMPANY_CHANGE_CHUNK_VALUE) {
					$TAGS=$this->Framework->direct->model->banner->tag(array('company'=>$COMPANY_CHANGE_CHUNK_VALUE));
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
				//\Метки//
					
				$TIME=$this->Framework->direct->company->get(array('id'=>$COMPANY_CHANGE), array('time'), array('time'), array('page'=>0, 'number'=>1));
				
				if (!empty($TIME['ELEMENT'][0]['time']) && $TIME['ELEMENT'][0]['time']!='0000-00-00 00:00:00')
					$time=$TIME['ELEMENT'][0]['time'];
				else
					$time=0;
				
				$CHANGE=$this->Framework->direct->model->change->get(array('id'=>$COMPANY_CHANGE, 'time'=>$time));
				
				if (!empty($CHANGE['Timestamp'])) {
					$time_yandex=ceil((strtotime(gmdate('Y-m-d').'T'.gmdate('H:i:s').'Z')-strtotime($CHANGE['Timestamp']))/60);
					if ($this->debug)
						echo 'yandex time='.$CHANGE['Timestamp'].', difference='.$time_yandex.", memory=".memory_get_usage(true)."<Br>\r\n";
					$this->Framework->direct->model->config->set(array('time_yandex'=>$time_yandex));
					$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET `delete`=1 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":'').(!empty($where_delete)?' AND '.$where_delete:''));
					$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `delete`=1 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":'').(!empty($where_delete)?' AND '.$where_delete:''));
					$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['retargeting']."` SET `delete`=1 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":'').(!empty($where_delete)?' AND '.$where_delete:''));
				}
				
				
				if (!empty($CHANGE['Banners']['NotUpdated'])) {
									
					$banner_no_delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET `delete`=0 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($where_delete)?' AND '.$where_delete:'').' AND (';
					$banner_no_delete_sql='';
					$phrase_no_delete_sql_prefix="UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `delete`=0 WHERE `account`='".(int)$LOGIN['id']."'".(!empty($where_delete)?' AND '.$where_delete:'').' AND (';
					$phrase_no_delete_sql='';
					foreach($CHANGE['Banners']['NotUpdated'] as &$banner_value) {
						$banner_value=preg_replace('[^0-9]', '', (string)$banner_value);
						$banner_no_delete_sql_postfix=(empty($banner_no_delete_sql)?$banner_no_delete_sql_prefix:" OR ")."`id`='".(string)$banner_value."'";
						if (strlen($banner_no_delete_sql.$banner_no_delete_sql_postfix)>49999) {
							$banner_no_delete_sql.=')';
							$this->Framework->db->set($banner_no_delete_sql);
							$banner_no_delete_sql=$banner_no_delete_sql_prefix."`id`='".(string)$banner_value."'";
						} else
							$banner_no_delete_sql.=$banner_no_delete_sql_postfix;
						
						$phrase_no_delete_sql_postfix=(empty($phrase_no_delete_sql)?$phrase_no_delete_sql_prefix:" OR ")."`banner`='".(string)$banner_value."'";
						if (strlen($phrase_no_delete_sql.$phrase_no_delete_sql_postfix)>49999) {
							$phrase_no_delete_sql.=')';
							$this->Framework->db->set($phrase_no_delete_sql);
							$phrase_no_delete_sql=$phrase_no_delete_sql_prefix."`banner`='".(string)$banner_value."'";
						} else
							$phrase_no_delete_sql.=$phrase_no_delete_sql_postfix;
					}
					if ($banner_no_delete_sql) {
						$banner_no_delete_sql.=')';
						$this->Framework->db->set($banner_no_delete_sql);
					}
					if ($phrase_no_delete_sql) {
						$phrase_no_delete_sql.=')';
						$this->Framework->db->set($phrase_no_delete_sql);
					}
				}
				//if ($this->debug) echo '<pre>'.print_r($CHANGE, true).'</pre>';
				//Сохраняем изменившиеся объявления, группы, фразы и ретаргетинг//
				if (!empty($CHANGE['Banners']['Updated'])) {
					if (!empty($BANNER_UNKNOWN))
						$CHANGE['Banners']['Updated']=array_diff($CHANGE['Banners']['Updated'], $BANNER_UNKNOWN);
					$UPDATED=array_chunk($CHANGE['Banners']['Updated'], 2000);
					foreach ($UPDATED as $UPDATE) {
						$RETARGETING_BANNERS=array();
						$offset=0;
						while (true) {							
							
							$BANNERS=array();
							$BANNER_GET=array(
								//'COMPANY'=>$VALUE['CampaignID'],
								'BANNER'=>$UPDATE,
								//'currency'=>$VALUE['CampaignCurrency'],
								'phrase'=>1,
								'FILTER'=>array('archive'=>0),
							);
							if ($limit<count($UPDATE)) {
								$BANNER_GET['limit']=$limit;
								$BANNER_GET['offset']=$offset;
							}
							if ($offset<count($UPDATE))
								$BANNERS=$this->Framework->direct->model->banner->get($BANNER_GET);														
										
							if (!empty($this->debug)) if (!empty($BANNERS)) echo 'banner get '.count($BANNERS).', memory='.memory_get_usage(true).' - '.$count_banner.' - '.$count_phrase.' - '.$offset.' - '.$limit.' - '.(!empty($BANNERS[0]['BannerID'])?$BANNERS[0]['BannerID']:'').' - '.date('H:i:s')."\r\n";
							
							if (!empty($BANNERS)) {
								foreach ($BANNERS as &$BANNER) {
									if ($this->microsecond>0)
										usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
									$count_banner++;
									if ($BANNER['StatusArchive'] == 'No') {
										$count_banners++;
										if ($BANNER['Type']=='UNKNOWN') $count_banners_unknown++;
										//Объявление//
										$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array(
											'id'=>$BANNER['BannerID'],
											'account'=>(int)$LOGIN['id'],
											'user'=>(int)$VALUE['id'], 
											'company'=>$BANNER['Type']!='UNKNOWN'?(int)$BANNER['CampaignID']:0, 
											'group'=>$BANNER['Type']!='UNKNOWN'&&!empty($BANNER['AdGroupID'])?$BANNER['AdGroupID']:0,
											'name'=>$BANNER['Type']!='UNKNOWN'?$BANNER['Title']:'Не поддерживается в АПИ4',
											'body'=>$BANNER['Type']!='UNKNOWN'?$BANNER['Text']:'Не поддерживается в АПИ4',
											'url'=>$BANNER['Type']!='UNKNOWN'?$BANNER['Href']:'',
											'domain'=>$BANNER['Type']!='UNKNOWN'?$BANNER['Domain']:'',
											'variant'=>!empty($BANNER['Type'])?$BANNER['Type']:'',
											'count'=>!empty($BANNER['Phrases'])?count($BANNER['Phrases']):0,
											'delete'=>0,
											'status'=>!empty($BANNER['StatusShow'])&&$BANNER['StatusShow'] == 'Yes'?1:2,
											'time'=>date('Y-m-d H:i:s'),
										));
										//\Объявление//
										if ($BANNER['Type']!='UNKNOWN') {
											$RETARGETING_BANNERS[$COMPANY[$BANNER['CampaignID']]['login']]['ID'][]=$BANNER['BannerID'];
											$RETARGETING_BANNERS[$COMPANY[$BANNER['CampaignID']]['login']]['COMPANY'][$BANNER['BannerID']]=$BANNER['CampaignID'];
											//Группа//
											$this->Framework->db->set("SELECT `id`, `group`, `domain` FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `group`='".$BANNER['AdGroupID']."' AND `status`=1 ORDER BY `id` LIMIT 1");
											$GROUP_BANNER=$this->Framework->db->get();
											$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], array(
												'id'=>$BANNER['AdGroupID'],
												'account'=>(int)$LOGIN['id'],
												'user'=>(int)$COMPANY[$BANNER['CampaignID']]['user'], 
												'company'=>(int)$BANNER['CampaignID'], 
												'banner'=>!empty($GROUP_BANNER['id'])?$GROUP_BANNER['id']:$BANNER['BannerID'], 
												'name'=>$BANNER['AdGroupName'],
												'domain'=>!empty($GROUP_BANNER['domain'])?$GROUP_BANNER['domain']:$BANNER['Domain'],
												'count'=>!empty($BANNER['Phrases'])?count($BANNER['Phrases']):0,
												'delete'=>0,
												'status'=>!empty($GROUP_BANNER['id'])?1:($BANNER['IsActive'] == 'No'?2:1),
												'time'=>date('Y-m-d H:i:s'),
											));
											//\Группа//
									
											if (!empty($BANNER['Phrases'])) {
		
												foreach ($BANNER['Phrases'] as &$PHRASE) {
													if ($this->microsecond>0)
														usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
													$count_phrase++;
													$count_phrases++;
													
													$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
														'id'=>$PHRASE['PhraseID'],
														'account'=>(int)$LOGIN['id'],
														'user'=>(int)$COMPANY[$BANNER['CampaignID']]['user'], 
														'company'=>(int)$BANNER['CampaignID'], 
														'group'=>$BANNER['AdGroupID'],
														'banner'=>!empty($GROUP_BANNER['id'])?$GROUP_BANNER['id']:$BANNER['BannerID'], 
														'name'=>$PHRASE['Phrase'],
														'delete'=>0,
														'status'=>$PHRASE['StatusPaused']=='Yes'?2:1,
														'datetime'=>date('Y-m-d H:i:s'),
													));
													
													
													
												}
											}
											unset($GROUP_BANNER);
										}
									}
								}
								$offset+=$limit;
								unset($BANNERS);

							} else
								break;
							if (empty($BANNER_GET['limit']))
								break;
						}
						
						//Ретаргетинг//
						if ($this->debug) if (!empty($VALUE['ClientCurrencies'])) echo $login.' currencies '.print_r($VALUE['ClientCurrencies'], true)."<br>\r\n";;
						foreach ($RETARGETING_BANNERS as $login=>$RETARGETINGS_ARRAY) {
							$RETARGETINGS_CHUNK=array_chunk($RETARGETINGS_ARRAY['ID'], $this->Framework->direct->model->retargeting->limit);
							foreach ($RETARGETINGS_CHUNK as $RETARGETING_CHUNK) {
								if ($this->microsecond>0)
									usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
							
								$RETARGETINGS=$this->Framework->direct->model->retargeting->get(array('login'=>$login, 'banner'=>$RETARGETING_CHUNK, 'currency'=>(!empty($VALUE['ClientCurrencies'][0])?$VALUE['ClientCurrencies'][0]:'')));
								if (!empty($this->debug)) if (!empty($RETARGETINGS)) echo 'retargeting get '.count($RETARGETINGS).', memory='.memory_get_usage(true).' - '.date('H:i:s')."<br>\r\n";
		
								if (!empty($RETARGETINGS)) {
									foreach ($RETARGETINGS as $RETARGETING) {
										$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['retargeting'], array(
											'id'=>$RETARGETING['RetargetingID'],
											'account'=>(int)$LOGIN['id'],
											'user'=>!empty($RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']])?(int)$COMPANY[$RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']]]['user']:0, 
											'company'=>!empty($RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']])?$RETARGETINGS_ARRAY['COMPANY'][$RETARGETING['AdID']]:0, 
											'group'=>(!empty($RETARGETING['AdGroupID'])?$RETARGETING['AdGroupID']:0),
											'banner'=>(!empty($RETARGETING['AdID'])?$RETARGETING['AdID']:0),
											'name'=>'Ретаргетинг: '.$RETARGETING['RetargetingID'],
											'delete'=>0,
											'status'=>($RETARGETING['StatusPaused'] == 'No'?1:2),
											'time'=>date('Y-m-d H:i:s'),
										));
									}
								}
								unset($RETARGETINGS);
							}
							unset($RETARGETINGS_ARRAY, $RETARGETING_BANNERS[$login], $RETARGETINGS_CHUNK, $RETARGETING_CHUNK);
						}
						//\Ретаргетинг//
						
					}
				}
				//\Сохраняем изменившиеся объявления, группы, фразы и ретаргетинг//
				
				//Записываем время последних изменений в кампании//
				if (!empty($CHANGE['Timestamp']) && $this->Framework->library->error->count()==$company_change_error) {
					$time=strtotime($CHANGE['Timestamp']);
					$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `time`='".date('Y-m-d H:i:s', $time)."' WHERE `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`id`='".implode("' OR `id`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `delete`>0 AND `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`id`='".implode("' OR `id`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `delete`>0 AND `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `delete`>0 AND `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` WHERE `delete`>0 AND `account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`company`='".implode("' OR `company`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) WHERE `t2`.`group` is NULL AND `t1`.`account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`t1`.`company`='".implode("' OR `t1`.`company`='", $COMPANY_CHANGE)."') ":''));
					$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL AND `t1`.`account`='".(int)$LOGIN['id']."'".(!empty($COMPANY_CHANGE)?" AND (`t1`.`company`='".implode("' OR `t1`.`company`='", $COMPANY_CHANGE)."') ":''));
				}
				unset($CHANGE);
				//\Записываем время последних изменений в кампании//
				
			}//\foreach
			//\Синхронизируем объявления компаний//
		}
		
		//Удаляем неактуальные данные//
		if ($this->Framework->library->error->count()==0) {
			if ($this->debug) $time1=time();
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['company']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL AND `t1`.`variant`!='UNKNOWN'");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['banner']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`account`=0, `t1`.`delete`=1 WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` WHERE `delete`>0 AND `account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').") ");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`) WHERE `t2`.`id` is NULL");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`group`=`t1`.`id`) WHERE `t2`.`group` is NULL AND `t1`.`account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').")");
			$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`) WHERE `t2`.`id` is NULL AND `t1`.`account` IN (0".(!empty($USERS_ID)?",".implode(',',$USERS_ID):'').")");
			if ($this->debug) echo 'DELETE '.(time()-$time1).' сек.'."<br>\r\n";
		}
		
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t1` LEFT JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`) SET `t1`.`time`=NULL WHERE `t2`.`id` is NULL");	
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`account`) SET `t1`.`plan`=0 WHERE `t2`.`status`!=1 OR `t2`.`id` is NULL");
		$this->Framework->db->set("DELETE `t1`.* FROM `".$this->Framework->user->model->model->TABLE[0]."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`parent`) WHERE `t1`.`parent`>0 AND `t2`.`id` is NULL");
		//\Удаляем неактуальные данные//
		
		//Объединям теги для баннеров с группами//
		$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['tag_banner']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`) SET `t1`.`group`=`t2`.`group`");
		$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['tag_banner']."` WHERE `group`=0");
		//\Объединям теги для баннеров с группами//
		
		if ($this->debug) echo 'Total banner='.$count_banners.' ('.$count_banners_unknown.'), phrase='.$count_phrases."<br>\r\n";
		
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