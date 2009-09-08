<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DooPHP Blog Demo - Comment Submitted</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="Shortcut Icon" href="http://doophp.com/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo $data['rootUrl']; ?>global/css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $data['rootUrl']; ?>global/css/demo.css" media="screen" />


</head>
<body>

<div id="wrap">

<?php include "top.php"; ?>

<div id="content">
    <div class="left">
        <div class="articles">
        <h2>Comment Saved!</h2>
        <p>Your comment is submitted and awaiting for approval before it is listed in the blog page.</p>
        </div>
    </div>

    <div class="right">
        <?php include "blog_sidebar.php"; ?>
    </div>

    <div style="clear: both;"> </div>
</div>

<div id="bottom"> </div>

    <div id="footer">
        Powered by <a href="http://www.doophp.com/">DooPHP framework</a>, for educational purpose.
    </div>
</div>

</body>
</html>