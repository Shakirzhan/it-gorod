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

final class Update extends \FrameWork\Common {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function set($PARAM=array()) {
		$this->get($PARAM);
	}
	
	public function get($PARAM=array()) {
		if (!class_exists('mysqli'))
			die('У вас не установлен драйвер базы данных Mysqli');
		elseif (!$this->Framework->db->connect()) {
			echo 'Неправильные параметры подключения к базе данных';
			$this->Framework->library->error->get(true);
			die('Отредактируйте файл config.php и впишите верные данные доступа');
			
			}
		else {
			if (file_exists($this->Framework->CONFIG['files_dir'].'mysql.sql')) {
				$sql=file_get_contents($this->Framework->CONFIG['files_dir'].'mysql.sql');
				if ($sql && isset($this->Framework->CONFIG['DATABASE'][0]['prefix']) && $this->Framework->CONFIG['DATABASE'][0]['prefix']!='direct_')
					$sql=str_replace('`direct_', '`'.$this->Framework->CONFIG['DATABASE'][0]['prefix'], $sql);
				$pattern='/-- BEFORE --(.+)-- \\\BEFORE --/isU';
				if (preg_match_all($pattern, $sql, $MATCH)) {
					$sql=preg_replace($pattern, '', $sql);
				}				
				if (!empty($MATCH[1])) 
					foreach($MATCH[1] as $match)
						$this->Framework->db->multi($match);
				$SQL=explode(';', $sql);
				$SQL=array_reverse($SQL);
				foreach ($SQL as $sql) 
					if (trim($sql))
						$this->Framework->db->multi($sql);

				unlink($this->Framework->CONFIG['files_dir'].'mysql.sql');
			}
		}
	}
	
	
	
}//\class
?>