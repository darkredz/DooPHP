
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "{$data['header']}.php"; ?> 
    </head>
	<body>
      <?php include "{$data['nav']}.php"; ?>
	  <div class="content">
	  	<h1>这是i18n演示</h1>
        <p class="normal">这是中文</p>

        <p class="normal">这是一个变数</p>
        <ol>
            <li><?php echo t($data['dynamic_msg']); ?></li>
            <li><?php echo t2($data['dynamic_key_msg']); ?></li>
            <li><?php echo t2($data['dynamic_key_msg2']); ?></li>
        </ol>
	  </div>
	</body>
</html>