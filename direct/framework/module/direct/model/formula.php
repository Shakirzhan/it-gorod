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

final class Formula extends \FrameWork\Common {
	private $MEMORY=array();
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function set($PARAM=array()) {
		
		$formula=$PARAM;
		if (!empty($formula)) {
			
			$ATTRIBUTE=array(
				'CONTEXT_COVERAGE',
				'context_optimum',
				'context_maximum',
				'context_minimum',
				'context_fixed',
				'context_max',
				'context_medium',
				'context_min',
				'context_price_old',
				'context_price',
				'context_percent',
				'context_type',
				'company_depth_context',
				'company_depth',
				'company_conversion_context',
				'company_conversion',
				'company_cost_context',
				'company_cost',
				'depth',
				'cost1',
				'cost2',
				'cost3',
				'cost4',
				'cost5',
				'cost6',
				'cost7',
				'cost8',
				'cost',
				'conversion',
				'sum_context',
				'click_context',
				'show_context',
				'ctr_context',
				'context',
				
				'position_value',
				'position1',
				'position2',
				'position3',
				'position4',
				'position5',
				'position6',
				'position7',
				'position',
				
				'bid1',
				'bid2',
				'bid3',
				'bid4',
				'bid5',
				'bid6',
				'bid7',
				'bid8',
				
				'step',
				
				'premium_min',
				'premium_max',										
				'real_price_old',
				'real_price',
				'down_second_price_min',				
				'down_second_price',
				'second_price_min',
				'second_price',
				'company_price',
				'min_price',
				'max_price',
				'price_old',
				
				'price1',
				'price2',
				'price3',
				'price4',
				'price5',
				'price6',
				'price7',
				'price8',
				
				'price',
				
				'maximum',
				'min',
				'max',
				'percent',
				'add',
				'type',

				'banner_status',
				'company_status',
				'banner_budget',
				'banner_sum',
				'banner_click28',
				'banner_show28',
				'banner_ctr28',				
				'banner_click',
				'banner_show',
				'banner_ctr',
				'company_budget',
				'company_sum',
				'company_click28',
				'company_show28',
				'company_ctr28',				
				'company_click',
				'company_show',
				'company_ctr',
				'company',
				'banner',
				'group',
				'id',
				'budget',
				'status',
				
				'click28',
				'show28',
				'ctr28',				
				'sum365',
				'click365',
				'show365',
				'ctr365',

				'revenue',
				'roi',				
				'sum',
				'click',
				'show',
				'ctr',
				'fixed',
				
				'param1',
				'param2',
				'param3',
				
				'place_old',
			);
			
			//Защита от взлома//
			$EVAL=array(
				'eval',
				'assert',
				
				'passthru',
				'system',
				'shell_exec',
				'proc_open',
				'exec',
				
				'mysql_query',
				'mysql',
				'sql',
				'pdo',
				
				'echo',
				'print',
				
				'include',
				'require',
				'readfile',
				'file_get_contents',
				'file_put_contents',
				'fopen',
				'file',
				'show_source',
				'highlight',
				'chmod',
				'chown',
				'delete',
				'remove',
				'unlink',
				'move',
				
				'import_request_variables', 
				'ini_set',
				'ini_get',
				'extract',
				'parse_str',
				
				'ob_',
				'this',
				'self',
				'parent',
				
				'open',
				'write',
				'read',
				'imap',
			);
			//\Защита от взлома//
			
			//Удаляем длинные пробелы//
			$formula=str_replace(chr(194).chr(160), ' ', $formula);
			//Удаляем длинные пробелы//
			
			foreach ($EVAL as &$value) {
				$formula=preg_replace("/".$value."/isU", '', $formula);
			}
			
			foreach ($ATTRIBUTE as &$value) {
				$formula=preg_replace("/([^']+|^)".$value."([^'_\(a-z0-9]+|$)/isU", '$1$PHRASE[\''.$value.'\']$2', $formula);
				$formula=preg_replace("/([^']+|^)".$value."([^'_\(a-z0-9]+|$)/isU", '$1$PHRASE[\''.$value.'\']$2', $formula);
			}
			
			$formula=preg_replace("/([^']+|^)company_start([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->company_start($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)company_stop([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->company_stop($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)banner_start([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->banner_start($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)banner_stop([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->banner_stop($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)start([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->start($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)stop([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->stop($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)place([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->place($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)real([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->real($PHRASE)$2', $formula);
			$formula=preg_replace("/([^']+|^)link([^'a-z\(]+|$)/isU", '$1\$this->Framework->direct->model->formula->link($PHRASE)$2', $formula);
			
			$formula=preg_replace("/datetime([^\(]+|$)/isU", "time()$1", $formula);
			$formula=preg_replace("/date([^\(]+|$)/isU", "time()$1", $formula);
			$formula=preg_replace("/week([^\(]+|$)/isU", "date('w')$1", $formula);
			$formula=preg_replace("/time([^\(]+|$)/isU", '$this->Framework->direct->model->formula->second()$1', $formula);
			$formula=preg_replace("/([^']+)([0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})(?!'\)|$)/is", "$1strtotime('$2')$3", $formula);
			
			$formula=preg_replace("/([^']+)([0-9]{4}-[0-9]{1,2}-[0-9]{2})(?!'\)|$)/is", "$1strtotime('$2')$3", $formula);
			$formula=preg_replace("/([^']+)([0-9]{2}:[0-9]{1,2}:[0-9]{2})(?!'\)|$)/isU", "$1\$this->Framework->direct->model->formula->timer('$2')$3", $formula);
			
		}
		return $formula;
	}
	
	public function second() {
		return (time() - mktime(0, 0, 0, DATE('m'),DATE('d'), DATE('Y')));
	}
	
	public function timer($string='') {
		$TIME=explode(':', $string);
		
		return (mktime(!empty($TIME[0])?$TIME[0]:0, !empty($TIME[1])?$TIME[1]:0, !empty($TIME[2])?$TIME[2]:0, DATE('m'),DATE('d'), DATE('Y')) - mktime(0, 0, 0, DATE('m'),DATE('d'), DATE('Y')));
	}
	
	public function company_start($PARAM=array()) {
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('company'=>$PARAM);
			if (empty($PARAM['company_status']))
				$PARAM['company_status']=0;
			if (empty($this->MEMORY['COMPANY']['START'][$PARAM['company']]) && $PARAM['company_status']!=1 && !empty($PARAM['company_stop'])) {
				$this->MEMORY['COMPANY']['START'][$PARAM['company']]=1;
				$this->Framework->api->yandex->direct->campaign->resume($PARAM['company']);
				$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array('id'=>$PARAM['company'], 'status'=>1, 'stop'=>0));
			}
		}
	}
	
	public function company_false_start() {
		$result=$this->Framework->db->set("SELECT `t1`.`id`, `t1`.`status`, `t2`.`login`, `t3`.`token` FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` INNER JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`id`=`t1`.`user`) INNER JOIN `".$this->Framework->user->model->param->TABLE[0]."` `t3` ON (`t3`.`user`=`t1`.`account`) WHERE `t1`.`stop`=1 AND `t1`.`strategy`>0 AND (`t1`.`sum`+`t1`.`sum_context`)<`t1`.`budget`");
		while ($ROW=$this->Framework->db->get($result)) {
			if (!empty($ROW['login']) && !empty($ROW['token'])) {
				//Устанавливаем авторизационные данные//
				$this->Framework->api->yandex->direct->config->login=(string)$ROW['login'];
				$this->Framework->api->yandex->direct->config->token=(string)$ROW['token'];
				//\Устанавливаем авторизационные данные//
				if (empty($ROW['status']) || $ROW['status']!=1)
					$this->Framework->api->yandex->direct->campaign->resume($ROW['id']);
				$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array('id'=>$ROW['id'], 'status'=>1, 'stop'=>0));
			}
		}
	}
	
	
	public function company_stop($PARAM=array()) {
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('company'=>$PARAM);
			if (empty($PARAM['company_status']))
				$PARAM['company_status']=0;
			if (empty($this->MEMORY['COMPANY']['STOP'][$PARAM['company']]) && $PARAM['company_status']!=2) {
				$this->MEMORY['COMPANY']['STOP'][$PARAM['company']]=1;
				$this->Framework->api->yandex->direct->campaign->suspend($PARAM['company']);
				$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array('id'=>$PARAM['company'], 'status'=>2, 'stop'=>1));
			}
		}
	}

	public function banner_start($PARAM=array()) {
		$limit=10000;
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('banner'=>$PARAM);
			if (empty($PARAM['account']))
				$PARAM['account']=0;
			if (empty($PARAM['banner_status']))
				$PARAM['banner_status']=0;
			if ($PARAM['banner_status']!=1) {
				if (!empty($this->MEMORY['BANNER']['START']) && count($this->MEMORY['BANNER']['START'])==$limit) {
					$SET=$this->MEMORY['BANNER']['START'];
					unset($this->MEMORY['BANNER']['START']);
				}
				$this->MEMORY['BANNER']['START'][]=$PARAM['banner'];
			}
		} elseif (!empty($this->MEMORY['BANNER']['START'])) { 
			$SET=$this->MEMORY['BANNER']['START'];
			unset($this->MEMORY['BANNER']['START']);
		}
			
		if (!empty($SET)) {
			$this->Framework->api->yandex->direct->advert->resume(array('id'=>$SET));
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array('id'=>$SET, 'status'=>1));
		}
	}
	
	public function banner_stop($PARAM=array()) {
		$limit=10000;
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('banner'=>$PARAM);
			if (empty($PARAM['account']))
				$PARAM['account']=0;
			if (empty($PARAM['banner_status']))
				$PARAM['banner_status']=0;
			if ($PARAM['banner_status']!=2) {
				if (!empty($this->MEMORY['BANNER']['STOP']) && count($this->MEMORY['BANNER']['STOP'])==$limit) {
					$SET=$this->MEMORY['BANNER']['STOP'];
					unset($this->MEMORY['BANNER']['STOP']);
				}
				$this->MEMORY['BANNER']['STOP'][]=$PARAM['banner'];
			}
		} elseif (!empty($this->MEMORY['BANNER']['STOP'])) { 
			$SET=$this->MEMORY['BANNER']['STOP'];
			unset($this->MEMORY['BANNER']['STOP']);
		}
			
		if (!empty($SET)) {
			$this->Framework->api->yandex->direct->advert->suspend(array('id'=>$SET));
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array('id'=>$SET, 'status'=>2));
		}
	}

	public function start($PARAM=array()) {
		$limit=10000;
		$SET=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (empty($PARAM['account']))
				$PARAM['account']=0;
			if (empty($PARAM['status']))
				$PARAM['status']=0;
			if (empty($PARAM['banner_count']))
				$PARAM['banner_count']=0;
			if ($PARAM['status']!=1 && $PARAM['banner_count']!=1) {
				if (!empty($this->MEMORY['PHRASE']['START']) && count($this->MEMORY['PHRASE']['START'])==$limit) {
					$SET=$this->MEMORY['PHRASE']['START'];
					unset($this->MEMORY['PHRASE']['START']);
				}
				$this->MEMORY['PHRASE']['START'][]=$PARAM['id'];
			} elseif ($PARAM['banner_count']==1) {
				//Получаем список всех объявлений группы для их остановки//
				if (!empty($PARAM['group'])) {
					$this->Framework->db->set("SELECT `t1`.`id`, `t1`.`status` FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` WHERE `t1`.`group`='".$PARAM['group']."'");
					while ($ROW=$this->Framework->db->get($result)) {
						$BANNER_PARAM=$PARAM;
						$BANNER_PARAM['banner']=$ROW['id'];
						$BANNER_PARAM['banner_status']=$ROW['status'];
						$this->banner_start($BANNER_PARAM);
					}
					if (!empty($BANNER_PARAM['group'])) 
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `status`=1 WHERE `id`='".$BANNER_PARAM['group']."'");
					unset($BANNER_PARAM);
				}
				
			}
		} elseif (!empty($this->MEMORY['PHRASE']['START'])) { 
			$SET=$this->MEMORY['PHRASE']['START'];
			unset($this->MEMORY['PHRASE']['START']);
		}
		
		if (!empty($SET)) {
			$this->Framework->api->yandex->direct->keyword->resume(array('id'=>$SET));
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array('id'=>$SET, 'status'=>1));
		}
	}
	
	public function stop($PARAM=array()) { 
		$limit=10000;
		$SET=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (empty($PARAM['account']))
				$PARAM['account']=0;
			if (empty($PARAM['status']))
				$PARAM['status']=0;
			if (empty($PARAM['banner_count']))
				$PARAM['banner_count']=0;

			if ($PARAM['status']!=2 && $PARAM['banner_count']!=1) {
				if (!empty($this->MEMORY['PHRASE']['STOP']) && count($this->MEMORY['PHRASE']['STOP'])==$limit) {
					$SET=$this->MEMORY['PHRASE']['STOP'];
					unset($this->MEMORY['PHRASE']['STOP']);
				}
				$this->MEMORY['PHRASE']['STOP'][]=$PARAM['id'];
			} elseif ($PARAM['banner_count']==1) {
				//Получаем список всех объявлений группы для их остановки//
				if (!empty($PARAM['group'])) {
					$this->Framework->db->set("SELECT `t1`.`id`, `t1`.`status` FROM `".$this->Framework->direct->model->config->TABLE['banner']."` `t1` WHERE `t1`.`group`='".$PARAM['group']."'");
					while ($ROW=$this->Framework->db->get($result)) {
						$BANNER_PARAM=$PARAM;
						$BANNER_PARAM['banner']=$ROW['id'];
						$BANNER_PARAM['banner_status']=$ROW['status'];
						$this->banner_stop($BANNER_PARAM);
					}
					if (!empty($BANNER_PARAM['group'])) 
						$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `status`=2 WHERE `id`='".$BANNER_PARAM['group']."'");
					unset($BANNER_PARAM);
				}
				
			}
		} elseif (!empty($this->MEMORY['PHRASE']['STOP'])) { 
			$SET=$this->MEMORY['PHRASE']['STOP'];
			unset($this->MEMORY['PHRASE']['STOP']);
		}

		if (!empty($SET)) {
			$this->Framework->api->yandex->direct->keyword->suspend(array('id'=>$SET));
			$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array('id'=>$SET, 'status'=>2));
		}
	}
	
	public function place($PARAM=array()) {
		if (empty($PARAM['bid1']))
			$PARAM['bid1']=$PARAM['premium_max'];
		if (empty($PARAM['bid2']))
			$PARAM['bid2']=$PARAM['position2'];
		if (empty($PARAM['bid3']))
			$PARAM['bid3']=$PARAM['premium_min'];
		if (empty($PARAM['bid5']))
			$PARAM['bid5']=$PARAM['max'];
		if (empty($PARAM['bid6']))
			$PARAM['bid6']=$PARAM['position5'];
		if (empty($PARAM['bid7']))
			$PARAM['bid7']=$PARAM['position6'];
		if (empty($PARAM['bid8']))
			$PARAM['bid8']=$PARAM['min'];
		if (empty($PARAM['cost1']))
			$PARAM['cost1']=$PARAM['price1'];
		if (empty($PARAM['cost2']))
			$PARAM['cost2']=$PARAM['price2'];
		if (empty($PARAM['cost3']))
			$PARAM['cost3']=$PARAM['price3'];
		if (empty($PARAM['cost4']))
			$PARAM['cost4']=$PARAM['price4'];
		if (empty($PARAM['cost5']))
			$PARAM['cost5']=$PARAM['price5'];
		if (empty($PARAM['cost6']))
			$PARAM['cost6']=$PARAM['price6'];
		if (empty($PARAM['cost7']))
			$PARAM['cost7']=$PARAM['price7'];
		if (empty($PARAM['cost8']))
			$PARAM['cost8']=$PARAM['price8'];
		if (empty($PARAM['strategy_name']))
			$PARAM['strategy_name']='';
		//По списываемой цене//
		$place=0;
		if (empty($PARAM['strategy_name'])) {
			if ($PARAM['real_price']>=$PARAM['cost1'])
				$place=1;
			elseif ($PARAM['real_price']>=$PARAM['cost2'])
				$place=2;
			elseif ($PARAM['real_price']>=$PARAM['cost3'])
				$place=3;
			elseif ($PARAM['real_price']>=$PARAM['cost4'])
				$place=4;
			elseif ($PARAM['real_price']>=$PARAM['cost5'])
				$place=5;
			elseif ($PARAM['real_price']>=$PARAM['cost6'])
				$place=6;
			elseif ($PARAM['real_price']>=$PARAM['cost7'])
				$place=7;
			elseif ($PARAM['real_price']>=$PARAM['cost8'])
				$place=8;
		}
		//\По списываемой цене//
		
		if ($place==0) {
			//API 4: 'LowestCostPremium', 'LowestCost', 'LowestCostGuarantee', 'RightBlockHighest'
			//API 5: 'HIGHEST_POSITION', 'LOWEST_COST', 'LOWEST_COST_PREMIUM', 'LOWEST_COST_GUARANTEE', 'IMPRESSIONS_BELOW_SEARCH', 'SERVING_OFF'
			if ($PARAM['price']>=$PARAM['bid1'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')))
				$place=1;
			elseif ($PARAM['price']>=$PARAM['bid2'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')))
				$place=2;
			elseif ($PARAM['price']>=$PARAM['bid3'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')))
				$place=3;
			elseif ($PARAM['price']>=$PARAM['bid4'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')))
				$place=4;
			elseif ($PARAM['price']>=$PARAM['bid5'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST', 'LowestCost', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee')))
				$place=5;
			elseif ($PARAM['price']>=$PARAM['bid6'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST', 'LowestCost', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee')))
				$place=6;
			elseif ($PARAM['price']>=$PARAM['bid7'] && !in_array($PARAM['strategy_name'], array('LOWEST_COST', 'LowestCost', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee')))
				$place=7;
			elseif ($PARAM['price']>=$PARAM['bid8'])
				$place=8;
		}
		
		return $place;
	}
	
	public function real($PARAM=array()) {		
		$place=$this->place($PARAM);
		if ($place>0) {
			$real=!empty($PARAM['cost'.$place])?$PARAM['cost'.$place]:0;
		} else
			$real=0;
		return $real;
	}
	
	public function context_place($PARAM=array()) {
		$place=0;
		if (!empty($PARAM['CONTEXT_COVERAGE']) && is_array(($PARAM['CONTEXT_COVERAGE']))) {
			foreach ($PARAM['CONTEXT_COVERAGE'] as $VALUE) {
				if ($PARAM['context_price']>=$VALUE['price']) {
					$place=$VALUE['percent'];
					break;
				}
				
			}
			
		}
		
		return $place;
	}
	
	public function link($PARAM=array()) {
		$limit=10000;
		$SET=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('banner_url'=>$PARAM);
			if (!empty($PARAM['banner_url']) && !isset($this->MEMORY['URL'][$PARAM['banner_url']])) {
				$result=file_get_contents($PARAM['banner_url']);
				if (!empty($result))
					$this->MEMORY['URL'][$PARAM['banner_url']]=1;
				else
					$this->MEMORY['URL'][$PARAM['banner_url']]=0;
			}
			elseif (isset($this->MEMORY['URL'][$PARAM['banner_url']]))
				return $this->MEMORY['URL'][$PARAM['banner_url']];
			else	
				return 0;
		}
	}
	
	public function get() {
		$DATA=array();
		
		return $DATA;
	}
	
}//\class
?>