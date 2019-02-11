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

final class Phrase extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/GetBannerPhrasesFilter-docpage/
	public function get($PARAM=array()) {
		$REQUEST=array();
		if (!empty($PARAM) && !is_array($PARAM))
			$REQUEST['BannerIDS']=array($PARAM);
		elseif (!empty($PARAM['id'])) {
			$REQUEST['BannerIDS']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
		}
		$REQUEST['ConsiderTimeTarget']='Yes';
		if (!empty($PARAM['currency']))
			$REQUEST['Currency']=$PARAM['currency'];
		if (!empty($PARAM['field']) && is_array($PARAM['field'])) 
			$REQUEST['FieldsNames']=$PARAM['field'];
		elseif (!empty($PARAM['field']))
			$REQUEST['FieldsNames']=array('AdGroupID', 'Price', 'ContextPrice', 'ContextClicks', 'ContextShows', 'Clicks', 'Shows', 'ContextCoverage', 'AuctionBids', 'CurrentOnSearch', 'MinPrice', 'StatusPaused');
		if (!empty($PARAM) && is_array($PARAM)) {		
			$Result = $this->Framework->direct->model->api->get ( 'GetBannerPhrasesFilter', $REQUEST );
			if (!empty($Result->data[0]))
				return $this->Framework->library->lib->objectToArray($Result->data);
			elseif (!empty($Result->error_str))
				$this->Framework->library->error->set('Не удалось получить список фраз ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return array();
	}
	
	public function start($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array(
				'Action'=>'Resume',
				'Login'=>!empty($PARAM['login'])?$PARAM['login']:$this->Framework->direct->model->config->CONFIG['login'],
				'KeywordIDS'=>is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']),
			);
			$Result = $this->Framework->direct->model->api->get ( 'Keyword', $REQUEST );
			if (!empty($Result->data))
				return $PARAM['id'];
			elseif (!empty($Result))
				$this->Framework->library->error->set('Не удалось запустить ключевую фразу №'.print_r($PARAM['id'], true).' ('.print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	public function stop($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>$PARAM);
		if (!empty($PARAM['id'])) {
			$REQUEST=array(
				'Action'=>'Suspend',
				'Login'=>!empty($PARAM['login'])?$PARAM['login']:$this->Framework->direct->model->config->CONFIG['login'],
				'KeywordIDS'=>is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']),
			);
			$Result = $this->Framework->direct->model->api->get ( 'Keyword', $REQUEST );

			if (!empty($Result->data) && empty($Result->data->ActionsResult[0]->Errors[0]->FaultString))
				return $PARAM['id'];
			else
				$this->Framework->library->error->set('Не удалось остановить ключевую фразу №'.print_r($PARAM['id'], true).' ('.print_r($REQUEST, true).print_r($Result, true).').', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return null;
	}
	
	public function minus($string='') {
		$DATA=array('name'=>'', 'minus'=>'', 'search'=>'', 'MINUS'=>array());
		$DATA['MINUS']=explode(' -', $string);
			if (!empty($DATA['MINUS']))
				$DATA['name']=array_shift($DATA['MINUS']);
			if (!empty($DATA['MINUS']))
				$DATA['minus']='-'.implode(' -', $DATA['MINUS']);
		$DATA['search']=mb_ereg_replace('[^а-яА-Яa-zA-Z0-9 ]', '', $DATA['name']);
		return $DATA;
	}
	
}//\class
?>