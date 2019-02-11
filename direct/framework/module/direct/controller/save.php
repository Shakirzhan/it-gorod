<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс контроллер                                ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\controller;

final class Save extends \FrameWork\Common {

	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
	}

	public function config($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$CONFIG=$this->Framework->direct->model->config->set($PARAM);
			if (!empty($CONFIG))
				$DATA['status']=1;
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function company($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0, 'count'=>0, 'element'=>0, 'ERROR'=>array());
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('company'=>$key)) && ($this->Framework->user->controller->controller->USER['group']==1 || $this->Framework->user->controller->controller->USER['right']>0)) {
							
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
								'id'=>$key));
							$ROW=array_shift($ROW);
							$error=false;
							/*if (!in_array($ROW['strategy_name'], array('UNKNOWN', 'HighestPosition', 'HIGHEST_POSITION', 'ShowsDisabled', 'SERVING_OFF', 'LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')) && $value>0) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Ручное управление ставками»!';
							}
							if (($ROW['context_strategy_name']!='MAXIMUM_COVERAGE' && $ROW['context_strategy_name']!='MaximumCoverage') && (!empty($PARAM['context'][$key]))) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Раздельно управлять ставками на поиске и в сетях»!';
							}*/
							
							if (!$error) {
								$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
									'id'=>$key,
									'strategy'=>intval($value),
									'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
									'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
									'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
									'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
									'fixed'=>isset($PARAM['fixed'][$key])?round(str_replace(',', '.', $PARAM['fixed'][$key]),2):0,
									'budget'=>isset($PARAM['budget'][$key])?round(str_replace(',', '.', $PARAM['budget'][$key]),2):0,
									'param1'=>isset($PARAM['param1'][$key])?round(str_replace(',', '.', $PARAM['param1'][$key]),2):0,
									'param2'=>isset($PARAM['param2'][$key])?round(str_replace(',', '.', $PARAM['param2'][$key]),2):0,
									'param3'=>isset($PARAM['param3'][$key])?round(str_replace(',', '.', $PARAM['param3'][$key]),2):0,
									'context'=>!empty($PARAM['context'][$key])?1:0,
									'context_percent'=>isset($PARAM['context_percent'][$key])?round(str_replace(',', '.', $PARAM['context_percent'][$key]),2):0,
									'context_maximum'=>isset($PARAM['context_maximum'][$key])?round(str_replace(',', '.', $PARAM['context_maximum'][$key]),2):0,
									'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,
									'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,
									'goal'=>!empty($PARAM['goal'][$key])?preg_replace('/[^0-9]/', '', $PARAM['goal'][$key]):'',
								));
								if (!$id)
									$DATA['ERROR'][]='Не удалось сохранить id='.$key;
								else {
									$DATA['count']+=1;
									$DATA['element']+=16;
								}
							}
						} else {
							$DATA['ERROR'][]='У вас нет прав для записи кампании '.$key;
							$this->Framework->library->error->set('У вас нет прав для записи кампании с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
						
					}
				}
				if (count($DATA['ERROR'])==0)
					$DATA['status']=1;
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function group($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0, 'count'=>0, 'element'=>0, 'ERROR'=>array());
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('group'=>$key)) && ($this->Framework->user->controller->controller->USER['group']==1 || $this->Framework->user->controller->controller->USER['right']>0)) {
						
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['group'], array(
									'id'=>$key));
							$ROW=array_shift($ROW);
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
									'id'=>$ROW['company']));
							$ROW=array_shift($ROW);
							$error=false;
							/*if (!in_array($ROW['strategy_name'], array('UNKNOWN', 'HighestPosition', 'HIGHEST_POSITION', 'ShowsDisabled', 'SERVING_OFF', 'LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')) && $value>0) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Ручное управление ставками»!';
							}
							if (($ROW['context_strategy_name']!='MAXIMUM_COVERAGE' && $ROW['context_strategy_name']!='MaximumCoverage') && (!empty($PARAM['context'][$key]))) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Раздельно управлять ставками на поиске и в сетях»!';
							}*/
							
							if (!$error) {
								$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['group'], array(
									'id'=>$key,
									'strategy'=>(int)$value,
									'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
									'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
									'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
									'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
									'fixed'=>isset($PARAM['fixed'][$key])?round(str_replace(',', '.', $PARAM['fixed'][$key]),2):0,
									'budget'=>isset($PARAM['budget'][$key])?round(str_replace(',', '.', $PARAM['budget'][$key]),2):0,
									'param1'=>isset($PARAM['param1'][$key])?round(str_replace(',', '.', $PARAM['param1'][$key]),2):0,
									'param2'=>isset($PARAM['param2'][$key])?round(str_replace(',', '.', $PARAM['param2'][$key]),2):0,
									'param3'=>isset($PARAM['param3'][$key])?round(str_replace(',', '.', $PARAM['param3'][$key]),2):0,
									'context'=>!empty($PARAM['context'][$key])?1:0,
									'context_percent'=>isset($PARAM['context_percent'][$key])?round(str_replace(',', '.', $PARAM['context_percent'][$key]),2):0,
									'context_maximum'=>isset($PARAM['context_maximum'][$key])?round(str_replace(',', '.', $PARAM['context_maximum'][$key]),2):0,
									'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,
									'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,
								));
							if (!$id)
								$DATA['ERROR'][]='Не удалось сохранить id='.$key;
							else {
									$DATA['count']+=1;
									$DATA['element']+=15;
								}
							}
						} else  {
							$DATA['ERROR'][]='У вас нет прав для записи объявления '.$key;
							$this->Framework->library->error->set('У вас нет прав для записи баннера с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
					}
				}
				if (count($DATA['ERROR'])==0)
					$DATA['status']=1;	
			}
			
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function phrase($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0, 'count'=>0, 'element'=>0, 'ERROR'=>array());
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('phrase'=>$key)) && ($this->Framework->user->controller->controller->USER['group']==1 || $this->Framework->user->controller->controller->USER['right']>0)) {
							
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['phrase'], array(
									'id'=>$key));
							$ROW=array_shift($ROW);
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['group'], array(
									'id'=>$ROW['group']));
							$ROW=array_shift($ROW);
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
									'id'=>$ROW['company']));
							$ROW=array_shift($ROW);
							$error=false;
							/*if (!in_array($ROW['strategy_name'], array('UNKNOWN', 'HighestPosition', 'HIGHEST_POSITION', 'ShowsDisabled', 'SERVING_OFF', 'LOWEST_COST', 'LowestCost', 'LOWEST_COST_PREMIUM', 'LowestCostPremium', 'LOWEST_COST_GUARANTEE', 'LowestCostGuarantee', 'IMPRESSIONS_BELOW_SEARCH', 'RightBlockHighest')) && $value>0) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Ручное управление ставками»!';
							}
							if (($ROW['context_strategy_name']!='MAXIMUM_COVERAGE' && $ROW['context_strategy_name']!='MaximumCoverage') && (!empty($PARAM['context'][$key]))) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: «Раздельно управлять ставками на поиске и в сетях»!';
							}*/
							
							if (!$error) {
								$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
									'id'=>$key,
									'strategy'=>(int)$value,
									'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
									'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
									'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
									'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
									'fixed'=>isset($PARAM['fixed'][$key])?round(str_replace(',', '.', $PARAM['fixed'][$key]),2):0,
									'budget'=>isset($PARAM['budget'][$key])?round(str_replace(',', '.', $PARAM['budget'][$key]),2):0,
									'param1'=>isset($PARAM['param1'][$key])?round(str_replace(',', '.', $PARAM['param1'][$key]),2):0,
									'param2'=>isset($PARAM['param2'][$key])?round(str_replace(',', '.', $PARAM['param2'][$key]),2):0,
									'param3'=>isset($PARAM['param3'][$key])?round(str_replace(',', '.', $PARAM['param3'][$key]),2):0,
									'context'=>!empty($PARAM['context'][$key])?1:0,
									'context_percent'=>isset($PARAM['context_percent'][$key])?round(str_replace(',', '.', $PARAM['context_percent'][$key]),2):0,
									'context_maximum'=>isset($PARAM['context_maximum'][$key])?round(str_replace(',', '.', $PARAM['context_maximum'][$key]),2):0,
									'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,
									'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,
									'position'=>!empty($PARAM['position'][$key])?1:0,
								));
								if (!$id)
									$DATA['ERROR'][]='Не удалось сохранить id='.$key;
								else {
									$DATA['count']+=1;
									if ($this->Framework->direct->model->config->CONFIG['xml_user']) 
										$DATA['element']+=16;
									else
										$DATA['element']+=15;
								}
							}
						} else  {
							$DATA['ERROR'][]='У вас нет прав для записи фразы '.$key;
							$this->Framework->library->error->set('У вас нет прав для записи фразы с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
					}
				}
				if (count($DATA['ERROR'])==0)
					$DATA['status']=1;
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}	
	
	public function retargeting($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0, 'count'=>0, 'element'=>0, 'ERROR'=>array());
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('retargeting'=>$key)) && ($this->Framework->user->controller->controller->USER['group']==1 || $this->Framework->user->controller->controller->USER['right']>0)) {
							
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['retargeting'], array(
									'id'=>$key));
							$ROW=array_shift($ROW);
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['group'], array(
									'id'=>$ROW['group']));
							$ROW=array_shift($ROW);
							$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
									'id'=>$ROW['company']));
							$ROW=array_shift($ROW);
							$error=false;
							if (($ROW['context_strategy_name']!='MAXIMUM_COVERAGE' && $ROW['context_strategy_name']!='Default' && $ROW['context_strategy_name']!='MaximumCoverage' && $ROW['context_strategy_name']!='WeeklyBudget') && (!empty($PARAM['context'][$key]))) {
								$error=true;
								$DATA['ERROR'][]='Стратегия в интерфейсе Яндекс.Директ для кампании №'.$ROW['id'].' ('.$ROW['name'].') должна быть: Раздельное управление ставками на поиске и в сетях!';
							}
							
							if (!$error) {
								$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['retargeting'], array(
									'id'=>$key,
									'strategy'=>(int)$value,
									//'budget'=>isset($PARAM['budget'][$key])?round(str_replace(',', '.', $PARAM['budget'][$key]),2):0,
									'context'=>!empty($PARAM['context'][$key])?1:0,
									'context_percent'=>isset($PARAM['context_percent'][$key])?round(str_replace(',', '.', $PARAM['context_percent'][$key]),2):0,
									'context_type'=>!empty($PARAM['context_type'][$key])?1:0,
									'context_maximum'=>isset($PARAM['context_maximum'][$key])?round(str_replace(',', '.', $PARAM['context_maximum'][$key]),2):0,
									'context_fixed'=>isset($PARAM['context_fixed'][$key])?round(str_replace(',', '.', $PARAM['context_fixed'][$key]),2):0,
									'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,'context_minimum'=>isset($PARAM['context_minimum'][$key])?round(str_replace(',', '.', $PARAM['context_minimum'][$key]),2):0,
									
								));
								if (!$id)
									$DATA['ERROR'][]='Не удалось сохранить id='.$key;
							}
						} else  {
							$DATA['ERROR'][]='У вас нет прав для записи ретаргетинга '.$key;
							$this->Framework->library->error->set('У вас нет прав для записи ретаргетинга с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						}
					}
				}
				if (count($DATA['ERROR'])==0)
					$DATA['status']=1;
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}		
	
	public function cron($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['interval']) && is_array($PARAM['interval'])) {
				foreach ($PARAM['interval'] as $key=>$value) {
					if (!empty($key)) {
						$SET=array(
							'id'=>$key,
							'interval'=>(int)$value,
						);
						if (isset($PARAM['status'][$key]))
							$SET['status']=(int)$PARAM['status'][$key];
						$id=$this->Framework->cron->cron->set($SET);
						if ($id)
							$DATA['status']=1;
						else
							$DATA['ERROR'][]='Не удалось сохранить id='.$key;
					} 
					
				}
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}		
	
	public function cron_save($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			
			if (!empty($PARAM)) {
				if (!empty($PARAM['account']) && (!empty($PARAM['user']) || !empty($PARAM['company'])))
					$PARAM['param']=serialize(array('account'=>(int)$PARAM['account'], 'user'=>(int)$PARAM['user'], 'company'=>$PARAM['company']));
				else
					$PARAM['param']=(int)$PARAM['account'];
				$id=$this->Framework->cron->cron->set($PARAM);
				if ($id)
					$DATA['status']=1;
				else
					$DATA['ERROR'][]='Не удалось сохранить id='.$key;
			} 
					
				
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}

	public function cron_delete($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['id'])) {
				$this->Framework->cron->cron->delete(array('id'=>$PARAM['id']));
				$DATA['status']=1;
			}
		}
		
		$this->Framework->library->header->set(array('Location'=>$_SERVER['HTTP_REFERER']));
		$this->Framework->library->header->get();
		
		//$this->Framework->template('json')->set('DATA', $DATA);
		//echo $this->Framework->template('json')->get();
	}	

}//\class
?>