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
namespace FrameWork\module\page\controller;

final class Controller extends \FrameWork\Common {

	public function __construct() {
		parent::__construct();
		
		$this->Framework->library->header->get('http');

	}
	
	public function index() {
		$DATA=array();
		$uri=$this->Framework->library->controller->uri();
		if ($uri) {
			$PAGE=$this->Framework->page->model->page->get(array('id'=>$uri, 'status'=>1), array(), array(), array('number'=>1));
			if (!empty($PAGE['ELEMENT'][0]['url'])) {
				$this->Framework->template->set('PAGE', $PAGE['ELEMENT'][0]);
				$this->Framework->library->controller->set($PAGE['ELEMENT'][0]['url']);	
			}
		} 
		if (empty($PAGE['ELEMENT'][0]['url']))
		{
			//Получаем категорию main//
			$PAGE=$this->Framework->page->model->page->get(array('parent'=>'main', 'status'=>1), array('parent'=>'ASC','sort'=>'ASC', 'id'=>'ASC'), array(), array('page'=>0, 'number'=>6));
			if (!empty($PAGE['ELEMENT'][0])) {
				$this->Framework->template->set('PAGE', $PAGE['ELEMENT'][0]);
				$DATA['PAGE']['MAIN']=$PAGE['ELEMENT'];
			}
			//\Получаем категорию main//
			
			//Получаем категорию blog//
			$PAGE=$this->Framework->page->model->page->get(array('parent'=>'blog', 'status'=>1), array('parent'=>'ASC','sort'=>'ASC', 'id'=>'ASC'), array(), array('page'=>0, 'number'=>7));
			if (!empty($PAGE['ELEMENT'][0])) {
				$DATA['PAGE']['BLOG']=$PAGE['ELEMENT'];
			}
			//\Получаем категорию blog//
			
			//Получаем анонс статьи about//
			$ARTICLE=$this->Framework->article->model->article->get(array('id'=>'about', 'status'=>1), array('parent'=>'ASC','sort'=>'ASC', 'id'=>'ASC'), array(), array('page'=>0, 'number'=>1));
			if (!empty($ARTICLE['ELEMENT'][0])) {
				$DATA['ARTICLE']['ANNOUNCE']=$ARTICLE['ELEMENT'];
			}
			//\Получаем категорию blog//		
			
			$this->Framework->template->set('USER', $this->Framework->user->controller->controller->USER);
			
			$this->Framework->template->set('DATA', $DATA);
			echo $this->Framework->template->get('index.html');
		}
	}	
	
	public function menu() {
		$DATA=array();
		//Получаем категорию main//
		$PAGE=$this->Framework->page->model->page->get(array('parent'=>'main', 'status'=>1), array('parent'=>'ASC','sort'=>'ASC', 'id'=>'ASC'), array(), array('page'=>0, 'number'=>6));
		if (!empty($PAGE['ELEMENT'][0])) {
			$DATA['PAGE']['MAIN']=$PAGE['ELEMENT'];
		}
		//\Получаем категорию main//
		
		//Получаем категорию blog//
		$PAGE=$this->Framework->page->model->page->get(array('parent'=>'blog', 'status'=>1), array('parent'=>'ASC','sort'=>'ASC', 'id'=>'ASC'), array(), array('page'=>0, 'number'=>7));
		if (!empty($PAGE['ELEMENT'][0])) {
			$DATA['PAGE']['BLOG']=$PAGE['ELEMENT'];
		}
		//\Получаем категорию blog//
		return $DATA;
	}

}//\class
?>