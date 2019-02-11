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

final class Auction extends \FrameWork\Common {
	
	private $updateprices_max = 1000;
	private $updateprices_call_max = 3000;
	private $limit = 1000;
	private $microsecond=0;
	private $debug=false;
	
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
	
	public function set($PARAMS=array()) {
		$REPORTS=array();
		$ERROR=array('PHRASE'=>array('empty'=>0));
		if (!empty($PARAMS) && is_array($PARAMS) && !empty($PARAMS['debug'])) {
			$this->debug=true;
			unset($PARAMS['debug']);
		}
		
		//Чиним сломавшиеся таблицы//
		$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		$CONFIG=$this->Framework->direct->model->config->CONFIG;
		$this->limit=($this->Framework->direct->model->config->CONFIG['auction']>0&&$this->Framework->direct->model->config->CONFIG['auction']<=1000)?(int)$this->Framework->direct->model->config->CONFIG['auction']:1000;
		$this->microsecond=!empty($CONFIG['microsecond'])?$CONFIG['microsecond']:$this->microsecond;
		$CURRENCY=$this->Framework->direct->model->currency->get();
		$STRATEGIES=array();
		
		//Получаем список аккаунтов//
		
		$GET=array(
			'group'=>4, 
			'status'=>1,
			'ID'=>(!empty($PARAMS)?(is_array($PARAMS) && !empty($PARAMS[0])?$PARAMS:(is_array($PARAMS) && !empty($PARAMS['account'])?array($PARAMS['account']):array($PARAMS))):0),
		);
		

		$USERS=$this->Framework->user->model->model->get($GET);
		$USERS=!empty($USERS['ELEMENT'])?$USERS['ELEMENT']:array();
		
		//\Получаем список аккаунтов//
		$count = 0;
		$count_banners = 0;
		$count_phrases = 0;
		$count_retargeting = 0;
		foreach ($USERS as $LOGIN) {
			//Проверяем лимиты//
			if ( $LOGIN['limit_status'] && !empty($LOGIN['limit_time']) && time()<mktime(date('H', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('i', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])+1, date('s', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])+1, date('m', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('d', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('Y', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])) && (!$this->Framework->direct->model->config->api_version || ($LOGIN['unit_status'] && !empty($LOGIN['unit_time']) && strtotime($LOGIN['unit_time'])>=mktime(0, $this->Framework->direct->model->config->time_yandex, 1, date('m'), date('d'), date('Y')))) ) {
				if ($this->debug) echo 'LIMIT API ALL';
				continue;
			}
			//\Проверяем лимиты//
			
			//Устанавливаем авторизационные данные//
			$this->Framework->direct->model->config->login=(string)$LOGIN['login'];
			$this->Framework->direct->model->config->token=(string)$LOGIN['token'];
			//\Устанавливаем авторизационные данные//
			
			$counter = 0;
			$PARAM = array ();
			$STRATEGY=array('strategy', 'type', 'percent', 'add', 'maximum', 'fixed', 'param1', 'param2', 'param3', 'context', 'context_percent', 'context_type', 'context_maximum', 'context_fixed', 'context_minimum');
			$banner=0;
			$offset=0;
			$where_param=(is_array($PARAMS) && !empty($PARAMS['user'])?' `t2`.`user`='.(int)$PARAMS['user'].' AND ':'').(is_array($PARAMS) && !empty($PARAMS['company']) && is_array($PARAMS['company']) && !empty($PARAMS['company'][0])?' `t2`.`company` IN ('.implode(', ',$PARAMS['company']).') AND ':'');
			$where_priority=($this->Framework->direct->model->config->CONFIG['priority_ctr']>0?"(`t1`.`ctr28`>".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." OR (`t1`.`ctr28`<=".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." AND (`t1`.`time` IS NULL OR `t1`.`time`<='".date('Y-m-d H:i:s', (time()-(int)$this->Framework->direct->model->config->CONFIG['priority_interval']*60))."'))) AND ":'');
			$where_strategy=" `t3`.`account`='".$LOGIN['id']."' AND ( (`t3`.`strategy`!=0 AND `t3`.`strategy`!=-1 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy`!=0 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy` IN (0,-1) AND `t1`.`strategy`!=0 AND `t1`.`strategy`!=-1) ) ";
			$where_status=(!empty($CONFIG['auction_status'])?' `t3`.`status`=1 AND `t2`.`status`=1 AND `t1`.`status`=1 AND `t3`.`price`>0 AND ':'');
			$where_price=(!empty($CONFIG['auction_status'])?' (`t3`.`price`>1 OR `t1`.`time` IS NULL)  AND ':'');
			$where_common=$where_status.$where_price.$where_strategy;
			$group=" GROUP BY `t1`.`id` ";
			$order=" ORDER BY `t4`.`id`, `t3`.`datetime`, `t2`.`datetime`";
			$join="	INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`)
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t3` ON (`t3`.`id`=`t2`.`company`)
					INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t4` ON (`t4`.`id`=`t3`.`user`)
			";
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` SET `t1`.`plan`=0 WHERE ".str_replace('t2', 't1', $where_param)." `t1`.`account`='".$LOGIN['id']."'");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t1` SET `t1`.`plan`=0 WHERE ".str_replace('t2', 't1', $where_param)." `t1`.`account`='".$LOGIN['id']."'");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` {$join} SET `t1`.`plan`=1 WHERE {$where_param} {$where_common}");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` `t2` 
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` ON (`t1`.`group`=`t2`.`id`)
					SET `t2`.`plan`=IF(`t2`.`datetime` is NULL, 1, UNIX_TIMESTAMP(`t2`.`datetime`))  
					WHERE {$where_priority} `t1`.`plan`=1 AND `t1`.`account`='".$LOGIN['id']."'");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` `t2` 
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t1` ON (`t1`.`company`=`t2`.`id`)
					SET `t2`.`plan`=IF(`t2`.`datetime` is NULL, 1, UNIX_TIMESTAMP(`t2`.`datetime`)) 
					WHERE {$where_priority} `t1`.`plan`=1 AND `t1`.`account`='".$LOGIN['id']."'");
					
			$where=" WHERE ".$where_param.$where_priority.$where_common.' AND `t2`.`plan`=1';
			if ($this->debug) echo 'priority='.$where_priority;
			while (true) {
				if ($this->debug) echo 'offset='.$offset.' '.date('Y-m-d H:i:s')."\r\n";
				$time=time();

				$limit=" LIMIT ".$offset.", ".$this->limit." ";
				
				$sql="SELECT 
					`t2`.`id` as `group`,
					`t2`.`banner` as `banner`,
					`t2`.`company` as `company`,
					`t2`.`user` as `user`,
					`t3`.`currency` as `company_currency`,
					`t4`.`login` as `login`
					FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t2`
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t3` ON (`t3`.`id`=`t2`.`company`)
					INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t4` ON (`t4`.`id`=`t3`.`user`)
					WHERE ".$where_param." `t2`.`plan`>0 AND `t2`.`account`='".$LOGIN['id']."'
					GROUP BY `t2`.`id`
					ORDER BY `t4`.`id`, `t3`.`currency`, `t3`.`plan`, `t3`.`id`, `t2`.`plan`
					{$limit}
				";
				$result=$this->Framework->db->set($sql);
				if ($result && $this->Framework->db->count($result)>0) {
					$BANNER_ID=array();
					$GROUP_ID=array();
					$GROUP_BANNER=array();
					$BANNERS_ID=array();
					$GROUPS_ID=array();
					$COMPANIES_ID=array();
					$count_company=-1;
					$company=-1;
					$banner=0;
					$group_count=-1;
					$group_login='';
					while ($ROW=$this->Framework->db->get($result)) {
						if ($this->microsecond>0)
							usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
						$ROW['banner']=(string)$ROW['banner'];
						$ROW['group']=(string)$ROW['group'];
						$count_banners++;
						if ($ROW['login']!=$group_login) {
							$group_count++;
							$GROUP_ID[$group_count]['login']=$ROW['login'];
						}
						$GROUP_ID[$group_count]['id'][]=$ROW['group'];
						$GROUP_ID[$group_count]['banner'][$ROW['group']]=$ROW['banner'];
						$GROUP_BANNER[$ROW['banner']]=$ROW['group'];
						$group_login=$ROW['login'];
						
						if ($ROW['company_currency']!=$company) {
							$count_company++;
							$count_banner=-1;
							$BANNER_ID[$count_company]['currency']=$ROW['company_currency'];
						}
						if ($ROW['banner']!=$banner) {
							$count_banner++;
							$BANNER_ID[$count_company]['banner'][$count_banner]=$ROW['banner'];
							$BANNER_ID[$count_company]['company'][$count_banner]=$ROW['company'];
							$BANNER_ID[$count_company]['group'][$count_banner]=$ROW['group'];
							$BANNER_ID[$count_company]['user'][$ROW['banner']]=$ROW['user'];
						}
						$banner=$ROW['banner'];
						$company=$ROW['company_currency'];
					}
					if ($this->debug) echo 'sql1 time='.(time()-$time).' sec'."\r\n";
					
					//Получаем ставки//
					$BANNERS=array();
					
					//АПИ 4//
					if ( !$LOGIN['limit_status'] || empty($LOGIN['limit_time']) || time()>=mktime(date('H', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('i', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])+1, date('s', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])+1, date('m', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('d', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep']), date('Y', strtotime($LOGIN['limit_time'])+$LOGIN['limit_sleep'])) ) {
						if ($this->debug) echo 'api4 ';
						for ($i=0; $i<count($BANNER_ID); $i++) {
							$time1=time();
							$BANNER=$this->Framework->direct->model->phrase->get(array(
								'id'=>$BANNER_ID[$i]['banner'],
								'currency'=>(!empty($BANNER_ID[$i]['currency']) && !empty($CURRENCY['ID'][$BANNER_ID[$i]['currency']])?$CURRENCY['ID'][$BANNER_ID[$i]['currency']]['key']:null),
								'field'=>1,
							));

							
							if ($this->debug) if ($this->Framework->direct->model->config->error==56) echo 'error='.$this->Framework->direct->model->config->error."<br>\r\n";
							//if ($this->debug) $this->Framework->direct->model->config->error=56;
							
							if (!empty($BANNER)) {
								$BANNERS_ID=array_merge($BANNERS_ID, $BANNER_ID[$i]['banner']);
								$GROUPS_ID=array_merge($GROUPS_ID, $BANNER_ID[$i]['group']);
								$COMPANIES_ID=array_merge($COMPANIES_ID, $BANNER_ID[$i]['company']);
								foreach ($BANNER as &$VALUE) {
									$VALUE['user']=$BANNER_ID[$i]['user'][(string)$VALUE['BannerID']];
									$BANNERS[(string)$VALUE['PhraseID']]=$VALUE; 
								}
								
							}
							
							if ($this->debug) echo 'phrase='.count($BANNER)."<br>\r\n";
							if ($this->debug) echo 'api time='.(time()-$time1).' sec'."<br>\r\n";							
							
							unset($BANNER);
							if ($this->Framework->direct->model->config->error==56)
								break;
						}
						unset($BANNER_ID);
						
						//Лимиты АПИ 4//
						
						if (($LOGIN['limit_status'] && $this->Framework->direct->model->config->error!=56) || !$LOGIN['limit_status']) {
							$USER_PARAM_SAVE=array(
								'user'=>$LOGIN['id'], 
								'limit'=>($this->Framework->direct->model->config->bid_daily-$this->Framework->direct->model->config->bid_limit),
								'limit_rest'=>$this->Framework->direct->model->config->bid_limit,
								'limit_total'=>$this->Framework->direct->model->config->bid_daily,
								'limit_sleep'=>$this->Framework->direct->model->config->bid_sleep,
								'limit_date'=>date('Y-m-d'),
								//'limit'=>$LOGIN['limit_status']?count($BANNERS):$LOGIN['limit']+count($BANNERS),
								'limit_status'=>0,
							);
							if ($this->Framework->direct->model->config->error==56) {
								$USER_PARAM_SAVE['limit_status']=1;
								$USER_PARAM_SAVE['limit_time']=date('Y-m-d H:i:s');
								$LOGIN['limit_status']=1;
							}
							
							$this->Framework->user->model->param->set($USER_PARAM_SAVE);
							$LOGIN['limit']=$USER_PARAM_SAVE['limit'];
							$LOGIN['limit_status']=$USER_PARAM_SAVE['limit_status'];
						}
						//\Лимиты АПИ 4//
					}
					//\АПИ 4//
					
					
					//АПИ 5//
					if ($this->Framework->direct->model->config->api_version) {
						if (($this->Framework->direct->model->config->error==56 || $LOGIN['limit_status']) && (!$LOGIN['unit_status'] || $LOGIN['unit_status'] && (empty($LOGIN['unit_time']) || strtotime($LOGIN['unit_time'])<mktime(0, $this->Framework->direct->model->config->time_yandex, 1, date('m'), date('d'), date('Y')))) ) {
							
							$this->Framework->api->yandex->direct->config->token=(string)$LOGIN['token'];//АПИ 5
							
							foreach ($GROUP_ID as $GROUP) {
								
								$this->Framework->api->yandex->direct->config->login=(string)$GROUP['login'];//АПИ 5
								$BIDS=$this->Framework->api->yandex->direct->bid->get(array(
									'group'=>$GROUP['id'],
								));
								
								if ($this->Framework->api->yandex->direct->config->error==152)
									break;
								if (!empty($BIDS)) {
									$GROUPS_ID=array_merge($GROUPS_ID, $GROUP['id']);
									foreach ($BIDS as &$BID) {
										$BANNERS[(string)$BID['KeywordId']]=array(
											'ContextPrice' => round($BID['ContextBid']/1000000, 2),
											'BannerID' => $GROUP['banner'][$BID['AdGroupId']],
											'Price' => round($BID['Bid']/1000000, 2),
											'PhraseID' => $BID['KeywordId'],
											'CampaignID' => $BID['CampaignId'],
											'AdGroupId' => $BID['AdGroupId'],
											'CurrentOnSearch' => round($BID['ContextBid']/1000000, 2),
											'ContextClicks' => 0,
											'Clicks' => 0,
											'Shows' => 0,
											'ContextShows' => 0,
											'MinPrice' => round($BID['MinSearchPrice']/1000000, 2),
										); 
										
										foreach ($BID['AuctionBids'] as &$AUCTIONBIDS) {
											$AUCTIONBIDS['Bid']=round($AUCTIONBIDS['Bid']/1000000, 2);
											$AUCTIONBIDS['Price']=round($AUCTIONBIDS['Price']/1000000, 2);
										}
										$BANNERS[(string)$BID['KeywordId']]['AuctionBids']=$BID['AuctionBids'];
										
										foreach ($BID['ContextCoverage'] as &$ContextCoverage) {
											$ContextCoverage['Price']=round($AUCTIONBIDS['Price']/1000000, 2);
										}
										$BANNERS[(string)$BID['KeywordId']]['ContextCoverage']=$BID['ContextCoverage'];
									}
								}
							}
							
							if (($LOGIN['unit_status'] && $this->Framework->api->yandex->direct->config->error!=152) || !$LOGIN['unit_status']) {
								$USER_PARAM_SAVE=array(
									'user'=>$LOGIN['id'], 
									'unit'=>$this->Framework->api->yandex->direct->config->limit,
									'unit_total'=>$this->Framework->api->yandex->direct->config->daily,
									'unit_status'=>0,
								);
								if ($this->Framework->api->yandex->direct->config->error==152) {
									$USER_PARAM_SAVE['unit_status']=1;
									$USER_PARAM_SAVE['unit_time']=date('Y-m-d H:i:s');
									$LOGIN['unit_status']=1;
								}
								
								$this->Framework->user->model->param->set($USER_PARAM_SAVE);
								$LOGIN['unit']=$this->Framework->api->yandex->direct->config->limit;
							}
							
						}
					}
					//\АПИ 5//
				} else
					break;

				if (!empty($BANNERS)) {
					if (!empty($GROUPS_ID)) {
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `datetime`='".date('Y-m-d H:i:s')."' WHERE (`id`='".implode("' OR `id`='",$GROUPS_ID)."')");
						if ($this->debug) echo ($this->Framework->db->number()!=count($GROUPS_ID)?'WARNING ':'').'set '.count($GROUPS_ID).' group update affected rows='.$this->Framework->db->number().' time '.date('Y-m-d H:i:s')."<br>\r\n";
					} else 
						if ($this->debug) echo "WARNING EMPTY GROUPS_ID\r\n";
					if (!empty($COMPANIES_ID)) {
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `datetime`='".date('Y-m-d H:i:s')."' WHERE (`id`='".implode("' OR `id`='",$COMPANIES_ID)."')");
						unset($COMPANIES_ID);
					}
					
					
				} else {
					if ($this->debug) echo "WARNING EMPTY PHRASE\r\n";
					break;
				}
					
				//Обновление ставок//				
				$sql="SELECT 
					`t1`.`id`, 
					`t1`.`account`, 
					`t1`.`user`, 
					`t1`.`company`, 
					`t1`.`group`, 
					`t1`.`banner`, 
					`t1`.`price`, 
					`t1`.`min_price`, 
					`t1`.`real_price`, 
					`t1`.`context_price`,  
					`t1`.`position`, 
					`t1`.`position_value`, 
					`t1`.`position_datetime`, 
					`t1`.`context_price`, 
					`t1`.`context_min`, 
					`t1`.`context_max`, 
					`t1`.`strategy` as `strategy`, 
					`t1`.`type` as `type`, 
					`t1`.`percent` as `percent`, 
					`t1`.`add` as `add`, 
					`t1`.`maximum` as `maximum`,
					`t1`.`fixed` as `fixed`,
					`t1`.`sum` as `sum`,
					`t1`.`budget` as `budget`,
					`t1`.`click` as `click`,
					`t1`.`show` as `show`,
					`t1`.`ctr` as `ctr`,
					`t1`.`click28` as `click28`,
					`t1`.`show28` as `show28`,
					`t1`.`ctr28` as `ctr28`,					
					
					`t1`.`sum365` as `sum365`,
					`t1`.`click365` as `click365`,
					`t1`.`show365` as `show365`,
					
					`t1`.`depth` as `depth`,
					`t1`.`cost` as `cost`,
					`t1`.`conversion` as `conversion`,
					`t1`.`revenue` as `revenue`,
					`t1`.`roi` as `roi`,
					
					`t1`.`param1` as `param1`,					
					`t1`.`param2` as `param2`,					
					`t1`.`param3` as `param3`,					
					
					`t1`.`context` as `context`,	
					`t1`.`context_percent` as `context_percent`,
					`t1`.`context_type` as `context_type`,
					`t1`.`context_maximum` as `context_maximum`,	
					`t1`.`context_fixed` as `context_fixed`,
					`t1`.`context_minimum` as `context_minimum`,
					`t1`.`status` as `status`, 
					
					`t2`.`strategy` as `banner_strategy`, 
					`t2`.`type` as `banner_type`, 
					`t2`.`percent` as `banner_percent`, 
					`t2`.`add` as `banner_add`, 
					`t2`.`maximum` as `banner_maximum`,
					`t2`.`fixed` as `banner_fixed`,
					`t2`.`sum` as `banner_sum`,
					`t2`.`budget` as `banner_budget`,
					`t2`.`click` as `banner_click`,
					`t2`.`show` as `banner_show`,
					`t2`.`ctr` as `banner_ctr`,
					`t2`.`click28` as `banner_click28`,
					`t2`.`show28` as `banner_show28`,
					`t2`.`ctr28` as `banner_ctr28`,					
					`t2`.`context` as `banner_context`,	
					`t2`.`context_percent` as `banner_context_percent`,
					`t2`.`context_type` as `banner_context_type`,
					`t2`.`context_maximum` as `banner_context_maximum`,	
					`t2`.`context_fixed` as `banner_context_fixed`,
					`t2`.`context_minimum` as `banner_context_minimum`,
					`t2`.`status` as `banner_status`,
					`t2`.`count` as `banner_count`,
					`t2`.`domain` as `banner_url`,
					`t2`.`param1` as `banner_param1`,					
					`t2`.`param2` as `banner_param2`,					
					`t2`.`param3` as `banner_param3`,
					
					`t3`.`strategy` as `company_strategy`, 
					`t3`.`type` as `company_type`, 
					`t3`.`percent` as `company_percent`, 
					`t3`.`add` as `company_add`, 
					`t3`.`maximum` as `company_maximum`,
					`t3`.`fixed` as `company_fixed`,
					`t3`.`context` as `company_context`,	
					`t3`.`context_percent` as `company_context_percent`,
					`t3`.`context_type` as `company_context_type`,
					`t3`.`context_maximum` as `company_context_maximum`,
					`t3`.`context_fixed` as `company_context_fixed`,					
					`t3`.`context_minimum` as `company_context_minimum`,					
					`t3`.`sum` as `company_sum`,
					`t3`.`sum_context` as `company_sum_context`,
					`t3`.`price` as `company_price`,
					`t3`.`budget` as `company_budget`,
					`t3`.`click` as `company_click`,
					`t3`.`show` as `company_show`,
					`t3`.`ctr` as `company_ctr`,
					`t3`.`click28` as `company_click28`,
					`t3`.`show28` as `company_show28`,
					`t3`.`ctr28` as `company_ctr28`,
					
					`t3`.`depth` as `company_depth`,
					`t3`.`depth_context` as `company_depth_context`,
					`t3`.`conversion` as `company_conversion`,
					`t3`.`conversion_context` as `company_conversion_context`,
					`t3`.`cost` as `company_cost`,
					`t3`.`cost_context` as `company_cost_context`,
					
					`t3`.`currency` as `company_currency`,
					`t3`.`status` as `company_status`,
					`t3`.`stop` as `company_stop`,
					`t3`.`user` as `user`,
					`t3`.`account` as `account`,
					
					`t3`.`strategy_name` as `strategy_name`,
					`t3`.`context_strategy_name` as `context_strategy_name`,
					
					`t3`.`param1` as `company_param1`,					
					`t3`.`param2` as `company_param2`,					
					`t3`.`param3` as `company_param3`,
					
					`t4`.`login` as `login`
					
						FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` 
					{$join}
					WHERE  `t1`.`account`='".$LOGIN['id']."' AND `t1`.`plan`=1 AND (`t1`.`group`='".implode("' OR `t1`.`group`='", $GROUPS_ID)."')
					{$group}
				";
				$time2=time();
				$result=$this->Framework->db->set($sql);
				if ($this->debug) echo 'sql2 time='.(time()-$time2).' sec'."\r\n";
				$count_result=$this->Framework->db->count($result);
				if ($result && $count_result>0) {
					if ($this->debug) echo ($this->Framework->db->count($result)!=count($BANNERS)?'WARNING ':'').'get phrase='.$this->Framework->db->count($result).' from '.count($BANNERS).', memory='.memory_get_usage(true).'('.memory_get_usage().')'."<br>\r\n";
					//Устанавливаем ставки//

					while ($PHRASE=$this->Framework->db->get($result)) {
						if ($this->microsecond>0)
							usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
						$PHRASE['id']=(string)$PHRASE['id'];
						$PHRASE['banner']=(string)$PHRASE['banner'];
						$PHRASE['group']=(string)$PHRASE['group'];
						
						if ($PHRASE['strategy']==0) {
							if ($PHRASE['banner_strategy']==0) {
								if ($PHRASE['company_strategy']==0) {
									
								} else
									foreach ($STRATEGY as $value) 
										$PHRASE[$value]=round($PHRASE['company_'.$value], 2);
							} else
								foreach ($STRATEGY as $value) 
										$PHRASE[$value]=round($PHRASE['banner_'.$value], 2);
						}
						
						$price = 0;
						if (!empty($PHRASE ['strategy']) && $PHRASE ['strategy']!=-1) { 
							//Получаем стратегию//
							if ($PHRASE ['strategy']>0 && !isset($STRATEGIES[$PHRASE ['strategy']])) {
								$STRATEGIES[$PHRASE['strategy']]=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('id'=>$PHRASE['strategy']));
								if (!empty($STRATEGIES[$PHRASE['strategy']][0]))
									$STRATEGIES[$PHRASE['strategy']]=$STRATEGIES[$PHRASE['strategy']][0]['value'];
								if (!empty($STRATEGIES[$PHRASE['strategy']]))
									$STRATEGIES[$PHRASE['strategy']]=$this->Framework->direct->model->formula->set($STRATEGIES[$PHRASE['strategy']]);
							}
							//\Получаем стратегию//
							
							//Получаем ставки//

							if (!empty($BANNERS[$PHRASE['id']])) {
								
								$PHRASE['min_price']=!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['min'],2):0.01;
								$PHRASE['max_price']=(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['max'],2):84);
								$PHRASE['minimum']=!empty($BANNERS[$PHRASE['id']]['MinPrice'])?$BANNERS[$PHRASE['id']]['MinPrice']:(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['min'],2):0.01);
								$PHRASE['maximum']=empty($PHRASE['maximum'])||(float)$PHRASE['maximum']==0?round($PHRASE['fixed'], 2):$PHRASE['maximum'];
								$PHRASE['maximum']=!empty($PHRASE['maximum'])&&$PHRASE['maximum']>0?round($PHRASE['maximum'], 2):$PHRASE['min_price'];//max_price
								
								//РСЯ//
								$PHRASE['context_maximum']=!empty($PHRASE['context_maximum'])&&$PHRASE['context_maximum']>0?round($PHRASE['context_maximum'], 2):$PHRASE['min_price'];//max_price
								$PHRASE['context_minimum']=!empty($PHRASE['context_minimum'])&&$PHRASE['context_minimum']>0?round($PHRASE['context_minimum'], 2):$PHRASE['min_price'];
								$PHRASE['context_fixed']=round($PHRASE['context_fixed'], 2);
								$PHRASE['context_optimum']=0;
								$PHRASE['context_optimum_percent']=0;
								$PHRASE['context_coverage']='';
								$PHRASE['context_percent']=!empty($PHRASE['context_percent']) && (int)$PHRASE['context_percent']>1?(int)$PHRASE['context_percent']:100;
								$PHRASE['context_max']=0;
								$PHRASE['context_medium']=0;
								$PHRASE['context_min']=0;
								if (!empty($BANNERS[$PHRASE['id']]['ContextCoverage']) && is_array($BANNERS[$PHRASE['id']]['ContextCoverage'])) {

									if (!empty($BANNERS[$PHRASE['id']]['ContextCoverage'][0]['Price'])) {
										if (!empty($BANNERS[$PHRASE['id']]['ContextCoverage'][0]['Probability']))
											$PHRASE['context_max']=round($BANNERS[$PHRASE['id']]['ContextCoverage'][0]['Price'], 2);										
									}
									
									if (!empty($BANNERS[$PHRASE['id']]['ContextCoverage'][1]['Price'])) {
										if (!empty($BANNERS[$PHRASE['id']]['ContextCoverage'][1]['Probability']))
											$PHRASE['context_medium']=round($BANNERS[$PHRASE['id']]['ContextCoverage'][1]['Price'], 2);
									}
									
									$CONTEXT_COVERAGES=array();
									foreach($BANNERS[$PHRASE['id']]['ContextCoverage'] as $CONTEXT_COVERAGE)
										$CONTEXT_COVERAGES[]=array('price'=>round($CONTEXT_COVERAGE['Price'], 2), 'percent'=>round($CONTEXT_COVERAGE['Probability'], 2));
									//$CONTEXT_COVERAGES=array_reverse($CONTEXT_COVERAGES);
									$PHRASE['CONTEXT_COVERAGE']=$CONTEXT_COVERAGES;
									if (!empty($PHRASE['CONTEXT_COVERAGE']) && is_array(($PHRASE['CONTEXT_COVERAGE']))) {
										foreach ($PHRASE['CONTEXT_COVERAGE'] as $VALUE) {
											if ($PHRASE['context_percent']>0 && $PHRASE['context_maximum']>0) {
												if ($PHRASE['context_percent']>=$VALUE['percent'] && $PHRASE['context_maximum']>=$VALUE['price']) {
													$PHRASE['context_optimum']=$VALUE['price'];
													$PHRASE['context_optimum_percent']=$VALUE['percent'];
													break;
												}
											}
											
										}
										
									}
									if (!empty($CONTEXT_COVERAGES))
										$PHRASE['context_coverage']=json_encode($CONTEXT_COVERAGES);
									
									$PHRASE['context_min']=array_pop($BANNERS[$PHRASE['id']]['ContextCoverage']);
									if (!empty($PHRASE['context_min']['Price']))
										$PHRASE['context_min']=round($PHRASE['context_min']['Price'], 2);
									else
										$PHRASE['context_min']=0;
									
								}
								//\РСЯ//
								
								$PHRASE['min']=$BANNERS[$PHRASE['id']]['AuctionBids'][6]['Bid'];
								$PHRASE['down_second_price']=$BANNERS[$PHRASE['id']]['AuctionBids'][4]['Bid'];
								$PHRASE['down_second_price_min']=$BANNERS[$PHRASE['id']]['AuctionBids'][5]['Bid'];
								$PHRASE['max']=$BANNERS[$PHRASE['id']]['AuctionBids'][3]['Bid'];
								$PHRASE['premium_min']=$BANNERS[$PHRASE['id']]['AuctionBids'][2]['Bid'];
								$PHRASE['second_price']=$PHRASE['second_price_min']=$BANNERS[$PHRASE['id']]['AuctionBids'][1]['Bid'];
								$PHRASE['premium_max']=$BANNERS[$PHRASE['id']]['AuctionBids'][0]['Bid'];
								$PHRASE['real_price']=(!empty($BANNERS[$PHRASE['id']]['CurrentOnSearch'])?$BANNERS[$PHRASE['id']]['CurrentOnSearch']:$BANNERS[$PHRASE['id']]['MinPrice']);
								$PHRASE['real_price_old']=(!empty($BANNERS[$PHRASE['id']]['CurrentOnSearch'])?$BANNERS[$PHRASE['id']]['CurrentOnSearch']:$BANNERS[$PHRASE['id']]['MinPrice']);
								if (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']]))
									if ($CURRENCY['ID'][$PHRASE['company_currency']]['round']==0)
										$PHRASE['step']=1;
									elseif ($CURRENCY['ID'][$PHRASE['company_currency']]['round']==1)
										$PHRASE['step']=0.1;
									else
										$PHRASE['step']=0.01;
								else
									$PHRASE['step']=0.01;
								
								$PHRASE['place']=($PHRASE['real_price']>=$PHRASE['premium_min'])?2:(($PHRASE['real_price']>=$PHRASE['min'])?1:0);
								$PHRASE['fixed']=round($PHRASE['fixed'], 2);
								
								$PHRASE['context_price']=!empty($BANNERS[$PHRASE['id']]['ContextPrice'])?$BANNERS[$PHRASE['id']]['ContextPrice']:$PHRASE['context_price'];
								$PHRASE['context_price_old']=!empty($BANNERS[$PHRASE['id']]['ContextPrice'])?$BANNERS[$PHRASE['id']]['ContextPrice']:$PHRASE['context_price'];
																
								$PHRASE['company_sum']=$PHRASE['company_sum']+$PHRASE['company_sum_context'];
								
								$PHRASE['show28']=!empty($BANNERS[$PHRASE['id']]['Shows'])?$BANNERS[$PHRASE['id']]['Shows']:$PHRASE['show28'];
								$PHRASE['show_context28']=!empty($BANNERS[$PHRASE['id']]['ContextShows'])?$BANNERS[$PHRASE['id']]['ContextShows']:0;
								$PHRASE['click28']=!empty($BANNERS[$PHRASE['id']]['Clicks'])?$BANNERS[$PHRASE['id']]['Clicks']:$PHRASE['click28'];
								$PHRASE['click_context28']=!empty($BANNERS[$PHRASE['id']]['ContextClicks'])?$BANNERS[$PHRASE['id']]['ContextClicks']:0;
								$PHRASE['ctr28']=!empty($PHRASE['show28'])?round(100*(int)$PHRASE['click28']/((int)$PHRASE['show28']>0?(int)$PHRASE['show28']:1),2):0;
								$PHRASE['ctr_context28']=!empty($PHRASE['show_context28'])?round(100*(int)$PHRASE['click_context28']/((int)$PHRASE['show_context28']>0?(int)$PHRASE['show_context28']:1),2):0;
																							
								$PHRASE['price_old']=!empty($BANNERS[$PHRASE['id']]['Price'])?$BANNERS[$PHRASE['id']]['Price']:$PHRASE['price'];
								
								$PHRASE['sum365']=round($PHRASE['sum365'], 2);
								$PHRASE['ctr365']=round(100*(int)$PHRASE['click365']/((int)$PHRASE['show365']>0?(int)$PHRASE['show365']:1),2);
								
								$PHRASE['param1']=round($PHRASE['param1'], 2);
								$PHRASE['param2']=round($PHRASE['param2'], 2);
								$PHRASE['param3']=round($PHRASE['param3'], 2);
								
								if (!empty($BANNERS[$PHRASE['id']]['AuctionBids'])) {
									for ($i=0; $i<=6; $i++) {
										$PHRASE['position'.($i+1)]=$BANNERS[$PHRASE['id']]['AuctionBids'][$i]['Bid'];
										$PHRASE['price'.($i+1)]=$BANNERS[$PHRASE['id']]['AuctionBids'][$i]['Price'];
									}
								}
							} else {
								$ERROR['PHRASE']['empty']++;
								$this->Framework->direct->phrase->delete($PHRASE['id']);
								if ($ERROR['PHRASE']['empty']<=1) 
									$this->Framework->library->error->set('Не удалось получить данные по фразе ('.$PHRASE['id'].').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
								continue;
							}
							//\Получаем ставки//
							
							//Обрабатываем стратегии//
							if (!empty($STRATEGIES[$PHRASE['strategy']])) {
								
								ob_start();
								eval($STRATEGIES[$PHRASE['strategy']]);
								$error=ob_get_contents();
								ob_end_clean();
								if (!empty($error))
									$this->Framework->library->error->set('Ошибка выполнения стратегии №'.$PHRASE['strategy'].' ('.$error.').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
	
								$price = $PHRASE['price'];
								if (!empty($PHRASE['context']) && !empty($PHRASE['context_price']) && $PHRASE['context_price']>0)
									$context_price = $this->Framework->library->math->ceil ( $PHRASE['context_price'], (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['round']:2) );
								else
									$context_price = 0;
							} else
								$price=0;
							//\Обрабатываем стратегии//
							
							
							
							
							$price=round($price, 3);
							$price = $this->Framework->library->math->ceil ( $price, (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['round']:2) );
							
							
							$BID=array (
									'PhraseID' => $PHRASE ['id'],
									//'BannerID' => $PHRASE ['banner'],
									'CampaignID' => $PHRASE ['company'],
									'Currency' => (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['key']:null)
							);
							
							if ($price>0) {
								if ($price<$PHRASE['min_price'])
									$price=$PHRASE['min_price'];
								elseif ($price>$PHRASE['max_price'])
									$price=$PHRASE['max_price'];	
								$BID['Price'] = $price;
							}								
							
							if ($context_price>0 && ($PHRASE['context_strategy_name']=='MaximumCoverage')) {
								//РСЯ//
								if ($context_price<$PHRASE['min_price'])
									$context_price=$PHRASE['min_price'];
								elseif ($context_price>$PHRASE['max_price'])
									$context_price=$PHRASE['max_price'];
		
								$BID['ContextPrice']=$context_price;
								//\РСЯ//
							} else
								$context_price=0;
															
							$SAVE=array(
								'id'=>$PHRASE['id'], 
								'min'=>$PHRASE['min'], 
								'max'=>$PHRASE['max'], 
								'premium_min'=>$PHRASE['premium_min'], 
								'premium_max'=>$PHRASE['premium_max'], 
								'context_min'=>$PHRASE['context_min'], 
								'context_max'=>$PHRASE['context_max'], 
								'real_price'=>$this->Framework->direct->model->formula->place($PHRASE)>0?$PHRASE['price'.$this->Framework->direct->model->formula->place($PHRASE)]:$PHRASE['real_price'],
								'position2'=>$PHRASE['position2'], 
								'position5'=>$PHRASE['position5'], 
								'position6'=>$PHRASE['position6'],
								'price1'=>$PHRASE['price1'], 
								'price2'=>$PHRASE['price2'], 
								'price3'=>$PHRASE['price3'], 
								'price4'=>$PHRASE['price4'], 
								'price5'=>$PHRASE['price5'], 
								'price6'=>$PHRASE['price6'], 
								'price7'=>$PHRASE['price7'],
								'min_price'=>$PHRASE['minimum'], 
								'price'=>($price>0?$price:$PHRASE['price_old']), 
								'place'=>$this->Framework->direct->model->formula->place($PHRASE),
								'show28'=>$PHRASE['show28'], 
								'click28'=>$PHRASE['click28'], 
								'ctr_context28'=>$PHRASE['ctr_context28'], 
								'show_context28'=>$PHRASE['show_context28'], 
								'click_context28'=>$PHRASE['click_context28'], 
								'ctr28'=>$PHRASE['ctr28'],	
								
								'position'=>(int)$PHRASE['position'],
								
								'param1'=>round($PHRASE['param1'], 2),								
								'param2'=>round($PHRASE['param2'], 2),								
								'param3'=>round($PHRASE['param3'], 2),
								
								'context_coverage'=>$PHRASE['context_coverage'],
								'time'=>date('Y-m-d H:i:s')
							);
							if (!empty($context_price) && $context_price>0)
								$SAVE['context_price']=$BID['ContextPrice'];
							else
								$SAVE['context_price']=$PHRASE['context_price'];
							if (isset($BANNERS[$PHRASE['id']]['StatusPaused']))
								$SAVE['status']=$BANNERS[$PHRASE['id']]['StatusPaused']=='Yes'?2:1;
							$save=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], $SAVE);
							if ($this->debug) if (!$save) echo "WARNING SAVE\r\n";
							if ($save)
								$count_phrases++;
							
							//Сохраняем статистику//
							$statistic_price=!empty($this->Framework->direct->model->config->CONFIG['statistic'])?(int)$this->Framework->direct->model->config->CONFIG['statistic_price']:0;
							if (!empty($statistic_price)) {
								$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic_price_temp'], array(
									'account'=>$PHRASE['account'],
									'user'=>$PHRASE['user'],
									'company'=>$PHRASE['company'],
									'group'=>$PHRASE['group'],
									'phrase'=>$PHRASE['id'], 
									'currency'=>(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['id']:0), 
									'position1'=>$PHRASE['position1'], 
									'position2'=>$PHRASE['position2'], 
									'position3'=>$PHRASE['position3'], 
									'position4'=>$PHRASE['position4'], 
									'position5'=>$PHRASE['position5'], 
									'position6'=>$PHRASE['position6'], 
									'position7'=>$PHRASE['position7'], 
									'price1'=>$PHRASE['price1'], 
									'price2'=>$PHRASE['price2'], 
									'price3'=>$PHRASE['price3'], 
									'price4'=>$PHRASE['price4'], 
									'price5'=>$PHRASE['price5'], 
									'price6'=>$PHRASE['price6'], 
									'price7'=>$PHRASE['price7'], 									
									'real_price'=>$this->Framework->direct->model->formula->place($PHRASE)>0?$PHRASE['price'.$this->Framework->direct->model->formula->place($PHRASE)]:$PHRASE['real_price'], 
									'price'=>$price, 
									'position'=>$this->Framework->direct->model->formula->place($PHRASE),
									'context'=>$PHRASE['context_price'],
									'context_max'=>$PHRASE['context_max'],
									'context_percent'=>$PHRASE['context_optimum_percent'],
									'datetime'=>date('Y-m-d H:i:s')
								));
							}
							//\Сохраняем статистику//
			
							if ($PHRASE ['strategy'] != -2 && ( ($price>0 && $PHRASE['price_old']!=$price) || ($context_price>0 && $PHRASE['context_price_old']!=$context_price)))
								$PARAM[] = $BID;
							else
								$counter--;
							if ($counter > 0 && fmod ( $counter + 1, $this->updateprices_max ) == 0) {
								$this->send($PARAM);
								$PARAM=array();
								$counter=0;
							} else 
								$counter ++;
							$count++;
							
						}
						
						if (isset($BANNERS[$PHRASE['id']]))
							unset($BANNERS[$PHRASE['id']]);
					}
					
					if (!empty($BANNERS)) {
						foreach ($BANNERS as $BANNER) {
							if (!empty($BANNER['PhraseID']) && !empty($BANNER['user'])) {
								$PHRASE_ID=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['phrase'], 
									array('id'=>$BANNER['PhraseID']),
									array(),
									array('id')
								);
								if (empty($PHRASE_ID[0]['id'])) {
									$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
										'id'=>$BANNER['PhraseID'],
										'account'=>$LOGIN['id'],
										'user'=>$BANNER['user'],
										'company'=>$BANNER['CampaignID'],
										'banner'=>$BANNER['BannerID'],
										'group'=>$BANNER['AdGroupId'],
										'price'=>$BANNER['Price'],
										'context_price'=>$BANNER['ContextPrice'],
										'name'=>'Ожидает синхронизации',
										'status'=>$BANNER['StatusPaused']=='Yes'?2:1,
									));
								}
							}
						}
						unset($BANNER);
					}
					unset($BANNERS);
					if ($this->debug) echo 'time='.(time()-$time).' sec'."<br>\r\n";
					if ($count_result<$this->limit)
						break;
					$offset+=$this->limit;
					
				} else
					break;
			}
			if ($this->debug) echo 'phrase count='.$count.' ('.$count_phrases.'), banner count='.$count_banners.', memory='.memory_get_peak_usage(true).'('.memory_get_peak_usage().')'."<br>\r\n";
			$this->send($PARAM);
			//\Устанавливаем ставки//		
			//Обновление ставок ретаргетинга//
			if (!empty($CONFIG['auction_retargeting'])) {
				$time=time();
				$offset=0;
				while (true) {
					if ($this->debug) echo 'offset='.$offset.' '.date('Y-m-d H:i:s')."\r\n";
					$time=time();
					$RETARGET=array();
					$RETARGETING_COMPANY=array();
					$limit=" LIMIT ".$offset.", ".$this->limit." ";
					$sql="SELECT 
					`t1`.`id`, 
					`t1`.`account`, 
					`t1`.`user`, 
					`t1`.`company`, 
					`t1`.`group`, 
					`t1`.`banner`,   
					`t1`.`context_price`,   
					`t1`.`context_price`, 
					`t1`.`context_min`, 
					`t1`.`context_max`, 
					`t1`.`strategy` as `strategy`, 
					`t1`.`type` as `type`, 
					`t1`.`percent` as `percent`, 
					`t1`.`add` as `add`, 
					`t1`.`maximum` as `maximum`,
					`t1`.`fixed` as `fixed`,
					`t1`.`sum` as `sum`,
					`t1`.`budget` as `budget`,
					`t1`.`click` as `click`,
					`t1`.`show` as `show`,
					`t1`.`ctr` as `ctr`,
					`t1`.`click28` as `click28`,
					`t1`.`show28` as `show28`,
					`t1`.`ctr28` as `ctr28`,					
					
					`t1`.`sum365` as `sum365`,
					`t1`.`click365` as `click365`,
					`t1`.`show365` as `show365`,
					
					`t1`.`depth` as `depth`,
					`t1`.`cost` as `cost`,
					`t1`.`conversion` as `conversion`,
					
					-- `t1`.`param1` as `param1`,					
					-- `t1`.`param2` as `param2`,					
					-- `t1`.`param3` as `param3`,					
					
					`t1`.`context` as `context`,	
					`t1`.`context_percent` as `context_percent`,
					`t1`.`context_type` as `context_type`,
					`t1`.`context_maximum` as `context_maximum`,	
					`t1`.`context_fixed` as `context_fixed`,
					`t1`.`context_minimum` as `context_minimum`,
					`t1`.`status` as `status`, 
					
					`t2`.`strategy` as `banner_strategy`, 
					`t2`.`type` as `banner_type`, 
					`t2`.`percent` as `banner_percent`, 
					`t2`.`add` as `banner_add`, 
					`t2`.`maximum` as `banner_maximum`,
					`t2`.`fixed` as `banner_fixed`,
					`t2`.`sum` as `banner_sum`,
					`t2`.`budget` as `banner_budget`,
					`t2`.`click` as `banner_click`,
					`t2`.`show` as `banner_show`,
					`t2`.`ctr` as `banner_ctr`,
					`t2`.`click28` as `banner_click28`,
					`t2`.`show28` as `banner_show28`,
					`t2`.`ctr28` as `banner_ctr28`,					
					`t2`.`context` as `banner_context`,	
					`t2`.`context_percent` as `banner_context_percent`,
					`t2`.`context_type` as `banner_context_type`,
					`t2`.`context_maximum` as `banner_context_maximum`,	
					`t2`.`context_fixed` as `banner_context_fixed`,
					`t2`.`context_minimum` as `banner_context_minimum`,
					`t2`.`status` as `banner_status`,
					`t2`.`count` as `banner_count`,
					`t2`.`domain` as `banner_url`,
					
					`t3`.`strategy` as `company_strategy`, 
					`t3`.`type` as `company_type`, 
					`t3`.`percent` as `company_percent`, 
					`t3`.`add` as `company_add`, 
					`t3`.`maximum` as `company_maximum`,
					`t3`.`fixed` as `company_fixed`,
					`t3`.`context` as `company_context`,	
					`t3`.`context_percent` as `company_context_percent`,
					`t3`.`context_type` as `company_context_type`,
					`t3`.`context_maximum` as `company_context_maximum`,
					`t3`.`context_fixed` as `company_context_fixed`,					
					`t3`.`context_minimum` as `company_context_minimum`,					
					`t3`.`sum` as `company_sum`,
					`t3`.`sum_context` as `company_sum_context`,
					`t3`.`price` as `company_price`,
					`t3`.`budget` as `company_budget`,
					`t3`.`click` as `company_click`,
					`t3`.`show` as `company_show`,
					`t3`.`ctr` as `company_ctr`,
					`t3`.`click28` as `company_click28`,
					`t3`.`show28` as `company_show28`,
					`t3`.`ctr28` as `company_ctr28`,
					
					`t3`.`depth` as `company_depth`,
					`t3`.`depth_context` as `company_depth_context`,
					`t3`.`conversion` as `company_conversion`,
					`t3`.`conversion_context` as `company_conversion_context`,
					`t3`.`cost` as `company_cost`,
					`t3`.`cost_context` as `company_cost_context`,
					
					`t3`.`currency` as `company_currency`,
					`t3`.`status` as `company_status`,
					`t3`.`user` as `user`,
					`t3`.`account` as `account`,
					
					`t3`.`strategy_name` as `strategy_name`,
					`t3`.`context_strategy_name` as `context_strategy_name`,
					
					`t4`.`login` as `login`
					FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` `t1` {$join}  WHERE {$where_param} {$where_status} {$where_strategy} 
					{$order}
					{$limit}
					";
					$result=$this->Framework->db->set($sql);
					$count_result=$this->Framework->db->count($result);
					if ($result && $count_result) {
						while ($PHRASE=$this->Framework->db->get($result)) {
							if ($this->microsecond>0)
								usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
							$count_retargeting++;
							$PHRASE['id']=(string)$PHRASE['id'];
							$PHRASE['banner']=(string)$PHRASE['banner'];
							$PHRASE['group']=(string)$PHRASE['group'];
							if (!isset($RETARGETING_COMPANY[$PHRASE['company']]))
								$RETARGETING_COMPANY[$PHRASE['company']]=$PHRASE['company'];
							if (empty($memory_login) || $memory_login!=$PHRASE['login']) {
								$RETARGET = array('login'=>$PHRASE['login'], 'currency'=>(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['key']:null), 'ELEMENT'=>array());
								if (!empty($RETARGET['ELEMENT']))
									$RETARGETING=$this->Framework->direct->model->retargeting->set($RETARGET);
							}
							
							if ($PHRASE['strategy']==0) {
								if ($PHRASE['banner_strategy']==0) {
									if ($PHRASE['company_strategy']==0) {
										
									} else
										foreach ($STRATEGY as $value) 
											$PHRASE[$value]=round($PHRASE['company_'.$value], 2);
								} else
									foreach ($STRATEGY as $value) 
											$PHRASE[$value]=round($PHRASE['banner_'.$value], 2);
							}
							
							//Получаем стратегию//
							if ($PHRASE ['strategy']>0 && !isset($STRATEGIES[$PHRASE ['strategy']])) {
								$STRATEGIES[$PHRASE['strategy']]=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('id'=>$PHRASE['strategy']));
								if (!empty($STRATEGIES[$PHRASE['strategy']][0]))
									$STRATEGIES[$PHRASE['strategy']]=$STRATEGIES[$PHRASE['strategy']][0]['value'];
								if (!empty($STRATEGIES[$PHRASE['strategy']]))
									$STRATEGIES[$PHRASE['strategy']]=$this->Framework->direct->model->formula->set($STRATEGIES[$PHRASE['strategy']]);
							}
							//\Получаем стратегию//
							
							$PHRASE['min_price']=!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['min'],2):0.01;
							$PHRASE['max_price']=(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['max'],2):84);
							$PHRASE['context_price_old']=round($PHRASE['context_price'], 2);
							
							//РСЯ//
							$PHRASE['context_maximum']=!empty($PHRASE['context_maximum'])&&$PHRASE['context_maximum']>0?round($PHRASE['context_maximum'], 2):$PHRASE['max_price'];
							$PHRASE['context_minimum']=!empty($PHRASE['context_minimum'])&&$PHRASE['context_minimum']>0?round($PHRASE['context_minimum'], 2):$PHRASE['min_price'];
							$PHRASE['context_fixed']=round($PHRASE['context_fixed'], 2);
							$PHRASE['context_optimum']=0;
							$PHRASE['context_optimum_percent']=0;
							$PHRASE['context_coverage']='';
							$PHRASE['context_percent']=0;
							$PHRASE['context_max']=0;
							$PHRASE['context_medium']=0;
							$PHRASE['context_min']=0;
							$PHRASE['CONTEXT_COVERAGE']=array();
							//\РСЯ//
							
							//Обрабатываем стратегии//
							if (!empty($STRATEGIES[$PHRASE['strategy']])) {
								
								ob_start();
								eval($STRATEGIES[$PHRASE['strategy']]);
								$error=ob_get_contents();
								ob_end_clean();
								if (!empty($error))
									$this->Framework->library->error->set('Ошибка выполнения стратегии №'.$PHRASE['strategy'].' ('.$error.').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
								if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
									$this->Framework->library->error->set('Диагностика выполнения стратегии №'.$PHRASE['strategy'].' ('.$STRATEGIES[$PHRASE['strategy']].').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__, true);

								if (!empty($PHRASE['context']) && !empty($PHRASE['context_price']) && $PHRASE['context_price']>0)
									$context_price = $this->Framework->library->math->ceil ( $PHRASE['context_price'], (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['round']:2) );
								else
									$context_price = 0;
							} else
								$context_price=0;
							//\Обрабатываем стратегии//
							
							if ($context_price>0 && $PHRASE['context_strategy_name']=='MaximumCoverage') {
								//РСЯ//
								if ($context_price<$PHRASE['min_price'])
									$context_price=$PHRASE['min_price'];
								elseif ($context_price>$PHRASE['max_price'])
									$context_price=$PHRASE['max_price'];
		
								$BID['ContextPrice']=$context_price;
								//\РСЯ//
							} else
								$context_price=0;
							
							if ($PHRASE ['strategy'] != -2 &&  ($context_price>0 && $PHRASE['context_price_old']!=$context_price)) 
								$RETARGET['ELEMENT'][]=array('id'=>$PHRASE['id'], 'price'=>$context_price);
							$SAVE=array(
								'id'=>$PHRASE['id'], 						
								'context_price'=>($context_price>0?$context_price:$PHRASE['context_price_old']), 					
								'time'=>date('Y-m-d H:i:s')
							);
							$save=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['retargeting'], $SAVE);
							if ($this->debug) if (!$save) echo "WARNING SAVE\r\n";
							$memory_login=$PHRASE['login'];
						}//\while
						if (!empty($RETARGET['ELEMENT']))
							$RETARGETING=$this->Framework->direct->model->retargeting->set($RETARGET);
						if (!empty($RETARGETING_COMPANY))
							$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."`  					
							SET `datetime`='".date('Y-m-d H:i:s')."' 
							WHERE `id` IN (".implode($RETARGETING_COMPANY).")");
					} else 
						break;
					if ($count_result<$this->limit)
						break;
					$offset+=$this->limit;
				}
				if ($this->debug) echo 'SQL Retargeting time='.(time()-$time).' sec, memory='.memory_get_usage(true)."<br>\r\n";
			}
			//\Обновление ставок ретаргетинга//
		}
		
		//Обрабатываем оптимизированные формулы//
		$this->Framework->direct->model->formula->start();
		$this->Framework->direct->model->formula->stop();
		$this->Framework->direct->model->formula->company_false_start();
		//\Обрабатываем оптимизированные формулы//
		
		//Перезаписываем статистику из временной таблицы в основную//
		if (!empty($statistic_price)) {
			$this->Framework->db->set("SELECT MAX(`id`) as `id` FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."`");
			$MAX_ID=$this->Framework->db->get();
			$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['statistic_price']."` (`account`, `user`, `company`, `group`, `phrase`, `currency`, `price`, `real_price`, `position`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`, `position7`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `context`, `context_percent`, `context_max`, `datetime`) SELECT `account`, `user`, `company`, `group`, `phrase`, `currency`, `price`, `real_price`, `position`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`, `position7`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `context`, `context_percent`, `context_max`, `datetime` FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."` WHERE `id`<='".$MAX_ID['id']."'");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."`  WHERE `id`<='".$MAX_ID['id']."'");
		}
		//\Перезаписываем статистику из временной таблицы в основную//

		
		//Обрабатываем ошибки//
		if (!empty($ERROR['PHRASE']['empty']))
			$this->Framework->library->error->set('Не удалось получить данные по фразам: '.$ERROR['PHRASE']['empty'].'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
			$this->Framework->library->error->set('Время запросов к Яндекс.Директ API: '.$this->Framework->direct->model->api->time.' сек. из '.round(microtime(true)-$this->Framework->CONFIG['time'], 4).' сек.', '', '', '', '', '', true);		
		//\Обрабатываем ошибки//
		if ($this->debug) echo 'phrase='.$count_phrases.', retargeting='.$count_retargeting."<br>\r\n";
		return $count;
	}
	
	private function send($PARAM=array()) {
		if ($this->debug) echo 'send='.count($PARAM)."<br>\r\n";
		if (!empty($PARAM) && is_array($PARAM)) {
			$UpdatePrices = $this->Framework->direct->model->api->get ( 'UpdatePrices', $PARAM );
					
			if (isset ( $UpdatePrices->error_str )) 
				$this->Framework->library->error->set('Ошибка обновления ставок ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
	}
	
	
	public function get($PARAM=array()) {
		$DATA=array();
		
		return $DATA;
	}
	
}//\class
?>