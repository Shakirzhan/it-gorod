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

final class Api extends \FrameWork\Common {
	
	private $time=0;
	private $sleep=60;
	
	public function __construct () {
		parent::__construct();
	}
	
	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return null;		
	}
	
	//Посылка данных//
	public function get($method, $PARAM) {
		$RETURN = array();
		$this->Framework->direct->model->config->error=0;
		$time=microtime(true);
		if (isset($method) && isset($PARAM)) {
			// Проверка oAuth токена//
			if (empty($this->Framework->direct->model->config->CONFIG['token']) || ! $this->Framework->direct->model->config->CONFIG['token']) {
				$token=null;
				$this->stop();
				$this->Framework->library->error->set('Отсутствует oAuth токен! Логин: '.$this->Framework->direct->model->config->CONFIG['login'].'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			} else {
				$token=$this->Framework->direct->model->config->CONFIG['token'];
			// \Проверка oAuth токена//
				
				// формирование запроса
				$request = array();
				$request['token'] = $token;
				$request['locale'] = 'ru';
				$request['method'] = $method;
				if (!empty($PARAM))
					$request['param'] = $PARAM;
				
				// преобразование в JSON-формат
				$request = json_encode($request);
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
					$allow_url_fopen=ini_get('allow_url_fopen');
					if (!empty($allow_url_fopen)) {
						$opts = array(
							'http'=>array(
								//'timeout' => 3600,
								'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
								"User-Agent:Direct-automate.ru/".$this->Framework->direct->model->config->CONFIG['version']."\r\n",
								'method'=>"POST",
								'content'=>$request,
							)
						); 
						# создание контекста потока
						$context = stream_context_create($opts); 
						# отправляем запрос и получаем ответ от сервера
						try {
						$result = file_get_contents($this->Framework->direct->model->config->CONFIG['api'], 0, $context);
						} catch (Exception $e) {
							$error=$e->getMessage();
							$error_number=1;
						}
						if (empty($result)) {
							if (empty($error))
								$error='Пустой ответ';
							$error_number=1;
						}
						
						if (!empty($http_response_header)) 
							$header=implode("\r\n", $http_response_header);
					} elseif (function_exists('curl_init')) {
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $this->Framework->direct->model->config->CONFIG['api']);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						//curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
						if ($method=='GetBannerPhrasesFilter')
							curl_setopt($ch, CURLOPT_HEADER, 1);
						$result = curl_exec($ch);
						if ($method=='GetBannerPhrasesFilter') {
							$position=strpos($result, "{");
							if ($position!==false) {
								$header=substr($result, 0, $position-1);
								$result=substr($result, $position);	
							}
						}
						$error_number=curl_errno($ch);
						$error=curl_error($ch);
						curl_close($ch);
					} else 
						$this->Framework->library->error->set("Не установлена библиотека PHP cURL для доступа к: ".$this->Framework->direct->model->config->CONFIG['api'].'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
					
					if (empty($error_number) || $error=='NSS: client certificate not found (nickname not specified)')
						break;
					
				}

				if ($method=='GetBannerPhrasesFilter') {
					preg_match("/GetPhrasesLimit: ([0-9]+)\/([0-9]+)\/([0-9]+)\/([0-9]+) secs/", $header, $MATCH);// GetPhrasesLimit: 124/110398/150450/2626 secs 
					$this->Framework->direct->model->config->bid_count=!empty($MATCH[1])?$MATCH[1]:0;
					$this->Framework->direct->model->config->bid_limit=!empty($MATCH[2])?$MATCH[2]:0;
					$this->Framework->direct->model->config->bid_daily=!empty($MATCH[3])?$MATCH[3]:0;
					$this->Framework->direct->model->config->bid_sleep=!empty($MATCH[4])?$MATCH[4]:0;
					unset($MATCH);
				}
				if (! $result) {
					//if (!empty($error))
						$this->Framework->library->error->set("Не удается открыть адрес (".$i." попытк".($i>1?'и подряд':'а')." длительностью ".round(microtime(true)-$time)." сек.): curl ".$this->Framework->direct->model->config->CONFIG['api'].". (".$error_number.': '.$error . $request.')'.' Время:'.date('Y-m-d H:i:s').' Логин: '.$this->Framework->direct->model->config->CONFIG['login'], __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				} else 
					$RETURN = json_decode($result);
			}
		}
		
		if ($method=='GetBannerPhrasesFilter' && isset($RETURN->error_code) && $RETURN->error_code==111) {
			if (!empty($RETURN->error_detail)) {
				preg_match("/ CampaignID = ([0-9]+) /", $RETURN->error_detail, $MATCH);//"Кампания с CampaignID = 12345678 заархивирована"
				if (!empty($MATCH[1]))
					$this->Framework->direct->company->delete((int)$MATCH[1]);
				unset($MATCH);
			}
			$this->Framework->library->error->set("Запрос АПИ4 «{$method}»: " . $RETURN->error_str . (!empty($RETURN->error_detail)?'. '.$RETURN->error_detail.'.':'') . '! ' . (!empty($this->Framework->direct->model->config->CONFIG['login'])?"\r\n 1) ".$this->Framework->direct->model->config->CONFIG['login']:'') . "\r\n 2) ".$request . "\r\n 3) ". $result. "\r\n 4) ".date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		} elseif ($method=='GetBanners' && isset($RETURN->error_code) && $RETURN->error_code==3500) {
			$this->Framework->library->error->set("Запрос АПИ4 «{$method}»: " . $RETURN->error_str . (!empty($RETURN->error_detail)?'. '.$RETURN->error_detail.'.':'') . '! ' . (!empty($this->Framework->direct->model->config->CONFIG['login'])?"\r\n 1) ".$this->Framework->direct->model->config->CONFIG['login']:'') . "\r\n 2) ".$request . "\r\n 3) ". $result. "\r\n 4) ".date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
			$banners=preg_replace('/[^0-9,]/', '', $RETURN->error_detail);
			$RETURN=array('data'=>array());
			if (!empty($banners)) {
				$BANNERS=explode(',', $banners);
				foreach ($BANNERS as &$banner)
					$RETURN['data'][]=array('BannerID'=>$banner, 'StatusArchive' => 'No', 'Type'=>'UNKNOWN');
				$RETURN=(object)$RETURN;
				unset($BANNERS, $banners, $banner);
			}
			
		} elseif (isset($RETURN->error_str) && $RETURN->error_str!='Нет статистики для данной кампании') {
			if ($RETURN->error_code==53 || ($RETURN->error_code==54 && (!empty($RETURN->error_detail) && $RETURN->error_detail!='CampaignID не найден' && $RETURN->error_detail!='Доступ к API закрыт на время перевода кампаний в валюту' && $RETURN->error_detail!='Доступ к API закрыт до конвертации в валюту')) || $RETURN->error_code==58 || $RETURN->error_code==510 || $RETURN->error_code==251 || $RETURN->error_code==513 || $RETURN->error_code==501)
				$this->stop();
			$this->Framework->direct->model->config->error=$RETURN->error_code;
			$this->Framework->library->error->set("Запрос АПИ4 «{$method}»: " . $RETURN->error_str . (!empty($RETURN->error_detail)?'. '.$RETURN->error_detail.'.':'') . '! ' . (!empty($this->Framework->direct->model->config->CONFIG['login'])?"\r\n 1) ".$this->Framework->direct->model->config->CONFIG['login']:'') . "\r\n 2) ".$request . "\r\n 3) ". $result. "\r\n 4) ".date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		//$this->Framework->library->error->set("Запрос {$method}: " . (!empty($RETURN->error_str)?'. '.$RETURN->error_str.'.':'') . (!empty($RETURN->error_detail)?'. '.$RETURN->error_detail.'.':'') . '! ' . (!empty($this->Framework->direct->model->config->CONFIG['login'])?"\r\n 1) ".$this->Framework->direct->model->config->CONFIG['login']:'') . "\r\n 2) ".$request . "\r\n 3) ". $result. "\r\n 4) ".date('H:i:s d.m.Y'), __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		$time=round(microtime(true)-$time, 4);
		if ((int)$this->Framework->CONFIG['DEBUG']['all']>0)
			$this->Framework->library->error->set('Диагностика API4.'.$method.': '.$time.' сек.', '', '', '', '', '', true);
		$this->time+=$time;
		return $RETURN; // Возвращаем ответ
	}
	//\Посылка данных//	
	
	private function stop() {
		if (!empty($this->Framework->direct->model->config->CONFIG['login']) && $this->Framework->direct->model->config->CONFIG['login'])
			$this->Framework->user->model->model->set(array('id'=>$this->Framework->direct->model->config->CONFIG['login'], 'status'=>2));
	}
	
	public function time() {
		return round($this->time, 4);
	}
	
}//\class
?>