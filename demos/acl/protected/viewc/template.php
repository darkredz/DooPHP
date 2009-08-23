
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
	  <div class="content">
	  	<h1><?php echo $data['title']; ?></h1>
		<p><span class="boldy">Result: </span><?php echo $data['content']; ?></p>
		<p><?php echo debug($data['printr']); ?>
		</p>
       <span class="totop"><a href="javascript:history.back()">BACK TO LAST PAGE</a></span>
	  </div>
	</body>
</html>