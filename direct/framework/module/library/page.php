<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с файловой системой     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Page {
	private $Framework=null;
	
	public function __construct () {
		$this->Framework=\FrameWork\Framework::singleton();
	}
	
	public function __call ($name, $ARGUMENTS=array()) {		
		$this->Framework->library->error->set('Нет такого метода: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}//\function
	
	
	public function __set($name, $value=false) {
		$this->Framework->library->error->set('Нельзя установить такое свойство: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;
	}
	
		
	public function __get($name) {
		$this->Framework->library->error->set('Нет такого свойства: "'.$name.'".', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		return false;		
	}
	
	private function __clone()
	{
	}
	
	public function get($PARAMS=array()) {
		$DATA=array();
		if (!empty($PARAMS['page']) && intval($PARAMS['page'])>0)
			$PARAMS['page']=intval($PARAMS['page']);
		else
			$PARAMS['page']=0;
		if (!isset($PARAMS['number']) || !intval($PARAMS['number'])>0)
			$PARAMS['number']=0;
		else
			$PARAMS['number']=intval($PARAMS['number']);
		$DATA['count']=(!empty($PARAMS['count']) && intval($PARAMS['count']))?intval($PARAMS['count']):0;
		$DATA['number']=$PARAMS['number'];
		$DATA['pages']=$DATA['number']>0?ceil($DATA['count']/$DATA['number']):0;
		$DATA['page']=$PARAMS['page']<$DATA['pages']-1?$PARAMS['page']:($DATA['pages']>0?$DATA['pages']-1:0);
		$DATA['prev']=$PARAMS['page']>0?$PARAMS['page']-1:'';
		$DATA['next']=$PARAMS['page']<$DATA['pages']-1?$PARAMS['page']+1:'';
		$DATA['first']=0;
		$DATA['current']=$DATA['page']+1;
		$DATA['last']=$DATA['pages']>0?$DATA['pages']-1:'';
		$DATA['start']=$PARAMS['page']*$DATA['number'];
		$DATA['elements']=($DATA['number']*$DATA['current']>$DATA['count'])?$DATA['count']-$DATA['number']*$DATA['page']:$DATA['number'];
		$DATA['limit']=$PARAMS['number']>0?' LIMIT '.$DATA['start'].', '.$PARAMS['number'].' ':'';
		return $DATA;
	}

}//\class
?>