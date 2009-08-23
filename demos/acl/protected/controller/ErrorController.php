<?php

class ErrorController extends DooController {

	function memberDefaultError() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Member is not allowed!';
		$data['content'] = 'Not allowed';
		$data['printr'] = 'Access denied!';
		$this->render('template', $data);
	}

	function memberSnsDeny() {
		switch($this->params['error']){
			case 'notAdmin': 
				$error = 'You are not the SNS admin!';
				break;
			case 'notVip': 
				$error = 'Sorry, this is for VIP only.';
				break;				
			default: 
				$error = 'Not allowed';
				break;				
		}
		
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Member is not allowed!';
		$data['content'] = $error;
		$data['printr'] = 'Access denied!';
		$this->render('template', $data);
	}

	function memberBlogDeny() {
		switch($this->params['error']){
			case 'notAdmin': 
				$error = 'You are not the Blog admin!';
				break;
			default: 
				$error = 'Not allowed';
				break;				
		}
		
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Member is not allowed!';
		$data['content'] = $error;
		$data['printr'] = 'Access denied!';
		$this->render('template', $data);
	}

	function vipDefaultError() {
		echo 'You are visiting '.$_SERVER['REQUEST_URI'];
	}

	function adminSnsDeny() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Admin is not allowed!';
		$data['content'] = ($this->params['error']=='vipOnly') ? 'This is VIP only!' : 'Not allowed';
		$data['printr'] = 'Access denied!';
		$this->render('template', $data);
	}

	function error404() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Page not found!';
		$data['content'] = 'default 404 error';
		$data['printr'] = 'Nothing is found...';
		$this->render('template', $data);
	}
	
	function loginRequire() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Login Required!';
		$data['content'] = 'You cannot access this!';
		$data['printr'] = 'You have to be logined to access this section.';
		$this->render('template', $data);
	}

}
?>