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

final class Strategy extends \FrameWork\Common {
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
	}
		
	public function get($PARAM=array()) {
		$DATA=array();
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			$DATA['CONFIG']=$this->Framework->direct->model->config->CONFIG;
			$DATA['STRATEGY']['ELEMENT']=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], $PARAM, array('id'));
		}
		$this->Framework->template->set('DATA', $DATA);
		echo $this->Framework->template->get('index.html');
	}
	
	public function set($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['value'])) {
				ob_start();
				$return=eval($this->Framework->direct->model->formula->set($PARAM['value']));
				$error=ob_get_contents();
				if (empty($error) && $return===false)
					$error='Ошибка синтаксиса';
				ob_end_clean();
				if ($error)
					$DATA['ERROR']=array(str_replace('/',' / ', str_replace('\\',' \\ ',$error)));
				$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['strategy'], $PARAM);
				$DATA['id']=$id;
				
			}
			if (!empty($id) && empty($error))
				$DATA['status']=1;
		}
		$this->Framework->template('json')->set('DATA', $DATA);
		echo $this->Framework->template('json')->get();
	}	
	
	public function delete($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['id']) && $PARAM['id']>0) {
				$this->Framework->library->model->delete($this->Framework->direct->model->config->TABLE['strategy'], $this->Framework->library->data->delete($PARAM, array('id')));
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['company']."` SET `strategy`='0' WHERE `strategy`='".(int)$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['group']."` SET `strategy`='0' WHERE `strategy`='".(int)$PARAM['id']."'");
				$this->Framework->db->set("UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."` SET `strategy`='0' WHERE `strategy`='".(int)$PARAM['id']."'");
				$DATA['status']=1;
			}
		}
		
		$this->Framework->library->header->set(array('Location'=>$_SERVER['HTTP_REFERER']));
		$this->Framework->library->header->get();
		
		//$this->Framework->template('json')->set('DATA', $DATA);
		//echo $this->Framework->template('json')->get();
	}
	
	public function copy($PARAM=array()) {
		if (empty($PARAM))
			$PARAM=$_REQUEST;
		$DATA=array('status'=>0);
		if ($this->Framework->user->controller->controller->USER && $this->Framework->user->controller->controller->USER['group']==1) {
			if (!empty($PARAM['id']) && $PARAM['id']>0) {
				$ROW=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['strategy'], $this->Framework->library->data->delete($PARAM, array('id')));

				if (!empty($ROW[0]['id']))
					$id=$this->Framework->library->model->set($this->Framework->direct->model->config->TABLE['strategy'], array('name'=>$ROW[0]['name'].' копия', 'value'=>$ROW[0]['value'], 'status'=>1));
				$DATA['status']=1;
			}
		}
		
		if (empty($id))
			$this->Framework->library->header->set(array('Location'=>$_SERVER['HTTP_REFERER']));
		else	
			$this->Framework->library->header->set(array('Location'=>$this->Framework->CONFIG['http_dir'].'/direct/strategy/get/param/id/'.$id.'/'));
		$this->Framework->library->header->get();
		
		//$this->Framework->template('json')->set('DATA', $DATA);
		//echo $this->Framework->template('json')->get();
	}

}//\class
?>