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

final class Position extends \FrameWork\Common {
	private $debug=false;
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
		$this->get($PARAM);
	}
	
	public function get($PARAM=array()) {
		if ($this->Framework->direct->model->config->CONFIG['xml_user'] && $this->Framework->direct->model->config->CONFIG['xml_key']) {
			if (!$this->Framework->api->yandex->xml->query->limit()) {
		
				$sql="SELECT `t1`.`id`, `t1`.`name`, IF (`t3`.`domain`!='', `t3`.`domain`, `t2`.`domain`) as `domain` FROM `".$this->Framework->direct->model->config->TABLE['phrase']."` `t1` 
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['banner']."` `t2` ON (`t2`.`id`=`t1`.`banner`)
					INNER JOIN `".$this->Framework->direct->model->config->TABLE['company']."` `t3` ON (`t3`.`id`=`t2`.`company`)
					WHERE `t1`.`position`>0 AND (`t1`.`position_datetime`='' OR `t1`.`position_datetime` is NULL OR `t1`.`position_datetime`<='".date('Y-m-d')." 00:00:00')
					ORDER BY `t1`.`position_datetime`
					LIMIT 0, ".($this->Framework->direct->model->config->CONFIG['xml_number']?$this->Framework->direct->model->config->CONFIG['xml_number']:10)."
				";
				
				$result=$this->Framework->db->set($sql);
				while ($ROW=$this->Framework->db->get($result)) {
					$NAME=$this->Framework->direct->model->phrase->minus($ROW['name']);
						
					$position=$this->Framework->api->yandex->xml->query->position(array(
						'url'=>$ROW['domain'],
						'query'=>preg_replace('/[!\|\+\-\'"!\(\)\[\]]+/', '', $NAME['name']),
						'user'=>$this->Framework->direct->model->config->CONFIG['xml_user'],
						'key'=>$this->Framework->direct->model->config->CONFIG['xml_key'],
						'region'=>$this->Framework->direct->model->config->CONFIG['xml_region'],
					));
					
					/*$position_google=$this->Framework->api->google->search->query->position(array(
						'url'=>$ROW['domain'],
						'name'=>$NAME['name'],
					));*/
					
					$sql="UPDATE `".$this->Framework->direct->model->config->TABLE['phrase']."`
						SET 
						`position_value`='".(int)$position."',
						-- `google_position_value`='".(int)$position_google."',
						`position_datetime`='".date('Y-m-d H:i:s')."'
						WHERE 
						`id`='".$ROW['id']."'
						LIMIT 1
					";
					$this->Framework->db->set($sql);
					sleep(1);
				}
			}
		}
	
	}
	
	
	
}//\class
?>