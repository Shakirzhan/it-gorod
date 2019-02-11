<?php 
/////////////////////////////////////////////////////////////
/// Project: FrameWork - фреймворк для веб-разработки     ///
/// Name: Класс контроллер                                ///
/// Author: Кононенко Станислав Александрович             ///
/// Email: info@direct-automate.ru                        ///
/// Url: http://direct-automate.ru                        ///
/// Language: PHP5.3+                                     ///
/// Charset: UTF-8                                        ///
/////////////////////////////////////////////////////////////
namespace FrameWork\module\article\controller;

final class Article extends \FrameWork\Common {
	private $dir='files/file/';
	private $price_min=100;
	
	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header()->get('http');
	}	
	
	public function get($id=0) {
		$DATA=array();
		$PARAMS=array();
		$ORDER=array('id'=>'DESC');
		$LIMIT=array();
		$template='second.html';
		if (!empty($id) && is_numeric($id)) {
			if ($id==-1) {
				$PARAMS=array('parent'=>2);
				$LIMIT=array('page'=>0, 'number'=>1);
			} else
				$PARAMS=array('id'=>$id);
			$template='index.html';
		}
		elseif (!empty($id) && !is_array($id))
			$PARAMS=array('key'=>$id);
		elseif (is_array($id)) {
			$PARAMS=array_merge($PARAMS, $id);
			if ($PARAMS['parent']==2)
				$template='news.html';
			elseif ($PARAMS['parent']==3)
				$template='otzivy.html';
		}
		
		$DATA=array_merge($DATA, $this->Framework->page->controller->controller->menu());
		$DATA['ARTICLE']=$this->Framework->article->model->article->get($PARAMS, $ORDER, array(), $LIMIT);
		$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
		$this->Framework->template->set('DATA', $DATA);
		
		echo $this->Framework->template->get($template);

	}	

}//\class
?>