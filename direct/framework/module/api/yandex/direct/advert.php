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
//https://tech.yandex.ru/direct/doc/ref-v5/ads/ads-docpage/
final class Advert extends \FrameWork\Common {
	
	private $limit=10000;
	private $group=1000;
	private $company=10;
	
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
	
	//https://tech.yandex.ru/direct/doc/ref-v5/ads/get-docpage/
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			elseif (empty($PARAM['id'])&&empty($PARAM['company'])&&empty($PARAM['group'])&&is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id']))
				$REQUEST['SelectionCriteria']['Ids']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			if (!empty($PARAM['group']))
				$REQUEST['SelectionCriteria']['AdGroupIds']=is_array($PARAM['group'])?$PARAM['group']:array($PARAM['group']);			
			if (!empty($PARAM['company']))
				$REQUEST['SelectionCriteria']['CampaignIds']=is_array($PARAM['company'])?$PARAM['company']:array($PARAM['company']);
			if (empty($PARAM['status']))
				$REQUEST['SelectionCriteria']['Statuses']=array( 
				'ACCEPTED',
				'DRAFT',
				'MODERATION',
				'PREACCEPTED',
				'REJECTED',
				);
			if (empty($PARAM['state']))
				$REQUEST['SelectionCriteria']['States']=array( 
					//'ARCHIVED', 
					'ON', 
					'OFF', 
					'SUSPENDED', 
					'OFF_BY_MONITORING',  
				);
			/*if (empty($PARAM['type']))
				$REQUEST['SelectionCriteria']['Types']=array( //"TEXT_AD" | "MOBILE_APP_AD" | "DYNAMIC_TEXT_AD" | "IMAGE_AD"
				'TEXT_AD',
				'IMAGE_AD',
				);*/
			if (empty($PARAM['field'])) {
				$REQUEST['FieldNames']=array(//( 'AdCategories' | 'AgeLabel' | 'AdGroupId' | 'CampaignId' | 'Id' | 'State' | 'Status' | 'StatusClarification' | 'Type' )
					'AdGroupId' , 
					'CampaignId' , 
					'Id' , 
					'State' , 
					'Status' , 
					'Type',
				);
				$REQUEST['TextAdFieldNames']=array(//( 'AdImageHash' | 'DisplayDomain' | 'Href' | 'SitelinkSetId' | 'Text' | 'Title' | 'Mobile' | 'VCardId' | 'AdImageModeration' | 'SitelinksModeration' | 'VCardModeration' )
					'DisplayDomain' , 
					'Href' , 
					'Text' , 
					'Title' , 
					'Mobile',
				);
				$REQUEST['MobileAppAdFieldNames']=array(
					'Title', 
					'TrackingUrl',
				);
				$REQUEST['DynamicTextAdFieldNames']=array(
					'Text',
				);
				$REQUEST['TextImageAdFieldNames']=array( 
					'AdImageHash', 
					'Href',
				);
				$REQUEST['MobileAppImageAdFieldNames']=array(
					'AdImageHash', 
					'TrackingUrl',
				);
			}
			
			if (empty($PARAM['limit']) || !$PARAM['limit']>0)			
				$REQUEST['Page']['Limit']=$this->limit;
			else	
				$REQUEST['Page']['Limit']=($PARAM['limit']>0 && $PARAM['limit']<=$this->limit)?(int)$PARAM['limit']:$this->limit;
			
			if (!empty($PARAM['page']) && $PARAM['page']>0)
				$REQUEST['Page']['Offset']=$REQUEST['Page']['Limit'] * (int)$PARAM['page'];
			
			if (empty($PARAM['offset']) || !$PARAM['offset']>0)
				$REQUEST['Page']['Offset']=0;
			else
				$REQUEST['Page']['Offset']=(int)$PARAM['offset'];
			
			$DATA=$this->Framework->api->yandex->direct->query->get('ads', 'get', $REQUEST);
			if (!empty($DATA['Ads']))
				$DATA=$DATA['Ads'];
			else
				$DATA=array();
		}
		/*[0] => Array
			(
				[Status] => ACCEPTED
				[AdGroupId] => 123456789
				[CampaignId] => 12345678
				[Id] => 123456789
				[State] => ON
				[TextAd] => Array
					(
						[Title] => Заголовок.
						[Text] => Текст
						[DisplayDomain] => site.ru
						[Mobile] => NO
						[Href] => http://site.ru
						[SitelinkSetId] => 
					)

			)*/
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		$DATA=array();
		
		
		return $DATA;
	}
	
	public function add ($PARAM=array()) {
		return $this->set($PARAM);
	}
	
	
	public function resume($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('ads', 'resume', $REQUEST);
				if (!empty($DATA['ResumeResults']))
					$DATA=$DATA['ResumeResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}
	
	public function suspend($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('ads', 'suspend', $REQUEST);
				if (!empty($DATA['SuspendResults']))
					$DATA=$DATA['SuspendResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}	
	
	public function archive($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('ads', 'archive', $REQUEST);
				if (!empty($DATA['ArchiveResults']))
					$DATA=$DATA['ArchiveResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}
	
	public function unarchive($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			if (!is_array($PARAM))
				$PARAM=array('id'=>array($PARAM));
			elseif (empty($PARAM['id']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			elseif (!empty($PARAM['id']) && !is_array($PARAM['id']))
				$PARAM=array('id'=>array($PARAM['id']));
			if (!empty($PARAM['id'])) {
				$REQUEST['SelectionCriteria']['Ids']=$PARAM['id'];
				$DATA=$this->Framework->api->yandex->direct->query->get('ads', 'unarchive', $REQUEST);
				if (!empty($DATA['UnarchiveResults']))
					$DATA=$DATA['UnarchiveResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}	
	
}//\class
?>