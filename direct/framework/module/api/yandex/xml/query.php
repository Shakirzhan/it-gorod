<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\xml;
//https://xml.yandex.ru/test/
final class Query extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$LIMIT=$this->Framework->model->api->model->get($this->Framework->api->yandex->xml->config->TABLE['limit'], array('key'=>$this->Framework->CONFIG['address']));
			if (empty($LIMIT['ELEMENT'][0]['status']) || (!empty($LIMIT['ELEMENT'][0]['date']) && $LIMIT['ELEMENT'][0]['date']!=date('Y-m-d'))) {
				if (!is_array($PARAM))
					$PARAM=array('query'=>$PARAM);
				if (empty($PARAM['user']))
					$PARAM['user']=$this->Framework->api->yandex->xml->config->CONFIG['user'];
				if (empty($PARAM['key']))
					$PARAM['key']=$this->Framework->api->yandex->xml->config->CONFIG['key'];
				if (!empty($PARAM['region'])) {
					$PARAM['lr']=$PARAM['region'];
					unset($PARAM['region']);
				} elseif (empty($PARAM['lr']))	
					$PARAM['lr']=$this->Framework->api->yandex->xml->config->CONFIG['region'];
				
				$this->Framework->library->data->delete_reference($PARAM, array('query', 'user', 'key', 'lr'));
				$url=$this->Framework->api->yandex->xml->config->CONFIG['api'].'?'.(!empty($PARAM)&&is_array($PARAM)?http_build_query($PARAM):'').'&l10n=ru&sortby=rlv&filter=none&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D50.docs-in-group%3D1';

				$allow_url_fopen=ini_get('allow_url_fopen');
				if (function_exists('curl_init')) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); 
					if (!empty($this->Framework->CONFIG['interface']) && $this->Framework->CONFIG['interface'])
						curl_setopt( $ch, CURLOPT_INTERFACE, $this->Framework->CONFIG['interface'] );
					//curl_setopt($ch, CURLOPT_POST, 1);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
					$result = curl_exec($ch);
					$error_number=curl_errno($ch);
					$error=curl_error($ch);
					curl_close($ch);
				} elseif (!empty($allow_url_fopen)) {
					$opts = array(
						'http'=>array(
							'header' => "User-Agent:".$this->Framework->CONFIG['name']."/".$this->Framework->CONFIG['version']."\r\n",
						),
						//'socket' => array('bindto' => $ip),
					); 
					
					# создание контекста потока
					$context = stream_context_create($opts); 
					# отправляем запрос и получаем ответ от сервера
					$result = file_get_contents($url, 0, $context);
					$error='';
					$error_number=0;
				} else
					$this->Framework->library->error->set("Не установлена библиотека PHP cURL для доступа к: ".$url.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				
				
				if (! $result) {
					$this->Framework->library->error->set("Не удается открыть адрес: ".$url.". (".$error_number.': '.$error.')'.' Время: `'.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				} else 
					$DATA = $this->parse($result);			
				
				if (!empty($DATA->response->error)) {
					$this->Framework->model->api->model->set($this->Framework->api->yandex->xml->config->TABLE['limit'], array('key'=>$this->Framework->CONFIG['address'], 'status'=>1, 'date'=>DATE('Y-m-d')), 'key');
					$this->Framework->library->error->set("Исчерпан лимит Яндекс.XML: ".$url.". (".(!empty($this->Framework->CONFIG['interface'])?$this->Framework->CONFIG['interface']:'').print_r($DATA, true).')'.' Время: '.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					$DATA=array();
				} else 
					$LIMIT=$this->Framework->model->api->model->set($this->Framework->api->yandex->xml->config->TABLE['limit'], array('key'=>$this->Framework->CONFIG['address'], 'status'=>0, 'value'=>(!empty($LIMIT['ELEMENT'][0]['value'])?$LIMIT['ELEMENT'][0]['value']+1:1), 'date'=>DATE('Y-m-d')), 'key');
			}
		}
		return $DATA;
	}
	
	public function limit () {
		$LIMIT=$this->Framework->model->api->model->get($this->Framework->api->yandex->xml->config->TABLE['limit'], array('key'=>$this->Framework->CONFIG['address']));
		if (empty($LIMIT['ELEMENT'][0]['status']) || (!empty($LIMIT['ELEMENT'][0]['date']) && $LIMIT['ELEMENT'][0]['date']!=date('Y-m-d'))) 
			return false;
		return true;
	}
	
	public function position($PARAM=array()) {
		$PARAM['url']=$this->Framework->library->punycode->get($PARAM['url']);
		$DATA=$this->get($PARAM);
		$position=0;
		//echo '<pre>'.print_r($PARAM, true).print_r($DATA, true).'</pre>';
		if (!empty($DATA) && !empty($DATA->response->results->grouping->group)) {
			$count=0;	
			if (!empty($PARAM['url'])) {
				foreach ($DATA->response->results->grouping->group as $Value) {
					if (!empty($Value->doc->url) && $this->Framework->library->controller->domain($Value->doc->url)==$this->Framework->library->controller->domain($PARAM['url'])) {
						$position=$count+1;			
						break;
					}
					$count++;
				}
			}
		}
		return $position;
	}
	
	public function parse($string='') {
		$DATA=array();
		if (!empty($string)) {
			$DATA=$this->Framework->library->xml->get($string);
		}

		return $DATA;
	}
	
}//\class
?>