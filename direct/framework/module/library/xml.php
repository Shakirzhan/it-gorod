<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с XML                   ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Xml {
	private $Framework=null;
	private $cdata_open='<![CDATA[';
	private $cdata_close=']]>';
	
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
	
	public function get($xml='') {
		$DATA=array();
		try {
			if (!empty($xml) && !preg_match('/<!DOCTYPE HTML/i', $xml))
				$DATA=new \SimpleXMLElement($xml);
		} catch (Exception $e) {
			$this->Framework->library->error->set("Ошибка чтения XML: ".$e->getMessage().'.', __FILE__, __NAMESPACE__, __CLASS__, __METHOD__, __LINE__);
		}
		return $DATA;
	}
	
	public function xmltoarray(&$xml) {
		$DATA=array();
		$this->__read($xml, $DATA);
		return $DATA;
	}
	
	private function __read(&$xml, &$DATA, $KEYS=array(), $offset=0) {
		if (!is_string($xml) || empty($xml)) 
			return null;
		if (empty($DATA) || !is_array($DATA))
			$DATA=array();
		$debug=0;
		if ($debug)
			echo '<pre>'.print_r($KEYS, true).'</pre>';
		$tag='';
		$cdata='';
		$ATTR=array();
		$start=strpos($xml, '<', $offset);
		$end=strpos($xml, '>', $start);
		$pop=false;
		$nochild=0;
		
		if ($start!==false && $end!==false) {
			if (substr($xml, $end-1, 1)=='/') {
				$nochild=1;
				$pop=true;
				if ($debug)
					echo 'Тег без детей<br>';
			}
			
			if (substr($xml, $start, 2)=='<?') {
				$offset=$end+1;
				//encoding='UTF-8'
			} 
			elseif (substr($xml, $start, 2)=='<!' && strtoupper(substr($xml, $start, strlen($this->cdata_open)))!=$this->cdata_open) {
				$offset=$end+1;
			}
			elseif (substr($xml, $start, 2)=='</') {
				if ($debug)
					echo 'Тег закрывающий<br>';
				$cdata=substr($xml, $offset, $start-$offset);
				$offset=$end+1;
				$pop=true;
			}
			elseif (strtoupper(substr($xml, $start, strlen($this->cdata_open)))==$this->cdata_open) {
				if ($debug)
					echo 'Тег cdata<br>';
				$end=strpos($xml, $this->cdata_close, $start+strlen($this->cdata_open));
				$cdata=substr($xml, $start+strlen($this->cdata_open), $end-$start-strlen($this->cdata_open));
				$offset=$end+strlen($this->cdata_close);
				if ($debug)
					echo $offset.'Контент='.$cdata.'<br>';
			}
			else {
				$offset=$end+1;
				$attr=substr($xml, $start+1, $end-$start-1-$nochild);
				$tag_position=strpos($attr, ' ');
				if ($tag_position===false)
					$tag=$attr;
				else {
					$tag=substr($attr, 0, $tag_position);
					//preg_match_all('/ ([a-z]+)=(("[^"]*")|(\'[^\']*\')|([^ \'"]+[ ]+))/isU', $attr, $MATCH);
					$ATTR=array();
					$this->__attributes($attr, $ATTR);
					if ($debug)
						echo '<pre>'.print_r($ATTR, true).'</pre>';
				}
					
				if ($debug)
					echo $start.'-'.$end.' Тег='.$tag.'<br>';
				
			}
			if ($debug)
				echo '<pre>'.print_r($KEYS, true).'</pre>';
			if ($tag) {
				if (count($KEYS)>0) 
					$ELEMENT=&$this->Framework->library->lib->get_array_element_by_keys($DATA, $KEYS);
				else
					$ELEMENT=&$DATA;
				if ($debug)
					echo 'Устанавливаем значение<br>';
				if ($debug)
					echo 'data до установки=<pre>'.print_r($ELEMENT, true).'</pre>';
				if (count($ATTR)>0) 
					if (!empty($ELEMENT[$tag]))
						if (!empty($ELEMENT[$tag][0])) {
							if ($debug)
								echo '!empty($ELEMENT[$tag]) && !empty($ELEMENT[$tag][0]<br>';
							$keys=max(array_keys($ELEMENT[$tag]))+1;
							$ELEMENT[$tag][$keys]=$ATTR;
							$KEYS[]=$tag;
							$KEYS[]=$keys;
						}
						else {
							if ($debug)
								echo '!empty($ELEMENT[$tag]) && empty($ELEMENT[$tag][0]<br>';
							$ELEMENT[$tag]=array($ELEMENT[$tag]);
							$ELEMENT[$tag][1]=$ATTR;
							$KEYS[]=$tag;
							$KEYS[]=1;
						}
					else {
						if ($debug)
							echo 'empty($ELEMENT[$tag])<br>';
						$ELEMENT[$tag]=$ATTR;
						$KEYS[]=$tag;
					}
				elseif (!empty($ELEMENT[$tag])) {
					if (!empty($ELEMENT[$tag][0])) {
						if ($debug)
							echo '!empty($ELEMENT[$tag][0]<br>';
						$keys=max(array_keys($ELEMENT[$tag]))+1;
						$ELEMENT[$tag][$keys]=null;
						$KEYS[]=$tag;
						$KEYS[]=$keys;
						}
					else {
						if ($debug)
							echo 'empty($ELEMENT[$tag][0]<br>';
						$ELEMENT[$tag]=array($ELEMENT[$tag]);
						$ELEMENT[$tag][1]=null;
						$KEYS[]=$tag;
						$KEYS[]=1;
					}
				}
				else {
					if ($debug)
						echo 'empty($ELEMENT[$tag])<br>';
					$ELEMENT[$tag]=null;
					$KEYS[]=$tag;
				}
			} 
			elseif (str_replace(' ', '', str_replace("\n", '', str_replace("\r", '', str_replace("\r\n", '', $cdata))))) {
				if ($debug)
					echo 'Устанавливаем cdata: "'.$cdata.'"<br>';
				if (count($KEYS)>0) 
					$ELEMENT=&$this->Framework->library->lib->get_array_element_by_keys($DATA, $KEYS);
				else
					$ELEMENT=&$DATA;
					if (is_array($ELEMENT))
						$ELEMENT[0]=$cdata;
					else
						$ELEMENT=$cdata;
			}
			if ($pop) {
				if (count($KEYS)>0 && is_numeric($KEYS[count($KEYS)-1])) 
					array_pop($KEYS);
				array_pop($KEYS);
			}
			if ($debug)
				echo 'keys=<pre>'.print_r($KEYS, true).'</pre>';
			if ($debug)
				echo 'data=<pre>'.print_r($DATA, true).'</pre><hr>';
			$this->__read($xml, $DATA, $KEYS, $offset);
		}
		return null;
	}	
	
	private function __attributes(&$xml, &$ATTR, $offset=0) {
		if (is_string($xml) && !empty($xml)) {
			$key='';
			$value='';
			$attr_position=strpos($xml, '=', $offset);
			if ($attr_position!==false) {
				for($i=$attr_position; $i>=0; $i--) 
					if (substr($xml, $i, 1)==' ') {
						$key=substr($xml, $i+1, $attr_position-$i-1);
						break;
					}
				if ($key) {
					if (substr($xml, $attr_position+1, 1)=='"') {
						$attr_value_position=strpos($xml, '"', $attr_position+2);
						if ($attr_value_position!==false) {
							$value=substr($xml, $attr_position+2, $attr_value_position-$attr_position-2);
						}
					} 
					elseif (substr($xml, $attr_position+1, 1)=="'") {
						$attr_value_position=strpos($xml, "'", $attr_position+2);
						if ($attr_value_position!==false) {
							$value=substr($xml, $attr_position+2, $attr_value_position-$attr_position-2);
						}
					}
					else {
						$attr_value_position=strpos($xml, " ", $attr_position+1);
						if ($attr_value_position!==false) {
							$value=substr($xml, $attr_position+1, $attr_value_position-$attr_position-1);
						}
						else
							$value=substr($xml, $attr_position+1);
					}
				}
				if ($key)
					$ATTR[$key]=$value;
				$this->__attributes($xml, $ATTR, $attr_position+1);
			}
		}
		return null;
	}

}//\class
?>