<?php

class SnsController extends DooController {
	
	public function beforeRun($resource, $action){
		session_start();
		
		//if not login, group = anonymous
		$role = (isset($_SESSION['user']['group'])) ? $_SESSION['user']['group'] : 'anonymous';
		
		if($role!='anonymous'){
			if($_SESSION['user']['vip'])
				$role = 'vip';
		}
		
		//check against the ACL rules
		if($rs = $this->acl()->process($role, $resource, $action )){
			//echo $role .' is not allowed for '. $resource . ' '. $action;
			return $rs;
		}
	}
	
	function game() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Welcome to SNS Games';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'Very cool 3D Flash games here.';
		$this->render('template', $data);
	}

	function viewProfile() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Profile of ' . $this->params['uname'];
		$data['content'] = 'You can access this~';
		$data['printr'] = 'Hi I am '. $this->params['uname'] . ' and I am a cool guy.';
		$this->render('template', $data);
	}

	function banUser() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'Banning User';
		$data['content'] = 'You can access this~';
		$data['printr'] = '<input type="button" value="Ban this user?" />';
		$this->render('template', $data);
	}

	function showVipHome() {
		$data['baseurl'] = Doo::conf()->APP_URL;
		$data['title'] = 'VIP Lounge';
		$data['content'] = 'You can access this~';
		$data['printr'] = 'SuperDuber contents! Thanks for being a paid member :)';
		$this->render('template', $data);
	}

}
?>