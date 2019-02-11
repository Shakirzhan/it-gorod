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

final class Path extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
	
	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		
		if (!empty($PARAM['phrase'])) {
				$PHRASE=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['phrase'], array('id'=>$PARAM['phrase']));
			$DATA['PHRASE']=$PHRASE[0];
		}
		
		if (!empty($DATA['PHRASE']['group']))
			$PARAM['group']=$DATA['PHRASE']['group'];
		
		if (!empty($PARAM['group'])) {
				$GROUP=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['group'], array('id'=>$PARAM['group']));
			$DATA['GROUP']=$GROUP[0];
		}
		
		if (!empty($DATA['GROUP']['company']))
			$PARAM['company']=$DATA['GROUP']['company'];
		
		if (!empty($PARAM['company'])) {
				$COMPANY=$this->Framework->library->model->get($this->Framework->direct->model->config->TABLE['company'], array('id'=>$PARAM['company']));
			$DATA['COMPANY']=$COMPANY[0];
		}
		
		if (!empty($DATA['COMPANY']['user']))
			$PARAM['user']=$DATA['COMPANY']['user'];
		$DATA['USERS']=array();
		$DATA['ACCOUNT']=array();
		if (!empty($PARAM['user'])) {
			$DATA['USER']=$this->Framework->user->model->model->get(array('id'=>$PARAM['user']));
			$DATA['USER']=!empty($DATA['USER']['ELEMENT'][0])?$DATA['USER']['ELEMENT'][0]:array();
			$DATA['ACCOUNT'][]=$DATA['USER'];
			if (!empty($DATA['USER']['id']))
				$DATA['USERS'][]=$DATA['USER']['id'];
			if (!empty($DATA['USER']['parent'])) {
				$DATA['MANAGER']=$this->Framework->user->model->model->get(array('id'=>$DATA['USER']['parent']));
				$DATA['MANAGER']=!empty($DATA['MANAGER']['ELEMENT'][0])?$DATA['MANAGER']['ELEMENT'][0]:array();
				if (!empty($DATA['MANAGER']['id'])) {
					$DATA['USERS'][]=$DATA['MANAGER']['id'];
					if (!empty($DATA['MANAGER']['parent'])) {
						$DATA['AGENCY']=$this->Framework->user->model->model->get(array('id'=>$DATA['MANAGER']['parent']));
						$DATA['AGENCY']=!empty($DATA['AGENCY']['ELEMENT'][0])?$DATA['AGENCY']['ELEMENT'][0]:array();
						if (!empty($DATA['AGENCY']['id'])) 
							$DATA['USERS'][]=$DATA['AGENCY']['id'];
					}
					
				}
			}
		
			if (!empty($DATA['USER']['parent'])) {
				$USER=$this->Framework->user->model->model->get(array('id'=>$DATA['USER']['parent']));
				if (!empty($USER['ELEMENT'][0])) {
					$DATA['ACCOUNT'][]=$USER['ELEMENT'][0];
					if (!empty($USER['ELEMENT'][0]['parent'])) {
						$USER=$this->Framework->user->model->model->get(array('id'=>$USER['ELEMENT'][0]['parent']));
						if (!empty($USER['ELEMENT'][0])) {
							$DATA['ACCOUNT'][]=$USER['ELEMENT'][0];
						}
					}
				}
			}
			$DATA['ACCOUNT']=array_reverse($DATA['ACCOUNT']);	
		}
		
		return $DATA;
	}
	
	
}//\class
?>