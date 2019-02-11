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
//https://tech.yandex.ru/direct/doc/ref-v5/bids/bids-docpage/
final class Bid extends \FrameWork\Common {
	
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
	
	//https://tech.yandex.ru/direct/doc/ref-v5/bids/get-docpage/
	public function get($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!empty($PARAM) && !is_array($PARAM)) 
				$PARAM=array('id'=>$PARAM);
			elseif (empty($PARAM['id']) && empty($PARAM['group']) && empty($PARAM['campaign']) && is_array($PARAM))
				$PARAM=array('id'=>$PARAM);
			if (!empty($PARAM['id']))
				$REQUEST['SelectionCriteria']['KeywordIds']=is_array($PARAM['id'])?$PARAM['id']:array($PARAM['id']);
			if (!empty($PARAM['group']))
				$REQUEST['SelectionCriteria']['AdGroupIds']=is_array($PARAM['group'])?$PARAM['group']:array($PARAM['group']);			
			if (!empty($PARAM['campaign']))
				$REQUEST['SelectionCriteria']['CampaignIds']=is_array($PARAM['campaign'])?$PARAM['campaign']:array($PARAM['campaign']);
			if (empty($PARAM['serving']))
				$REQUEST['SelectionCriteria']['ServingStatuses']=array( 'ELIGIBLE' );// , 'RARELY_SERVED'			
			if (empty($PARAM['field'])) {
				$REQUEST['FieldNames']=array(//( "KeywordId" | "AdGroupId" | "CampaignId" | "Bid" | "ContextBid" | "StrategyPriority" | "CompetitorsBids" | "SearchPrices" | "ContextCoverage" | "MinSearchPrice" | "CurrentSearchPrice" | "AuctionBids" )
					"KeywordId" , 
					"AdGroupId" , 
					"CampaignId" , 
					"Bid" , 
					"ContextBid" , 
					"ContextCoverage" , 
					"CurrentSearchPrice" , 
					"MinSearchPrice" , 
					"AuctionBids" , 
				);
			} elseif (!is_array($PARAM['field']) && $PARAM['field']=='search') {
				$REQUEST['FieldNames']=array(
					"KeywordId" , 
					//"AdGroupId" , 
					//"CampaignId" , 
					"Bid" , 
					"ContextBid" , 
					//"ContextCoverage" , 
					"CurrentSearchPrice" , 
					//"MinSearchPrice" , 
					"AuctionBids" , 
				);
			} elseif (!is_array($PARAM['field']) && $PARAM['field']=='context') {
				$REQUEST['FieldNames']=array(
					"KeywordId" , 
					//"AdGroupId" , 
					//"CampaignId" , 
					"Bid" , 
					"ContextBid" , 
					"ContextCoverage" , 
					"CurrentSearchPrice" , 
					//"MinSearchPrice" , 
					"AuctionBids" , 
				);
			}
			
			$DATA=$this->Framework->api->yandex->direct->query->get('bids', 'get', $REQUEST);
			if (!empty($DATA['Bids']))
				$DATA=$DATA['Bids'];
			else
				$DATA=array();
		}
		return $DATA;
	}
	
	public function set($PARAM=array()) {
		$DATA=array();
		if (!empty($PARAM)) {
			$REQUEST=array();
			if (!is_array($PARAM)) 
				$REQUEST=array('Bids'=>array($PARAM));
			elseif (is_array($PARAM))
				$REQUEST=array('Bids'=>$PARAM);
			
			$DATA=$this->Framework->api->yandex->direct->query->get('bids', 'set', $REQUEST);
			if (!empty($DATA['SetResults']))
				$DATA=$DATA['SetResults'];
			else
				$DATA=array();
		}
		return $DATA;
	}
	
	public function add () {
		return $this->set();
	}
	
	
}//\class
?>