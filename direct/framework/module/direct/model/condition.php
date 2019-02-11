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

final class Condition extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {

	}
	
	//https://tech.yandex.ru/direct/doc/dg-v4/live/RetargetingCondition_Get-docpage/
	public function get($PARAM=array()) {
		if (!empty($PARAM) && !is_array($PARAM))
			$PARAM=array('id'=>array($PARAM));
		if (!empty($PARAM['id']) && !is_array($PARAM['id']))
			$PARAM['id']=array($PARAM['id']);
		
		$REQUEST = array ('Action' => 'Get', 'SelectionCriteria'=>array());
		if (!empty($PARAM['id']))
			$REQUEST['SelectionCriteria']['RetargetingConditionIDS'] = $PARAM['id'];
		if (!empty($PARAM['login']) && !is_array($PARAM['login']))
			$REQUEST['SelectionCriteria']['Logins'] = array($PARAM['login']);
		elseif (!empty($PARAM['login']) && is_array($PARAM['login']))
			$REQUEST['SelectionCriteria']['Logins'] = $PARAM['login'];

		$Result = $this->Framework->direct->model->api->get ( 'RetargetingCondition', $REQUEST );
/*
	[RetargetingConditions] => Array
    (
        [0] => Array
        (
            [RetargetingCondition] => Array
                (
                    [0] => Array
                        (
                            [Goals] => Array
                                (
                                    [0] => Array
                                        (
                                            [Time] => 30
                                            [GoalID] => 6348465
                                        )

                                )

                            [Type] => or
                        )

                )

            [Login] => direct-automate
            [RetargetingConditionName] => Заказ
            [RetargetingConditionDescription] => 
            [IsAccessible] => Yes
            [RetargetingConditionID] => 455589
        )

    )
*/
		if (!empty($Result) && !empty($Result->data->RetargetingConditions))
			return $this->Framework->library->lib->objectToArray($Result->data->RetargetingConditions);
		elseif (!empty($Result) && !empty($Result->error_str))
			$this->Framework->library->error->set('Не удалось получить условия ретаргетинга. '.$Result->error_str, __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return array();
	}
	
}//\class
?>