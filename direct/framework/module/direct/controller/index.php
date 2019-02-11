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

final class Index extends \FrameWork\Common {
	private $limit=100;
	
	public function __construct() {
		parent::__construct();
		$this->Framework->library->header()->get('http');
		$this->Framework->direct->model->update->set();//Автообновление
		$this->limit=$this->Framework->direct->model->config->number?$this->Framework->direct->model->config->number:$this->limit;
	}
	
	public function index($PARAM=array()) {
		$this->company($PARAM);
	}
	
	public function auction($PARAM=array()) {
		if (!empty($PARAM['where']) && $PARAM['where']==4) 
			$this->retargeting($PARAM);
		elseif (!empty($PARAM['where']) && $PARAM['where']==3) 
			$this->phrase($PARAM);
		elseif (!empty($PARAM['where']) && $PARAM['where']==2)
			$this->group($PARAM);
		else
			$this->company($PARAM);
	}
	
	private function filter(&$PARAM) {
		$DATA=array('PARAM'=>array('user'=>0, 'company'=>0, 'group'=>0, 'phrase'=>0, 'search'=>'', 'where'=>1, 'status'=>0, 'tag'=>0));
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			
			//Получаем фразы//
			if (!empty($PARAM['phrase'])) {
				$PHRASE=$this->Framework->direct->phrase->get(array('id'=>$PARAM['phrase']));
				if (!empty($PHRASE['ELEMENT'][0])) {
					$PARAM['group']=$PHRASE['ELEMENT'][0]['group'];
					$DATA['FILTER']['SELECT']['PHRASE']=$PHRASE['ELEMENT'][0];
				} else	
					$PARAM['phrase']=0;
			}
			
			if (!empty($PARAM['group'])) {
				$DATA['FILTER']['PHRASE']=$this->Framework->direct->phrase->get(array('group'=>$PARAM['group']), array('id'), array('id', 'name'));
			}
			//\Получаем фразы//
			
			//Получаем объявления//
			if (!empty($PARAM['group'])) {
				$GROUP=$this->Framework->direct->group->get(array('id'=>$PARAM['group']));
				if (!empty($GROUP['ELEMENT'][0])) {
					$PARAM['company']=$GROUP['ELEMENT'][0]['company'];
					$DATA['FILTER']['SELECT']['GROUP']=$GROUP['ELEMENT'][0];
				} else	
					$PARAM['company']=0;
			}
			
			if (!empty($PARAM['company'])) {
				$DATA['FILTER']['GROUP']=$this->Framework->direct->group->get(array('company'=>$PARAM['company']), array('id'), array('id', 'name'));
			}
			//\Получаем объявления//
			
			//Получаем кампании//
			if (!empty($PARAM['company'])) {
				$COMPANY=$this->Framework->direct->company->get(array('id'=>$PARAM['company']));
				if (!empty($COMPANY['ELEMENT'][0])) {
					$PARAM['user']=$COMPANY['ELEMENT'][0]['user'];
					$DATA['FILTER']['SELECT']['COMPANY']=$COMPANY['ELEMENT'][0];
				} else	
					$PARAM['company']=0;
			}
			
			if (!empty($PARAM['company'])) {
				$DATA['FILTER']['TAG']=$this->Framework->direct->tag->get(array('company'=>$PARAM['company']), array('name','id'), array('id', 'name'));
			}
			
			if (!empty($PARAM['user'])) {
				$DATA['FILTER']['COMPANY']=$this->Framework->direct->company->get(array('user'=>$PARAM['user']), array('id'), array('id', 'name'));
			}
			//\Получаем кампании//
			
			//Получаем клиентов//
			$GET=array(
					'group'=>array(2,4),
				);
			if ($this->Framework->user->controller->controller->USER['group']==3) {
				$GET['parent']=$this->Framework->user->controller->controller->USER['id'];
			} elseif ($this->Framework->user->controller->controller->USER['group']==2) {
				$GET['id']=$this->Framework->user->controller->controller->USER['id'];
			} elseif ($this->Framework->user->controller->controller->USER['group']==4) {
				$MANAGER=$this->Framework->user->model->model->get(array('group'=>3, 'parent'=>$this->Framework->user->controller->controller->USER['id']));
				$CLIENT=$this->Framework->user->model->model->get(array('group'=>2, 'parent'=>$this->Framework->user->controller->controller->USER['id']));
				if (!empty($MANAGER['ID'])) {
					$GET['PARENT']=$MANAGER['ID'];
					$GET['PARENT'][]=$this->Framework->user->controller->controller->USER['id'];
				} elseif (!empty($CLIENT['ID'])) 
					$GET['parent']=$this->Framework->user->controller->controller->USER['id'];
				else
					$GET['id']=$this->Framework->user->controller->controller->USER['id'];
			}
			$DATA['FILTER']['USER']=$this->Framework->user->model->model->get($GET, array('login'), array('id', 'login', 'name'));
			//\Получаем клиентов//
		
		}
		$DATA['PARAM']=array_merge($DATA['PARAM'], $PARAM);
		return $DATA;
	}
	
	public function filters($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			
			$DATA=$this->filter($PARAM);
			
			$this->Framework->template('json')->set('DATA', $DATA);
			
		}
		echo $this->Framework->template('json')->get();
	}
	
	public function config($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['CONFIG']['ELEMENT']=$this->Framework->direct->model->config->get();
			$DATA['CONFIG']['cron']=' * * * * * '.$this->Framework->library->lib->php().' '.$this->Framework->CONFIG['document_root'].'cron.php';
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	
	public function company($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['CURRENCY']=$this->Framework->direct->model->currency->get();
			$DATA['STRATEGY']['ELEMENT']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('status'=>array(1,2)), array('id'));

			
			switch ($this->Framework->user->controller->controller->USER['group']) {
				case 2:
				case 3:
				case 4:
					if (empty($PARAM['user']) || (!empty($PARAM['user'])&&$this->Framework->user->controller->controller->USER['group']==4)) {
						$DATA['USER']=$this->Framework->user->model->model->get(array('parent'=>!empty($PARAM['user'])?$PARAM['user']:$this->Framework->user->controller->controller->USER['id']), array('login'), array(), array('page'=>(!empty($PARAM['page'])?(int)$PARAM['page']:0), 'number'=>$this->limit));
						
						$MANAGER=$this->Framework->user->model->model->get(array('id'=>$this->Framework->user->controller->controller->USER['id']));
						$MANAGER=!empty($MANAGER['ELEMENT'][0])?$MANAGER['ELEMENT'][0]:array();
						
						if (!empty($DATA['USER']['ELEMENT'])) {
							foreach($DATA['USER']['ELEMENT'] as &$VALUE) {
								$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get($VALUE['id']), array('MANAGER'=>$MANAGER));
							}
						}
					} 
					
					if (empty($DATA['USER']['ELEMENT']) && empty($PARAM['user']))
						$PARAM['user']=$this->Framework->user->controller->controller->USER['id'];
						
					if (empty($DATA['USER']['ELEMENT']) && !empty($PARAM['user'])) {
						if (!empty($PARAM['user']))
							$DATA['PARAM']['user']=$PARAM['user'];
						$DATA['DIRECT']=$this->Framework->direct->model->path->get($PARAM);
						if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'strategy', 'maximum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue')))
							$ORDER=array($PARAM['sort']);
						else
							$ORDER=array('id');
						$SELECT=$this->Framework->library->data->delete($PARAM, array('user', 'search', 'status', 'strategy'));
						$DATA['COMPANY']=$this->Framework->direct->company->get($SELECT, $ORDER, array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));
						foreach($DATA['COMPANY']['ELEMENT'] as &$VALUE) {
							$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get(array('company'=>$VALUE['id'])));
							//$this->Framework->db->set("SELECT MAX(`t1`.`time`) as `max` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.id=`t1`.`group`) WHERE `t2`.`company`='".$VALUE['id']."' AND `t1`.`plan`=1");
							//$ROW=$this->Framework->db->get();
							$ROW['max']=$VALUE['datetime'];
							$VALUE['phrase_max_time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $ROW['max']);
							$VALUE['expire']=(!empty($ROW['max']) && strtotime($ROW['max'])+$this->Framework->direct->model->config->CONFIG['expire']<time())?true:false;
						}
					}
				break;				
				case 1:
				case 'admin':
					if (empty($PARAM['user']))
						if (is_array($PARAM))
							$PARAM['user']=0;
						else
							$PARAM=array('user'=>0);
					$DATA['USER']=$this->Framework->user->model->model->get(array('group'=>(!empty($PARAM['user'])?array(2,3):4), 'parent'=>$PARAM['user']), array('login'), array(), array('page'=>(!empty($PARAM['page'])?(int)$PARAM['page']:0), 'number'=>$this->limit));
					
					if (!empty($DATA['USER']['ELEMENT']) && count($DATA['USER']['ELEMENT'])>1) {
						foreach($DATA['USER']['ELEMENT'] as &$VALUE) {
							$MANAGER=array();
							if ($VALUE['parent']) {
								$MANAGER=$this->Framework->user->model->model->get(array('id'=>$VALUE['parent']));
								$MANAGER=!empty($MANAGER['ELEMENT'][0])?$MANAGER['ELEMENT'][0]:array();
							}
							$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get($VALUE['id']), array('MANAGER'=>$MANAGER));
						}
					} elseif (!empty($DATA['USER']['ELEMENT']) && count($DATA['USER']['ELEMENT'])==1) {
						$PARAM['user']=!empty($DATA['USER']['ELEMENT'][0])?$DATA['USER']['ELEMENT'][0]['id']:(!empty($PARAM['user'])?$PARAM['user']:0);
						$DATA['USER']=$this->Framework->user->model->model->get(array('group'=>array(2,3), 'parent'=>$PARAM['user']), array('login'), array(), array('page'=>(!empty($PARAM['page'])?(int)$PARAM['page']:0), 'number'=>$this->limit));
						
						if (!empty($DATA['USER']['ELEMENT']) && count($DATA['USER']['ELEMENT'])==1) {
							$PARAM['user']=!empty($DATA['USER']['ELEMENT'][0])?$DATA['USER']['ELEMENT'][0]['id']:(!empty($PARAM['user'])?$PARAM['user']:0);
							$DATA['USER']=$this->Framework->user->model->model->get(array('group'=>array(2), 'parent'=>$PARAM['user']), array('login'), array(), array('page'=>(!empty($PARAM['page'])?(int)$PARAM['page']:0), 'number'=>$this->limit));
						}
						
						if (!empty($DATA['USER']['ELEMENT']) && count($DATA['USER']['ELEMENT'])>1) {
							foreach($DATA['USER']['ELEMENT'] as &$VALUE) {
								$MANAGER=array();
								if ($VALUE['parent']) {
									$MANAGER=$this->Framework->user->model->model->get(array('id'=>$VALUE['parent']));
									$MANAGER=!empty($MANAGER['ELEMENT'][0])?$MANAGER['ELEMENT'][0]:array();
								}
								$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get($VALUE['id']), array('MANAGER'=>$MANAGER));
							}
						} else {
							$PARAM['user']=!empty($DATA['USER']['ELEMENT'][0])?$DATA['USER']['ELEMENT'][0]['id']:(!empty($PARAM['user'])?$PARAM['user']:0);
							$DATA['USER']['ELEMENT']='';
						}
					} else {
						$PARAM['user']=!empty($PARAM['user'])?$PARAM['user']:(!empty($DATA['USER']['ELEMENT'][0])?$DATA['USER']['ELEMENT'][0]['id']:0);
						$DATA['USER']['ELEMENT']='';
					}
					
					if (!empty($PARAM['user']))
						$DATA['PARAM']['user']=$PARAM['user'];
					if (empty($DATA['USER']['ELEMENT'])) {	
						$DATA['DIRECT']=$this->Framework->direct->model->path->get($PARAM);
						if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'strategy', 'maximum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue')))
							$ORDER=array($PARAM['sort']);
						else
							$ORDER=array('id');
						$SELECT=$this->Framework->library->data->delete($PARAM, array('user', 'search', 'status', 'strategy'));
						$DATA['COMPANY']=$this->Framework->direct->company->get($SELECT, $ORDER, array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));	
						foreach($DATA['COMPANY']['ELEMENT'] as &$VALUE)  {
							$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get(array('company'=>$VALUE['id'])));
							//$this->Framework->db->set("SELECT MAX(`t1`.`time`) as `max` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.id=`t1`.`group`) WHERE `t2`.`company`='".$VALUE['id']."' AND `t1`.`plan`=1");
							//$ROW=$this->Framework->db->get();
							$ROW['max']=$VALUE['datetime'];
							$VALUE['phrase_max_time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $ROW['max']);
							$VALUE['expire']=(!empty($ROW['max']) && strtotime($ROW['max'])+$this->Framework->direct->model->config->CONFIG['expire']<time())?true:false;
						}
					} else {
						foreach ($DATA['USER']['ELEMENT'] as $key=>&$VALUE)
							if ($VALUE['group']==3 && empty($VALUE['children']))
								unset($DATA['USER']['ELEMENT'][$key]);
					}
				break;				

			}
			$DATA=array_merge($DATA, $this->filter($PARAM));
		} 
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	
	public function group($PARAM=array()) {
		$DATA=array();

		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA=$this->filter($PARAM);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['CURRENCY']=$this->Framework->direct->model->currency->get();
			$DATA['DIRECT']=$this->Framework->direct->model->path->get($this->Framework->library->data->delete($PARAM, array('user', 'company')));
			$DATA['STRATEGY']['ELEMENT']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('status'=>array(1,2)), array('id'));
			$DATA['STRATEGY']['ID']=$this->Framework->direct->model->strategy->get(array(), array(), array(), array(), array('id'));
			
			if (!empty($PARAM['user']) || !empty($PARAM['company'])) {
				$DATA['COMPANY']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
					'id'=>(int)$PARAM['company']
				));
				
				if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'strategy', 'maximum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue')))
					$ORDER=array($PARAM['sort']);
				else
					$ORDER=array('id');
				$SELECT=$this->Framework->library->data->delete($PARAM, array('user', 'company', 'search', 'tag', 'status', 'strategy'));
				$DATA['GROUP']=$this->Framework->direct->group->get($SELECT, $ORDER, array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));
				foreach($DATA['GROUP']['ELEMENT'] as &$VALUE) {
					$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get(array('group'=>$VALUE['id'])));
					//$this->Framework->db->set("SELECT MAX(`time`) as `max` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` WHERE `group`='".$VALUE['id']."' AND `plan`=1");
					$ROW['max']=$VALUE['datetime'];
					$VALUE['phrase_max_time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $ROW['max']);
					$VALUE['expire']=(!empty($ROW['max']) && strtotime($ROW['max'])+$this->Framework->direct->model->config->CONFIG['expire']<time())?true:false;
					$VALUE['currency']=empty($VALUE['currency'])?$DATA['COMPANY'][0]['currency']:$VALUE['currency'];
					$this->Framework->db->set("SELECT 
					`t1`.`strategy` as `company_strategy`,
					`t1`.`percent` as `company_percent`,
					`t1`.`type` as `company_type`,
					`t1`.`add` as `company_add`,
					`t1`.`fixed` as `company_fixed`,
					`t1`.`maximum` as `company_maximum`,
					`t1`.`budget` as `company_budget`,
					`t1`.`context` as `company_context`,
					`t1`.`context_percent` as `company_context_percent`,
					`t1`.`context_type` as `company_context_type`,
					`t1`.`context_maximum` as `company_context_maximum`,
					`t1`.`context_fixed` as `company_context_fixed`,
					
					`t1`.`strategy_name`
					
					FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1` WHERE `t1`.`id`='".$VALUE['company']."'");
					$ROW=$this->Framework->db->get();
					$VALUE['strategy_name']=$ROW['strategy_name'];
					if ($VALUE['strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$VALUE['strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['strategy']]['name'],
							'percent'=>round($VALUE['percent'],2),
							'type'=>(int)$VALUE['type'],
							'add'=>round($VALUE['add'],2),
							'fixed'=>round($VALUE['fixed'],2),
							'maximum'=>round($VALUE['maximum'],2),
							'budget'=>round($VALUE['budget'],2),
							'context'=>round($VALUE['context'],2),
							'context_percent'=>round($VALUE['context_percent'],2),
							'context_type'=>round($VALUE['context_type'],2),
							'context_maximum'=>round($VALUE['context_maximum'],2),
							'context_fixed'=>round($VALUE['context_fixed'],2),
							'context_minimum'=>round($VALUE['context_minimum'],2),
						);
					} elseif ($ROW['company_strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$ROW['company_strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$ROW['company_strategy']]['name'],
							'percent'=>round($ROW['company_percent'],2),
							'type'=>(int)$ROW['company_type'],
							'add'=>round($ROW['company_add'],2),
							'fixed'=>round($ROW['company_fixed'],2),
							'maximum'=>round($ROW['company_maximum'],2),
							'budget'=>round($ROW['company_budget'],2),
							'context'=>round($ROW['company_context'],2),
							'context_percent'=>round($ROW['company_context_percent'],2),
							'context_type'=>round($ROW['company_context_type'],2),
							'context_maximum'=>round($ROW['company_context_maximum'],2),
							'context_fixed'=>round($ROW['company_context_fixed'],2),
							'context_minimum'=>round($ROW['company_context_minimum'],2),
						);
					} elseif ($DATA['COMPANY'][0]['strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$DATA['COMPANY'][0]['strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$DATA['COMPANY'][0]['strategy']]['name'],
							'percent'=>round($DATA['COMPANY'][0]['percent'],2),
							'type'=>(int)$DATA['COMPANY'][0]['type'],
							'add'=>round($DATA['COMPANY'][0]['add'],2),
							'fixed'=>round($DATA['COMPANY'][0]['fixed'],2),
							'maximum'=>round($DATA['COMPANY'][0]['maximum'],2),
							'budget'=>round($DATA['COMPANY'][0]['budget'],2),
							'context'=>round($DATA['COMPANY'][0]['context'],2),
							'context_percent'=>round($DATA['COMPANY'][0]['context_percent'],2),
							'context_type'=>round($DATA['COMPANY'][0]['context_type'],2),
							'context_maximum'=>round($DATA['COMPANY'][0]['context_maximum'],2),
							'context_fixed'=>round($DATA['COMPANY'][0]['context_fixed'],2),
							'context_minimum'=>round($DATA['COMPANY'][0]['context_minimum'],2),
						);
					}
					
				}
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}	
	
	public function phrase($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA=$this->filter($PARAM);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['CURRENCY']=$this->Framework->direct->model->currency->get();
			$DATA['DIRECT']=$this->Framework->direct->model->path->get($this->Framework->library->data->delete($PARAM, array('user', 'company', 'group')));
			$DATA['STRATEGY']['ELEMENT']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('status'=>array(1,2)), array('id'));
			$DATA['STRATEGY']['ID']=$this->Framework->direct->model->strategy->get(array(), array(), array(), array(), array('id'));
			
			if (!empty($PARAM['user']) || !empty($PARAM['company']) || !empty($PARAM['group'])) {
				
				if (!empty($PARAM['group']))
					$DATA['GROUP']=$this->Framework->direct->group->get(array(
						'id'=>(int)$PARAM['group']
					));
			
				$DATA['COMPANY']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
					'id'=>(!empty($DATA['GROUP'][0]['company'])?(int)$DATA['GROUP'][0]['company']:(int)$PARAM['company']),
				));
				//Поиск//
				if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'strategy', 'maximum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue')))
					$ORDER=array($PARAM['sort']);
				else
					$ORDER=array('id');
				$SELECT=$this->Framework->library->data->delete($PARAM, array('user', 'company', 'group', 'search', 'tag', 'status', 'strategy', 'place', 'pricelist'));
				if (!empty($PARAM['real'])) {
					if ($PARAM['price1']) 
						$SELECT['real_price'][0]='&>'.$PARAM['price1'];
					if ($PARAM['price2']) 
						$SELECT['real_price'][1]='&<'.$PARAM['price2'];
				} else {
					if ($PARAM['price1']) 
						$SELECT['price'][0]='&>'.$PARAM['price1'];
					if ($PARAM['price2']) 
						$SELECT['price'][1]='&<'.$PARAM['price2'];
				}
				//\Поиск//
				$DATA['PHRASE']=$this->Framework->direct->phrase->get($SELECT, $ORDER, array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));

				foreach($DATA['PHRASE']['ELEMENT'] as &$VALUE) {
					$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get(array('phrase'=>$VALUE['id'])));
					$VALUE['expire']=($VALUE['plan'] && strtotime($VALUE['time'])+$this->Framework->direct->model->config->CONFIG['expire']<time())?true:false;
					$VALUE['time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $VALUE['time']);

					$VALUE['CONTEXT_COVERAGE']=array();
					if ($VALUE['context_coverage']) {
						$VALUE['CONTEXT_COVERAGE']=json_decode($VALUE['context_coverage']);
						$VALUE['CONTEXT_COVERAGE']=$this->Framework->library->data->get($VALUE['CONTEXT_COVERAGE']);
					}
					$VALUE['context_place']=$this->Framework->direct->model->formula->context_place($VALUE);
					
					$VALUE['currency']=empty($VALUE['currency'])?$DATA['COMPANY'][0]['currency']:$VALUE['currency'];
					$this->Framework->db->set("SELECT 
					`t1`.`strategy` as `group_strategy`,
					`t1`.`percent` as `group_percent`,
					`t1`.`type` as `group_type`,
					`t1`.`add` as `group_add`,
					`t1`.`fixed` as `group_fixed`,
					`t1`.`maximum` as `group_maximum`,
					`t1`.`budget` as `group_budget`,
					`t1`.`context` as `group_context`,
					`t1`.`context_percent` as `group_context_percent`,
					`t1`.`context_type` as `group_context_type`,
					`t1`.`context_maximum` as `group_context_maximum`,
					`t1`.`context_fixed` as `group_context_fixed`,
					
					`t2`.`strategy` as `company_strategy`,
					`t2`.`percent` as `company_percent`,
					`t2`.`type` as `company_type`,
					`t2`.`add` as `company_add`,
					`t2`.`fixed` as `company_fixed`,
					`t2`.`maximum` as `company_maximum`,
					`t2`.`budget` as `company_budget`,
					`t2`.`context` as `company_context`,
					`t2`.`context_percent` as `company_context_percent`,
					`t2`.`context_type` as `company_context_type`,
					`t2`.`context_maximum` as `company_context_maximum`,
					`t2`.`context_fixed` as `company_context_fixed`,
					`t2`.`context_minimum` as `company_context_minimum`, 
					
					`t2`.`strategy_name`
					
					FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.id=`t1`.`company`) WHERE `t1`.`id`='".$VALUE['group']."'");
					$ROW=$this->Framework->db->get();
					$VALUE['strategy_name']=$ROW['strategy_name'];
					$VALUE['place']=$this->Framework->direct->model->formula->place($VALUE);
					if ($VALUE['strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$VALUE['strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['strategy']]['name'],
							'percent'=>round($VALUE['percent'],2),
							'type'=>(int)$VALUE['type'],
							'add'=>round($VALUE['add'],2),
							'fixed'=>round($VALUE['fixed'],2),
							'maximum'=>round($VALUE['maximum'],2),
							'budget'=>round($VALUE['budget'],2),
							'context'=>round($VALUE['context'],2),
							'context_percent'=>round($VALUE['context_percent'],2),
							'context_type'=>round($VALUE['context_type'],2),
							'context_maximum'=>round($VALUE['context_maximum'],2),
							'context_fixed'=>round($VALUE['context_fixed'],2),
							'context_minimum'=>round($VALUE['context_minimum'],2),
						);
					} elseif ($ROW['group_strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$ROW['group_strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$ROW['group_strategy']]['name'],
							'percent'=>round($ROW['group_percent'],2),
							'type'=>(int)$ROW['group_type'],
							'add'=>round($ROW['group_add'],2),
							'fixed'=>round($ROW['group_fixed'],2),
							'maximum'=>round($ROW['group_maximum'],2),
							'budget'=>round($ROW['group_budget'],2),
							'context'=>round($ROW['group_context'],2),
							'context_percent'=>round($ROW['group_context_percent'],2),
							'context_type'=>round($ROW['group_context_type'],2),
							'context_maximum'=>round($ROW['group_context_maximum'],2),
							'context_fixed'=>round($ROW['group_context_fixed'],2),
							'context_minimum'=>round($ROW['group_context_minimum'],2),
						);
					} elseif ($ROW['company_strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$ROW['company_strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$ROW['company_strategy']]['name'],
							'percent'=>round($ROW['company_percent'],2),
							'type'=>(int)$ROW['company_type'],
							'add'=>round($ROW['company_add'],2),
							'fixed'=>round($ROW['company_fixed'],2),
							'maximum'=>round($ROW['company_maximum'],2),
							'budget'=>round($ROW['company_budget'],2),
							'context'=>round($ROW['company_context'],2),
							'context_percent'=>round($ROW['company_context_percent'],2),
							'context_type'=>round($ROW['company_context_type'],2),
							'context_maximum'=>round($ROW['company_context_maximum'],2),
							'context_fixed'=>round($ROW['company_context_fixed'],2),
							'context_minimum'=>round($ROW['company_context_minimum'],2),
						);
					}
					
					//
					//ob_start();
					//$PHRASE=$VALUE;
					//echo $this->Framework->direct->model->formula->set($DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['STRATEGY']['id']]['value']);
					//echo '"'.str_replace(']', ']."', str_replace('$', '".$', $this->Framework->direct->model->formula->set($DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['STRATEGY']['id']]['value']))).'"';
					//eval('echo "'.str_replace(']', ']."', str_replace('$', '".$', $this->Framework->direct->model->formula->set($DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['STRATEGY']['id']]['value']))).'"');
					//$VALUE['STRATEGY']['formula']=ob_get_contents();
					//ob_end_clean();
				
				}
			
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	
	public function retargeting($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA=$this->filter($PARAM);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['CURRENCY']=$this->Framework->direct->model->currency->get();
			$DATA['DIRECT']=$this->Framework->direct->model->path->get($this->Framework->library->data->delete($PARAM, array('user', 'company', 'group')));
			$DATA['STRATEGY']['ELEMENT']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], array('status'=>array(1,2)), array('id'));
			$DATA['STRATEGY']['ID']=$this->Framework->direct->model->strategy->get(array(), array(), array(), array(), array('id'));
			
			if (!empty($PARAM['user']) || !empty($PARAM['company']) || !empty($PARAM['group'])) {
				
				if (!empty($PARAM['group']))
					$DATA['GROUP']=$this->Framework->direct->group->get(array(
						'id'=>(int)$PARAM['group']
					));
			
				$DATA['COMPANY']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array(
					'id'=>(!empty($DATA['GROUP'][0]['company'])?(int)$DATA['GROUP'][0]['company']:(int)$PARAM['company']),
				));
			
				if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'strategy', 'maximum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue')))
					$ORDER=array($PARAM['sort']);
				else
					$ORDER=array('id');
				$DATA['RETARGETING']=$this->Framework->direct->retargeting->get($this->Framework->library->data->delete($PARAM, array('user', 'company', 'group', 'search', 'tag', 'status')), $ORDER, array(), array('page'=>(!empty($PARAM['page'])?$PARAM['page']:0), 'number'=>(!empty($PARAM['number'])?$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number'])));

				foreach($DATA['RETARGETING']['ELEMENT'] as &$VALUE) {
					$VALUE=array_merge($VALUE, $this->Framework->direct->model->statistic->get(array('retargeting'=>$VALUE['id'])));
					$VALUE['expire']=($VALUE['plan'] && strtotime($VALUE['time'])+$this->Framework->direct->model->config->CONFIG['expire']<time())?true:false;
					$VALUE['time']=$this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $VALUE['time']);

					$VALUE['CONTEXT_COVERAGE']=array();
					if ($VALUE['context_coverage']) {
						$VALUE['CONTEXT_COVERAGE']=json_decode($VALUE['context_coverage']);
						$VALUE['CONTEXT_COVERAGE']=$this->Framework->library->data->get($VALUE['CONTEXT_COVERAGE']);
					}
					$VALUE['context_place']=$this->Framework->direct->model->formula->context_place($VALUE);
					
					$VALUE['currency']=$DATA['COMPANY'][0]['currency'];
					$this->Framework->db->set("SELECT 
					`t1`.`strategy` as `group_strategy`,
					`t1`.`percent` as `group_percent`,
					`t1`.`type` as `group_type`,
					`t1`.`add` as `group_add`,
					`t1`.`fixed` as `group_fixed`,
					`t1`.`maximum` as `group_maximum`,
					`t1`.`budget` as `group_budget`,
					`t1`.`context` as `group_context`,
					`t1`.`context_percent` as `group_context_percent`,
					`t1`.`context_type` as `group_context_type`,
					`t1`.`context_maximum` as `group_context_maximum`,
					`t1`.`context_fixed` as `group_context_fixed`,
					
					`t2`.`strategy` as `company_strategy`,
					`t2`.`percent` as `company_percent`,
					`t2`.`type` as `company_type`,
					`t2`.`add` as `company_add`,
					`t2`.`fixed` as `company_fixed`,
					`t2`.`maximum` as `company_maximum`,
					`t2`.`budget` as `company_budget`,
					`t2`.`context` as `company_context`,
					`t2`.`context_percent` as `company_context_percent`,
					`t2`.`context_type` as `company_context_type`,
					`t2`.`context_maximum` as `company_context_maximum`,
					`t2`.`context_fixed` as `company_context_fixed`,
					`t2`.`context_minimum` as `company_context_minimum`, 
					
					`t2`.`strategy_name`
					
					FROM `".$this->Framework->direct->model->config->TABLE['group']."` `t1` INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.id=`t1`.`company`) WHERE `t1`.`id`='".$VALUE['group']."'");
					$ROW=$this->Framework->db->get();
					$VALUE['strategy_name']=$ROW['strategy_name'];
					$VALUE['place']=$this->Framework->direct->model->formula->place($VALUE);
					if ($VALUE['strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$VALUE['strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$VALUE['strategy']]['name'],
							'percent'=>round($VALUE['percent'],2),
							'type'=>(int)$VALUE['type'],
							'add'=>round($VALUE['add'],2),
							'fixed'=>round($VALUE['fixed'],2),
							'maximum'=>round($VALUE['maximum'],2),
							'budget'=>round($VALUE['budget'],2),
							'context'=>round($VALUE['context'],2),
							'context_percent'=>round($VALUE['context_percent'],2),
							'context_type'=>round($VALUE['context_type'],2),
							'context_maximum'=>round($VALUE['context_maximum'],2),
							'context_fixed'=>round($VALUE['context_fixed'],2),
							'context_minimum'=>round($VALUE['context_minimum'],2),
						);
					} elseif ($ROW['group_strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$ROW['group_strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$ROW['group_strategy']]['name'],
							'percent'=>round($ROW['group_percent'],2),
							'type'=>(int)$ROW['group_type'],
							'add'=>round($ROW['group_add'],2),
							'fixed'=>round($ROW['group_fixed'],2),
							'maximum'=>round($ROW['group_maximum'],2),
							'budget'=>round($ROW['group_budget'],2),
							'context'=>round($ROW['group_context'],2),
							'context_percent'=>round($ROW['group_context_percent'],2),
							'context_type'=>round($ROW['group_context_type'],2),
							'context_maximum'=>round($ROW['group_context_maximum'],2),
							'context_fixed'=>round($ROW['group_context_fixed'],2),
							'context_minimum'=>round($ROW['group_context_minimum'],2),
						);
					} elseif ($ROW['company_strategy']>0) {
						$VALUE['STRATEGY']=array(
							'id'=>$ROW['company_strategy'],
							'name'=>$DATA['STRATEGY']['ID']['ELEMENT'][$ROW['company_strategy']]['name'],
							'percent'=>round($ROW['company_percent'],2),
							'type'=>(int)$ROW['company_type'],
							'add'=>round($ROW['company_add'],2),
							'fixed'=>round($ROW['company_fixed'],2),
							'maximum'=>round($ROW['company_maximum'],2),
							'budget'=>round($ROW['company_budget'],2),
							'context'=>round($ROW['company_context'],2),
							'context_percent'=>round($ROW['company_context_percent'],2),
							'context_type'=>round($ROW['company_context_type'],2),
							'context_maximum'=>round($ROW['company_context_maximum'],2),
							'context_fixed'=>round($ROW['company_context_fixed'],2),
							'context_minimum'=>round($ROW['company_context_minimum'],2),
						);
					}
					
				
				}
			
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}	
	
	public function cron($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			if (!empty($PARAM))
				if (!is_array($PARAM))
					$PARAM=array('id'=>$PARAM);
			$DATA['CRON']=$this->Framework->cron->cron->get($PARAM, array('id'));
			foreach ($DATA['CRON'] as &$VALUE) {
				$VALUE['time']=$this->Framework->library->time->datetime($this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $VALUE['time']));
				$VALUE['time_end']=$this->Framework->library->time->datetime($this->Framework->library->time->timezone($this->Framework->direct->model->config->CONFIG['timezone'], $VALUE['time_end']));
			}
				
			
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}

	public function cron_edit($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			if (!empty($PARAM))
				if (!is_array($PARAM))
					$PARAM=array('id'=>$PARAM);
			$DATA['PARAM']=$PARAM;
			if (!empty($PARAM))
				$DATA['CRON']=$this->Framework->cron->cron->get($PARAM, array('id'));
			$DATA['ACCOUNT']=$this->Framework->user->model->model->get(array('group'=>4), array('login'), array('id', 'login', 'name'));
			if (!empty($DATA['CRON'][0]['param']))
				if (is_numeric($DATA['CRON'][0]['param']))
					$DATA['PARAM']['account']=$DATA['CRON'][0]['param'];
				else
					$DATA['PARAM']=array_merge($DATA['PARAM'], unserialize($DATA['CRON'][0]['param']));
			if (!empty($DATA['PARAM']['account'])) {
				$DATA['MANAGER']=$this->Framework->user->model->model->get(array('parent'=>$DATA['PARAM']['account'], 'group'=>3), array('login'), array('id', 'login', 'name'));
				$DATA['MANAGER']['ID'][]=$DATA['PARAM']['account'];
				if (!empty($DATA['MANAGER']['ID']))
					$DATA['USER']=$this->Framework->user->model->model->get(array('PARENT'=>$DATA['MANAGER']['ID'], 'group'=>2), array('login'), array('id', 'login', 'name'));
				$this->Framework->db->set("SELECT `t1`.`id`, CONCAT(`t1`.`name`, ' (', COUNT(`t2`.`id`),')') as `name`, COUNT(`t2`.`id`) as `count` FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
				INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`)
				WHERE `t2`.`id` IS NOT NULL ".(!empty($DATA['PARAM']['account'])?" AND `t1`.`account`='".(int)$DATA['PARAM']['account']."'":'').(!empty($DATA['PARAM']['user'])?" AND `t1`.`user`='".(int)$DATA['PARAM']['user']."'":'')."
				GROUP BY `t1`.`id`
				ORDER BY `count` desc, `t1`.`id`
				");

				$DATA['COMPANY']['ELEMENT']=array();
				while($ROW=$this->Framework->db->get())
					$DATA['COMPANY']['ELEMENT'][]=$ROW;
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}	
	
	public function cron_edit_company($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			//Получаем кампании//
			if (!empty($PARAM['company'])) {
				$COMPANY=$this->Framework->direct->company->get(array('id'=>$PARAM['company']));
				if (!empty($COMPANY['ELEMENT'][0])) {
					$PARAM['account']=$COMPANY['ELEMENT'][0]['account'];
					$DATA['CRON']['SELECT']['COMPANY']=$COMPANY['ELEMENT'][0];
				} else	
					$PARAM['company']=0;
			}
			
			if (!empty($PARAM['account'])) {
				$DATA['MANAGER']=$this->Framework->user->model->model->get(array('parent'=>$PARAM['account'], 'group'=>3), array('login'), array('id', 'login', 'name'));
				$DATA['MANAGER']['ID'][]=$PARAM['account'];
				if (!empty($DATA['MANAGER']['ID']))
					$DATA['CRON']['USER']=$this->Framework->user->model->model->get(array('PARENT'=>$DATA['MANAGER']['ID'], 'group'=>2), array('login'), array('id', 'login', 'name'));
				else
					$DATA['CRON']['USER']['ELEMENT']=array();
				$this->Framework->db->set("SELECT `t1`.`id`, `t1`.`name`, COUNT(`t2`.`id`) as `count` FROM `".$this->Framework->direct->model->config->TABLE['company']."` `t1`
				INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`company`=`t1`.`id`)
				WHERE `t2`.`id` IS NOT NULL ".(!empty($PARAM['account'])?" AND `t1`.`account`='".(int)$PARAM['account']."'":'').(!empty($PARAM['user'])?" AND `t1`.`user`='".(int)$PARAM['user']."'":'')."
				GROUP BY `t1`.`id`
				ORDER BY `count` desc, `t1`.`id`
				");
				$DATA['CRON']['COMPANY']['ELEMENT']=array();
				while($ROW=$this->Framework->db->get())
					$DATA['CRON']['COMPANY']['ELEMENT'][]=$ROW;
			}
			//\Получаем кампании//
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}

	public function article($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			if (!empty($PARAM))
				if (!is_array($PARAM))
					$PARAM=array('id'=>$PARAM);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['COUNT']=$this->Framework->direct->statistic->count->get();
			$DATA['ARTICLE']=$this->Framework->article->model->article->get($PARAM, array('id'=>'desc'), array(), array('page'=>0, 'number'=>1000));
		}	
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	

}//\class
?>