
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
      <div class="content">
	  	<h1>ACL Example</h1>
        <p class="normal">ACL rules are defined in <strong>acl.conf.php</strong>. You define what a group of users can do and vice-versa.</p>
        <pre>
<span style="color:yellow"># Allow member to access all actions in Sns and Blog resource.</span>
$acl['member']['allow'] = array(
				'SnsController'=>'*', 
				'BlogController'=>'*', 
			);
			
			

<span style="color:yellow"># Allow anonymous visitors for Blog index only.</span>
$acl['anonymous']['allow'] = array(
				'BlogController'=>'index', 
			);
			
			
			
<span style="color:yellow"># Deny member from banUser, showVipHome, etc.</span>
$acl['member']['deny'] = array(
				'SnsController'=>array('banUser', 'showVipHome'), 
				'BlogController' =>array('deleteComment', 'writePost')
			);


			
<span style="color:yellow"># Deny member from all Sns resources and Blog writePost</span>
$acl['member']['deny'] = array(
				'SnsController', 
				'BlogController' =>array('writePost')
			);

			

<span style="color:yellow"># Admin can access all except Sns showVipHome</span>
$acl['admin']['allow'] = '*';
$acl['admin']['deny'] = array(
				'SnsController'=>array('showVipHome')
			);
						

						
<span style="color:yellow"># If member is denied, reroute to the following routes.</span>
$acl['member']['failRoute'] = array(
				<span style="color:yellow">//if not found this will be used</span>
				'_default'=>'/error/member',	
				
				<span style="color:yellow">//if denied from sns banUser</span>
				'SnsController/banUser'=>'/error/member/sns/notAdmin', 
				
				'SnsController/showVipHome'=>'/error/member/sns/notVip', 
				'BlogController'=>'/error/member/blog/notAdmin' 
			);

			
        </pre>

        <p class="normal">You have to assign the rules to DooAcl in bootstrap.</p>
		<pre>
Doo::acl()->rules = $acl;

<span style="color:yellow"># The default route to be reroute to when resource is denied. If not set, 404 error will be displayed.</span>
Doo::acl()->defaultFailedRoute = '/error';
		</pre>
<br/>
		<h3>ACL methods</h3>
        <p class="normal">You can check against the ACL rules whenever you need. Both <strong>isAllowed</strong> and <strong>isDenied</strong> return true or false so that you can use both methods to do your auth logic.</p>
		<pre>
<span style="color:yellow"># Check if allowed.</span>
$this->acl()->isAllowed($role, $resource, $action);
Doo::acl()->isAllowed($role, $resource, $action);

<span style="color:yellow"># Check if denied.</span>
$this->acl()->isDenied($role, $resource, $action);
Doo::acl()->isDenied($role, $resource, $action);
		</pre>
<br/>
		<p class="normal">If you want the framework to automatically reroute the denied request, use <strong>process()</strong>. It will return the failRoute defined in acl.conf.php. Then, you can do this in methods you wish to authenticate.</p>
		<pre>
<span style="color:yellow"># Get $role from Session.</span>
if($rs = $this->acl()->process($role, 'resourceName', 'actionName' )){
	echo $role .' is not allowed for resourceName actionName';
	return $rs;
}

<span style="color:yellow"># Example usage in a method.</span>
class SnsController extends DooController{
	
	function banUser(){
		if($rs = $this->acl()->process($role, __CLASS__, __METHOD__ )){
			return $rs;
		}	
		
		//if is admin then continue to ban user.
	}
	
}
		</pre>		

<br/>
		<p class="normal">If you don't wish to check in every method, you can perform the check in <strong>beforeRun()</strong>. The method needs to have 2 parameters, <strong>$resource</strong> and <strong>$action</strong>.</p>
		<pre>

<span style="color:yellow"># Example usage in a method.</span>
class SnsController extends DooController{
	
	function beforeRun( $resource, $action ){
		//Get role from Sessions
		if($rs = $this->acl()->process($role, $resource, $action )){
			return $rs;
		}
	}
	
	function banUser(){		
		//if is admin then continue to ban user.
	}
	
}
		</pre>		

		
<p>Go ahead and <a class="file" href="http://doophp.com/download">download the code</a> to learn more!</p>
       <span class="totop"><a href="#top">BACK TO TOP</a></span>  
       </div>
	</body>
</html>
