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

final class Statistic extends \FrameWork\Common {
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
		$session=$this->Framework->CONFIG['microtime']*10000;
		if (is_array($PARAMS) && !empty($PARAMS['debug'])) {
			$this->debug=true;
			unset($PARAMS['debug']);
		}
		if ($this->debug) 
			echo 'session='.$session."\r\n";
		
		//Чиним сломавшиеся таблицы//
		$this->Framework->direct->model->repair->set();
		//\Чиним сломавшиеся таблицы//
		
		$CURRENCY=$this->Framework->direct->model->currency->get();
		//Получаем список аккаунтов//
		$GET=array(
			'group'=>4, 
			'status'=>1,
			'ID'=>(!empty($PARAMS)?(is_array($PARAMS) && !empty($PARAMS[0])?$PARAMS:(is_array($PARAMS) && !empty($PARAMS['account'])?array($PARAMS['account']):array($PARAMS))):0),
		);
		
		$USERS=$this->Framework->user->model->model->get($GET);
		$USERS=!empty($USERS['ELEMENT'])?$USERS['ELEMENT']:array();
		//\Получаем список аккаунтов//
		
		$statistic=!empty($this->Framework->direct->model->config->CONFIG['statistic'])?(int)$this->Framework->direct->model->config->CONFIG['statistic']:0;
		$statistic_price=!empty($this->Framework->direct->model->config->CONFIG['statistic'])?(int)$this->Framework->direct->model->config->CONFIG['statistic_price']:0;
		
		foreach ($USERS as $LOGIN) {
			if (!empty($this->debug))
				echo $LOGIN['login']."\r\n";
			//Устанавливаем авторизационные данные//
			$this->Framework->direct->model->config->login=(string)$LOGIN['login'];
			$this->Framework->direct->model->config->token=(string)$LOGIN['token'];
			//\Устанавливаем авторизационные данные//
			
			//Получаем конверсии//
			$REPORT=$this->Framework->direct->model->report->get();
			if (!empty($REPORT['COMPANY'])) {
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `report`='".date('Y-m-d')."' WHERE `id` IN (".implode(',', $REPORT['COMPANY']).")");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `show28`=0, `click28`=0, `sum365`=0, `show365`=0, `click365`=0, `revenue`=0, `roi`=0, `conversion`=0, `cost`=0, `depth`=0 WHERE `company` IN (".implode(',', $REPORT['COMPANY']).")");
			}
			$REPORT['count']=!empty($REPORT['count'])?$REPORT['count']:0;
			if (!empty($REPORT['ELEMENT'])) {
				foreach ($REPORT['ELEMENT'] as $VALUE) {
					$phrase=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
						'id'=>$VALUE['phrase_id'],
						'conversion'=>!empty($VALUE['goal_conversions_num'])?$VALUE['goal_conversions_num']:0,
						'cost'=>!empty($VALUE['goal_cost'])?$VALUE['goal_cost']:0,
						'depth'=>!empty($VALUE['session_depth'])?$VALUE['session_depth']:0,
						'roi'=>!empty($VALUE['roi'])?$VALUE['roi']:0,
						'revenue'=>!empty($VALUE['revenue'])?$VALUE['revenue']:0,
						'show28'=>(!empty($VALUE['shows_search'])?$VALUE['shows_search']:0)+(!empty($VALUE['shows_context'])?$VALUE['shows_context']:0),
						'click28'=>(!empty($VALUE['clicks_search'])?$VALUE['clicks_search']:0)+(!empty($VALUE['clicks_context'])?$VALUE['clicks_context']:0),
						'sum365'=>(!empty($VALUE['sum_search'])?$VALUE['sum_search']:0)+(!empty($VALUE['sum_context'])?$VALUE['sum_context']:0),
						'show365'=>(!empty($VALUE['shows_search'])?$VALUE['shows_search']:0)+(!empty($VALUE['shows_context'])?$VALUE['shows_context']:0),
						'click365'=>(!empty($VALUE['clicks_search'])?$VALUE['clicks_search']:0)+(!empty($VALUE['clicks_context'])?$VALUE['clicks_context']:0),
					));
				}
				unset($REPORT['ELEMENT'], $VALUE);
			}
			//\Получаем конверсии//
			
			//Суммарная статистика//
			if (rand(0, 1)) {
				$time=time();
				$STATISTIC_COMPANY=array();
				$start=0;
				$limit=1000;
				while (true) {
					$sql="SELECT 
						`t1`.id, `t1`.`currency`
						FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
						WHERE `t1`.`account`='".$LOGIN['id']."'
						ORDER BY `t1`.`currency`
						LIMIT ".$start.", ".$limit."
					";
					$result=$this->Framework->db->set($sql);
					$COMPANY_ID=array();
					while ($ROW=$this->Framework->db->get($result)) {
						$COMPANY_ID[$ROW['currency']][]=$ROW['id'];
					}
					if (!empty($COMPANY_ID)) {
						foreach ($COMPANY_ID as $key=>&$VALUE) {
							$STATISTIC_COMPANYS=$this->Framework->direct->model->company->statistic(array('id'=>$VALUE, 'start'=>DATE('Y-m-d H:i:s', mktime(0, 0, 0, DATE('m'), DATE('d'), DATE('Y'))), 'end'=>DATE('Y-m-d H:i:s'), 'currency'=>(!empty($key) && !empty($CURRENCY['ID'][$key])?$CURRENCY['ID'][$key]['key']:null)));
							if (!empty($STATISTIC_COMPANYS) && is_array($STATISTIC_COMPANYS))
								foreach ($STATISTIC_COMPANYS as &$VALUE) {
									$STATISTIC_COMPANY[$VALUE['CampaignID']]=$VALUE;
									unset($VALUE);
								}
							unset($STATISTIC_COMPANYS);
						}
					} else
						break;
					$start+=$limit;
				}
				if (!empty($this->debug))
					echo 'GETSUMMARYSTAT='.(time()-$time)." сек.\r\n";
			}
			//\Суммарная статистика//
			
			//Компании//
			$sql="SELECT 
				`t1`.*
				FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
				WHERE `t1`.`account`='".$LOGIN['id']."'
			";
			$result=$this->Framework->db->set($sql);
			while ($ROW=$this->Framework->db->get($result)) {
				//Создаем отчет по конверсиям//
				if ($ROW['report']!=date('Y-m-d') && $REPORT['count']>0) {
					$report_status=$this->Framework->direct->model->report->set(array('id'=>$ROW['id'], 'goal'=>$ROW['goal'], 'currency'=>(!empty($ROW['currency']) && !empty($CURRENCY['ID'][$ROW['currency']])?$CURRENCY['ID'][$ROW['currency']]['key']:null)));
					if ($report_status==2)
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `report`='".date('Y-m-d')."' WHERE `id`='".(int)$ROW['id']."'");
					$REPORT['count']--;
				}
				//\Создаем отчет по конверсиям//
				
				$GROUP_ID=array();
				$COMPANY=array('SumSearch'=>0, 'ShowsSearch'=>0, 'ClicksSearch'=>0, 'SumContext'=>0, 'ShowsContext'=>0, 'ClicksContext'=>0, 'ShowsAveragePosition'=>0, 'ClicksAveragePosition'=>0, 'ShowsAverageCount'=>0, 'ClicksAverageCount'=>0);
				$GROUP=array();
				$BANNER=array();
				$PHRASE=array();
				if (!empty($this->debug))
					echo 'company='.$ROW['id'].' '.date('H:i:s')."\r\n";
				$error_count=$this->Framework->library->error->count();
				$time=time();
				$STATISTIC=$this->Framework->direct->model->banner->statistic(array('id'=>$ROW['id'], 'currency'=>(!empty($ROW['currency']) && !empty($CURRENCY['ID'][$ROW['currency']])?$CURRENCY['ID'][$ROW['currency']]['key']:null)));
				
				if (!empty($STATISTIC['ERROR']['code'])) {
					if ($STATISTIC['ERROR']['code']==1 && $STATISTIC['ERROR']['string']=='Неверный CampaignID' && !empty($ROW['id']))
						$this->Framework->direct->company->delete($ROW['id']);
				}
				
				if (!empty($this->debug))
					echo 'GetBannersStat='.(time()-$time)." сек.".print_r($STATISTIC['ERROR'], true)."\r\n";
				
				$time=time();
				if (!empty($STATISTIC['Stat'])) {
					foreach ($STATISTIC['Stat'] as &$VALUE) { 
						$VALUE['PhraseID']=(string)$VALUE['PhraseID'];
						$VALUE['BannerID']=(string)$VALUE['BannerID'];
						if (!isset($GROUP_ID[$VALUE['BannerID']])) {
							$BANNER_GET=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['banner'], array(
								'id'=>$VALUE['BannerID']
							));
							if (!empty($BANNER_GET[0]['group']))
								$GROUP_ID[$VALUE['BannerID']]=$BANNER_GET[0]['group'];
							unset($BANNER_GET);
						}
						
						$COMPANY['SumSearch']+=$VALUE['SumSearch'];
						$COMPANY['ShowsSearch']+=$VALUE['ShowsSearch'];
						$COMPANY['ClicksSearch']+=$VALUE['ClicksSearch'];
						$COMPANY['SumContext']+=$VALUE['SumContext'];
						$COMPANY['ShowsContext']+=$VALUE['ShowsContext'];
						$COMPANY['ClicksContext']+=$VALUE['ClicksContext'];
						if (!empty($VALUE['ShowsAveragePosition'])) {
							$COMPANY['ShowsAveragePosition']+=$VALUE['ShowsAveragePosition'];
							$COMPANY['ShowsAverageCount']++;
						}
						if (!empty($VALUE['ClicksAveragePosition'])) {
							$COMPANY['ClicksAveragePosition']+=$VALUE['ClicksAveragePosition'];
							$COMPANY['ClicksAverageCount']++;
						}
						
						if (!isset($BANNER[$VALUE['BannerID']]))
							$BANNER[$VALUE['BannerID']]=array('SumSearch'=>0, 'ShowsSearch'=>0, 'ClicksSearch'=>0, 'SumContext'=>0, 'ShowsContext'=>0, 'ClicksContext'=>0, 'ShowsAveragePosition'=>0, 'ClicksAveragePosition'=>0, 'ShowsAverageCount'=>0, 'ClicksAverageCount'=>0);
						$BANNER[$VALUE['BannerID']]['SumSearch']+=$VALUE['SumSearch'];
						$BANNER[$VALUE['BannerID']]['ShowsSearch']+=$VALUE['ShowsSearch'];
						$BANNER[$VALUE['BannerID']]['ClicksSearch']+=$VALUE['ClicksSearch'];						
						$BANNER[$VALUE['BannerID']]['SumContext']+=$VALUE['SumContext'];
						$BANNER[$VALUE['BannerID']]['ShowsContext']+=$VALUE['ShowsContext'];
						$BANNER[$VALUE['BannerID']]['ClicksContext']+=$VALUE['ClicksContext'];
						if (!empty($VALUE['ShowsAveragePosition'])) {
							$BANNER[$VALUE['BannerID']]['ShowsAveragePosition']+=$VALUE['ShowsAveragePosition'];
							$BANNER[$VALUE['BannerID']]['ShowsAverageCount']++;
						}
						if (!empty($VALUE['ClicksAveragePosition'])) {
							$BANNER[$VALUE['BannerID']]['ClicksAveragePosition']+=$VALUE['ClicksAveragePosition'];
							$BANNER[$VALUE['BannerID']]['ClicksAverageCount']++;
						}
						
						if (!empty($GROUP_ID[$VALUE['BannerID']]) && !isset($GROUP[$GROUP_ID[$VALUE['BannerID']]]))
							$GROUP[$GROUP_ID[$VALUE['BannerID']]]=array('SumSearch'=>0, 'ShowsSearch'=>0, 'ClicksSearch'=>0, 'SumContext'=>0, 'ShowsContext'=>0, 'ClicksContext'=>0, 'ShowsAveragePosition'=>0, 'ClicksAveragePosition'=>0, 'ShowsAverageCount'=>0, 'ClicksAverageCount'=>0);
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['SumSearch']+=$VALUE['SumSearch'];
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ShowsSearch']+=$VALUE['ShowsSearch'];
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ClicksSearch']+=$VALUE['ClicksSearch'];						
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['SumContext']+=$VALUE['SumContext'];
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ShowsContext']+=$VALUE['ShowsContext'];
						$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ClicksContext']+=$VALUE['ClicksContext'];
						if (!empty($VALUE['ShowsAveragePosition'])) {
							$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ShowsAveragePosition']+=$VALUE['ShowsAveragePosition'];
							$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ShowsAverageCount']++;
						}
						if (!empty($VALUE['ClicksAveragePosition'])) {
							$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ClicksAveragePosition']+=$VALUE['ClicksAveragePosition'];
							$GROUP[$GROUP_ID[$VALUE['BannerID']]]['ClicksAverageCount']++;
						}
						
						
						$phrase=0;
						if (!empty($VALUE['PhraseID'])) {
							if (!isset($PHRASE[$VALUE['PhraseID']]))
								$PHRASE[$VALUE['PhraseID']]=array('SumSearch'=>0, 'ShowsSearch'=>0, 'ClicksSearch'=>0, 'SumContext'=>0, 'ShowsContext'=>0, 'ClicksContext'=>0, 'ShowsAveragePosition'=>0, 'ClicksAveragePosition'=>0);
							$PHRASE[$VALUE['PhraseID']]['SumSearch']+=$VALUE['SumSearch'];
							$PHRASE[$VALUE['PhraseID']]['ShowsSearch']+=$VALUE['ShowsSearch'];
							$PHRASE[$VALUE['PhraseID']]['ClicksSearch']+=$VALUE['ClicksSearch'];
							$PHRASE[$VALUE['PhraseID']]['SumContext']+=$VALUE['SumContext'];
							$PHRASE[$VALUE['PhraseID']]['ShowsContext']+=$VALUE['ShowsContext'];
							$PHRASE[$VALUE['PhraseID']]['ClicksContext']+=$VALUE['ClicksContext'];
							if (!empty($VALUE['ShowsAveragePosition'])) $PHRASE[$VALUE['PhraseID']]['ShowsAveragePosition']=$VALUE['ShowsAveragePosition'];
							if (!empty($VALUE['ClicksAveragePosition'])) $PHRASE[$VALUE['PhraseID']]['ClicksAveragePosition']=$VALUE['ClicksAveragePosition'];
								
							$phrase=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
								'id'=>$VALUE['PhraseID'],
								'sum'=>round($PHRASE[$VALUE['PhraseID']]['SumSearch'],2),
								'show'=>(int)$PHRASE[$VALUE['PhraseID']]['ShowsSearch'],
								'click'=>(int)$PHRASE[$VALUE['PhraseID']]['ClicksSearch'],
								'ctr'=>round(100*(int)$PHRASE[$VALUE['PhraseID']]['ClicksSearch']/((int)$PHRASE[$VALUE['PhraseID']]['ShowsSearch']>0?(int)$PHRASE[$VALUE['PhraseID']]['ShowsSearch']:1),2),
								'sum_context'=>round($PHRASE[$VALUE['PhraseID']]['SumContext'],2),
								'show_context'=>!empty($PHRASE[$VALUE['PhraseID']]['ShowsContext'])?(int)$PHRASE[$VALUE['PhraseID']]['ShowsContext']:0,
								'click_context'=>(int)$PHRASE[$VALUE['PhraseID']]['ClicksContext'],
								'ctr_context'=>round(100*(int)$PHRASE[$VALUE['PhraseID']]['ClicksContext']/(!empty($PHRASE[$VALUE['PhraseID']]['ShowsContext'])?(int)$PHRASE[$VALUE['PhraseID']]['ShowsContext']:1),2),
								'position_show'=>!empty($PHRASE[$VALUE['PhraseID']]['ShowsAveragePosition'])?(float)$PHRASE[$VALUE['PhraseID']]['ShowsAveragePosition']:0,
								'position_click'=>!empty($PHRASE[$VALUE['PhraseID']]['ClicksAveragePosition'])?(float)$PHRASE[$VALUE['PhraseID']]['ClicksAveragePosition']:0,
								
							));
						} elseif (!empty($VALUE['BannerID'])) {
							
							
						} else
							$this->Framework->library->error->set(print_r($VALUE, true));
						
						if (!empty($phrase)) {
							if (!empty($statistic) && !empty($GROUP_ID[$VALUE['BannerID']])) {
								$timer=time();
								$this->Framework->db->set("SELECT `id` FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` WHERE 
									`company`=".(int)$ROW['id']." AND
									`group`=".$this->Framework->db->quote($GROUP_ID[$VALUE['BannerID']])." AND
									`banner`=0 AND
									`phrase`='".$this->Framework->db->quote($phrase)."' AND 
									`date`='".date('Y-m-d', $timer)."'
								");
								$STAT_ID=$this->Framework->db->get();
								
								$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic'], array(
									'id'=>!empty($STAT_ID['id'])?$STAT_ID['id']:0,
									'account'=>$ROW['account'],
									'user'=>$ROW['user'],
									'company'=>$ROW['id'],
									'group'=>$GROUP_ID[$VALUE['BannerID']],
									//'banner'=>$VALUE['BannerID'],
									'phrase'=>$phrase,
									'sum'=>round($VALUE['SumSearch'],2),
									'show'=>(int)$VALUE['ShowsSearch'],
									'click'=>(int)$VALUE['ClicksSearch'],
									'ctr'=>round(100*(int)$VALUE['ClicksSearch']/((int)$VALUE['ShowsSearch']>0?(int)$VALUE['ShowsSearch']:1),2),
									'sum_context'=>round($VALUE['SumContext'],2),
									'show_context'=>(int)$VALUE['ShowsContext'],
									'click_context'=>(int)$VALUE['ClicksContext'],
									'ctr_context'=>round(100*(int)$VALUE['ClicksContext']/((int)$VALUE['ShowsContext']>0?(int)$VALUE['ShowsContext']:1),2),									
									'position_show'=>!empty($PHRASE[$VALUE['PhraseID']]['ShowsAveragePosition'])?(float)$PHRASE[$VALUE['PhraseID']]['ShowsAveragePosition']:0,
								'position_click'=>!empty($PHRASE[$VALUE['PhraseID']]['ClicksAveragePosition'])?(float)$PHRASE[$VALUE['PhraseID']]['ClicksAveragePosition']:0,
									
									'date'=>date('Y-m-d', $timer),
								));
							}
						}
					}
					unset($PHRASE);
				}
				
				if (!empty($this->debug))
					echo 'INSERT PHRASEs='.(time()-$time)." сек.\r\n";
				$time=time();
				if (($this->Framework->library->error->count()-$error_count)==0 || !empty($COMPANY['ShowsSearch']) || !empty($COMPANY['ShowsContext'])) {
					//Обнуляем статистику для нового дня//
					if (empty($COMPANY['ShowsSearch']) && empty($COMPANY['ShowsContext'])) {
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET 
						`sum`='0',
						`show`='0',
						`click`='0',
						`ctr`='0',
						`sum_context`='0',
						`show_context`='0',
						`click_context`='0',
						`ctr_context`='0'
						WHERE `company`='".$ROW['id']."'");
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['banner']."` SET 
						`sum`='0',
						`show`='0',
						`click`='0',
						`ctr`='0',
						`sum_context`='0',
						`show_context`='0',
						`click_context`='0',
						`ctr_context`='0'
						WHERE `company`='".$ROW['id']."'");
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."`
						SET 
						`sum`='0',
						`show`='0',
						`click`='0',
						`ctr`='0',
						`sum_context`='0',
						`show_context`='0',
						`click_context`='0',
						`ctr_context`='0'
						WHERE `company`='".$ROW['id']."'");
					}
					//\Обнуляем статистику для нового дня//
					
					$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
						'id'=>$ROW['id'],
						'sum'=>round($COMPANY['SumSearch'],2),
						'show'=>(int)$COMPANY['ShowsSearch'],
						'click'=>(int)$COMPANY['ClicksSearch'],
						'ctr'=>round(100*(int)$COMPANY['ClicksSearch']/((int)$COMPANY['ShowsSearch']>0?(int)$COMPANY['ShowsSearch']:1),2),
						'sum_context'=>round($COMPANY['SumContext'],2),
						'show_context'=>(int)$COMPANY['ShowsContext'],
						'click_context'=>(int)$COMPANY['ClicksContext'],
						'ctr_context'=>round(100*(int)$COMPANY['ClicksContext']/((int)$COMPANY['ShowsContext']>0?(int)$COMPANY['ShowsContext']:1),2),						
						'depth'=>!empty($STATISTIC_COMPANY[$ROW['id']]['SessionDepthSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['SessionDepthSearch']:0,
						'depth_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['SessionDepthContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['SessionDepthContext']:0,
						'conversion'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalConversionSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalConversionSearch']:0,
						'conversion_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalConversionContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalConversionContext']:0,
						'cost'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalCostSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalCostSearch']:0,
						'cost_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalCostContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalCostContext']:0,
					));
					
					if (!empty($statistic)) {
						$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic'], array(
							'account'=>$ROW['account'],
							'user'=>$ROW['user'],
							'company'=>$ROW['id'],
							'group'=>0,
							'banner'=>0,
							'phrase'=>0,
							'sum'=>round($COMPANY['SumSearch'],2),
							'show'=>(int)$COMPANY['ShowsSearch'],
							'click'=>(int)$COMPANY['ClicksSearch'],
							'ctr'=>round(100*(int)$COMPANY['ClicksSearch']/((int)$COMPANY['ShowsSearch']>0?(int)$COMPANY['ShowsSearch']:1),2),
							'sum_context'=>round($COMPANY['SumContext'],2),
							'show_context'=>(int)$COMPANY['ShowsContext'],
							'click_context'=>(int)$COMPANY['ClicksContext'],
							'ctr_context'=>round(100*(int)$COMPANY['ClicksContext']/((int)$COMPANY['ShowsContext']>0?(int)$COMPANY['ShowsContext']:1),2),
							'position_show'=>!empty($COMPANY['ShowsAveragePosition'])&&!empty($COMPANY['ShowsAverageCount'])?round($COMPANY['ShowsAveragePosition']/$COMPANY['ShowsAverageCount'], 2):0,
							'position_click'=>!empty($COMPANY['ClicksAveragePosition'])&&!empty($COMPANY['ClicksAverageCount'])?round($COMPANY['ClicksAveragePosition']/$COMPANY['ClicksAverageCount'], 2):0,
							//'depth'=>!empty($STATISTIC_COMPANY[$ROW['id']]['SessionDepthSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['SessionDepthSearch']:0,
							//'depth_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['SessionDepthContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['SessionDepthContext']:0,
							//'conversion'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalConversionSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalConversionSearch']:0,
							//'conversion_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalConversionContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalConversionContext']:0,
							//'cost'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalCostSearch'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalCostSearch']:0,
							//'cost_context'=>!empty($STATISTIC_COMPANY[$ROW['id']]['GoalCostContext'])?(float)$STATISTIC_COMPANY[$ROW['id']]['GoalCostContext']:0,
							'date'=>date('Y-m-d'),
						), '', true);
						
					}
				}
				if (!empty($this->debug))
					echo 'INSERT COMPANY='.(time()-$time)." сек.\r\n";
				
				//Группы//
				$time=time();
				if (($this->Framework->library->error->count()-$error_count)==0 || !empty($GROUP)) {
					if (!empty($GROUP))
						foreach ($GROUP as $key=>&$VALUE) {
							$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], array(
								'id'=>$key,
								'sum'=>round($VALUE['SumSearch'],2),
								'show'=>(int)$VALUE['ShowsSearch'],
								'click'=>(int)$VALUE['ClicksSearch'],
								'ctr'=>round(100*(int)$VALUE['ClicksSearch']/((int)$VALUE['ShowsSearch']>0?(int)$VALUE['ShowsSearch']:1),2),
								'sum_context'=>round($VALUE['SumContext'],2),
								'show_context'=>(int)$VALUE['ShowsContext'],
								'click_context'=>(int)$VALUE['ClicksContext'],
								'ctr_context'=>round(100*(int)$VALUE['ClicksContext']/((int)$VALUE['ShowsContext']>0?(int)$VALUE['ShowsContext']:1),2),
							));
							if (!empty($statistic)) {
								
								$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic'], array(
									'account'=>$ROW['account'],
									'user'=>$ROW['user'],
									'company'=>$ROW['id'],
									'group'=>(int)$key,
									'banner'=>0,
									'phrase'=>0,
									'sum'=>round($VALUE['SumSearch'],2),
									'show'=>(int)$VALUE['ShowsSearch'],
									'click'=>(int)$VALUE['ClicksSearch'],
									'ctr'=>round(100*(int)$VALUE['ClicksSearch']/((int)$VALUE['ShowsSearch']>0?(int)$VALUE['ShowsSearch']:1),2),
									'sum_context'=>round($VALUE['SumContext'],2),
									'show_context'=>(int)$VALUE['ShowsContext'],
									'click_context'=>(int)$VALUE['ClicksContext'],
									'ctr_context'=>round(100*(int)$VALUE['ClicksContext']/((int)$VALUE['ShowsContext']>0?(int)$VALUE['ShowsContext']:1),1),									
									'position_show'=>!empty($VALUE['ShowsAveragePosition'])&&!empty($VALUE['ShowsAverageCount'])?round($VALUE['ShowsAveragePosition']/$VALUE['ShowsAverageCount'], 1):0,
									'position_click'=>!empty($VALUE['ClicksAveragePosition'])&&!empty($VALUE['ClicksAverageCount'])?round($VALUE['ClicksAveragePosition']/$VALUE['ClicksAverageCount'], 2):0,
									'date'=>date('Y-m-d'),
								), '', true);
							}
						}
				
				}
				if (!empty($this->debug))
					echo 'INSERT GROUPS ('.count($GROUP).')='.(time()-$time)." сек.\r\n";
				//\Группы//
				
				//Баннеры//
				$time=time();
				if (($this->Framework->library->error->count()-$error_count)==0 || !empty($BANNER)) {
					if (!empty($BANNER))
						foreach ($BANNER as $key=>&$VALUE) {
							$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array(
								'id'=>$key,
								'sum'=>round($VALUE['SumSearch'],2),
								'show'=>(int)$VALUE['ShowsSearch'],
								'click'=>(int)$VALUE['ClicksSearch'],
								'ctr'=>round(100*(int)$VALUE['ClicksSearch']/((int)$VALUE['ShowsSearch']>0?(int)$VALUE['ShowsSearch']:1),2),
								'sum_context'=>round($VALUE['SumContext'],2),
								'show_context'=>(int)$VALUE['ShowsContext'],
								'click_context'=>(int)$VALUE['ClicksContext'],
								'ctr_context'=>round(100*(int)$VALUE['ClicksContext']/((int)$VALUE['ShowsContext']>0?(int)$VALUE['ShowsContext']:1),2),
							));
							if (!empty($statistic) && !empty($GROUP_ID[$key])) {
								
								$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['statistic'], array(
									'account'=>$ROW['account'],
									'user'=>$ROW['user'],
									'company'=>$ROW['id'],
									'group'=>$GROUP_ID[$key],
									'banner'=>$key,
									'phrase'=>0,
									'sum'=>round($VALUE['SumSearch'],2),
									'show'=>(int)$VALUE['ShowsSearch'],
									'click'=>(int)$VALUE['ClicksSearch'],
									'ctr'=>round(100*(int)$VALUE['ClicksSearch']/((int)$VALUE['ShowsSearch']>0?(int)$VALUE['ShowsSearch']:1),2),
									'sum_context'=>round($VALUE['SumContext'],2),
									'show_context'=>(int)$VALUE['ShowsContext'],
									'click_context'=>(int)$VALUE['ClicksContext'],
									'ctr_context'=>round(100*(int)$VALUE['ClicksContext']/((int)$VALUE['ShowsContext']>0?(int)$VALUE['ShowsContext']:1),1),									
									'position_show'=>!empty($VALUE['ShowsAveragePosition'])&&!empty($VALUE['ShowsAverageCount'])?round($VALUE['ShowsAveragePosition']/$VALUE['ShowsAverageCount'], 1):0,
									'position_click'=>!empty($VALUE['ClicksAveragePosition'])&&!empty($VALUE['ClicksAverageCount'])?round($VALUE['ClicksAveragePosition']/$VALUE['ClicksAverageCount'], 2):0,
									'date'=>date('Y-m-d'),
								), '', true);
							}
						}
				
				}
				if (!empty($this->debug))
					echo 'INSERT BANNERS ('.count($BANNER).')='.(time()-$time)." сек.\r\n";
				//\Баннеры//
				
				if (!empty($STATISTIC_COMPANY[$ROW['id']]))
					unset($STATISTIC_COMPANY[$ROW['id']]);
				
			}//\while
			//\Компании//
		}//\foreach
		
		//Аггрегируем статистику из таблицы логирования ставок//
		if ($statistic && $statistic_price) {
			$sql="INSERT INTO `".$this->Framework->direct->model->config->TABLE['statistic']."` (`account`, `user`, `company`, `group`, `phrase`, `currency`, `count`, `price`, `real_price`, `position1`, `position2`, `position3`, `position4`, `position5`, `position6`, `position7`, `position8`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `price8`, `context`, `context_percent`, `context_max`, `position`, `date`) 
				SELECT 
				MAX(`account`) as `account`
				, MAX(`user`) as `user`
				, MAX(`company`) as `company`
				, MAX(`group`) as `group`
				, MAX(`phrase`) as `phrase`
				, MAX(`currency`) as `currency`
				, COUNT(`id`) as `count`
				, AVG(`price`) as `price` 
				, AVG(`real_price`) as `real_price` 
				
				, AVG(`position1`) as `position1` 
				, AVG(`position2`) as `position2`
				, AVG(`position3`) as `position3`
				, AVG(`position4`) as `position4`
				, AVG(`position5`) as `position5`
				, AVG(`position6`) as `position6`
				, AVG(`position7`) as `position7`
				, AVG(`position8`) as `position8`
				, AVG(`price1`) as `price1` 
				, AVG(`price2`) as `price2`
				, AVG(`price3`) as `price3`
				, AVG(`price4`) as `price4`
				, AVG(`price5`) as `price5`
				, AVG(`price6`) as `price6`
				, AVG(`price7`) as `price7`
				, AVG(`price8`) as `price8`
				
				, AVG(`context`) as `context`
				, AVG(`context_percent`) as `context_percent`
				, AVG(`context_max`) as `context_max`
				
				, AVG(`position`) as `position`
				
				, '".date('Y-m-d')."' as `date`
				
				FROM `".$this->Framework->direct->model->config->TABLE['statistic_price']."` WHERE `datetime`>='".date('Y-m-d')." 00:00:00' AND `datetime`<='".date('Y-m-d')." 23:59:59' GROUP BY `phrase` 
				ON DUPLICATE KEY UPDATE `banner`=0
				";
			$this->Framework->db->set($sql);

			$sql="UPDATE `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`, (	
				SELECT 
				MAX(`account`) as `account`
				, MAX(`user`) as `user`
				, MAX(`company`) as `company`
				, MAX(`group`) as `group`
				, MAX(`phrase`) as `phrase`
				, MAX(`currency`) as `currency`
				, COUNT(`id`) as `count`
				, AVG(`price`) as `price` 
				, AVG(`real_price`) as `real_price` 
				
				, AVG(`position1`) as `position1` 
				, AVG(`position2`) as `position2`
				, AVG(`position3`) as `position3`
				, AVG(`position4`) as `position4`
				, AVG(`position5`) as `position5`
				, AVG(`position6`) as `position6`
				, AVG(`position7`) as `position7`
				, AVG(`position8`) as `position8`
				, AVG(`price1`) as `price1` 
				, AVG(`price2`) as `price2`
				, AVG(`price3`) as `price3`
				, AVG(`price4`) as `price4`
				, AVG(`price5`) as `price5`
				, AVG(`price6`) as `price6`
				, AVG(`price7`) as `price7`
				, AVG(`price8`) as `price8`
				
				, AVG(`context`) as `context`
				, AVG(`context_percent`) as `context_percent`
				, AVG(`context_max`) as `context_max`
				
				, SUM(IF(`position`>0,`position`,0))/IF(SUM(IF(`position`>0,1,0)),SUM(IF(`position`>0,1,0)), 1) as `position`
				, 100*SUM(IF(`position`>0,1,0))/IF(COUNT(`position`)>0,COUNT(`position`),1) as `position_visible`
				
				, '".date('Y-m-d')."' as `date` 
				FROM `".$this->Framework->direct->model->config->TABLE['statistic_price']."` WHERE `datetime`>='".date('Y-m-d')." 00:00:00' AND `datetime`<='".date('Y-m-d')." 23:59:59' GROUP BY `phrase` ) `t2` 
			SET 
			`t1`.`account`=`t2`.`account`, 
			`t1`.`user`=`t2`.`user`, 
			`t1`.`company`=`t2`.`company`, 
			`t1`.`group`=`t2`.`group`, 
			`t1`.`phrase`=`t2`.`phrase`, 
			`t1`.`currency`=`t2`.`currency`, 
			`t1`.`count`=`t2`.`count`, 
			`t1`.`price`=`t2`.`price`, 
			`t1`.`real_price`=`t2`.`real_price`, 
			`t1`.`position1`=`t2`.`position1`, 
			`t1`.`position2`=`t2`.`position2`, 
			`t1`.`position3`=`t2`.`position3`, 
			`t1`.`position4`=`t2`.`position4`, 
			`t1`.`position5`=`t2`.`position5`, 
			`t1`.`position6`=`t2`.`position6`, 
			`t1`.`position7`=`t2`.`position7`, 
			`t1`.`position8`=`t2`.`position8`, 
			`t1`.`price1`=`t2`.`price1`, 
			`t1`.`price2`=`t2`.`price2`, 
			`t1`.`price3`=`t2`.`price3`, 
			`t1`.`price4`=`t2`.`price4`, 
			`t1`.`price5`=`t2`.`price5`, 
			`t1`.`price6`=`t2`.`price6`, 
			`t1`.`price7`=`t2`.`price7`, 
			`t1`.`price8`=`t2`.`price8`, 
			`t1`.`context`=`t2`.`context`, 
			`t1`.`context_percent`=`t2`.`context_percent`, 
			`t1`.`context_max`=`t2`.`context_max`,
			
			`t1`.`position`=`t2`.`position`,
			`t1`.`position_visible`=`t2`.`position_visible`
				
			WHERE 
			`t1`.`phrase`=`t2`.`phrase` AND `t1`.`date`='".date('Y-m-d')."'
	
				";
			$this->Framework->db->set($sql);
	
			//Высчитываем среднее по кампании//
			$sql="UPDATE `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`, (	
				SELECT 
				MAX(`account`) as `account`
				, MAX(`user`) as `user`
				, MAX(`company`) as `company`
				, MAX(`group`) as `group`
				, MAX(`banner`) as `banner`
				, MAX(`phrase`) as `phrase`
				, MAX(`currency`) as `currency`
				, COUNT(`id`) as `count`
				, AVG(`price`) as `price` 
				, AVG(`real_price`) as `real_price` 
				
				, AVG(`position1`) as `position1` 
				, AVG(`position2`) as `position2`
				, AVG(`position3`) as `position3`
				, AVG(`position4`) as `position4`
				, AVG(`position5`) as `position5`
				, AVG(`position6`) as `position6`
				, AVG(`position7`) as `position7`
				, AVG(`position8`) as `position8`
				, AVG(`price1`) as `price1` 
				, AVG(`price2`) as `price2`
				, AVG(`price3`) as `price3`
				, AVG(`price4`) as `price4`
				, AVG(`price5`) as `price5`
				, AVG(`price6`) as `price6`
				, AVG(`price7`) as `price7`
				, AVG(`price8`) as `price8`
				
				, AVG(`context`) as `context`
				, AVG(`context_percent`) as `context_percent`
				, AVG(`context_max`) as `context_max`
				
				, AVG(`position`) as `position`
				, AVG(`position_visible`) as `position_visible`
				-- , AVG(`position_show`) as `position_show`
				-- , AVG(`position_click`) as `position_click`
				, AVG(`position_value`) as `position_value`
				
				, SUM(`conversion`) as `conversion`
				, AVG(`cost`) as `cost`
				, AVG(`depth`) as `depth`
				
				, '".date('Y-m-d')."' as `date` 
				FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` WHERE `phrase`>0 AND `date`='".date('Y-m-d')."' GROUP BY `company` ) `t2` 
			SET 
			`t1`.`group`=0, 
			`t1`.`banner`=0, 
			`t1`.`phrase`=0, 
			`t1`.`currency`=`t2`.`currency`, 
			`t1`.`count`=`t2`.`count`, 
			`t1`.`price`=`t2`.`price`, 
			`t1`.`real_price`=`t2`.`real_price`, 
			`t1`.`position1`=`t2`.`position1`, 
			`t1`.`position2`=`t2`.`position2`, 
			`t1`.`position3`=`t2`.`position3`, 
			`t1`.`position4`=`t2`.`position4`, 
			`t1`.`position5`=`t2`.`position5`, 
			`t1`.`position6`=`t2`.`position6`, 
			`t1`.`position7`=`t2`.`position7`, 
			`t1`.`position8`=`t2`.`position8`, 
			`t1`.`price1`=`t2`.`price1`, 
			`t1`.`price2`=`t2`.`price2`, 
			`t1`.`price3`=`t2`.`price3`, 
			`t1`.`price4`=`t2`.`price4`, 
			`t1`.`price5`=`t2`.`price5`, 
			`t1`.`price6`=`t2`.`price6`, 
			`t1`.`price7`=`t2`.`price7`, 
			`t1`.`price8`=`t2`.`price8`, 
			`t1`.`context`=`t2`.`context`, 
			`t1`.`context_percent`=`t2`.`context_percent`, 
			`t1`.`context_max`=`t2`.`context_max`,
			`t1`.`position`=`t2`.`position`,
			`t1`.`position_visible`=`t2`.`position_visible`,
			-- `t1`.`position_show`=`t2`.`position_show`,
			-- `t1`.`position_click`=`t2`.`position_click`,
			`t1`.`position_value`=`t2`.`position_value`,
			`t1`.`conversion`=`t2`.`conversion`,
			`t1`.`cost`=`t2`.`cost`,
			`t1`.`depth`=`t2`.`depth`
				
			WHERE 
			`t1`.`company`=`t2`.`company` AND `t1`.`group`=0 AND `t1`.`banner`=0 AND `t1`.`phrase`=0 AND `t1`.`date`='".date('Y-m-d')."'
	
				";
			$this->Framework->db->set($sql);
			//Высчитываем среднее по кампании//
			
			//Высчитываем среднее по группе//
			$sql="UPDATE `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`, (	
				SELECT 
				MAX(`account`) as `account`
				, MAX(`user`) as `user`
				, MAX(`company`) as `company`
				, MAX(`group`) as `group`
				, MAX(`banner`) as `banner`
				, MAX(`phrase`) as `phrase`
				, MAX(`currency`) as `currency`
				, COUNT(`id`) as `count`
				, AVG(`price`) as `price` 
				, AVG(`real_price`) as `real_price` 
				
				, AVG(`position1`) as `position1` 
				, AVG(`position2`) as `position2`
				, AVG(`position3`) as `position3`
				, AVG(`position4`) as `position4`
				, AVG(`position5`) as `position5`
				, AVG(`position6`) as `position6`
				, AVG(`position7`) as `position7`
				, AVG(`position8`) as `position8`
				, AVG(`price1`) as `price1` 
				, AVG(`price2`) as `price2`
				, AVG(`price3`) as `price3`
				, AVG(`price4`) as `price4`
				, AVG(`price5`) as `price5`
				, AVG(`price6`) as `price6`
				, AVG(`price7`) as `price7`
				, AVG(`price8`) as `price8`
				
				, AVG(`context`) as `context`
				, AVG(`context_percent`) as `context_percent`
				, AVG(`context_max`) as `context_max`
				
				, AVG(`position`) as `position`
				, AVG(`position_visible`) as `position_visible`
				, AVG(`position_show`) as `position_show`
				, AVG(`position_click`) as `position_click`
				, AVG(`position_value`) as `position_value`
				
				, SUM(`conversion`) as `conversion`
				, AVG(`cost`) as `cost`
				, AVG(`depth`) as `depth`

				FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` WHERE `phrase`>0 AND `date`='".date('Y-m-d')."' GROUP BY `group` ) `t2` 
			SET 	  
			`t1`.`banner`=0, 
			`t1`.`phrase`=0, 
			`t1`.`currency`=`t2`.`currency`, 
			`t1`.`count`=`t2`.`count`, 
			`t1`.`price`=`t2`.`price`, 
			`t1`.`real_price`=`t2`.`real_price`, 
			`t1`.`position1`=`t2`.`position1`, 
			`t1`.`position2`=`t2`.`position2`, 
			`t1`.`position3`=`t2`.`position3`, 
			`t1`.`position4`=`t2`.`position4`, 
			`t1`.`position5`=`t2`.`position5`, 
			`t1`.`position6`=`t2`.`position6`, 
			`t1`.`position7`=`t2`.`position7`, 
			`t1`.`position8`=`t2`.`position8`, 
			`t1`.`price1`=`t2`.`price1`, 
			`t1`.`price2`=`t2`.`price2`, 
			`t1`.`price3`=`t2`.`price3`, 
			`t1`.`price4`=`t2`.`price4`, 
			`t1`.`price5`=`t2`.`price5`, 
			`t1`.`price6`=`t2`.`price6`, 
			`t1`.`price7`=`t2`.`price7`, 
			`t1`.`price8`=`t2`.`price8`, 
			`t1`.`context`=`t2`.`context`, 
			`t1`.`context_percent`=`t2`.`context_percent`, 
			`t1`.`context_max`=`t2`.`context_max`,
			`t1`.`position`=`t2`.`position`,
			`t1`.`position_visible`=`t2`.`position_visible`,
			`t1`.`position_show`=`t2`.`position_show`,
			`t1`.`position_click`=`t2`.`position_click`,
			`t1`.`position_value`=`t2`.`position_value`,
			`t1`.`conversion`=`t2`.`conversion`,
			`t1`.`cost`=`t2`.`cost`,
			`t1`.`depth`=`t2`.`depth`
				
			WHERE 
			`t1`.`company`>0 AND `t1`.`group`=`t2`.`group` AND `t1`.`banner`=0 AND `t1`.`phrase`=0 AND `t1`.`date`='".date('Y-m-d')."'
	
				";
			$this->Framework->db->set($sql);
			//Высчитываем среднее по группе//			

			$sql="UPDATE `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t1`.`phrase`=`t2`.`id` AND (`t2`.`position`>0 OR `t2`.`conversion`>0 OR `t2`.`cost`>0 OR `t2`.`depth`>0)) SET `t1`.`position_value`=`t2`.`position_value`, `t1`.`conversion`=`t2`.`conversion`, `t1`.`cost`=`t2`.`cost`, `t1`.`depth`=`t2`.`depth` WHERE `t1`.`banner`=0 AND `t1`.`date`='".date('Y-m-d')."'";
			$this->Framework->db->set($sql);
			
		}
		//\Аггрегируем статистику из таблицы логирования ставок//
		
		//Удаляем старую статистику//
		if (!empty($this->debug)) 
			echo 'delete\r\n';
			
		$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` WHERE `date`<='".date('Y-m-d', mktime(23, 59, 59, DATE('m'), date('d')-$statistic, date('Y')))."'");
		$this->Framework->db->set("DELETE FROM `".$this->Framework->direct->model->config->TABLE['statistic_price']."` WHERE `datetime`<='".date('Y-m-d H:i:s', mktime(23, 59, 59, DATE('m'), date('d')-$statistic_price, date('Y')))."'");
		//\Удаляем старую статистику//
	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('user'=>$PARAM);
			if (!empty($PARAM['user'])) {
				$sql="SELECT GROUP_CONCAT(`id`) as `id` FROM `".$this->Framework->user->model->model->TABLE[0]."` WHERE `parent`='".(int)$PARAM['user']."'";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				if (!empty($ROW['id']))
					$users=$ROW['id'];
				$sql="SELECT COUNT(`id`) as `company`,
					IF (COUNT(`price`)>COUNT(DISTINCT `price`)+1, MIN(`price`), SUM(DISTINCT `price`)) as `price`, 
					SUM(`sum`+`sum_context`) as `sum`, 
					SUM(`show`+`show_context`) as `show`, 
					SUM(`click`+`click_context`) as `click`, 
					SUM(`conversion`+`conversion_context`) as `conversion`,
					(SUM(`click`+`click_context`)/SUM(`show`+`show_context`))*100 as `ctr` 
					FROM `".$this->Framework->direct->model->config->TABLE['company']."`
					WHERE 
					`account`='".(int)$PARAM['user']."' OR `user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `user` IN ('.$users.') ':'')."
				";
				
				$this->Framework->db->set($sql);
				$DATA=$this->Framework->db->get();
				
				
				$sql="SELECT 
					`t1`.`currency` as `currency`
					FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
					WHERE 
					`t1`.`account`='".(int)$PARAM['user']."' OR `t1`.`user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `t1`.`user` IN ('.$users.') ':'')."
					GROUP BY `t1`.`id`
					LIMIT 1
				";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				$DATA=array_merge($DATA, $ROW);
				/*
				$sql="SELECT 
					COUNT(`t1`.`id`) as `banner`
					FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1`
					WHERE 
					`t1`.`account`='".(int)$PARAM['user']."' OR `t1`.`user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `t1`.`user` IN ('.$users.') ':'')."
					
				";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				$DATA=array_merge($DATA, $ROW);	
				
				$sql="SELECT 
					COUNT(DISTINCT `t3`.`group`) as `groups`,
					COUNT(`t3`.`id`) as `phrase`,
					SUM(IF(`t3`.`plan`>0, 1, 0)) as `plan`
					FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t3`
					WHERE 
					`t3`.`account`='".(int)$PARAM['user']."' OR `t3`.`user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `t3`.`user` IN ('.$users.') ':'')."
					
				";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				$DATA=array_merge($DATA, $ROW);
				
				$sql="SELECT 
					MIN(UNIX_TIMESTAMP(`t3`.`time`)) as `min`, 
					MAX(UNIX_TIMESTAMP(`t3`.`time`)) as `max`
					FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t3`
					WHERE 
					`t3`.`plan`>0 AND (`t3`.`account`='".(int)$PARAM['user']."' OR `t3`.`user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `t3`.`user` IN ('.$users.') ':'').")
					
				";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				if (!empty($ROW['min'])) 
					$ROW['speed']=max(1,ceil(($ROW['max']-$ROW['min'])/60));
				else 
					$ROW['speed']=0;
				$DATA=array_merge($DATA, $ROW);*/
			}
			
			$DATA['automate']=0;
			if (!empty($PARAM['user']) || !empty($PARAM['company'])) {
				$sql="SELECT COUNT(`t1`.`plan`) as `count` FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
					WHERE 
					".(!empty($PARAM['user'])?"(`t1`.`account`='".(int)$PARAM['user']."' OR `t1`.`user`='".(int)$PARAM['user']."'".(!empty($users)?' OR `t1`.`user` IN ('.$users.') ':'').") AND ":'')."
					".(!empty($PARAM['company'])?"`t1`.`id`='".(int)$PARAM['company']."' AND ":'')."
					(`t1`.`strategy`>0 OR `t1`.`datetime`>='".date('Y-m-d')." 00:00:00')
					LIMIT 1
				";
				$this->Framework->db->set($sql);
				$ROW=$this->Framework->db->get();
				$DATA['automate']=(!empty($ROW['count'])?1:0);
			}	
		}
		return $DATA;
	}
	
}//\class
?>