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

final class Statistic extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
		
	}
	
	
	private function filter(&$PARAM) {
		$DATA=array();
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
		$DATA['PARAM']=$PARAM;
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
	
	public function index($PARAM=array()) { 
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
			if (empty($PARAM['date_start']))
				$PARAM['date_start']=date('Y-m-d');
			if (empty($PARAM['date_end']))
				$PARAM['date_end']=date('Y-m-d');
			
			$DATA=$this->filter($PARAM);
			
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			//Диапазон дат//
			if (!empty($PARAM['user']) || !empty($PARAM['company']) || !empty($PARAM['group']) || !empty($PARAM['phrase']))
				$STAT=$this->Framework->direct->statistic->sum->get(array(
					'user'=>(!empty($PARAM['user'])?$PARAM['user']:0),
					'company'=>(!empty($PARAM['company'])?$PARAM['company']:0),
					'group'=>(!empty($PARAM['group'])?$PARAM['group']:0),
					'phrase'=>(!empty($PARAM['phrase'])?$PARAM['phrase']:0),
				), array('date'), array('date'), array('number'=>1));
			if (!empty($STAT['ELEMENT'][0])) 
				$DATA['PARAM']['time_min']=strtotime($STAT['ELEMENT'][0]['date']);
			else
				$DATA['PARAM']['time_min']=strtotime(date('Y-m-d'));
			$date=0;
			while (mktime(0, 0, 0, date('m', $DATA['PARAM']['time_min']), date('d', $DATA['PARAM']['time_min'])+$date, date('Y', $DATA['PARAM']['time_min']))<=time()) {
				$DATA['DATE'][$date]=date('Y-m-d', mktime(23, 59, 59, date('m', $DATA['PARAM']['time_min']), date('d', $DATA['PARAM']['time_min'])+$date, date('Y', $DATA['PARAM']['time_min']))); 
				$date++;
			}
			//\Диапазон дат//
			
			
			//Сортировка//
			if (!empty($PARAM['sort']) && in_array(str_replace('-', '', $PARAM['sort']), array('id', 'name', 'price', 'real_price', 'sum', 'show', 'click', 'ctr', 'conversion', 'roi', 'revenue', 'position', 'position_show', 'position_click', 'position_value', 'position_visible', 'position1', 'position2', 'position3', 'position4', 'position5', 'position6', 'position7', 'position8', 'price1', 'price2', 'price3', 'price4', 'price5', 'price6', 'price7', 'price8')))
				$ORDER=array($PARAM['sort']);
			else
				$ORDER=array();
			$order='';
			if (!empty($ORDER) && is_array($ORDER)) {
				foreach($ORDER as $key=>$value)	{
					if (is_numeric($key))
						if ($value=='sum')
							$order.=($order?',':'')."(`t1`.`sum`+`t1`.`sum_context`) ASC";
						elseif ($value=='-sum')
							$order.=($order?',':'')."(`t1`.`sum`+`t1`.`sum_context`) DESC";
						elseif ($value=='show')
							$order.=($order?',':'')."(`t1`.`show`+`t1`.`show_context`) ASC";
						elseif ($value=='-show')
							$order.=($order?',':'')."(`t1`.`show`+`t1`.`show_context`) DESC";
						elseif ($value=='click')
							$order.=($order?',':'')."(`t1`.`click`+`t1`.`click_context`) ASC";
						elseif ($value=='-click')
							$order.=($order?',':'')."(`t1`.`click`+`t1`.`click_context`) DESC";
						elseif ($value=='ctr')
							$order.=($order?',':'')."((`t1`.`click` + `t1`.`click_context`)/(`t1`.`show` + `t1`.`show_context`)) ASC";
						elseif ($value=='-ctr')
							$order.=($order?',':'')."((`t1`.`click` + `t1`.`click_context`)/(`t1`.`show` + `t1`.`show_context`)) DESC";
						elseif (substr($value, 0, 1)=='-') 
							$order.=($order?',':'')."`t1`.`".substr($value, 1)."` DESC";
						else
							$order.=($order?',':'')."`t1`.`".$value."` ASC";
					else
						$order.=($order?',':'')."`t1`.`".$key."` ".($value?$value:'ASC')."";
				}
			}
			if ($order)
				$order=' ORDER BY '.$order.' ';
			else
				$order=' ORDER BY `t1`.`company`, `t1`.`group`, `t1`.`phrase` ';
			//\Сортировка//

			//Поиск//
			$where='';
			$SELECT=$this->Framework->library->data->delete($PARAM, array('user', 'company', 'group', 'search', 'tag', 'status', 'sort', 'real_price', 'price1', 'price2', 'place', 'real'));
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
			
			if (!empty($SELECT['price'])) {
				if (!is_array($SELECT['price']))
					$SELECT['price']=array($SELECT['price']);
				$where_sql='';
				$where_logic='OR';
				$where_sign='=';
				foreach ($SELECT['price'] as $value) {
					$value=trim((string)$value);
					if (substr((string)$value, 0, 1)=='&' || substr((string)$value, 1, 1)=='&') 
						$where_logic='AND';
					if (substr($value, 0, 1)=='>' || substr($value, 1, 1)=='>') 
						$where_sign='>=';
					if (substr($value, 0, 1)=='<' || substr($value, 1, 1)=='<') 
						$where_sign='<=';
					$value=round((float)preg_replace('/[^0-9\.]/', '', str_replace(',', '.', $value)), 2);
					$where_sql.=($where_sql?' '.$where_logic.' ':'')."`t1`.`price`".$where_sign."'".$value."'";
				}
				if ($where_sql)
					$where.=' AND ('.$where_sql.') ';
			}
			
			if (!empty($SELECT['real_price'])) {
				if (!is_array($SELECT['real_price']))
					$SELECT['real_price']=array($SELECT['real_price']);
				$where_sql='';
				$where_logic='OR';
				$where_sign='=';
				foreach ($SELECT['real_price'] as $value) {
					$value=trim((string)$value);
					if (substr((string)$value, 0, 1)=='&' || substr((string)$value, 1, 1)=='&') 
						$where_logic='AND';
					if (substr($value, 0, 1)=='>' || substr($value, 1, 1)=='>') 
						$where_sign='>=';
					if (substr($value, 0, 1)=='<' || substr($value, 1, 1)=='<') 
						$where_sign='<=';
					$value=round((float)preg_replace('/[^0-9\.]/', '', str_replace(',', '.', $value)), 2);
					$where_sql.=($where_sql?' '.$where_logic.' ':'')."`t1`.`real_price`".$where_sign."'".$value."'";
				}
				if ($where_sql)
					$where.=' AND ('.$where_sql.') ';
			}
			
			if (!empty($SELECT['place']))
				if ((int)$SELECT['place']>0)
					$where.=" AND `t1`.`position`='".intval($SELECT['place'])."' ";	
				else
					$where.=" AND `t1`.`position`=0 ";	
			//\Поиск//
			
			$DATA['ANALIZE']=array();
			
			if (!empty($PARAM['user'])) {
				if (empty($PARAM['where']))
					if (!empty($PARAM['phrase']))
						$PARAM['where']=3;
					elseif (!empty($PARAM['group']))
						$PARAM['where']=2;
					elseif (!empty($PARAM['company']))
						$PARAM['where']=1;
					else
						$PARAM['where']=0;
				elseif (!empty($PARAM['phrase']))
					$PARAM['where']=3;
				elseif (!empty($PARAM['group']) && !$PARAM['where']>=2)
					$PARAM['where']=2;

				$DATA['PARAM']['where']=$PARAM['where'];
				
				//Статистика//
				switch ($PARAM['where']) {
					case 3: 
						$sql="SELECT 
						COUNT(DISTINCT `t1`.`phrase`) as `count`			
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`id`=`t1`.`phrase`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'')."
						`t1`.`banner`=0 
						{$where}
						";
					break;
					case 2: 
						$sql="SELECT 
						COUNT(DISTINCT `t1`.`group`) as `count`			
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'')."
						`t1`.`banner`=0 
						{$where}
						";
					break;
					case 3: 
						$sql="SELECT 
						COUNT(DISTINCT `t1`.`company`) as `count`			
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'')."
						`t1`.`banner`=0 
						{$where}
						";

					break;
					default: 
						$sql="SELECT 
						COUNT(DISTINCT `t1`.`user`) as `count`			
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'')."
						`t1`.`banner`=0 
						{$where}
						";

					break;

				}
				$this->Framework->db->set($sql);
				$COUNT=$this->Framework->db->get();
				
				$LIMIT=array('count'=>$COUNT['count'],'page'=>(int)$PARAM['page'], 'number'=>(int)$PARAM['number']>0?(int)$PARAM['number']:$this->Framework->direct->model->config->CONFIG['number']);
				$DATA['ANALIZE']['PAGE']=$this->Framework->library->page->get($LIMIT);
				
				$sql="SELECT 
				MIN(`t1`.`account`) as `account`, 
				MIN(`t1`.`user`) as `user`, 
				MIN(`t1`.`company`) as `company`, 
				MIN(`t1`.`group`) as `group`, 
				MIN(`t1`.`banner`) as `banner`, 
				MIN(`t1`.`phrase`) as `phrase`, 
				MIN(`t1`.`currency`) as `currency`, 
				SUM(`t1`.`count`) as `count`, 
				SUM(`t1`.`show` + `t1`.`show_context`) as `show`, 
				SUM(`t1`.`click` + `t1`.`click_context`) as `click`, 
				100*SUM(`t1`.`click` + `t1`.`click_context`)/SUM(`t1`.`show` + `t1`.`show_context`) as `ctr`, 
				SUM(`t1`.`sum` + `t1`.`sum_context`) as `sum`, 
				".($PARAM['where']==0?'SUM':'MAX')."(`t1`.`conversion`) as `conversion`, 
				AVG(`t1`.`price`) as `price`, 
				AVG(`t1`.`real_price`) as `real_price`, 
				AVG(`t1`.`position1`) as `position1`, 
				AVG(`t1`.`position2`) as `position2`, 
				AVG(`t1`.`position3`) as `position3`, 
				AVG(`t1`.`position4`) as `position4`, 
				AVG(`t1`.`position5`) as `position5`, 
				AVG(`t1`.`position6`) as `position6`, 
				AVG(`t1`.`position7`) as `position7`, 
				AVG(`t1`.`position8`) as `position8`, 
				AVG(`t1`.`price1`) as `price1`, 
				AVG(`t1`.`price2`) as `price2`, 
				AVG(`t1`.`price3`) as `price3`, 
				AVG(`t1`.`price4`) as `price4`, 
				AVG(`t1`.`price5`) as `price5`, 
				AVG(`t1`.`price6`) as `price6`, 
				AVG(`t1`.`price7`) as `price7`, 
				AVG(`t1`.`price8`) as `price8`, 
				AVG(`t1`.`context`) as `context`, 
				AVG(`t1`.`context_percent`) as `context_percent`, 
				AVG(`t1`.`context_max`) as `context_max`, 
				SUM(IF(`t1`.`position`>0,`t1`.`position`,0))/SUM(IF(`t1`.`position`>0,1,0)) as `position`,
				AVG(`t1`.`position_show`) as `position_show`, 
				AVG(`t1`.`position_click`) as `position_click`, 
				AVG(`t1`.`position_value`) as `position_value`,
				AVG(`t1`.`position_visible`) as `position_visible`,
				`t2`.`name` as `name` ";
				
				switch ($PARAM['where']) {
					case 3: 
						$sql.="FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['phrase']."` `t2` ON (`t2`.`id`=`t1`.`phrase`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'')."
						`t1`.`banner`=0  
						{$where}
						GROUP BY `t1`.`phrase`
						{$order}
						".$DATA['ANALIZE']['PAGE']['limit'];
					break;
					case 2: 
						$sql.="		
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['group']."` `t2` ON (`t2`.`id`=`t1`.`group`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'`t1`.`phrase`=0 AND ')."
						`t1`.`banner`=0 
						{$where}
						GROUP BY `t1`.`group`
						{$order}
						".$DATA['ANALIZE']['PAGE']['limit'];
					break;
					case 1: 
						$sql.="		
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'`t1`.`group`=0 AND ')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'`t1`.`phrase`=0 AND ')."
						`t1`.`banner`=0 
						{$where}
						GROUP BY `t1`.`company`
						{$order}
						".$DATA['ANALIZE']['PAGE']['limit'];

					break;
					default: 
						$sql.="		
						FROM `".$this->Framework->direct->model->config->TABLE['statistic']."` `t1`
						INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t2` ON (`t2`.`id`=`t1`.`company`)
						WHERE 
						`t1`.`date`>='".$PARAM['date_start']."' AND 
						`t1`.`date`<='".$PARAM['date_end']."' AND 
						".(!empty($PARAM['user'])?"`t1`.`user`='".$PARAM['user']."' AND ":'')."
						".(!empty($PARAM['company'])?"`t1`.`company`='".$PARAM['company']."' AND ":'')."
						".(!empty($PARAM['group'])?"`t1`.`group`='".$PARAM['group']."' AND ":'`t1`.`group`=0 AND ')." 
						".(!empty($PARAM['phrase'])?"`t1`.`phrase`='".$PARAM['phrase']."' AND ":'`t1`.`phrase`=0 AND ')."
						`t1`.`banner`=0 
						{$where}
						GROUP BY `t1`.`user`
						{$order}
						".$DATA['ANALIZE']['PAGE']['limit'];

					break;

				}

				$this->Framework->db->set($sql);
				$DATA['ANALIZE']['AVG']=array('count'=>0, 'none'=>0, 'visible'=>0, 'premium'=>0, 'garantee'=>0, 'premium_min'=>0, 'premium_max'=>0, 'min'=>0, 'max'=>0, 'price'=>0, 'real_price'=>0, 'phrase_position_value'=>0);
				$DATA['ANALIZE']['ELEMENT']=array();
				while ($ROW=$this->Framework->db->get()) {
					$MINUS=$this->Framework->direct->model->phrase->minus($ROW['name']);
					$ROW['name']=$MINUS['name'];
					$ROW['minus']=$MINUS['minus'];
					$DATA['ANALIZE']['ELEMENT'][]=$ROW;
					$DATA['ANALIZE']['AVG']['count']++;
					/*
					$DATA['ANALIZE']['AVG']['none']+=$ROW['none'];
					$DATA['ANALIZE']['AVG']['visible']+=$ROW['visible'];
					$DATA['ANALIZE']['AVG']['premium']+=$ROW['premium'];
					$DATA['ANALIZE']['AVG']['garantee']+=$ROW['garantee'];
					$DATA['ANALIZE']['AVG']['premium_min']+=$ROW['premium_min'];
					$DATA['ANALIZE']['AVG']['premium_max']+=$ROW['premium_max'];
					$DATA['ANALIZE']['AVG']['min']+=$ROW['min'];
					$DATA['ANALIZE']['AVG']['max']+=$ROW['max'];
					$DATA['ANALIZE']['AVG']['price']+=$ROW['price'];
					$DATA['ANALIZE']['AVG']['real_price']+=$ROW['real_price'];
					$DATA['ANALIZE']['AVG']['phrase_position_value']+=$ROW['phrase_position_value'];*/
				}
				if ($DATA['ANALIZE']['AVG']['count']>0) {
					$DATA['ANALIZE']['AVG']['none']=$DATA['ANALIZE']['AVG']['none']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['visible']=$DATA['ANALIZE']['AVG']['visible']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['premium']=$DATA['ANALIZE']['AVG']['premium']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['garantee']=$DATA['ANALIZE']['AVG']['garantee']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['premium_min']=$DATA['ANALIZE']['AVG']['premium_min']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['premium_max']=$DATA['ANALIZE']['AVG']['premium_max']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['min']=$DATA['ANALIZE']['AVG']['min']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['max']=$DATA['ANALIZE']['AVG']['max']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['price']=$DATA['ANALIZE']['AVG']['price']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['real_price']=$DATA['ANALIZE']['AVG']['real_price']/$DATA['ANALIZE']['AVG']['count'];
					$DATA['ANALIZE']['AVG']['phrase_position_value']=$DATA['ANALIZE']['AVG']['phrase_position_value']/$DATA['ANALIZE']['AVG']['count'];
				}
				//\Статистика//
				
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		if (!empty($PARAM['export'])) {
			$this->csv(__METHOD__);
			echo pack('CCC', 0xef, 0xbb, 0xbf);
			foreach ($DATA['ANALIZE']['ELEMENT'] as &$VALUE) {
				if (empty($key)) {
					$key=array_keys($VALUE);
					echo implode(';', $key)."\r\n";
				}
				array_walk($VALUE, function(&$value) { 
					if (is_numeric($value)) 
						$value=str_replace('.', ',', (string)$value); 
				});
				echo implode(';', $VALUE)."\r\n";
			}
		} else
			echo $this->Framework->template->get('index.html');
	}	
	
	public function price($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
			if (empty($PARAM['date_start']))
				$PARAM['date_start']=date('Y-m-d');
			if (empty($PARAM['date_end']))
				$PARAM['date_end']=date('Y-m-d');
			
			$DATA=$this->filter($PARAM);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			//Диапазон дат//
			$STAT=$this->Framework->direct->statistic->price->get(array(
				'phrase'=>$PARAM['phrase']
			), array('datetime'), array('datetime'), array('number'=>1));
			if (!empty($STAT['ELEMENT'][0])) 
				$DATA['PARAM']['time_min']=strtotime($STAT['ELEMENT'][0]['datetime']);
			else
				$DATA['PARAM']['time_min']=strtotime(date('Y-m-d'));
			$date=0;
			while (mktime(0, 0, 0, date('m', $DATA['PARAM']['time_min']), date('d', $DATA['PARAM']['time_min'])+$date, date('Y', $DATA['PARAM']['time_min']))<=time()) {
				$DATA['DATE'][$date]=date('Y-m-d', mktime(23, 59, 59, date('m', $DATA['PARAM']['time_min']), date('d', $DATA['PARAM']['time_min'])+$date, date('Y', $DATA['PARAM']['time_min']))); 
				$date++;
			}
			//\Диапазон дат//
			
			if (!empty($PARAM['phrase'])) {
				//Статистика//
				$DATA['STATISTIC']=$this->Framework->direct->statistic->price->get(array(
					'phrase'=>$PARAM['phrase'],
					'date_start'=>$PARAM['date_start'],
					'date_end'=>$PARAM['date_end'],
				), array('datetime'));
				//\Статистика//
				
				//Аналитика//
				$DATA['ANALIZE']=array('position_count'=>0,'none'=>0, 'visible'=>0, 'premium'=>0, 'garantee'=>0, 'position'=>0, 'position1'=>0, 'position2'=>0, 'position3'=>0, 'position4'=>0, 'position5'=>0, 'position6'=>0, 'position7'=>0, 'position8'=>0, 'price1'=>0, 'price2'=>0, 'price3'=>0, 'price4'=>0, 'price5'=>0, 'price6'=>0, 'price7'=>0, 'price8'=>0, 'price'=>0, 'real_price'=>0);
				$DATA['ANALIZE']['MAX']=array('position_count'=>0,'none'=>0, 'visible'=>0, 'premium'=>0, 'garantee'=>0, 'position'=>0, 'position1'=>0, 'position2'=>0, 'position3'=>0, 'position4'=>0, 'position5'=>0, 'position6'=>0, 'position7'=>0, 'position8'=>0, 'price1'=>0, 'price2'=>0, 'price3'=>0, 'price4'=>0, 'price5'=>0, 'price6'=>0, 'price7'=>0, 'price8'=>0, 'price'=>0, 'real_price'=>0);
				$DATA['ANALIZE']['MIN']=array('position_count'=>0,'none'=>0, 'visible'=>0, 'premium'=>0, 'garantee'=>0, 'position'=>0, 'position1'=>0, 'position2'=>0, 'position3'=>0, 'position4'=>0, 'position5'=>0, 'position6'=>0, 'position7'=>0, 'position8'=>0, 'price1'=>0, 'price2'=>0, 'price3'=>0, 'price4'=>0, 'price5'=>0, 'price6'=>0, 'price7'=>0, 'price8'=>0, 'price'=>0, 'real_price'=>0);
				$DATA['ANALIZE']['count']=count($DATA['STATISTIC']['ELEMENT']);
				if (!empty($DATA['ANALIZE']['count'])) {
					foreach($DATA['STATISTIC']['ELEMENT'] as &$VALUE) {
						if ($VALUE['position']>0 && $VALUE['position']<=3)
							$DATA['ANALIZE']['premium']++;
						elseif ($VALUE['positione']>3)
							$DATA['ANALIZE']['garantee']++;
						if ($VALUE['position']>0) {
							$DATA['ANALIZE']['position']+=$VALUE['position'];
							$DATA['ANALIZE']['position_count']++;
							$DATA['ANALIZE']['MAX']['position']=($VALUE['position']>$DATA['ANALIZE']['MAX']['position'])?$VALUE['position']:$DATA['ANALIZE']['MAX']['position'];
							$DATA['ANALIZE']['MIN']['position']=($VALUE['position']<$DATA['ANALIZE']['MIN']['position'] || !$DATA['ANALIZE']['MIN']['position']>0)?$VALUE['position']:$DATA['ANALIZE']['MIN']['position'];
						}
						
						$DATA['ANALIZE']['position1']+=$VALUE['position1'];
						$DATA['ANALIZE']['MAX']['position1']=($VALUE['position1']>$DATA['ANALIZE']['MAX']['position1'])?$VALUE['position1']:$DATA['ANALIZE']['MAX']['position1'];
						$DATA['ANALIZE']['MIN']['position1']=($VALUE['position1']<$DATA['ANALIZE']['MIN']['position1'] || !$DATA['ANALIZE']['MIN']['position1']>0)?$VALUE['position1']:$DATA['ANALIZE']['MIN']['position1'];

						$DATA['ANALIZE']['position2']+=$VALUE['position2'];
						$DATA['ANALIZE']['MAX']['position2']=($VALUE['position2']>$DATA['ANALIZE']['MAX']['position2'])?$VALUE['position2']:$DATA['ANALIZE']['MAX']['position2'];
						$DATA['ANALIZE']['MIN']['position2']=($VALUE['position2']<$DATA['ANALIZE']['MIN']['position2'] && $DATA['ANALIZE']['MIN']['position2']>0)?$VALUE['position2']:$DATA['ANALIZE']['MIN']['position2'];
						
						$DATA['ANALIZE']['position3']+=$VALUE['position3'];
						$DATA['ANALIZE']['MAX']['position3']=($VALUE['position3']>$DATA['ANALIZE']['MAX']['position3'])?$VALUE['position3']:$DATA['ANALIZE']['MAX']['position3'];
						$DATA['ANALIZE']['MIN']['position3']=($VALUE['position3']<$DATA['ANALIZE']['MIN']['position3'] || !$DATA['ANALIZE']['MIN']['position3']>0)?$VALUE['position3']:$DATA['ANALIZE']['MIN']['position3'];

						$DATA['ANALIZE']['position4']+=$VALUE['position4'];
						$DATA['ANALIZE']['MAX']['position4']=($VALUE['position4']>$DATA['ANALIZE']['MAX']['position4'])?$VALUE['position4']:$DATA['ANALIZE']['MAX']['position4'];
						$DATA['ANALIZE']['MIN']['position4']=($VALUE['position4']<$DATA['ANALIZE']['MIN']['position4'] || !$DATA['ANALIZE']['MIN']['position4']>0)?$VALUE['position4']:$DATA['ANALIZE']['MIN']['position4'];

						$DATA['ANALIZE']['position5']+=$VALUE['position5'];
						$DATA['ANALIZE']['MAX']['position5']=($VALUE['position5']>$DATA['ANALIZE']['MAX']['position5'])?$VALUE['position5']:$DATA['ANALIZE']['MAX']['position5'];
						$DATA['ANALIZE']['MIN']['position5']=($VALUE['position5']<$DATA['ANALIZE']['MIN']['position5'] || !$DATA['ANALIZE']['MIN']['position5']>0)?$VALUE['position5']:$DATA['ANALIZE']['MIN']['position5'];

						$DATA['ANALIZE']['position6']+=$VALUE['position6'];
						$DATA['ANALIZE']['MAX']['position6']=($VALUE['position6']>$DATA['ANALIZE']['MAX']['position6'])?$VALUE['position6']:$DATA['ANALIZE']['MAX']['position6'];
						$DATA['ANALIZE']['MIN']['position6']=($VALUE['position6']<$DATA['ANALIZE']['MIN']['position6'] || !$DATA['ANALIZE']['MIN']['position6']>0)?$VALUE['position6']:$DATA['ANALIZE']['MIN']['position6'];

						$DATA['ANALIZE']['position7']+=$VALUE['position7'];
						$DATA['ANALIZE']['MAX']['position7']=($VALUE['position7']>$DATA['ANALIZE']['MAX']['position7'])?$VALUE['position7']:$DATA['ANALIZE']['MAX']['position7'];
						$DATA['ANALIZE']['MIN']['position7']=($VALUE['position7']<$DATA['ANALIZE']['MIN']['position7'] || !$DATA['ANALIZE']['MIN']['position7']>0)?$VALUE['position7']:$DATA['ANALIZE']['MIN']['position7'];

						$DATA['ANALIZE']['position8']+=$VALUE['position8'];
						$DATA['ANALIZE']['MAX']['position8']=($VALUE['position8']>$DATA['ANALIZE']['MAX']['position8'])?$VALUE['position8']:$DATA['ANALIZE']['MAX']['position8'];
						$DATA['ANALIZE']['MIN']['position8']=($VALUE['position8']<$DATA['ANALIZE']['MIN']['position8'] || !$DATA['ANALIZE']['MIN']['position8']>0)?$VALUE['position8']:$DATA['ANALIZE']['MIN']['position8'];

						$DATA['ANALIZE']['price1']+=$VALUE['price1'];
						$DATA['ANALIZE']['MAX']['price1']=($VALUE['price1']>$DATA['ANALIZE']['MAX']['price1'])?$VALUE['price1']:$DATA['ANALIZE']['MAX']['price1'];
						$DATA['ANALIZE']['MIN']['price1']=($VALUE['price1']<$DATA['ANALIZE']['MIN']['price1'] || !$DATA['ANALIZE']['MIN']['price1']>0)?$VALUE['price1']:$DATA['ANALIZE']['MIN']['price1'];

						$DATA['ANALIZE']['price2']+=$VALUE['price2'];
						$DATA['ANALIZE']['MAX']['price2']=($VALUE['price2']>$DATA['ANALIZE']['MAX']['price2'])?$VALUE['price2']:$DATA['ANALIZE']['MAX']['price2'];
						$DATA['ANALIZE']['MIN']['price2']=($VALUE['price2']<$DATA['ANALIZE']['MIN']['price2'] || !$DATA['ANALIZE']['MIN']['price2']>0)?$VALUE['price2']:$DATA['ANALIZE']['MIN']['price2'];

						$DATA['ANALIZE']['price3']+=$VALUE['price3'];
						$DATA['ANALIZE']['MAX']['price3']=($VALUE['price3']>$DATA['ANALIZE']['MAX']['price3'])?$VALUE['price3']:$DATA['ANALIZE']['MAX']['price3'];
						$DATA['ANALIZE']['MIN']['price3']=($VALUE['price3']<$DATA['ANALIZE']['MIN']['price3'] || !$DATA['ANALIZE']['MIN']['price3']>0)?$VALUE['price3']:$DATA['ANALIZE']['MIN']['price3'];

						$DATA['ANALIZE']['price4']+=$VALUE['price4'];
						$DATA['ANALIZE']['MAX']['price4']=($VALUE['price4']>$DATA['ANALIZE']['MAX']['price4'])?$VALUE['price4']:$DATA['ANALIZE']['MAX']['price4'];
						$DATA['ANALIZE']['MIN']['price4']=($VALUE['price4']<$DATA['ANALIZE']['MIN']['price4'] && $DATA['ANALIZE']['MIN']['price4']>0)?$VALUE['price4']:$DATA['ANALIZE']['MIN']['price4'];

						$DATA['ANALIZE']['price5']+=$VALUE['price5'];
						$DATA['ANALIZE']['MAX']['price5']=($VALUE['price5']>$DATA['ANALIZE']['MAX']['price5'])?$VALUE['price5']:$DATA['ANALIZE']['MAX']['price5'];
						$DATA['ANALIZE']['MIN']['price5']=($VALUE['price5']<$DATA['ANALIZE']['MIN']['price5'] || !$DATA['ANALIZE']['MIN']['price5']>0)?$VALUE['price5']:$DATA['ANALIZE']['MIN']['price5'];

						$DATA['ANALIZE']['price6']+=$VALUE['price6'];
						$DATA['ANALIZE']['MAX']['price6']=($VALUE['price6']>$DATA['ANALIZE']['MAX']['price6'])?$VALUE['price6']:$DATA['ANALIZE']['MAX']['price6'];
						$DATA['ANALIZE']['MIN']['price6']=($VALUE['price6']<$DATA['ANALIZE']['MIN']['price6'] || !$DATA['ANALIZE']['MIN']['price6']>0)?$VALUE['price6']:$DATA['ANALIZE']['MIN']['price6'];

						$DATA['ANALIZE']['price7']+=$VALUE['price7'];
						$DATA['ANALIZE']['MAX']['price7']=($VALUE['price7']>$DATA['ANALIZE']['MAX']['price7'])?$VALUE['price7']:$DATA['ANALIZE']['MAX']['price7'];
						$DATA['ANALIZE']['MIN']['price7']=($VALUE['price7']<$DATA['ANALIZE']['MIN']['price7'] || !$DATA['ANALIZE']['MIN']['price7']>0)?$VALUE['price7']:$DATA['ANALIZE']['MIN']['price7'];

						$DATA['ANALIZE']['price8']+=$VALUE['price8'];
						$DATA['ANALIZE']['MAX']['price8']=($VALUE['price8']>$DATA['ANALIZE']['MAX']['price8'])?$VALUE['price8']:$DATA['ANALIZE']['MAX']['price8'];
						$DATA['ANALIZE']['MIN']['price8']=($VALUE['price8']<$DATA['ANALIZE']['MIN']['price8'] || !$DATA['ANALIZE']['MIN']['price8']>0)?$VALUE['price8']:$DATA['ANALIZE']['MIN']['price8'];

						$DATA['ANALIZE']['price']+=$VALUE['price'];
						$DATA['ANALIZE']['MAX']['price']=($VALUE['price']>$DATA['ANALIZE']['MAX']['price'])?$VALUE['price']:$DATA['ANALIZE']['MAX']['price'];
						$DATA['ANALIZE']['MIN']['price']=($VALUE['price']<$DATA['ANALIZE']['MIN']['price'] || !$DATA['ANALIZE']['MIN']['price']>0)?$VALUE['price']:$DATA['ANALIZE']['MIN']['price'];

						$DATA['ANALIZE']['real_price']+=$VALUE['real_price'];
						$DATA['ANALIZE']['MAX']['real_price']=($VALUE['real_price']>$DATA['ANALIZE']['MAX']['real_price'])?$VALUE['real_price']:$DATA['ANALIZE']['MAX']['real_price'];
						$DATA['ANALIZE']['MIN']['real_price']=($VALUE['real_price']<$DATA['ANALIZE']['MIN']['real_price'] || !$DATA['ANALIZE']['MIN']['real_price']>0)?$VALUE['real_price']:$DATA['ANALIZE']['MIN']['real_price'];

					}
					
					$DATA['ANALIZE']['premium']=round($DATA['ANALIZE']['premium']/$DATA['ANALIZE']['count'],2)*100;
					$DATA['ANALIZE']['garantee']=round($DATA['ANALIZE']['garantee']/$DATA['ANALIZE']['count'],2)*100;
					$DATA['ANALIZE']['none']=100 - $DATA['ANALIZE']['premium'] - $DATA['ANALIZE']['garantee'];
					$DATA['ANALIZE']['visible']=$DATA['ANALIZE']['premium'] + $DATA['ANALIZE']['garantee'];
					
					$DATA['ANALIZE']['position']=!empty($DATA['ANALIZE']['position_count'])?round($DATA['ANALIZE']['position']/$DATA['ANALIZE']['position_count'], 1):0;
					
					$DATA['ANALIZE']['position1']=round($DATA['ANALIZE']['position1']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position2']=round($DATA['ANALIZE']['position2']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position3']=round($DATA['ANALIZE']['position3']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position4']=round($DATA['ANALIZE']['position4']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position5']=round($DATA['ANALIZE']['position5']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position6']=round($DATA['ANALIZE']['position6']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position7']=round($DATA['ANALIZE']['position7']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['position8']=round($DATA['ANALIZE']['position8']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price1']=round($DATA['ANALIZE']['price1']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price2']=round($DATA['ANALIZE']['price2']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price3']=round($DATA['ANALIZE']['price3']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price4']=round($DATA['ANALIZE']['price4']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price5']=round($DATA['ANALIZE']['price5']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price6']=round($DATA['ANALIZE']['price6']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price7']=round($DATA['ANALIZE']['price7']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['price8']=round($DATA['ANALIZE']['price8']/$DATA['ANALIZE']['count'],2);
					
					$DATA['ANALIZE']['price']=round($DATA['ANALIZE']['price']/$DATA['ANALIZE']['count'],2);
					$DATA['ANALIZE']['real_price']=round($DATA['ANALIZE']['real_price']/$DATA['ANALIZE']['count'],2);
				}
				//\Аналитика//
			}
		}
		$this->Framework->template->set('DATA', $DATA);
		if (!empty($PARAM['export'])) {
			$this->csv(__METHOD__);
			echo pack('CCC', 0xef, 0xbb, 0xbf);
			foreach ($DATA['STATISTIC']['ELEMENT'] as &$VALUE) {
				if (empty($key)) {
					$key=array_keys($VALUE);
					echo implode(';', $key)."\r\n";
				}
				array_walk($VALUE, function(&$value) { 
					if (is_numeric($value)) 
						$value=str_replace('.', ',', (string)$value); 
				}); 
				echo implode(';', $VALUE)."\r\n";
			}
		} else
			echo $this->Framework->template->get('index.html');
	}
	
	public function count($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
						
			$DATA['COUNT']=array();
			
			//Статистика//
			$sql="SELECT 
			(SELECT COUNT(`t1`.`id`) FROM `".$this->Framework->user->model->model->TABLE[0]."` `t1` LEFT JOIN `".$this->Framework->user->model->model->TABLE[0]."` `t2` ON (`t2`.`parent`=`t1`.`id`) WHERE `t2`.`id` is NULL  AND `t1`.`group` IN (2,4)) as `client`,
			(SELECT COUNT(`id`) FROM `".$this->Framework->direct->model->config->TABLE['company']."`) as `company`,
			(SELECT COUNT(`id`) FROM `".$this->Framework->direct->model->config->TABLE['group']."`) as `group`,
			(SELECT COUNT(`id`) FROM `".$this->Framework->direct->model->config->TABLE['banner']."`) as `banner`,
			(SELECT COUNT(`id`) FROM `".$this->Framework->direct->model->config->TABLE['phrase']."`) as `phrase`,
			(SELECT SUM(DISTINCT `price`) FROM `".$this->Framework->direct->model->config->TABLE['company']."`) as `sum`

			";
			
			$this->Framework->db->set($sql);				
			
			while ($ROW=$this->Framework->db->get()) {
				$DATA['COUNT']=$ROW;
				$DATA['COUNT']['company_client']=$ROW['client']>0?$ROW['company']/$ROW['client']:0;
				$DATA['COUNT']['group_company']=$ROW['company']>0?$ROW['group']/$ROW['company']:0;
				$DATA['COUNT']['banner_group']=$ROW['group']>0?$ROW['banner']/$ROW['group']:0;
				$DATA['COUNT']['phrase_group']=$ROW['group']>0?$ROW['phrase']/$ROW['group']:0;
			}
			//\Статистика//
			
		
			$this->Framework->template->set('DATA', $DATA);
		}
		echo $this->Framework->template->get('index.html');
	}
	
	private function csv($name='export') {
		header("Content-Description: File Transfer\r\n");
		header("Pragma: public\r\n");
		header("Expires: 0\r\n");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0\r\n");
		header("Cache-Control: public\r\n");
		header("Content-Type: text/plain; charset=UTF-8\r\n");
		header("Content-Disposition: attachment; filename=\"".$name.".csv\"\r\n");
	}

}//\class
?>