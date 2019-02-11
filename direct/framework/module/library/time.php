<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с временем              ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Time extends \DateTime {
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
	
	public function countdown($date=null, $start=null) {
		$DATA=array();
		if (!empty($date))
			$date = strtotime($date);
		if (empty($date))
			$date=time();
		if (!empty($start))
			$start = strtotime($start);
		if (empty($start))
			$start=time();
		
		$sec=$date - $start;  
		$days=floor(($date - $start) /86400);  
		$h1=floor(($date - $start) /3600);  
		$m1=floor(($date - $start) /60);  
		$hour=floor($sec/60/60 - $days*24);  
		$hours=floor($sec/60/60);  
		$min=floor($sec/60 - $hours*60);  
		$secs=floor($sec - $min*60 - $hours*60*60);
		switch(substr($days, -1)){  
		case 1: $o='остался';  
		break;  
		case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 0: $o='осталось';  
		break;}  

		switch(substr($days, -2)){  
		case 1: $d='день';  
		break;  
		case 2: case 3: case 4: $d='дня';  
		break;  
		default: $d='дней';  
		}  

		switch(substr($hour, -2)) {  
		case 1: $h='час';  
		break;  
		case 2: case 3: case 4: $h='часа';  
		break;  
		default: $h='часов';  
		}  

		switch(substr($min, -2)) {  
		case 1: $m='минута';  
		break;  
		case 2: case 3: case 4: $m='минуты';  
		break;
		default:$m='минут';
		}   
		$DATA=array(
			'day'=>$days,
			'hour'=>$hour,
			'minute'=>$min,
			'second'=>$secs,
		);
		return $DATA;
	}
	
	public function timezone($timezone=0, $time=0) {
		if (!empty($time) && !is_numeric($time) && $time!='0000-00-00 00:00:00')
			$time=strtotime($time);
		elseif (is_numeric($time))
			$time=time();
		else
			$time=0;
		if ($time>0)
			$time=$time+(int)$timezone*3600;
		return $time;
	}
	
	public function datetime($time='') {
		if (!empty($time) && !is_numeric($time))
			$time=strtotime($time);
		if (empty($time))
			$time=time();
		return date('Y-m-d H:i:s', $time);
	}
	
	public function date($time='') {
		if (!empty($time))
			$time=strtotime($time);
		if (empty($time))
			$time=time();
		return date('Y-m-d', $time);
	}
	
	public function time($time='') {
		if (!empty($time))
			$time=strtotime($time);
		if (empty($time))
			$time=time();
		return date('H:i:s', $time);
	}
	
	public function day($value=0, $unix=false) {
		$time=time()+(int)$value*24*60*60;
		if ($unix)
			return $time;
		else
			return date('Y-m-d H:i:s', $time);
	}
	
	public function week($week=0, $full=false) {
		$name='';
		$name=($week==1?($full?'Понельник':'Пн'):($week==2?($full?'Вторник':'Вт'):($week==3?($full?'Среда':'Ср'):($week==4?($full?'Четверг':'Чт'):($week==5?($full?'Пятница':'Пт'):($week==6?($full?'Суббота':'Сб'):($full?'Воскресенье':'Вс')))))));
		return $name;
	}

}//\class
?>