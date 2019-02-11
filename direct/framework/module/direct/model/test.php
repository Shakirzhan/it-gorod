<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@business-automate.ru                      ///
/// Url: http://business-automate.ru                      ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\direct\model;

final class Test extends \FrameWork\Common {
	
	
	public function __construct () {
		parent::__construct();
	}	
	
	public function get() {
		$DATA=array();
		$formula=$this->Framework->direct->model->formula->set("if (maximum >= premium_min)
{
start;
price=min(max(premium_min*1.1,premium_min+2),maximum);
if (second_price_min-0.1>premium_min) price=second_price_min-0.1;
if (premium_max <= max(premium_min+4.5,premium_min*1.1) AND maximum >= premium_max) price=max(premium_max*1.1,premium_max+2);
elseif (premium_max <= max(second_price+1,second_price*1.05) OR second_price >= min(premium_min+7.5,premium_min*6)) price=second_price_min-0.1;
elseif (maximum>=second_price) if (premium_max <= max(second_price+3,second_price*1.1) AND maximum >= premium_max) price=min(max(premium_max*1.1,premium_max+2),maximum); else price=premium_max-0.1;
}

if (premium_min > maximum AND maximum >= min)
{
start;
price=min(1607, max(min*1.1,min+2), maximum);
if (down_second_price_min-0.1>min) price=down_second_price_min-0.1;
if (max <= max(min+4.5,min*1.1) AND maximum >= max) price=max(max*1.1,max+2);
elseif (max <= max(down_second_price+1,down_second_price*1.05) OR down_second_price >= min(min+7.5,min*6)) price=down_second_price_min-0.1;
elseif (maximum>=down_second_price) if (max <= max(down_second_price+3,down_second_price*1.1) AND maximum >= max) price=min(max(max*1.1,max+2),maximum); else 

price=max-0.1;
}

if (maximum < min) stop;");
		echo $formula;
		eval($formula);
		print_r($PHRASE);
		return $DATA;
	}
	
}//\class
?>