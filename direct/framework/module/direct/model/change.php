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
//https://tech.yandex.ru/direct/doc/dg-v4/reference/GetChanges-docpage/
final class Change extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
		$REQUEST=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$REQUEST['CampaignIDS']=array($PARAM);
		elseif (!empty($PARAM['id'])) {
			$REQUEST['CampaignIDS']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
		} elseif (!empty($PARAM['banner'])) {
			$REQUEST['BannerIDS']=is_array($PARAM['banner'])?$PARAM['banner']:array($PARAM['banner']);
		} elseif (!empty($PARAM) && is_array($PARAM))
			$REQUEST['CampaignIDS']=$PARAM;
		if (!empty($PARAM['time'])) {
			if (!is_numeric($PARAM['time']) && $PARAM['time']!='0000-00-00 00:00:00')
				$PARAM['time']=strtotime($PARAM['time']);
			else
				$PARAM['time']=0;
			$REQUEST['Timestamp'] = gmdate('Y-m-d', $PARAM['time']).'T'.gmdate('H:i:s', $PARAM['time']).'Z';
		} else
			$REQUEST['Timestamp'] = '1970-01-01'.'T'.'00:00:00'.'Z';
		if (!empty($REQUEST)) {
			$Result = $this->Framework->direct->model->api->get ( 'GetChanges', $REQUEST );
			if (!empty($Result->data))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_str))
				$this->Framework->library->error->set('Не удалось получить список изменений у кампаний и баннеров ('.print_r($Result, true).print_r($REQUEST, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return array();
	}
	
	
	
}//\class
?>