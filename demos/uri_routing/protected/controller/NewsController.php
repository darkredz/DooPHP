<?php

class NewsController extends DooController{

	public function show_news_by_id(){
        $data['title'] = 'NewsController->show_news_by_id';
        $data['content'] ='News id is '.$this->params['id'];
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
	}
    
	public function show_news_by_year_month(){
        $data['title'] = 'NewsController->show_news_by_year_month';
        $data['content'] ='News in month '.$this->params['month'] .', year '.$this->params['year'] ;
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
	}

	public function show_news_by_title(){
        $data['title'] = 'NewsController->show_news_by_title';
        $data['content'] ='News title is '. str_replace('%20', ' ', $this->params['title']);
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = $this->params;
        $this->view()->render('template', $data);
	}
}

?>