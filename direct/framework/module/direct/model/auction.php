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
	
	private $id = 0;
	private $updateprices_max = 10000;
	private $updateprices_call_max = 3000;
	private $limit = 10000;
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
		$this->id=microtime(true)*10000;
		//Чиним сломавшиеся таблицы//
		//$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		$CONFIG=$this->Framework->direct->model->config->CONFIG;
		$this->limit=((int)$this->Framework->direct->model->config->CONFIG['auction']>0&&(int)$this->Framework->direct->model->config->CONFIG['auction']<=$this->limit)?(int)$this->Framework->direct->model->config->CONFIG['auction']:$this->limit;
		//$this->microsecond=!empty($CONFIG['microsecond'])?$CONFIG['microsecond']:$this->microsecond;
		
		$CURRENCY=$this->Framework->direct->model->currency->get();
		$STRATEGIES=array();
		//Получаем список аккаунтов//
		$GET=array(
			'group'=>4, 
			'status'=>1,
			'ID'=>(!empty($PARAMS)?(is_array($PARAMS) && !empty($PARAMS[0])?$PARAMS:(is_array($PARAMS) && !empty($PARAMS['account'])?array($PARAMS['account']):array($PARAMS))):0),
		);
		$ACCOUNTS=$this->Framework->user->model->model->get($GET);
		$USERS=array();
		if (!empty($ACCOUNTS['ELEMENT'])) {
			foreach ($ACCOUNTS['ELEMENT'] as $ACCOUNT) {
				if ($ACCOUNT['children']>0) {
					$SUBACCOUNTS=$this->Framework->user->model->model->get(array('group'=>2, 'account'=>$ACCOUNT['id']));
					foreach ($SUBACCOUNTS['ELEMENT'] as $SUBACCOUNT) {
						unset($SUBACCOUNT['token']);//, $SUBACCOUNT['unit'], $SUBACCOUNT['unit_total'], $SUBACCOUNT['unit_status'], $SUBACCOUNT['unit_time']);
						$USERS[]=array_merge($ACCOUNT, $SUBACCOUNT);
					}
				} else {
					$USERS[]=$ACCOUNT;
				}
			}
			unset($ACCOUNT);
			$ACCOUNTS=$ACCOUNTS['ELEMENT'];
		}
		//\Получаем список аккаунтов//
		
		$count = 0;
		$count_banners = 0;
		$count_phrases = 0;
		$count_retargeting = 0;
		foreach ($USERS as $LOGIN) {
			//Проверяем лимиты//
			$this->Framework->api->yandex->direct->query->reset();
			if ( $LOGIN['unit_status'] && !empty($LOGIN['unit_time']) && strtotime($LOGIN['unit_time'])>=mktime(0, $this->Framework->direct->model->config->time_yandex, 1, date('m'), date('d'), date('Y')) ) {
				if ($this->debug) echo 'LIMIT API ALL for login='.$LOGIN['login'];
				if (strtotime($LOGIN['unit_time'])+3601<time())
					$this->Framework->direct->limit->set(array('id'=>$LOGIN['id'], 'login'=>$LOGIN['login'], 'total'=>$LOGIN['unit_total'], 'unit'=>$LOGIN['unit_total']/24, 'percent'=>100));
				elseif ($this->Framework->direct->limit->get(array('id'=>$LOGIN['id'], 'login'=>$LOGIN['login'], 'total'=>$LOGIN['unit_total'], 'unit'=>$LOGIN['unit'], 'percent'=>$CONFIG['api_percent'])))
					continue;
			}
			//\Проверяем лимиты//
			if ($this->debug) echo 'Auction login='.$LOGIN['login'].($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
			//Устанавливаем авторизационные данные АПИ5//
			$this->Framework->api->yandex->direct->config->login=(string)$LOGIN['login'];
			$this->Framework->api->yandex->direct->config->token=(string)$LOGIN['token'];
			//\Устанавливаем авторизационные данные АПИ4//
			
			$counter = 0;
			$PARAM = array ();
			$STRATEGY=array('strategy', 'type', 'percent', 'add', 'maximum', 'fixed', 'param1', 'param2', 'param3', 'context', 'context_percent', 'context_type', 'context_maximum', 'context_fixed', 'context_minimum');
			$banner=0;

			$where_param=(is_array($PARAMS) && !empty($PARAMS['user'])?' `t1`.`user`='.(int)$PARAMS['user'].' AND ':'').(is_array($PARAMS) && !empty($PARAMS['company']) && is_array($PARAMS['company']) && !empty($PARAMS['company'][0])?' `t1`.`company` IN ('.implode(', ',$PARAMS['company']).') AND ':'');
			$where_priority=($this->Framework->direct->model->config->CONFIG['priority_ctr']>0?"(`t1`.`ctr28`>".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." OR (`t1`.`ctr28`<=".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." AND (`t1`.`time` IS NULL OR `t1`.`time`<='".date('Y-m-d H:i:s', (time()-(int)$this->Framework->direct->model->config->CONFIG['priority_interval']*60))."'))) AND ":'');
			$where_strategy=" `t3`.`user`='".$LOGIN['id']."' AND ( (`t3`.`strategy`!=0 AND `t3`.`strategy`!=-1 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy`!=0 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy` IN (0,-1) AND `t1`.`strategy`!=0 AND `t1`.`strategy`!=-1) ) ";
			$where_status=(!empty($CONFIG['auction_status'])?' `t3`.`status`=1 AND `t2`.`status`=1 AND `t1`.`status`=1 AND `t2`.`rarely`=0 AND ':'');
			$where_price='';
			$where_common=$where_status.$where_price.$where_strategy;
			$group=" GROUP BY `t1`.`id` ";
			$order=" ORDER BY `t3`.`id`, `t2`.`id`, `t1`.`id` ";
			$join="	INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`)
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t3` ON (`t3`.`id`=`t2`.`company`)
			";
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` SET `t1`.`plan`=0 WHERE ".$where_param." `t1`.`user`='".$LOGIN['id']."'");
			$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` {$join} SET `t1`.`plan`='".$this->id."' WHERE {$where_param} {$where_common}");		
			
			$offset=0;			
			while (true) {
				//Проверяем лимиты//
				if ($this->Framework->direct->limit->get(array('id'=>$LOGIN['id'])))
					break;
				//\Проверяем лимиты//
				$BIDS=array();
				$ID=array();
				if ($this->debug) $time=time();
				$sql="SELECT 
					`t1`.`id`, IF(`t1`.`strategy`=6 OR `t2`.`strategy`=6 OR `t3`.`strategy`=6, 1, 0) as `context_flag1`, IF (MAX(`t1`.`context`+`t2`.`context`+`t3`.`context`)>0, 1, 0) as `context_flag`
						FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` 
					{$join}
					WHERE  `t1`.`user`='".$LOGIN['id']."' AND `t1`.`plan`='".$this->id."'
					{$group}
					{$order}
					LIMIT ".$offset.", ".$this->limit."
				";
				$this->Framework->db->set($sql);
				$context_flag=0;
				while ($ROW=$this->Framework->db->get()) {
					$ID[]=$ROW['id'];
					if (!empty($ROW['context_flag1']) || !empty($ROW['context_flag']))
						$context_flag=1;
				}
				if ($this->debug) echo 'Keywords get sql count='.count($ID).', context_flag='.$context_flag.', time='.(time()-$time).' sec'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";

				
				//Получаем фразы из АПИ5//					
				if ($this->debug) $time=time();
				
				if (!empty($ID)) {
					
					$BIDDING=$this->Framework->api->yandex->direct->bid->get(array('id'=>$ID, 'field'=>($context_flag?'context':'search')));
					//Проверяем лимиты//
					if ($this->Framework->direct->limit->get(array('id'=>$LOGIN['id'])))
						break;
					//\Проверяем лимиты//
					if (!empty($BIDDING)) {
						foreach ($BIDDING as $bid=>&$BID) {
							$BID['KeywordId']=(string)$BID['KeywordId'];
							$BID['Bid']=round($BID['Bid']/1000000, 2);
							$BID['ContextBid']=round($BID['ContextBid']/1000000, 2);
							$BID['CurrentSearchPrice']=round($BID['CurrentSearchPrice']/1000000, 2);
							$BID['MinSearchPrice']=round($BID['MinSearchPrice']/1000000, 2);
							/*$BIDS[(string)$BID['KeywordId']]=array(
								'ContextBid' => round($BID['ContextBid']/1000000, 2),
								'BannerID' => $GROUP['banner'][$BID['AdGroupId']],
								'Price' => round($BID['Bid']/1000000, 2),
								'KeywordId' => $BID['KeywordId'],
								'CampaignId' => $BID['CampaignId'],
								'AdGroupId' => $BID['AdGroupId'],
								'CurrentSearchPrice' => round($BID['ContextBid']/1000000, 2),
								'ContextClicks' => 0,
								'Clicks' => 0,
								'Shows' => 0,
								'ContextShows' => 0,
								'MinSearchPrice' => round($BID['MinSearchPrice']/1000000, 2),
							); */
							
							foreach ($BID['AuctionBids'] as $key=>&$AUCTIONBIDS) {
								$AUCTIONBIDS['Bid']=round($AUCTIONBIDS['Bid']/1000000, 2);
								$AUCTIONBIDS['Price']=round($AUCTIONBIDS['Price']/1000000, 2);
								//if ($AUCTIONBIDS['Position']=='P14') unset($BID['AuctionBids'][$key]);
							}
							//$BID['AuctionBids']=array_values($BID['AuctionBids']);
							//if ($this->debug) if ($BID['KeywordId']=='9650421145') echo 'BID=<pre>'.print_r($BID, true).'</pre>';
							//$BIDS[(string)$BID['KeywordId']]['AuctionBids']=$BID['AuctionBids'];
							if (!empty($BID['ContextCoverage']['Items']))
								foreach ($BID['ContextCoverage']['Items'] as &$ContextCoverage) 
									$ContextCoverage['Price']=round($ContextCoverage['Price']/1000000, 2);
							
							//$BIDS[(string)$BID['KeywordId']]['ContextCoverage']=!empty($BID['ContextCoverage']['Items'])?$BID['ContextCoverage']['Items']:array();
							$BIDS[$BID['KeywordId']]=$BID;
							unset($BIDDING[$bid]);
						}
						unset($BIDDING, $BID);
					} else
						break;

					$LIMIT=$this->Framework->api->yandex->direct->query->limit();
					if ($this->debug) echo 'Bids get API5 count='.count($BIDS).', percent='.$CONFIG['api_percent'].'%, time='.(time()-$time).' sec, LIMIT unit='.$LIMIT['unit'].', limit='.$LIMIT['limit'].', daily='.$LIMIT['daily'].($this->Framework->CONFIG['http']?'<br>':'')."\r\n";				
					if (!empty($BIDS)) {
						$ID_DELETE=array_diff($ID, array_keys($BIDS));
						if (!empty($ID_DELETE))
							$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `id`='".implode("' OR `id`='", $ID_DELETE)."'");
						unset($ID_DELETE);
					}
					unset($ID);
				} else 
					break;
				//\Получаем фразы из АПИ5//
				if (empty($BIDS))
					break;
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
					`t1`.`sum_context` as `sum_context`,
					`t1`.`click_context` as `click_context`,
					`t1`.`show_context` as `show_context`,
					`t1`.`ctr_context` as `ctr_context`,
					
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
					
					`t1`.`place` as `place`,
					
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
					`t3`.`param3` as `company_param3`
					
					FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` 
					{$join}
					WHERE  `t1`.`user`='".$LOGIN['id']."' AND `t1`.`plan`='".$this->id."'
					{$group}
					{$order}
					LIMIT ".$offset.", ".$this->limit."
				";

				$time=time();
				$result=$this->Framework->db->set($sql);
				if ($this->debug) echo 'Keywords get sql2 time='.(time()-$time).' sec'."\r\n";
				$count_result=$this->Framework->db->count($result);
				if ($result && $count_result>0) {
					if ($this->debug) echo ($this->Framework->db->count($result)!=count($BIDS)?'WARNING ':'').'get phrase='.$this->Framework->db->count($result).' from '.count($BIDS).', memory='.memory_get_usage(true).'('.memory_get_usage().')'."<br>\r\n";
					//Устанавливаем ставки//
					$BIDS_COMPANY=array();
					$BIDS_GROUP=array();
					while ($PHRASE=$this->Framework->db->get($result)) {
						//if ($this->microsecond>0)
							//usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
						$PHRASE['id']=(string)$PHRASE['id'];
						$PHRASE['banner']=(string)$PHRASE['banner'];
						$PHRASE['group']=(string)$PHRASE['group'];
						$PHRASE['position1']=$PHRASE['position2']=$PHRASE['position3']=$PHRASE['position4']=$PHRASE['position5']=$PHRASE['position6']=$PHRASE['position7']=0;
						$PHRASE['price1']=$PHRASE['price2']=$PHRASE['price3']=$PHRASE['price4']=$PHRASE['price5']=$PHRASE['price6']=$PHRASE['price7']=0;
						$PHRASE['click_context28']=$PHRASE['show_context28']=$PHRASE['ctr_context28']=0;
						
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
							
							//Вычисляем ставки//

							if (!empty($BIDS[$PHRASE['id']])) {
								$BIDS_COMPANY[]=$PHRASE['company'];
								$BIDS_GROUP[]=$PHRASE['group'];
								
								$PHRASE['min_price']=!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['min'],2):0.01;
								$PHRASE['max_price']=(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['max'],2):84);
								$PHRASE['minimum']=!empty($BIDS[$PHRASE['id']]['MinSearchPrice'])?$BIDS[$PHRASE['id']]['MinSearchPrice']:(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?round($CURRENCY['ID'][$PHRASE['company_currency']]['min'],2):0.01);
								$PHRASE['maximum']=empty($PHRASE['maximum'])||(float)$PHRASE['maximum']==0?round($PHRASE['fixed'], 2):$PHRASE['maximum'];
								$PHRASE['maximum']=!empty($PHRASE['maximum'])&&$PHRASE['maximum']>0?round($PHRASE['maximum'], 2):$PHRASE['min_price'];//max_price
								
								//РСЯ//
								$PHRASE['context_minimum']=!empty($PHRASE['context_minimum'])&&$PHRASE['context_minimum']>0?round($PHRASE['context_minimum'], 2):$PHRASE['min_price'];
								$PHRASE['context_maximum']=!empty($PHRASE['context_maximum'])&&$PHRASE['context_maximum']>0?round($PHRASE['context_maximum'], 2):$PHRASE['min_price'];//max_price								
								$PHRASE['context_fixed']=round($PHRASE['context_fixed'], 2);
								$PHRASE['context_optimum']=0;
								$PHRASE['context_optimum_percent']=0;
								$PHRASE['context_coverage']='';
								$PHRASE['context_percent']=!empty($PHRASE['context_percent']) && (int)$PHRASE['context_percent']>1?(int)$PHRASE['context_percent']:100;
								$PHRASE['context_max']=0;
								$PHRASE['context_medium']=0;
								$PHRASE['context_min']=0;
								if (!empty($BIDS[$PHRASE['id']]['ContextCoverage']['Items']) && is_array($BIDS[$PHRASE['id']]['ContextCoverage']['Items'])) {

									if (!empty($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][0]['Price'])) {
										if (!empty($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][0]['Probability']))
											$PHRASE['context_max']=round($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][0]['Price'], 2);										
									}
									
									if (!empty($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][1]['Price'])) {
										if (!empty($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][1]['Probability']))
											$PHRASE['context_medium']=round($BIDS[$PHRASE['id']]['ContextCoverage']['Items'][1]['Price'], 2);
									}
									
									$CONTEXT_COVERAGES=array();
									foreach($BIDS[$PHRASE['id']]['ContextCoverage']['Items'] as $CONTEXT_COVERAGE)
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
									
									$PHRASE['context_min']=array_pop($BIDS[$PHRASE['id']]['ContextCoverage']['Items']);
									if (!empty($PHRASE['context_min']['Price']))
										$PHRASE['context_min']=round($PHRASE['context_min']['Price'], 2);
									else
										$PHRASE['context_min']=0;
									
								}
								//\РСЯ//
								
								$PHRASE['min']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][7]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][7]['Bid']:0;
								$PHRASE['down_second_price']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][5]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][5]['Bid']:0;
								$PHRASE['down_second_price_min']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][6]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][6]['Bid']:0;
								$PHRASE['premium_min']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][2]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][2]['Bid']:0;
								$PHRASE['max']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][4]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][4]['Bid']:0;
								$PHRASE['second_price']=$PHRASE['second_price_min']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][1]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][1]['Bid']:0;
								$PHRASE['premium_max']=!empty($BIDS[$PHRASE['id']]['AuctionBids'][0]['Bid'])?$BIDS[$PHRASE['id']]['AuctionBids'][0]['Bid']:0;
								$PHRASE['real_price']=(!empty($BIDS[$PHRASE['id']]['CurrentSearchPrice'])?$BIDS[$PHRASE['id']]['CurrentSearchPrice']:$BIDS[$PHRASE['id']]['MinSearchPrice']);
								$PHRASE['real_price_old']=(!empty($BIDS[$PHRASE['id']]['CurrentSearchPrice'])?$BIDS[$PHRASE['id']]['CurrentSearchPrice']:$BIDS[$PHRASE['id']]['MinSearchPrice']);
								if (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']]))
									if ($CURRENCY['ID'][$PHRASE['company_currency']]['round']==0)
										$PHRASE['step']=1;
									elseif ($CURRENCY['ID'][$PHRASE['company_currency']]['round']==1)
										$PHRASE['step']=0.1;
									else
										$PHRASE['step']=0.01;
								else
									$PHRASE['step']=0.01;
								
								$PHRASE['place']=$this->Framework->direct->model->formula->place($PHRASE);
								$PHRASE['fixed']=round($PHRASE['fixed'], 2);
								
								$PHRASE['context_price']=!empty($BIDS[$PHRASE['id']]['ContextBid'])?$BIDS[$PHRASE['id']]['ContextBid']:$PHRASE['context_price'];
								$PHRASE['context_price_old']=!empty($BIDS[$PHRASE['id']]['ContextBid'])?$BIDS[$PHRASE['id']]['ContextBid']:$PHRASE['context_price'];
																
								$PHRASE['company_sum']=$PHRASE['company_sum']+$PHRASE['company_sum_context'];
								
								$PHRASE['sum']=round($PHRASE['sum'], 2);
								$PHRASE['sum_context']=round($PHRASE['sum_context'], 2);
								$PHRASE['ctr']=round($PHRASE['ctr'], 2);
								$PHRASE['ctr_context']=round($PHRASE['ctr_context'], 2);
								$PHRASE['ctr28']=round($PHRASE['ctr28'], 2);
								$PHRASE['ctr_context28']=round($PHRASE['ctr_context28'], 2);
																							
								$PHRASE['price_old']=!empty($BIDS[$PHRASE['id']]['Bid'])?$BIDS[$PHRASE['id']]['Bid']:$PHRASE['price'];
								$PHRASE['place_old']=$PHRASE['place'];
								
								$PHRASE['cost']=round($PHRASE['cost'], 2);
								$PHRASE['conversion']=round($PHRASE['conversion'], 2);
								$PHRASE['sum365']=round($PHRASE['sum365'], 2);
								$PHRASE['ctr365']=round(100*(int)$PHRASE['click365']/((int)$PHRASE['show365']>0?(int)$PHRASE['show365']:1),2);
								
								$PHRASE['param1']=round($PHRASE['param1'], 2);
								$PHRASE['param2']=round($PHRASE['param2'], 2);
								$PHRASE['param3']=round($PHRASE['param3'], 2);
								//if ($this->debug) if ($PHRASE['id']=='9650421145') echo '<pre>'.print_r($BIDS[$PHRASE['id']], true).'</pre>';
								if (!empty($BIDS[$PHRASE['id']]['AuctionBids'])) {
									$position=0;
									for ($i=0; $i<=7; $i++) {
										$PHRASE['bid'.($i+1)]=$BIDS[$PHRASE['id']]['AuctionBids'][$i]['Bid'];
										$PHRASE['cost'.($i+1)]=$BIDS[$PHRASE['id']]['AuctionBids'][$i]['Price'];
										if ($i!=3) {
											$PHRASE['position'.($position+1)]=$BIDS[$PHRASE['id']]['AuctionBids'][$i]['Bid'];
											$PHRASE['price'.($position+1)]=$BIDS[$PHRASE['id']]['AuctionBids'][$i]['Price'];
											$position++;
										}
									}
									unset($position);
								}
								//if ($this->debug) if ($PHRASE['id']=='9650421145') echo '====<pre>'.print_r($PHRASE, true).'</pre>';
							} else {
								$ERROR['PHRASE']['empty']++;
								//$this->Framework->direct->phrase->delete($PHRASE['id']);
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
							
							//БИДЫ//
							$BID=array (
								'KeywordId' => $PHRASE ['id'],
							);
							//Поиск//
							if ($PHRASE ['strategy']!=-2 && $price>0) {// && (empty($PHRASE['strategy_name']) || in_array($PHRASE['strategy_name'], array('UNKNOWN', 'AVERAGE_CPA', 'AVERAGE_CPC', 'AVERAGE_CPI', 'AVERAGE_ROI', 'WB_MAXIMUM_CLICKS', 'WB_MAXIMUM_CONVERSION_RATE', 'WEEKLY_CLICK_PACKAGE', 'HighestPosition', 'HIGHEST_POSITION', 'ShowsDisabled', 'SERVING_OFF', 'LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')))
								if ($price<$PHRASE['min_price'])
									$price=$PHRASE['min_price'];
								elseif ($price>$PHRASE['max_price'])
									$price=$PHRASE['max_price'];	
							}  else
								$price=0;
							if ($PHRASE ['strategy']!=-2 && $price==0 && $context_price>0 && in_array($PHRASE['context_strategy_name'], array('Default', 'NETWORK_DEFAULT')))
								$price=$PHRASE['min_price'];
							if ($price>0)
								$BID['Bid'] = $price*1000000;
							//\Поиск//
							if (in_array($PHRASE['strategy_name'], array('WB_MAXIMUM_CLICKS', 'WB_MAXIMUM_CONVERSION_RATE', 'WEEKLY_CLICK_PACKAGE', 'AVERAGE_CPA', 'AVERAGE_CPC', 'AVERAGE_CPI', 'AVERAGE_ROI')) || in_array($PHRASE['context_strategy_name'], array('WB_MAXIMUM_CLICKS', 'WB_MAXIMUM_CONVERSION_RATE', 'WEEKLY_CLICK_PACKAGE', 'AVERAGE_CPA', 'AVERAGE_CPC', 'AVERAGE_CPI', 'AVERAGE_ROI')))
								$BID['StrategyPriority']='NORMAL';	
							//РСЯ//
							if ($PHRASE ['strategy']!=-2 && $context_price>0 && ($PHRASE['context_strategy_name']=='MaximumCoverage' || $PHRASE['context_strategy_name']=='MAXIMUM_COVERAGE')) {
								if ($context_price<$PHRASE['min_price'])
									$context_price=$PHRASE['min_price'];
								elseif ($context_price>$PHRASE['max_price'])
									$context_price=$PHRASE['max_price'];
							} else
								$context_price=0;
							if ($PHRASE ['strategy']!=-2 && $price>0 && empty($context_price) && ($PHRASE['context_strategy_name']=='MaximumCoverage' || $PHRASE['context_strategy_name']=='MAXIMUM_COVERAGE')) 
								$context_price=$PHRASE['min_price'];
							elseif ($PHRASE ['strategy']!=-2 && $price==0 && $context_price==0 && in_array($PHRASE['context_strategy_name'], array('WB_MAXIMUM_CLICKS', 'WB_MAXIMUM_CONVERSION_RATE', 'WEEKLY_CLICK_PACKAGE', 'AVERAGE_CPA', 'AVERAGE_CPC', 'AVERAGE_CPI', 'AVERAGE_ROI')))
								$context_price=$PHRASE['min_price'];
							if ($context_price>0) 
								$BID['ContextBid']=$context_price*1000000;
							//\РСЯ//
							//\БИДЫ//
							//if ($PHRASE['id']=='5975641184') echo 'place='.$PHRASE['cost'.$this->Framework->direct->model->formula->place($PHRASE)].'<pre>'.print_r($BID, true).print_r($PHRASE, true).'</pre>';
							$SAVE=array(
								'id'=>$PHRASE['id'], 
								'min'=>$PHRASE['bid8'], 
								'max'=>$PHRASE['bid5'], 
								'premium_min'=>$PHRASE['bid3'], 
								'premium_max'=>$PHRASE['bid1'], 
								'bid4'=>$PHRASE['bid4'],
								'context_min'=>$PHRASE['context_min'], 
								'context_max'=>$PHRASE['context_max'], 
								'real_price'=>$this->Framework->direct->model->formula->place($PHRASE)>0?$PHRASE['cost'.$this->Framework->direct->model->formula->place($PHRASE)]:$PHRASE['real_price'],
								'position2'=>$PHRASE['bid2'], 
								'position5'=>$PHRASE['bid6'], 
								'position6'=>$PHRASE['bid7'],
								'price1'=>$PHRASE['cost1'], 
								'price2'=>$PHRASE['cost2'], 
								'price3'=>$PHRASE['cost3'], 
								'price4'=>$PHRASE['cost4'], 
								'price5'=>$PHRASE['cost5'], 
								'price6'=>$PHRASE['cost6'], 
								'price7'=>$PHRASE['cost7'],
								'price8'=>$PHRASE['cost8'],
								'min_price'=>$PHRASE['minimum'], 
								'price'=>($price>0?$price:$PHRASE['price_old']), 
								'place'=>$this->Framework->direct->model->formula->place($PHRASE),	
								
								'param1'=>round($PHRASE['param1'], 2),								
								'param2'=>round($PHRASE['param2'], 2),								
								'param3'=>round($PHRASE['param3'], 2),
								'context_coverage'=>$PHRASE['context_coverage'],
							);
							if ($price>0 || $context_price>0 || $PHRASE ['strategy']==-2)
								$SAVE['time']=date('Y-m-d H:i:s');
							if (!empty($context_price) && $context_price>0)
								$SAVE['context_price']=$context_price;
							else
								$SAVE['context_price']=$PHRASE['context_price_old'];
							if (isset($BIDS[$PHRASE['id']]['StatusPaused']))
								$SAVE['status']=$BIDS[$PHRASE['id']]['StatusPaused']=='Yes'?2:1;
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
									'position1'=>$PHRASE['bid1'], 
									'position2'=>$PHRASE['bid2'], 
									'position3'=>$PHRASE['bid3'], 
									'position4'=>$PHRASE['bid4'], 
									'position5'=>$PHRASE['bid5'], 
									'position6'=>$PHRASE['bid6'], 
									'position7'=>$PHRASE['bid7'], 
									'position8'=>$PHRASE['bid8'], 
									'price1'=>$PHRASE['cost1'], 
									'price2'=>$PHRASE['cost2'], 
									'price3'=>$PHRASE['cost3'], 
									'price4'=>$PHRASE['cost4'], 
									'price5'=>$PHRASE['cost5'], 
									'price6'=>$PHRASE['cost6'], 
									'price7'=>$PHRASE['cost7'], 									
									'price8'=>$PHRASE['cost8'], 									
									'real_price'=>$this->Framework->direct->model->formula->place($PHRASE)>0?$PHRASE['cost'.$this->Framework->direct->model->formula->place($PHRASE)]:$PHRASE['real_price'], 
									'price'=>($price>0?$price:$PHRASE['price_old']), 
									'position'=>$this->Framework->direct->model->formula->place($PHRASE),
									'context'=>($context_price>0?$context_price:$PHRASE['context_price_old']),
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
						
						if (isset($BIDS[$PHRASE['id']]))
							unset($BIDS[$PHRASE['id']]);
						unset($PHRASE);
					}
					if (!empty($BIDS_COMPANY))
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."`  					
							SET `datetime`='".date('Y-m-d H:i:s')."' 
							WHERE `id` IN (".implode(',', $BIDS_COMPANY).")
						");
					if (!empty($BIDS_GROUP))
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."`  					
							SET `datetime`='".date('Y-m-d H:i:s')."' 
							WHERE `id` IN (".implode(',', $BIDS_GROUP).")
						");

					unset($BIDS, $BIDS_COMPANY, $BIDS_GROUP);
					
					if ($this->debug) echo 'time='.(time()-$time).' sec'."<br>\r\n";
					if ($count_result<$this->limit)
						break;
					$offset+=$this->limit;
					
				} else
					break;
			}//\while

			if ($this->debug) echo 'phrase count='.$count.' ('.$count_phrases.'), banner count='.$count_banners.', memory='.memory_get_peak_usage(true).'('.memory_get_peak_usage().')'."<br>\r\n";
			$this->send($PARAM);
			//\Устанавливаем ставки//
			
			//Обрабатываем оптимизированные формулы//
			$this->Framework->direct->model->formula->banner_start();
			$this->Framework->direct->model->formula->banner_stop();
			$this->Framework->direct->model->formula->start();
			$this->Framework->direct->model->formula->stop();
			//\Обрабатываем оптимизированные формулы//
			
			//Записываем лимиты//
			$this->Framework->direct->limit->set(array('id'=>$LOGIN['id'], 'login'=>$LOGIN['login'], 'total'=>$this->Framework->api->yandex->direct->config->daily, 'unit'=>$this->Framework->api->yandex->direct->config->limit, 'percent'=>$CONFIG['api_percent']));
			//\Записываем лимиты//
		}

				
		//Обновление ставок ретаргетинга//
		if (!empty($CONFIG['auction_retargeting'])) {
			foreach ($ACCOUNTS as $LOGIN) {
				$time=time();
				//Устанавливаем авторизационные данные//
				$this->Framework->direct->model->config->login=(string)$LOGIN['login'];
				$this->Framework->direct->model->config->token=(string)$LOGIN['token'];
				//\Устанавливаем авторизационные данные//
				$where_param=(is_array($PARAMS) && !empty($PARAMS['user'])?' `t1`.`user`='.(int)$PARAMS['user'].' AND ':'').(is_array($PARAMS) && !empty($PARAMS['company']) && is_array($PARAMS['company']) && !empty($PARAMS['company'][0])?' `t1`.`company` IN ('.implode(', ',$PARAMS['company']).') AND ':'');
				$where_priority=($this->Framework->direct->model->config->CONFIG['priority_ctr']>0?"(`t1`.`ctr28`>".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." OR (`t1`.`ctr28`<=".((float)$this->Framework->direct->model->config->CONFIG['priority_ctr'])." AND (`t1`.`time` IS NULL OR `t1`.`time`<='".date('Y-m-d H:i:s', (time()-(int)$this->Framework->direct->model->config->CONFIG['priority_interval']*60))."'))) AND ":'');
				$where_strategy=" `t3`.`account`='".$LOGIN['id']."' AND ( (`t3`.`strategy`!=0 AND `t3`.`strategy`!=-1 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy`!=0 AND `t2`.`strategy`!=-1 AND `t1`.`strategy`!=-1) OR (`t3`.`strategy`=0 AND `t2`.`strategy` IN (0,-1) AND `t1`.`strategy`!=0 AND `t1`.`strategy`!=-1) ) ";
				$where_status=(!empty($CONFIG['auction_status'])?' `t3`.`status`=1 AND `t2`.`status`=1 AND `t1`.`status`=1 AND ':'');
				$where_price='';
				$where_common=$where_status.$where_price.$where_strategy;
				$group=" GROUP BY `t1`.`id` ";
				$order=" ORDER BY `t3`.`id`, `t2`.`id`, `t1`.`id` ";
				$join="	INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`)
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t3` ON (`t3`.`id`=`t2`.`company`)
				";
				
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
					`t3`.`user` as `user`,
					`t3`.`account` as `account`,
					
					`t3`.`strategy_name` as `strategy_name`,
					`t3`.`context_strategy_name` as `context_strategy_name`, 
										
					`t3`.`param1` as `company_param1`,					
					`t3`.`param2` as `company_param2`,					
					`t3`.`param3` as `company_param3`
					
					FROM `".$this->Framework->direct->model->config->TABLE['retargeting']."` `t1` {$join}  WHERE {$where_param} {$where_status} {$where_strategy} 
					{$order}
					{$limit}
					";
					$result=$this->Framework->db->set($sql);
					$count_result=$this->Framework->db->count($result);
					if ($result && $count_result) {
						while ($PHRASE=$this->Framework->db->get($result)) {
							//if ($this->microsecond>0)
								//usleep($this->microsecond);//Задержка в микросекундах снижающая нагрузку на процессор для некоторых виртуальных хостингов
							$count_retargeting++;
							$PHRASE['id']=(string)$PHRASE['id'];
							$PHRASE['banner']=(string)$PHRASE['banner'];
							$PHRASE['group']=(string)$PHRASE['group'];
							if (!isset($RETARGETING_COMPANY[$PHRASE['company']]))
								$RETARGETING_COMPANY[$PHRASE['company']]=$PHRASE['company'];
							if (empty($memory_login) || $memory_login!=$LOGIN['login']) {
								$RETARGET = array('login'=>$LOGIN['login'], 'currency'=>(!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['key']:null), 'ELEMENT'=>array());
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
							$PHRASE['position1']=$PHRASE['position2']=$PHRASE['position3']=$PHRASE['position4']=$PHRASE['position5']=$PHRASE['position6']=$PHRASE['position7']=0;
							$PHRASE['price1']=$PHRASE['price2']=$PHRASE['price3']=$PHRASE['price4']=$PHRASE['price5']=$PHRASE['price6']=$PHRASE['price7']=0;
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

								if (!empty($PHRASE['context']) && !empty($PHRASE['context_price']) && $PHRASE['context_price']>0)
									$context_price = $this->Framework->library->math->ceil ( $PHRASE['context_price'], (!empty($PHRASE['company_currency']) && !empty($CURRENCY['ID'][$PHRASE['company_currency']])?$CURRENCY['ID'][$PHRASE['company_currency']]['round']:2) );
								else
									$context_price = 0;
							} else
								$context_price=0;
							//\Обрабатываем стратегии//

							if ($context_price>0 && ($PHRASE['context_strategy_name']=='MaximumCoverage' || $PHRASE['context_strategy_name']=='MAXIMUM_COVERAGE')) {
								//РСЯ//
								if ($context_price<$PHRASE['min_price'])
									$context_price=$PHRASE['min_price'];
								elseif ($context_price>$PHRASE['max_price'])
									$context_price=$PHRASE['max_price'];
		
								$BID['ContextBid']=$context_price;
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
							$memory_login=$LOGIN['login'];
						}//\while

						if (!empty($RETARGET['ELEMENT']))
							$RETARGETING=$this->Framework->direct->model->retargeting->set($RETARGET);
						if (!empty($RETARGETING_COMPANY))
							$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."`  					
							SET `datetime`='".date('Y-m-d H:i:s')."' 
							WHERE `id` IN (".implode(',', $RETARGETING_COMPANY).")");
					} else 
						break;
					if ($count_result<$this->limit)
						break;
					$offset+=$this->limit;
				}
				if ($this->debug) echo 'SQL Retargeting time='.(time()-$time).' sec, memory='.memory_get_usage(true)."<br>\r\n";
			}
		}
		//\Обновление ставок ретаргетинга//
		
		
		//Обрабатываем оптимизированные формулы//
		$this->Framework->direct->model->formula->company_false_start();
		//\Обрабатываем оптимизированные формулы//
		
		//Перезаписываем статистику из временной таблицы в основную//
		if (!empty($statistic_price)) {
			$this->Framework->db->set("SELECT MAX(`id`) as `id` FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."`");
			$MAX_ID=$this->Framework->db->get();
			$this->Framework->db->set("INSERT INTO `".$this->Framework->direct->model->config->TABLE['statistic_price']."` (`account`, `user`, `company`, `group`, `phrase`, `currency`, `price`, `real_price`, `position`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`, `position7`, `position8`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `price8`, `context`, `context_percent`, `context_max`, `datetime`) SELECT `account`, `user`, `company`, `group`, `phrase`, `currency`, `price`, `real_price`, `position`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`, `position7`, `position8`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `price8`, `context`, `context_percent`, `context_max`, `datetime` FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."` WHERE `id`<='".$MAX_ID['id']."'");
			$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['statistic_price_temp']."`  WHERE `id`<='".$MAX_ID['id']."'");
		}
		//\Перезаписываем статистику из временной таблицы в основную//
		
		//Обрабатываем ошибки//
		if (!empty($ERROR['PHRASE']['empty']))
			$this->Framework->library->error->set('Не удалось получить данные по фразам: '.$ERROR['PHRASE']['empty'].'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
			$this->Framework->library->error->set('Время запросов к Яндекс.Директ API5: '.$this->Framework->api->yandex->direct->query->time().' сек.', '', '', '', '', '', true);
		if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
			$this->Framework->library->error->set('Время запросов в базу данных: '.$this->Framework->db->time().' сек.', '', '', '', '', '', true);
		
		//\Обрабатываем ошибки//

		$LIMIT=$this->Framework->api->yandex->direct->query->limit();
		if ($this->debug) echo 'Auction phrase='.$count_phrases.', retargeting='.$count_retargeting.', LIMIT unit='.$LIMIT['unit'].', limit='.$LIMIT['limit'].', daily='.$LIMIT['daily'].', время запросов к АПИ4+5='.($this->Framework->direct->model->api->time()+$this->Framework->api->yandex->direct->query->time()).', время запросов SQL='.$this->Framework->db->time().', memory='.$this->Framework->library->lib->mb(memory_get_peak_usage(true)).'Mb'.($this->Framework->CONFIG['http']?'<br>':'')."\r\n";
		return $count;
	}
	
	private function send($PARAM=array()) {
		if ($this->debug) echo 'send='.count($PARAM)."<br>\r\n";
		if (!empty($PARAM) && is_array($PARAM)) {
			$DATA = $this->Framework->api->yandex->direct->bid->set ( $PARAM );
			if (!empty($DATA))
				foreach ($DATA as $VALUE)
					if (!empty($VALUE['Errors']))
						foreach ($VALUE['Errors'] as $ERRORS) {
							//8800 Объект не найден. Не удалось найти ключевые слова для KeywordId = 4244881065
							if (!empty($ERRORS['Code']) && $ERRORS['Code']==8800 && $ERRORS['Message']=='Объект не найден') {
								preg_match("/ KeywordId = ([0-9]+)$/", $ERRORS['Details'], $MATCH);
								if (!empty($MATCH[1]))
									$this->Framework->direct->phrase->delete($MATCH[1]);
								unset($MATCH);
							}
							//$this->Framework->library->error->set('Ошибка обновления ставок №'.$ERRORS['Code'].' '.$ERRORS['Message'].'. '.$ERRORS['Details'].'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
		}
	}
	
	
	public function get($PARAM=array()) {
		$DATA=array();
		
		return $DATA;
	}
	
}//\class
?>