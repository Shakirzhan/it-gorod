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

final class Retargeting extends \FrameWork\Common {
	
	private $limit=1000;
	
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
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/Retargeting_Update-docpage/
	public function set($PARAM=array()) {

		if (!empty($PARAM)) {
		
			$REQUEST = array ('Action' => 'Update', 'Retargetings'=>array());
			if (!empty($PARAM['login']))
				$REQUEST['Login'] = $PARAM['login'];
			if (empty($PARAM['ELEMENT']) && !empty($PARAM['id']))
				$PARAM['ELEMENT']=array(array('id'=>$PARAM['id'], 'price'=>round($PARAM['price'], 2)));
			if (!empty($PARAM['ELEMENT']) && is_array($PARAM['ELEMENT']))
				foreach ($PARAM['ELEMENT'] as &$VALUE) {
					if (!empty($VALUE['id']) && !empty($VALUE['price']) && (float)$VALUE['price']>0)
						$REQUEST['Retargetings'][]=array(
							'Fields' => array('ContextPrice'),
							'RetargetingID' => $VALUE['id'],
							'ContextPrice' => round((float)$VALUE['price'], 2),
							'Currency'=>!empty($VALUE['currency'])?strtoupper((string)$VALUE['currency']):(!empty($PARAM['currency'])?strtoupper((string)$PARAM['currency']):null),
							'AutoBudgetPriority' => 'High'
						);
				}
				
			$Result = $this->Framework->direct->model->api->get ( 'Retargeting', $REQUEST );
			
			if (!empty($Result) && !empty($Result->data->ActionsResult))
				return $this->Framework->library->lib->objectToArray($Result->data->ActionsResult);
			elseif (!empty($Result) && !empty($Result->error_str))
				$this->Framework->library->error->set('Не удалось установить ретаргетиг. '.$Result->error_str, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return array();
	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/Retargeting_Get-docpage/
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>array($PARAM));
		if (!empty($PARAM['id']) && !is_array($PARAM['id']))
			$PARAM['id']=array($PARAM['id']);
		if (!empty($PARAM['banner']) && !is_array($PARAM['banner']))
			$PARAM['banner']=array($PARAM['banner']);
		if (!empty($PARAM['condition']) && !is_array($PARAM['condition']))
			$PARAM['condition']=array($PARAM['condition']);
		
		$REQUEST = array ('Action' => 'Get');
		if (!empty($PARAM['login']))
			$REQUEST['Login'] = $PARAM['login'];
		if (!empty($PARAM['id']))
			$REQUEST['SelectionCriteria']['RetargetingIDS'] = $PARAM['id'];
		if (!empty($PARAM['banner']))
			$REQUEST['SelectionCriteria']['AdIDS'] = $PARAM['banner'];
		if (!empty($PARAM['condition']))
			$REQUEST['SelectionCriteria']['RetargetingConditionIDS'] = $PARAM['condition'];
		if (!empty($PARAM['currency']))
			$REQUEST['Options']['Currency'] = strtoupper((string)$PARAM['currency']);

		$Result = $this->Framework->direct->model->api->get ( 'Retargeting', $REQUEST );
/*
	[Retargetings] => Array
        (
            [0] => Array
                (
                    [ContextPrice] => 0.02
                    [AdID] => 1137060697
                    [StatusPaused] => No
                    [AdGroupID] => 855225920
                    [AutoBudgetPriority] => Medium
                    [RetargetingConditionID] => 455589
                    [RetargetingID] => 4166266
                )

        )
*/
		if (!empty($Result) && !empty($Result->data->Retargetings))
			return $this->Framework->library->lib->objectToArray($Result->data->Retargetings);
		elseif (!empty($Result) && !empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить ретаргетинг. '.$Result->error_str, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return array();
	}
	
}//\class
?>