<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DooPHP Blog Demo</title>
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

     <?php foreach($data['posts'] as $k1=>$v1): ?>
        <h2><a href="<?php echo $data['rootUrl']; ?>article/<?php echo $v1->id; ?>"><?php echo $v1->title; ?></a></h2>
        <div class="articles">
            <?php echo shorten($v1->content); ?>
            <div class="tagContainer">
                <strong>Tags: </strong>
                <?php foreach($v1->Tag as $k2=>$v2): ?>
                <span class="tag"><a href="<?php echo $data['rootUrl']; ?>tag/<?php echo $v2->name; ?>"><?php echo $v2->name; ?></a></span>
                <?php endforeach; ?>
            </div>
            <em class="datePosted">&nbsp;<a href="<?php echo $data['rootUrl']; ?>article/<?php echo $v1->id; ?>#comments" style="text-decoration:none;">Comments (<?php echo $v1->totalcomment; ?>)</a> | Posted on <?php echo formatDate($v1->createtime); ?></em>
        </div>
        <hr class="divider"/>
    <?php endforeach; ?>
    <div><?php echo $data['pager']; ?></div>

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