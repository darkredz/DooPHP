<?php

$route['*']['/'] = array('BlogController', 'home');

$route['*']['/page/:pindex'] = array('BlogController', 'page');

$route['*']['/article/:postId'] = array('BlogController', 'getArticle');

$route['*']['/archive/:year/:month'] = array('BlogController', 'getArchive');
$route['*']['/archive/:year/:month/page/:pindex'] = array('BlogController', 'getArchive');

$route['*']['/tag/:name'] = array('BlogController', 'getTag');
$route['*']['/tag/:name/page/:pindex'] = array('BlogController', 'getTag');

$route['*']['/comment/submit'] = array('BlogController', 'newComment');


//admin home page
$route['*']['/admin'] =
$route['*']['/admin/post'] = array('AdminController', 'home',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

//admin list posts pagination
$route['*']['/admin/page/:pindex'] =
$route['*']['/admin/post/page/:pindex'] = array('AdminController', 'page',
                                                'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

//admin list posts Sorting (asc/desc) and pagination
$route['*']['/admin/sort/:sortField/:orderType'] =
$route['*']['/admin/post/sort/:sortField/:orderType'] =
$route['*']['/admin/post/sort/:sortField/:orderType/page/:pindex'] = array('AdminController', 'sortBy',
                                                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

//admin edit Post
$route['*']['/admin/post/edit/:pid'] = array('AdminController', 'getArticle',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

$route['post']['/admin/post/save'] = array('AdminController', 'savePostChanges',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');


//admin create Post
$route['*']['/admin/post/create'] = array('AdminController', 'createPost',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

$route['post']['/admin/post/saveNew'] = array('AdminController', 'saveNewPost',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

//admin delete Post
$route['*']['/admin/post/delete/:pid'] = array('AdminController', 'deletePost',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

//admin list unapproved comments
$route['*']['/admin/comment'] = array('AdminController', 'listComment',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

$route['*']['/admin/comment/approve/:cid'] = array('AdminController', 'approveComment',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');

$route['*']['/admin/comment/reject/:cid'] = array('AdminController', 'rejectComment',
                              'authName'=>'Blog Admin', 'auth'=>array('admin'=>'1234'), 'authFailURL'=>'./error/loginFail');


//error displays
$route['*']['/error'] = array('ErrorController', 'defaultError');
$route['*']['/error/loginFail'] = array('ErrorController', 'loginError');
$route['*']['/error/postNotFound/:pid'] = array('ErrorController', 'postError');

?>