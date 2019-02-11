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

final class User extends \FrameWork\Common {

	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
	}

	public function delete($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['id'])) {
				$this->Framework->user->model->model->delete(array('id'=>$PARAM['id']));
				$USER=$this->Framework->user->model->model->get(array('parent'=>$PARAM['id']));
				if (!empty($USER['ELEMENT']))
					foreach($USER['ELEMENT'] as $VALUE)
						$this->Framework->user->model->model->delete(array('parent'=>$VALUE['id']));
				$this->Framework->user->model->model->delete(array('parent'=>$PARAM['id']));
				$this->Framework->user->model->param->delete(array('user'=>$PARAM['id']));
				$DATA['status']=1;
			}
		}
		
		$this->Framework->library->header->set(array('Location'=>$_SERVER['HTTP_REFERER']));
		$this->Framework->library->header->get();
		
		//$this->Framework->template('json')->set('DATA', $DATA);
		//echo $this->Framework->template('json')->get();
	}
	
	public function company($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('company'=>$key))) {
							$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['company'], array(
								'id'=>$key,
								'strategy'=>intval($value),
								'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
								'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
								'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
								'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
								'budget'=>isset($PARAM['budget'][$key])?round(str_replace(',', '.', $PARAM['budget'][$key]),2):0,
							));
							if ($id)
								$DATA['status']=1;
							else
								$DATA['ERROR'][]='Не удалось сохранить id='.$key;
						} else 
							$this->Framework->library->error->set('У вас нет прав для записи компании с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						
					}
				}
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function banner($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('banner'=>$key))) {
							$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['banner'], array(
								'id'=>$key,
								'strategy'=>(int)$value,
								'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
								'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
								'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
								'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
							));
							if ($id)
								$DATA['status']=1;
							else
								$DATA['ERROR'][]='Не удалось сохранить id='.$key;
						} else 
							$this->Framework->library->error->set('У вас нет прав для записи баннера с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						
					}
				}
			}
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}
	
	public function phrase($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']>=1) {
			if (!empty($PARAM['strategy']) && is_array($PARAM['strategy'])) {
				foreach ($PARAM['strategy'] as $key=>$value) {
					if (!empty($key)) {
						if ($this->Framework->direct->model->right->get(array('phrase'=>$key))) {
							$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['phrase'], array(
								'id'=>$key,
								'strategy'=>(int)$value,
								'percent'=>isset($PARAM['percent'][$key])?round(str_replace(',', '.', $PARAM['percent'][$key]),2):0,
								'type'=>isset($PARAM['type'][$key])?(int)$PARAM['type'][$key]:0,
								'add'=>isset($PARAM['add'][$key])?round(str_replace(',', '.', $PARAM['add'][$key]),2):0,
								'maximum'=>isset($PARAM['maximum'][$key])?round(str_replace(',', '.', $PARAM['maximum'][$key]),2):0,
							));
							if ($id)
								$DATA['status']=1;
							else
								$DATA['ERROR'][]='Не удалось сохранить id='.$key;
						} else 
							$this->Framework->library->error->set('У вас нет прав для записи фразы с id: '.$key.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
						
					}
				}
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

}//\class
?>