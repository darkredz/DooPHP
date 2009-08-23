<?php
//no need authentication checking here
class PageController extends DooController {

	function home() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Welcome to SNS home';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'Some latest photo, feeds, news, highlights to be displayed. ';
		$this->render('template', $data);		
	}

	function about() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'About Us';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'Something about us. Very cool!';
		$this->render('template', $data);		
	}

	function contact() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Contact Us';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'Contact Us form.';
		$this->render('template', $data);		
	}

}
?>