
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
	  <div class="content">
	  	<h1>Welcome to ACL Demo Home</h1>
		<p class="normal">Here you can test and learn how to use Access Control List feature in DooPHP. You can use it along with your own authentication methods.</p>
		<p class="normal">ACL rules are defined in SITE_PATH/protected/config/acl.conf.php</p>
		<p class="boldy">Below is the skeleton of a social network site:</p>
		
		<h2>Welcome to My SNS!</h2>
		<?php if( $data['user'] == Null ): ?>
		<form action="<?php echo $data['baseurl']; ?>index.php/login" method="post">
			<div style="float:left">
				<span class="normal">Username:</span>
				<input name="username" type="text" style="width:230px;" maxlength="64" value=""/>
				<br/><span class="normal">Password:</span>
				<input name="password" type="password" style="width:230px;" maxlength="64" value=""/>
				<br/>
			</div>
			<div style="float:left">
				<input type="submit" value="Login" style="margin-left:10px;margin-top:5px;width:90px;height:60px;" />
			</div>
		</form>
		<div  style="clear:both;"></div>
		<p style="font-size:50%">Members: demo, david<br/>Admin: admin<br/>VIP: doophp
		<br/><br/>All passwords are <strong>1234</strong></p>
		<?php else: ?>
		<form action="<?php echo $data['baseurl']; ?>index.php/logout" method="post">
			<p class="normal">Hi, <strong><?php echo $data['user']['username']; ?></strong>! You are a <?php echo $data['user']['group']; ?> <input type="submit" value="Logout"/></p>			
		</form>
		<?php endif; ?>
		
		<p class="normal">All can access these pages:</p>
		<ul style="font-weight:bold;font-size:80%;">
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns">Home</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/blog">Read Blog</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/about">About Us</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/contact">Contact Us</a></li>
		</ul>
		
		<p class="normal">Members Only:</p>
		<ul style="font-weight:bold;font-size:80%;">
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/games">Play games</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/blog/comments">Post blog comments</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/people/john">View friend profile</a></li>
		</ul>
		
		<p class="normal">Admin Only:</p>
		<ul style="font-weight:bold;font-size:80%;">
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/blog/write">Write official blog</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/ban">Ban user</a></li>
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/blog/comments/delete">Delete comments</a></li>
		</ul>
		
		<p class="normal">Paid VIP Only:</p>
		<ul style="font-weight:bold;font-size:80%;">
			<li class="file"><a href="<?php echo $data['baseurl']; ?>index.php/sns/vip/lounge">Super fun!</a></li>
		</ul>
		
		<p class="boldy"><a name="extension_name" id="extension_name"></a>Click on the links to try!</p>

       <span class="totop"><a href="#top">BACK TO TOP</a></span>
	  </div>
	</body>
</html>