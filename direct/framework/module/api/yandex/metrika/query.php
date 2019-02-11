<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\metrika;
//https://tech.yandex.ru/metrika/doc/ref/concepts/metrika-api-intro-docpage/
final class Query extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
		$DATA=array();
		//if (!empty($PARAM)) {
			//$url=$this->Framework->api->yandex->xml->config->CONFIG['api'].'?'.(!empty($PARAM)&&is_array($PARAM)?http_build_query($PARAM):'').'&l10n=ru&sortby=rlv&filter=none&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D50.docs-in-group%3D1';
			//$url='https://'.$this->Framework->api->yandex->metrika->config->CONFIG['api'].'/stat/v1/data.json?id=25484177&metrics=ym:s:visits&dimensions=ym:s:searchPhrase,ym:s:goal&oauth_token=182ad8a77ff5497aa3dd727072ff2fe5';
			$url='https://beta.api-metrika.yandex.ru/stat/v1/data?ids=25484177&metrics=ym:s:goal6348465reaches&dimensions=ym:s:directPhraseOrCond&oauth_token=182ad8a77ff5497aa3dd727072ff2fe5&pretty=true';
			$allow_url_fopen=ini_get('allow_url_fopen');
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
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
			
			echo $result;
			if (! $result) {
				$this->Framework->library->error->set("Не удается открыть адрес: ".$url.". (".$error_number.': '.$error.')'.' Время: `'.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			} else 
				$DATA = json_decode($result);			

			if (!empty($DATA->response->error)) {
				$this->Framework->library->error->set("Исчерпан лимит Яндекс.XML: ".$url.". (".print_r($DATA, true).')'.' Время: '.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				$DATA=array();
			} 
		//}
		
		return $DATA;
	}
	
	
}//\class
?>