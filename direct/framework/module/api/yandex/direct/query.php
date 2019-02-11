<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\api\yandex\direct;
//https://tech.yandex.ru/money/doc/dg/concepts/protocol-request-docpage/
final class Query extends \FrameWork\Common {
	
	public $STOP=array();
	private $time=0;
	private $unit=0;
	private $limit=0;
	private $daily=0;
	private $sleep=60;	
	private $login=null;	
	private $token=null;	
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function set($url='', $method='', $PARAM=array()) {
		$this->get($url, $method, $PARAM);
	}
	
	public function get($url='', $method='get', $PARAM=array()) {
		$DATA=array();
		$this->reset();
		
		if (!empty($this->login) && !empty($this->token) && empty($this->STOP[$this->login])) {
			$time=microtime(true);
			if (!empty($url) && !empty($method) && !empty($PARAM)) {
				$url=preg_match('/^https:\/\//i', $url)?$url:$this->Framework->api->yandex->direct->config->CONFIG['url'].preg_replace('[^a-z]', '', $url).'/';
				$REQUEST=array(
					'method'=>$method, 
					'params'=>$PARAM,
				);
				$HEADER=array(
					'Accept-Language: ru',
					'Content-Type: application/json; charset=utf-8'
				);
				if (!empty($this->token))
					$HEADER[]='Authorization: Bearer '.$this->token;
				if (!empty($this->login))
					$HEADER[]='Client-Login: '.$this->login;
				
				$request=json_encode($REQUEST);
				
				$allow_url_fopen=ini_get('allow_url_fopen');
				$error='';
				$error_number=0;
				$header='';
				$time_sleep=microtime(true);
				for ($i=0; $i<3; $i++) {
					if ($i>1) {
						$time_sleep=ceil(microtime(true)-$time_sleep);
						if ($time_sleep<$this->sleep)
							sleep($this->sleep-$time_sleep);
					}
					if (!empty($allow_url_fopen)) {
						$opts = array(
							'http'=>array(
								'method'=>"POST",
								'protocol_version' => 1.1,
								'header' => implode("\r\n", $HEADER),
								'content'=>$request,
							)
						); 
						
						# создание контекста потока
						$context = stream_context_create($opts); 
						# отправляем запрос и получаем ответ от сервера
						$result = file_get_contents($url, 0, $context);
						$header=implode("\r\n", $http_response_header);
						if (empty($result)) {
							if (empty($error))
								$error='Пустой ответ';
							$error_number=1;
						}
					} elseif (function_exists('curl_init')) {
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
						curl_setopt($ch,CURLOPT_HTTPHEADER, $HEADER);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
						curl_setopt($ch, CURLOPT_HEADER, 1);
						$result = curl_exec($ch);
						$position=strpos($result, "{");
						if ($position!==false) {
							$header=substr($result, 0, $position-1);
							$result=substr($result, $position);	
						}
						
						$error_number=curl_errno($ch);
						$error=curl_error($ch);
						curl_close($ch);
					} else
						$this->Framework->library->error->set("Не установлена библиотека PHP cURL для доступа к: ".$url.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					if (!empty($result)) {
						$Result=json_decode($result);
					if (!empty($Result->error->error_code) && ($Result->error->error_code==1000 || $Result->error->error_code==1002)) {
							//$this->Framework->library->error->set('Ошибка АПИ Яндекс.Директ: '. $i .' '.$Result->error->error_code . ' ' . $Result->error->error_string . (!empty($Result->error->error_detail)?'. '.$Result->error->error_detail.'.':'') . ' 1) Идентификатор запроса: '.$Result->error->request_id.' (Баллы: '.(!empty($MATCH[1])?$MATCH[1]:0).'/'.(!empty($MATCH[2])?$MATCH[2]:0).'/'.(!empty($MATCH[3])?$MATCH[3]:0).') 2) Сервис: "'.$url.'" 3) Метод: "'.$method.'" 4) Логин: ' . (!empty($this->login)?$this->login:'') . ' 5) Токен: '. (!empty($this->token)?$this->token:'') . ' 6) Код запроса: ' . $request . ' 7) Код ответа: ' . $result. ' 8) Время: ' . date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
							unset($Result); 
						} else 
							break;
					} elseif (empty($error_number) || $error=='NSS: client certificate not found (nickname not specified)')
						break;
				}
				
				//Получаем лимиты баллов//
				preg_match("/\r\nUnits: ([0-9]+)\/([0-9]+)\/([0-9]+)\r\n/", $header, $MATCH);
				$unit=!empty($MATCH[1])?$MATCH[1]:0;
				$limit=!empty($MATCH[2])?$MATCH[2]:0;
				$daily=!empty($MATCH[3])?$MATCH[3]:0;
				$this->Framework->api->yandex->direct->config->unit=$unit;
				$this->Framework->api->yandex->direct->config->limit=$limit;
				$this->Framework->api->yandex->direct->config->daily=$daily;
				$this->unit+=$this->Framework->api->yandex->direct->config->unit;
				$this->limit=$this->Framework->api->yandex->direct->config->limit;
				$this->daily=$this->Framework->api->yandex->direct->config->daily;
				//\Получаем лимиты баллов//

				if (!$result) {
					$this->Framework->library->error->set("Не удается открыть адрес (".$i." попытк".($i>1?'и подряд':'а')." длительностью ".round(microtime(true)-$time)." сек.): curl ".$url.". (".$error_number.': '.$error.')'.' Время: `'.date('Y-m-d H:i:s'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				} else {
					$Result=json_decode($result);
					if (!empty($Result->error)) {
						if ($Result->error->error_code==53 && !empty($Result->error->error_detail) && $Result->error->error_detail=='Client-Login должен быть логином клиента или субклиента')
							$this->Framework->api->yandex->direct->config->error=-1;
						else
							$this->Framework->api->yandex->direct->config->error=$Result->error->error_code;
						$this->Framework->library->error->set('Ошибка АПИ5 Яндекс.Директ: '. $Result->error->error_code . ' ' . $Result->error->error_string . (!empty($Result->error->error_detail)?'. '.$Result->error->error_detail.'.':'') . ' 1) Идентификатор запроса: '.$Result->error->request_id.' (Баллы: '.(!empty($MATCH[1])?$MATCH[1]:0).'/'.(!empty($MATCH[2])?$MATCH[2]:0).'/'.(!empty($MATCH[3])?$MATCH[3]:0).') 2) Сервис: "'.$url.'" 3) Метод: "'.$method.'" 4) Логин: ' . (!empty($this->login)?$this->login:'') . ' 5) Токен: '. (!empty($this->token)?$this->token:'') . ' 6) Код запроса: ' . $request . ' 7) Код ответа: ' . $result. ' 8) Время: ' . date('H:i:s d.m.Y').'. Кол-во попыток коннекта='.$i.' длительностью '.round(microtime(true)-$time).' сек.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					}
					else {
						if (!empty($Result->result)) {
							$DATA=$this->Framework->library->data->get($Result->result);
							
							$error_details='';
							$error_count=0;
							if (!empty($DATA))
								foreach ($DATA as $VALUES) {
									if (!empty($VALUES) && is_array($VALUES))
										foreach ($VALUES as $VALUE) 
											if (!empty($VALUE['Errors']))
												foreach ($VALUE['Errors'] as $ERRORS) 
													$error_count++;//$error_details.='№'.$ERRORS['Code'].' '.$ERRORS['Message'].'. '.$ERRORS['Details'].'. ';
									break;
								}
							if ($error_count)
								$this->Framework->library->error->set('Частичная ошибка АПИ5 ('.$error_count.'): 1) Идентификатор запроса: '.$Result->error->request_id.' (Баллы: '.(!empty($MATCH[1])?$MATCH[1]:0).'/'.(!empty($MATCH[2])?$MATCH[2]:0).'/'.(!empty($MATCH[3])?$MATCH[3]:0).') 2) Сервис: "'.$url.'" 3) Метод: "'.$method.'" 4) Логин: ' . (!empty($this->login)?$this->login:'') . ' 5) Токен: '. (!empty($this->token)?$this->token:'') . ' 6) Код запроса: ' . $request . ' 7) Код ответа: ' . $result. ' 8) Время: ' . date('H:i:s d.m.Y').'. Кол-во попыток коннекта='.$i.' длительностью '.round(microtime(true)-$time).' сек.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
							unset($error_count, $error_details);
						}
						else
							$this->Framework->library->error->set('Hеизвестная ошибка АПИ5 Яндекс.Директ: 1) Идентификатор запроса: 0 2) Сервис: "'.$url.'" 3) Метод: "'.$method.'" 4) Логин: ' . (!empty($this->login)?$this->login:'') . ' 5) Токен: '. (!empty($this->token)?$this->token:'') . ' 6) Код запроса: ' . $request . ' 7) Код ответа: ' . $header. ' ' . $result . ' 8) Время: '.date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					}
					//echo '2) Сервис: "'.$url.'" 3) Метод: "'.$method.'" 4) Логин: ' . (!empty($this->login)?$this->login:'') . ' 5) Токен: '. (!empty($this->token)?$this->token:'') . ' 6) Код запроса: ' . $request . ' 7) Код ответа: ' . $header. ' ' . $result . ' 8) Время: '.date('H:i:s d.m.Y')."<br>\r\n";
				}
			}
			$time=round(microtime(true)-$time, 4);
			if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
				$this->Framework->library->error->set('Диагностика API5: '.$url.' '.$method.' - '.$time.' сек., баллы: '.$unit.'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__, true);
			$this->time+=$time;
		} elseif ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
			$this->Framework->library->error->set('Диагностика API5: STOP login '.$this->login.'. '.$url.' '.$method, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__, true);
			
		return $DATA;
	}
	
	
	public function reset() {
		$this->unit=empty($this->Framework->api->yandex->direct->config->CONFIG['login'])||$this->login!=$this->Framework->api->yandex->direct->config->CONFIG['login']?0:$this->unit;
		$this->limit=0;
		$this->daily=0;
		$this->Framework->api->yandex->direct->config->error=0;
		$this->Framework->api->yandex->direct->config->unit=0;
		$this->Framework->api->yandex->direct->config->limit=0;
		$this->Framework->api->yandex->direct->config->daily=0;
		$this->login=!empty($this->Framework->api->yandex->direct->config->CONFIG['login'])?$this->Framework->api->yandex->direct->config->CONFIG['login']:'';
		$this->token=!empty($this->Framework->api->yandex->direct->config->CONFIG['token'])?$this->Framework->api->yandex->direct->config->CONFIG['token']:'';		
	}
	public function limit() {
		return array('unit'=>$this->unit, 'limit'=>$this->limit, 'daily'=>$this->daily);
	}
	
	public function time() {
		return round($this->time, 4);
	}
	
}//\class
?>