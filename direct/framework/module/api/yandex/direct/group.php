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
//https://tech.yandex.ru/direct/doc/ref-v5/adgroups/adgroups-docpage/
final class Group extends \FrameWork\Common {
	
	private $limit=10000;
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
	
	//https://tech.yandex.ru/direct/doc/ref-v5/adgroups/get-docpage/
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			elseif (empty($PARAM['id'])&&empty($PARAM['company'])&&is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id']))
				$REQUEST['SelectionCriteria']['Ids']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			elseif (!empty($PARAM['company']))
				$REQUEST['SelectionCriteria']['CampaignIds']=is_array($PARAM['company'])?$PARAM['company']:array($PARAM['company']);
			if (empty($PARAM['status']))
				$REQUEST['SelectionCriteria']['Statuses']=array( 'ACCEPTED' , 'PREACCEPTED' , 'MODERATION' , 'REJECTED' , 'DRAFT' );//
			if (empty($PARAM['serving']))
				$REQUEST['SelectionCriteria']['ServingStatuses']=array( 'ELIGIBLE' );// , 'RARELY_SERVED'
			if (empty($PARAM['field']))
				$REQUEST['FieldNames']=array(
					'Id',
					'Name',
					'CampaignId',
					'Status',
					'ServingStatus',
					//"RegionIds",
					//"NegativeKeywords",
				);
			
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

			$DATA=$this->Framework->api->yandex->direct->query->get('adgroups', 'get', $REQUEST);

			if (!empty($DATA['AdGroups']))
				$DATA=$DATA['AdGroups'];
			else
				$DATA=array();
		}
		/*0=> 
			[Name] => Новая группа объявлений
			[CampaignId] => 123456789
			[Id] => 1234567890
			[Status] => ACCEPTED*/
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		$DATA=array();
		
		
		return $DATA;
	}
	
	public function add () {
		return $this->set();
	}
	
	
}//\class
?>