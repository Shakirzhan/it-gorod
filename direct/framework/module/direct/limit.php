<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct;

final class Limit extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array(), $debug=false) {
		$stop=0;
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>(int)$PARAM);
			if (empty($PARAM['login']))
				$PARAM['login']=(string)$this->Framework->api->yandex->direct->config->login;
			if (isset($PARAM['percent']))
				$PARAM['percent']=(int)$PARAM['percent'];
			if (empty($PARAM['percent']))
				$PARAM['percent']=(int)$this->Framework->direct->model->config->CONFIG['api_percent'];
			if (empty($PARAM['percent']))
				$PARAM['percent']=100;
			if (!empty($PARAM['unit']))
				$PARAM['unit']=(int)$PARAM['unit'];
			if (!empty($PARAM['total']))
				$PARAM['total']=(int)$PARAM['total'];

			if (!empty($PARAM['id']) && (!empty($PARAM['total']) || $this->Framework->api->yandex->direct->config->daily>0 || $this->Framework->api->yandex->direct->config->error==152)) {
					$SAVE=array();
					if (!empty($PARAM['total']) && ($PARAM['total']-$PARAM['unit'] < $PARAM['total'] * (int)$PARAM['percent']/100)) {
						$SAVE=array(
							'user'=>$PARAM['id'], 
							'unit_status'=>0,
						);
						$stop=0;
					} elseif (!empty($PARAM['total'])) {
						$stop=1;
					}
					
					if (!empty($PARAM['total']) && $this->Framework->api->yandex->direct->config->daily>0) {
						$SAVE['user']=$PARAM['id'];
						$SAVE['unit']=$this->Framework->api->yandex->direct->config->limit;
						$SAVE['unit_total']=$this->Framework->api->yandex->direct->config->daily;
					}
					
					if ($this->Framework->api->yandex->direct->config->error==152 || ($this->Framework->api->yandex->direct->config->daily>0 && $this->Framework->api->yandex->direct->config->daily-$this->Framework->api->yandex->direct->config->limit>$this->Framework->api->yandex->direct->config->daily * (int)$PARAM['percent']/100)) {
						$SAVE['user']=$PARAM['id'];
						$SAVE['unit']=0;
						$SAVE['unit_status']=1;
						$SAVE['unit_time']=date('Y-m-d H:i:s');
						$stop=1;
						$this->Framework->library->error->set('Превышен лимит баллов АПИ5'.(!empty($PARAM['login'])?' логин '.$PARAM['login']:'').': '.($this->Framework->api->yandex->direct->config->daily * (int)$PARAM['percent']/100).'.'.' ('.print_r($SAVE, true).')', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					}
					if ((!empty($PARAM['id']) || !empty($PARAM['login'])) && ($this->Framework->api->yandex->direct->config->error==53)) {//|| $this->Framework->api->yandex->direct->config->error==58 || $this->Framework->api->yandex->direct->config->error==510 || $this->Framework->api->yandex->direct->config->error==251 || $this->Framework->api->yandex->direct->config->error==513 || $this->Framework->api->yandex->direct->config->error==501)
						$this->Framework->user->model->model->set(array('id'=>!empty($PARAM['id'])?$PARAM['id']:$PARAM['login'], 'status'=>2));
						$stop=1;
					}
					if (!empty($PARAM['login']) && ($this->Framework->api->yandex->direct->config->error==54))
						$stop=1;
					if (!empty($SAVE)) {	
						$this->Framework->user->model->param->set($SAVE);
					}
					
					if (!empty($PARAM['login'])) {
						if ($stop)
							$this->Framework->api->yandex->direct->query->STOP[$PARAM['login']]=1;
						elseif (!empty($this->Framework->api->yandex->direct->query->STOP[$PARAM['login']]))
							unset($this->Framework->api->yandex->direct->query->STOP[$PARAM['login']]);
					}
			}
		}
		if ($debug) {
			$stop=1;
			$this->Framework->library->error->set('Превышен лимит баллов АПИ5', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
			
		return $stop;
	}
	
	public function get($PARAM=array(), $debug=false) {
		return $this->set($PARAM, $debug);
	}
	
}//\class
?>