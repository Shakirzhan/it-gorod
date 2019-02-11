<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс-плагин для работы с шифрованием           ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\library;

final class Crypt {
	private $Framework=null;
	private $key;
	
	public function __construct ($key='') {
		$this->Framework=\FrameWork\Framework::singleton();
		if (empty($key))
			$key='akdfhnpflb231an';
        $this->key = hash('sha256', $key, TRUE);
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
	
	public function set($string) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_ECB));
    }
	
    public function get($string) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, base64_decode($string), MCRYPT_MODE_ECB));
    }

}//\class
?>