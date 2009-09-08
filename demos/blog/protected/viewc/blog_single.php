<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DooPHP Blog Demo - <?php echo $data['post']->title; ?></title>
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

        <h2><a href="<?php echo $data['rootUrl']; ?>article/<?php echo $data['post']->id; ?>"><?php echo $data['post']->title; ?></a></h2>
        <div class="articles">
            <?php echo $data['post']->content; ?>
            <div class="tagContainer">
                <strong>Tags: </strong>
                <?php foreach($data['post']->Tag as $k1=>$v1): ?>
                <span class="tag"><a href="<?php echo $data['rootUrl']; ?>tag/<?php echo $v1->name; ?>"><?php echo $v1->name; ?></a></span>
                <?php endforeach; ?>
            </div>
            <em class="datePosted">Posted on <?php echo formatDate($data['post']->createtime); ?></em>
        </div>

        <hr class="divider"/>
        <div id="comments" name="comments">
          <?php if( isset($data['comments']) ): ?>
            <strong>Total Comments (<?php echo $data['post']->totalcomment; ?>)</strong><br/><br/>
              <?php foreach($data['comments'] as $k1=>$v1): ?>

                  <span id="comment<?php echo $v1->id; ?>" name="comment<?php echo $v1->id; ?>" style="font-weight:bold;">
                  <?php if( !empty($v1->url) ): ?>
                      <a href="<?php echo $v1->url; ?>"><?php echo $v1->author; ?></a>
                  <?php else: ?>
                      <?php echo $v1->author; ?>
                  <?php endif; ?>
                  </span> said on <em><?php echo formatDate($v1->createtime, 'd M, y h:i:s A'); ?></em>,<br/>

                  <div class="commentItem"><?php echo $v1->content; ?></div><br/>

              <?php endforeach; ?>
            <hr class="divider"/>
          <?php endif; ?>
        </div>

        <p><strong>Leave a comment :)</strong></p>
        <form method="POST" action="<?php echo $data['rootUrl']; ?>comment/submit">
            <input type="hidden" name="post_id" value="<?php echo $data['post']->id; ?>"/>
            <span class="field"><span class="commentInput">Name*:</span><input type="text" name="author" size="32"/></span>
            <span class="field"><span class="commentInput">Email*:</span><input type="text" name="email" size="32"/></span>
            <span class="field"><span class="commentInput">Website:</span><input type="text" name="url" value="http://" size="32"/></span>
            <span class="field"><span class="commentInput">Content*:</span><textarea cols="45" rows="6" name="content"></textarea></span>
            <span class="field"><span class="commentInput">&nbsp;</span><input type="submit" value="Send Comment"/></span>
        </form>
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