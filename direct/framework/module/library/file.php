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

final class File {
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
	
	public function get($file='') {
		$DATA=array();
		if (!empty($file) && file_exists($file)) {
			if (is_dir($file)) {
				$Dir = dir($file);
				$path=$Dir->path.'/';
				while (false !== ($filename = $Dir->read())) {
				   if (!empty($filename) && $filename!='.' && $filename!='..') {
						$DATA[]=array(
							'name'=>$filename,
							'path'=>$path,
							'dir'=>is_dir($path.$filename),
							'ext'=>$this->Framework->library->lib->filename($filename, true, true),
							'size'=>filesize($path.$filename),
							'time'=>DATE('Y-m-d H:i:s', filemtime($path.$filename)),
							'access'=>fileatime($path.$filename),
							'change'=>filectime($path.$filename),
							'modify'=>filemtime($path.$filename),
							'owner'=>fileowner($path.$filename),
							'group'=>filegroup($path.$filename),
							'permission'=>fileperms($path.$filename),
						);
				   }
				}
				$Dir->close();
			}
		}
		return $DATA;
	}
	
	public function add($file='', $content='') {
		$resource=fopen($file, 'a');
		$return=fwrite($resource, $content);
		fclose($resource);
		return $return;
	}

}//\class
?>