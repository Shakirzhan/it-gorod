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

final class Currency extends \FrameWork\Common {
	
	private $CURRENCY=array();
	
	public function __construct () {
		parent::__construct();
	}
	
	public function get() {
		if (empty ($this->CURRENCY)) {
			$this->CURRENCY=array();
			$sql="SELECT *  
					FROM `".$this->Framework->direct->model->config->TABLE['currency']."`
			";
			$result=$this->Framework->db->set($sql);
			while ($ROW=$this->Framework->db->get($result)) {
				$this->CURRENCY['ELEMENT'][]=$ROW;
				$this->CURRENCY['ID'][$ROW['id']]=$ROW;
				$this->CURRENCY['KEY'][$ROW['key']]=$ROW;
			}
			return $this->CURRENCY;
		} 
		return $this->CURRENCY;
	}
	
	public function convert($PARAM=array()) {
		$value=0;
		if (!empty($PARAM) && !empty($PARAM['value'])) {
			$value=$PARAM['value'];
			if (!empty($PARAM['currency'])) {
				$CURRENCY=$this->get();
				if (!is_numeric($PARAM['currency']) && !empty($CURRENCY['KEY'][$PARAM['currency']])) 
					$PARAM['currency']=$CURRENCY['KEY'][$PARAM['currency']]['id'];
				
				if (is_numeric($PARAM['currency']) && !empty($CURRENCY['ID'][$PARAM['currency']])) {
					$value=round($PARAM['value']*$CURRENCY['ID'][$PARAM['currency']]['value'] * (1 - round($CURRENCY['ID'][$PARAM['currency']]['tax'])/100) ,2);
				}
			}
			
		}
		return $value;
	}
	
	public function nds($PARAM=array()) {
		$value=0;
		if (!empty($PARAM) && !empty($PARAM['value'])) {
			$value=$PARAM['value'];
			if (!empty($PARAM['currency'])) {
				$CURRENCY=$this->get();
				if (!is_numeric($PARAM['currency']) && !empty($CURRENCY['KEY'][$PARAM['currency']])) 
					$PARAM['currency']=$CURRENCY['KEY'][$PARAM['currency']]['id'];
				
				if (is_numeric($PARAM['currency']) && !empty($CURRENCY['ID'][$PARAM['currency']])) {
					$value=round($PARAM['value'] / (1 + round($CURRENCY['ID'][$PARAM['currency']]['tax'])/100) ,2);
				}
			}
			
		}
		return $value;
	}
	
}//\class
?>