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

final class Tag extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	
	public function get($PARAM=array()) {
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