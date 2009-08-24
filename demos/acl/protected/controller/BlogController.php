<?php

class BlogController extends DooController {

	public function beforeRun($resource, $action){
		session_start();
		
		//if not login, group = anonymous
		$role = (isset($_SESSION['user']['group'])) ? $_SESSION['user']['group'] : 'anonymous';
		
		//check against the ACL rules
		if($rs = $this->acl()->process($role, $resource, $action )){
			//echo $role .' is not allowed for '. $resource . ' '. $action;
			return $rs;
		}
	}

	function index() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Welcome to Blog home';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'This is some blog content. <br/>This is some blog content. <br/>This is some blog content. <br/>This is some blog content. <br/>This is some blog content. ';
		$this->render('template', $data);		
	}

	function comments() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Blog - posting comments';
		$data['content'] = (isset($_SESSION['user']))?'You can access this~' : 'Have to login to post';
		$data['printr'] = (isset($_SESSION['user']))?'You can post blog comment here' : 'Need to login to post comments!';
		$this->render('template', $data);		
	}

	function deleteComment() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Blog - delete comment';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'You are the admin <input type="button" value="Delete this comment" />';
		$this->render('template', $data);		
	}

	function writePost() {
		echo 'You are visiting '.$_SERVER['REQUEST_URI'];
	}

}
?>