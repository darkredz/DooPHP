
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "{$data['header']}.php"; ?>
    </head>
	<body>
      <?php include "{$data['nav']}.php"; ?>
	  <div class="content">
	  	<h1><?php echo t($data['title']); ?></h1>
		<p><span class="boldy">Ergebnis: </span><?php echo t($data['content']); ?></p>
       <span class="totop"><a href="javascript:history.back()">Zurück zur vorherigen Seite</a></span>
	  </div>
	</body>
</html>