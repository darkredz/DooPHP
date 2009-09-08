<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DooPHP Blog Admin</title>
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

        <h1>Manage Blog Posts</h1><br/>
        <p>You can sort by the fields' value. Click the title to edit.</p><br/>
        <div class="articles">
            <table>
              <tbody><tr>
                <th width="150"><a href="<?php echo $data['rootUrl']; ?>admin/post/sort/status/<?php echo $data['order']; ?>">Status</a></th>
                <th width="500"><a href="<?php echo $data['rootUrl']; ?>admin/post/sort/title/<?php echo $data['order']; ?>">Title</a></th>
                <th width="360"><a href="<?php echo $data['rootUrl']; ?>admin/post/sort/createtime/<?php echo $data['order']; ?>">Create Time</a></th>
              </tr>
              <?php foreach($data['posts'] as $k1=>$v1): ?>
              <tr class="trecord">
                <td><?php if( $v1->status==1 ): ?>Published<?php else: ?>Draft<?php endif; ?></td>
                <td><a href="<?php echo $data['rootUrl']; ?>admin/post/edit/<?php echo $v1->id; ?>"><?php echo $v1->title; ?></a></td>
                <td><?php echo formatDate($v1->createtime); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody></table>
        </div>

        <hr class="divider"/>
        <?php echo $data['pager']; ?>
    </div>

    <div class="right">
        <?php include "admin_sidebar.php"; ?>
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