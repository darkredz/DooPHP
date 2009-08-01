
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "{$data['header']}.php"; ?>
    </head>
	<body>
      <?php include "{$data['nav']}.php"; ?>
	  <div class="content">
	  	<h1>This is a i18n demo</h1>
        <p class="normal">This is English.</p>  

        <p class="normal">This is a variable</p>
        
        <ol>
            <li><?php echo t($data['dynamic_msg']); ?></li>
            <li><?php echo t2($data['dynamic_key_msg']); ?></li>
            <li><?php echo t2($data['dynamic_key_msg2']); ?></li>
        </ol>
	  </div> 
	</body>
</html>