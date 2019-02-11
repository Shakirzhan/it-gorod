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
//https://tech.yandex.ru/direct/doc/ref-v5/changes/changes-docpage/
final class Change extends \FrameWork\Common {
	
	private $company=3000;
	private $group=10000;
	private $banner=50000;
	
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
	
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			
			if (!empty($PARAM['id']))
				$REQUEST['AdIds']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			elseif (!empty($PARAM['banner']))
				$REQUEST['AdIds']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			elseif (!empty($PARAM['group']))
				$REQUEST['AdGroupIds']=is_array($PARAM['group'])?$PARAM['group']:array($PARAM['group']);
			elseif (!empty($PARAM['company']))
				$REQUEST['CampaignIds']=is_array($PARAM['company'])?$PARAM['company']:array($PARAM['company']);
			
			if (!empty($PARAM['time'])) {
				if (!is_numeric($PARAM['time']) && $PARAM['time']!='0000-00-00 00:00:00')
					$PARAM['time']=strtotime($PARAM['time']);
				elseif (!is_numeric($PARAM['time']))
					$PARAM['time']=0;
				$REQUEST['Timestamp'] = gmdate('Y-m-d', $PARAM['time']).'T'.gmdate('H:i:s', $PARAM['time']).'Z';
			} else
				$REQUEST['Timestamp'] = '1970-01-01'.'T'.'00:00:00'.'Z';
			
			if (empty($PARAM['field']))
				$REQUEST['FieldNames']=array(
					//"CampaignIds",
					"AdGroupIds",
					"AdIds",
					//"CampaignsStat",
				);
			
			$DATA=$this->Framework->api->yandex->direct->query->get('changes', 'check', $REQUEST);
		}
	
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		return $this->get($PARAM);
	}
	
	public function check ($PARAM=array()) {
		return $this->get($PARAM);
	}
	
	public function campaign($PARAM=array()) {
		$DATA=array();
		$REQUEST=array();
		if (!empty($PARAM) && !is_array($PARAM)) 
			$PARAM=array('time'=>$PARAM);
		
		if (!empty($PARAM['time'])) {
			if (!is_numeric($PARAM['time']) && $PARAM['time']!='0000-00-00 00:00:00')
				$PARAM['time']=strtotime($PARAM['time']);
			elseif (!is_numeric($PARAM['time']))
				$PARAM['time']=0;
			$REQUEST['Timestamp'] = gmdate('Y-m-d', $PARAM['time']).'T'.gmdate('H:i:s', $PARAM['time']).'Z';
		} else
			$REQUEST['Timestamp'] = '1970-01-01'.'T'.'00:00:00'.'Z';
		
		$DATA=$this->Framework->api->yandex->direct->query->get('changes', 'checkCampaigns', $REQUEST);
		/*Array
		(
			[Timestamp] => 2016-08-02T12:37:09Z
			[Campaigns] => Array
				(
					[0] => Array
						(
							[ChangesIn] => Array
								(
									[0] => SELF
									[1] => CHILDREN
									[2] => STAT
								)

							[CampaignId] => 10000001
						)

				)

		)*/
		return $DATA;
	}
	
	
}//\class
?>