<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\google\search;
//https://developers.google.com/web-search/docs/reference#_intro_fonje
final class Query extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('q'=>$PARAM);
			if (!empty($PARAM['name']))
				$PARAM['q']=$PARAM['name'];
			elseif (!empty($PARAM['query']))
				$PARAM['q']=$PARAM['query'];
			if (empty($PARAM['start']) || !is_numeric($PARAM['start']) || $PARAM['start']<0)
				$PARAM['start']=0;
			else
				$PARAM['start']=(int)$PARAM['start'];
			if (!empty($PARAM['number']) && $PARAM['number']>0 && $PARAM['number']<8) 
				$PARAM['rsz']=$PARAM['number'];
			else
				$PARAM['rsz']=8;
			if (!empty($PARAM['language'])) 
				$PARAM['hl']=$PARAM['language'];
			$this->Framework->library->data->delete_reference($PARAM, array('q', 'start', 'rsz', 'hl'));
			$url=$this->Framework->api->google->search->config->CONFIG['api'].'?v=1.0&'.(!empty($PARAM)&&is_array($PARAM)?http_build_query($PARAM):'').'&userip='.$this->Framework->CONFIG['address'];
echo $url;
			$allow_url_fopen=ini_get('allow_url_fopen');
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); 
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
					)
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
				$DATA = json_decode($result);			
			
			if (!empty($DATA->response->error)) {				
				$this->Framework->library->error->set("Ошибка АПИ GOOGLE WEB SEARCH: ".$url.". (".print_r($DATA, true).')'.' Время: '.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				$DATA=array();
			} 
		}
		return $DATA;
	}
	
	
	public function position($PARAM=array()) {
		$PARAM['url']=$this->Framework->library->punycode->get($PARAM['url']);
		$position=0;		
		for ($PARAM['start']=0; $PARAM['start']<=16; $PARAM['start']+=8) {
			$DATA=$this->get($PARAM);

			echo '<pre>'.print_r($PARAM, true).print_r($DATA, true).'</pre>';
			if (!empty($DATA) && !empty($DATA->responseData->results)) {
				$count=0;	
				if (!empty($PARAM['url'])) {
					foreach ($DATA->responseData->results as $Value) {
						if (!empty($Value->visibleUrl) && $this->Framework->library->controller->domain($Value->visibleUrl)==$this->Framework->library->controller->domain($PARAM['url'])) {
							$position=$PARAM['start']+$count+1;			
							break;
						}
						$count++;
					}
				}
			}
		}
		return $position;
	}
	
	
}//\class
?>