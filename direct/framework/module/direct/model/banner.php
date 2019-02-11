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

final class Banner extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	//https://tech.yandex.ru/direct/doc/dg-v4/live/GetBanners-docpage/
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('COMPANY'=>$PARAM);
		if (empty($PARAM['COMPANY']))
			$PARAM['COMPANY'] = array();
		elseif (!is_array($PARAM['COMPANY']))
			$PARAM['COMPANY']=array($PARAM['COMPANY']);
		if (empty($PARAM['BANNER']))
			$PARAM['BANNER'] = array();
		elseif (!is_array($PARAM['BANNER']))
			$PARAM['BANNER']=array($PARAM['BANNER']);
		
		$FILTER=array();
		if (!empty($PARAM['FILTER']['show']))			
			$FILTER['StatusShow'] = array('Yes');
		if (!empty($PARAM['FILTER']['archive']))			
			$FILTER['StatusArchive'] = array('Yes');
		elseif (isset($PARAM['FILTER']['archive']))			
			$FILTER['StatusArchive'] = array('No');
			
		$REQUEST = array ();
		if (!empty($PARAM['COMPANY']))
			$REQUEST['CampaignIDS'] = $PARAM['COMPANY'];
		if (!empty($PARAM['BANNER']))
			$REQUEST['BannerIDS'] = $PARAM['BANNER'];
		if (!empty($FILTER))
			$REQUEST['Filter'] = $FILTER;
		if (!empty($PARAM['currency']))
			$REQUEST['Currency'] = $PARAM['currency'];
		if (!empty($PARAM['phrase']))
			$REQUEST['GetPhrases'] = 'Yes';//'WithPrices';
		else
			$REQUEST['GetPhrases'] = 'No';
		if (!empty($PARAM['limit']))
			$REQUEST['Limit'] = (int)$PARAM['limit'];
		if (!empty($PARAM['offset']))
			$REQUEST['Offset'] = (int)$PARAM['offset'];
		
				
		$Result = $this->Framework->direct->model->api->get ( 'GetBanners', $REQUEST );
		if (!empty($Result->data[0]))
			return $this->Framework->library->lib->objectToArray($Result->data);
		elseif (!empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить список объявлений. '.$Result->error_str, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return array();
	}
	
	public function start($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			if (is_array($PARAM['id']))
				$REQUEST=array('BannerIDS'=>$PARAM['id']);
			else
				$REQUEST=array('BannerIDS'=>array((string)$PARAM['id']));
			$Result = $this->Framework->direct->model->api->get ( 'ResumeBanners', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			elseif (!empty($Result))
				$this->Framework->library->error->set('Не удалось запустить баннер №'.print_r($PARAM['id'], true).' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	public function stop($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			if (is_array($PARAM['id']))
				$REQUEST=array('BannerIDS'=>$PARAM['id']);
			else
				$REQUEST=array('BannerIDS'=>array((string)$PARAM['id']));
			$Result = $this->Framework->direct->model->api->get ( 'StopBanners', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			else
				$this->Framework->library->error->set('Не удалось остановить баннер №'.print_r($PARAM['id'], true).' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	//http://api.yandex.ru/direct/doc/live/GetBannersStat.xml
	public function statistic($PARAM=array()) {
		if (!empty($PARAM) && empty($PARAM['id']))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['date']))
			$PARAM['start']=date('Y-m-d', strtotime($PARAM['date']));
		if (empty($PARAM['start']))
			$PARAM['start']=date('Y-m-d');
		else
			$PARAM['start']=date('Y-m-d', strtotime($PARAM['start']));
		if (empty($PARAM['end']))
			$PARAM['end']=$PARAM['start'];
		else
			$PARAM['end']=date('Y-m-d', strtotime($PARAM['end']));
			
		if (!empty($PARAM['id'])) {	
		
			$REQUEST=array(
			   'CampaignID' => $PARAM['id'],
			   'StartDate' => $PARAM['start'],
			   'EndDate' => $PARAM['end'],
			   'GroupByColumns' => array('clDate', 'clPhrase', 'clAveragePosition'),
			   //'Limit' => 10,
			   //'Offset' => 0,
			   'OrderBy' => array('clDate', 'clBanner'),
			   'IncludeVAT' => 'No',
			);
			if (!empty($PARAM['currency']))
				$REQUEST['Currency']=$PARAM['currency'];
			$Result = $this->Framework->direct->model->api->get ( 'GetBannersStat', $REQUEST );
			if (!empty($Result->data))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_code) && $Result->error_code!=2) {
				$this->Framework->library->error->set('Не удалось получить статистику баннеров компании ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
				return array('ERROR'=>array('code'=>$Result->error_code, 'string'=>$Result->error_str, 'detail'=>$Result->error_detail));
			}
				
		}
		return array();
	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/GetBannersTags-docpage/
	public function tag($PARAM=array()) {
		$REQUEST=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$REQUEST['BannerIDS']=array($PARAM);
		elseif (!empty($PARAM['id'])) {
			$REQUEST['BannerIDS']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
		} elseif (!empty($PARAM['company'])) {
			$REQUEST['CampaignIDS']=is_array($PARAM['company'])?$PARAM['company']:array($PARAM['company']);
		} elseif (!empty($PARAM) && is_array($PARAM))
			$REQUEST['BannerIDS']=$PARAM;
	
		if (!empty($REQUEST) && is_array($REQUEST)) {		
			$Result = $this->Framework->direct->model->api->get ( 'GetBannersTags', $REQUEST );
			if (!empty($Result->data[0]))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_str))
				$this->Framework->library->error->set('Не удалось получить список тегов у баннеров ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return array();
	}
	
}//\class
?>