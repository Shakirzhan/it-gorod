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
//https://tech.yandex.ru/direct/doc/ref-v5/keywords/keywords-docpage/
final class Keyword extends \FrameWork\Common {
	
	private $limit=10000;
	private $group=1000;
	private $campaign=10;
	
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
	
	//https://tech.yandex.ru/direct/doc/ref-v5/keywords/get-docpage/
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id']))
				$REQUEST['SelectionCriteria']['Ids']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			elseif (!empty($PARAM['group']))
				$REQUEST['SelectionCriteria']['AdGroupIds']=is_array($PARAM['group'])?$PARAM['group']:array($PARAM['group']);
			elseif (!empty($PARAM['campaign']))
				$REQUEST['SelectionCriteria']['CampaignIds']=is_array($PARAM['campaign'])?$PARAM['campaign']:array($PARAM['campaign']);
			if (!empty($PARAM['moderate']))
				$REQUEST['SelectionCriteria']['Statuses']=array( 'ACCEPTED', 'REJECTED', 'DRAFT' );//
			if (!empty($PARAM['status']))
				$REQUEST['SelectionCriteria']['States']=array( 'ON', 'SUSPENDED', 'OFF' );//
			if (empty($PARAM['serving']))
				$REQUEST['SelectionCriteria']['ServingStatuses']=array( 'ELIGIBLE');// , 'RARELY_SERVED'
			if (!empty($PARAM['time'])) {
				if (!is_numeric($PARAM['time']) && $PARAM['time']!='0000-00-00 00:00:00')
					$PARAM['time']=strtotime($PARAM['time']);
				elseif (!is_numeric($PARAM['time']))
					$PARAM['time']=0;
				if (!empty($PARAM['time']))
					$REQUEST['SelectionCriteria']['ModifiedSince'] = gmdate('Y-m-d', $PARAM['time']).'T'.gmdate('H:i:s', $PARAM['time']).'Z';
			}
			if (empty($PARAM['field']))
				$REQUEST['FieldNames']=array(
					"Id",
					"Keyword",
					"State",
					"Status",
					"AdGroupId",
					"CampaignId",
					"Bid",
					"ContextBid",
					"StrategyPriority",
					"Productivity",
					//"ServingStatus",
				);
			
			if (empty($PARAM['limit']) || !$PARAM['limit']>0)			
				$REQUEST['Page']['Limit']=$this->limit;
			else	
				$REQUEST['Page']['Limit']=($PARAM['limit']>0 && $PARAM['limit']<=$this->limit)?(int)$PARAM['limit']:$this->limit;
			
			if (isset($PARAM['page']) && (int)$PARAM['page']>=0)
				$REQUEST['Page']['Offset']=$REQUEST['Page']['Limit'] * (int)$PARAM['page'];
			else {
				if (empty($PARAM['offset']) || !$PARAM['offset']>0)
					$REQUEST['Page']['Offset']=0;
				else
					$REQUEST['Page']['Offset']=(int)$PARAM['offset'];
			}
			
			$DATA=$this->Framework->api->yandex->direct->query->get('keywords', 'get', $REQUEST);
			if (!empty($DATA['Keywords']))
				$DATA=$DATA['Keywords'];
			else
				$DATA=array();
		}
		/* [0] => Array
        (
            [Id] => 1234567890
            [ContextBid] => 0
            [Status] => ACCEPTED
            [Keyword] => Кей -кей
            [Bid] => 41000000
            [Productivity] => Array
                (
                    [References] => Array
                        (
                            [0] => 2
                            [1] => 4
                        )

                    [Value] => 7.8
                )

            [AdGroupId] => 123456789
            [State] => ON
            [CampaignId] => 12345678
            [StrategyPriority] => NORMAL
        )*/
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		$DATA=array();
		
		
		return $DATA;
	}
	
	public function add($PARAM=array()) {
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
				$DATA=$this->Framework->api->yandex->direct->query->get('keywords', 'resume', $REQUEST);
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
				$DATA=$this->Framework->api->yandex->direct->query->get('keywords', 'suspend', $REQUEST);
				if (!empty($DATA['SuspendResults']))
					$DATA=$DATA['SuspendResults'];
				else
					$DATA=array();
			}
		}
		return $DATA;
	}		
	
	
}//\class
?>