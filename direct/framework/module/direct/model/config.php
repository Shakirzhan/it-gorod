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

final class Config extends \FrameWork\Common {
	private $TABLE=array(
				'config'=>'direct_config',
				'company'=>'direct_company',
				'group'=>'direct_group',
				'banner'=>'direct_banner',
				'phrase'=>'direct_phrase',				
				'currency'=>'direct_currency',
				'statistic'=>'direct_statistic',
				'statistic_price'=>'direct_statistic_price',
				'statistic_price_temp'=>'direct_statistic_price_temp',
				'strategy'=>'direct_strategy',
				'tag'=>'direct_tag',
				'tag_banner'=>'direct_tag_banner',
				'retargeting'=>'direct_retargeting',
				'sinchronize_advert'=>'direct_sinchronize_advert',
				'sinchronize_group'=>'direct_sinchronize_group',
				'sinchronize_keyword'=>'direct_sinchronize_keyword',
				'auction_keyword'=>'direct_sinchronize_keyword',
			);
	private $CONFIG=array(
				'oauth'=>'https://oauth.yandex.ru/token',
				'authorize'=>'https://oauth.yandex.ru/authorize',
				'id'=>'f5ff8f217d0d44b59d6e6e1d335e13f5',//oauth id приложения
				'api'=>'https://api.direct.yandex.ru/live/v4/json/',
				'updateprices_max'=>1000,
				'updateprices_call_max'=>3000,
				'threads'=>0,
				'max_execution_time'=>10800,
				'version'=>'1.4.12',
				'release'=>'2017-08-06',
				'number'=>100,
				'expire'=>3600,				
				'microsecond'=>0,				
				'login'=>'',
				'password'=>'',
				'client_id'=>'',
				'client_secret'=>'',
				'token'=>'',
				'time_yandex'=>0,
				'error'=>0,
				'error_day'=>2,
				'error_number'=>10000,
			);
	
	public function __construct () {
		parent::__construct();
		$this->Framework->library->model()->table($this->TABLE);
		$CONFIG=$this->get();
		foreach ($CONFIG as $VALUE)
			if (!empty($VALUE['key']))
				$this->CONFIG[$VALUE['key']]=$VALUE['value'];
	}
		
	public function __get($name) {
		if (strtoupper($name)=='CONFIG')
			return $this->CONFIG;
		if (strtoupper($name)=='TABLE')
			return $this->TABLE;
		elseif (isset($this->CONFIG[$name]))
			return $this->CONFIG[$name];
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	public function __set($name, $value=null) {
		if (isset($this->CONFIG[$name]))
			$this->CONFIG[$name]=$value;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
	}	
	
	public function set($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM) && is_array($PARAM)) {
			foreach ($PARAM as $key=>$value) {
				if (!empty($key)) {
					if (!is_numeric($key)) {
						$ID=$this->Framework->library->model->get($this->TABLE[0], array('key'=>$key));
						if (!empty($ID[0]['id']))
							$key=$ID[0]['id'];
						else
							$key=0;
					}
					if (!empty($key)) 
						$DATA[]=$this->Framework->library->model->set($this->TABLE[0], array('id'=>$key, 'value'=>(string)$value));
				}
			}
		}
		return $DATA;
	}
	
	public function get() {
		$DATA=array();
		$sql="SELECT *  
				FROM `".$this->TABLE[0]."`
			WHERE
			`status`>0
		";
		$result=$this->Framework->db->set($sql);
		while ($ROW=$this->Framework->db->get($result)) {
			$DATA[]=$ROW;
		}
		return $DATA;
	}
	
}//\class
?>