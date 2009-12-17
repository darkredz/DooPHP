<?php
/**
 * Define your URI routes here.
 *
 * $route[Request Method][Uri] = array( Controller class, action method, other options, etc. )
 *
 * RESTful api support, *=any request method, GET PUT POST DELETE
 * POST 	Create
 * GET      Read
 * PUT      Update, Create
 * DELETE 	Delete
 *
 * Use lowercase for Request Method
 *
 * If you have your controller file name different from its class name, eg. home.php HomeController
 * $route['*']['/'] = array('HomeController', 'index', 'className'=>'HomeController');
 * 
 * If you need to reverse generate URL based on route ID with DooUrlBuilder in template view, please defined the id along with the routes
 * $route['*']['/'] = array('HomeController', 'index', 'id'=>'home');
 *
 * If you need dynamic routes on root domain, such as http://facebook.com/username
 * Use the key 'root':  $route['*']['root']['/:username'] = array('UserController', 'showProfile');
 *
 * If you need to catch unlimited parameters at the end of the url, eg. http://localhost/paramA/paramB/param1/param2/param.../.../..
 * Use the key 'catchall': $route['*']['catchall']['/:first'] = array('TestController', 'showAllParams');
 */
$route['*']['/'] = array('MainController', 'index');
$route['*']['/url'] = array('MainController', 'url');
$route['*']['/example'] = array('MainController', 'example');

$route['*']['/simple'] = array('SimpleController', 'simple');
$route['*']['/simple.html'] = array('SimpleController', 'simple');
$route['*']['/simple.rss'] = array('SimpleController', 'simple');
$route['*']['/simple.json'] = array('SimpleController', 'simple');
$route['*']['/simple/:pagename'] = array('SimpleController', 'simple', 'extension'=>array('.json','.rss'));
$route['*']['/simple/only_xml/:pagename'] = array('SimpleController', 'simple', 'extension'=>'.xml');

$route['*']['/api/food/list/:id'] = array('RestController', 'listFood','extension'=>array('.json','.xml'));
$route['post']['/api/food/create'] = array('RestController', 'createFood');         //post only
$route['put']['/api/food/update'] = array('RestController', 'updateFood');         //put only
$route['delete']['/api/food/delete/:id'] = array('RestController', 'deleteFood');     //delete only

//here's how you do redirection to an existing route internally
//http status code is optional, default 302 Moved Temporarily
$route['*']['/about'] = $route['*']['/home'] = $route['*']['/'];
$route['*']['/easy'] = array('redirect', './simple.html');
$route['*']['/easier'] = array('redirect', './simple.html', 301);
$route['*']['/doophp'] = array('redirect', 'http://doophp.com/');


//Http digest auth and subfolder example
$route['*']['/admin'] = array('admin/AdminController', 'index',
                              'authName'=>'Food Api Admin',
                              'auth'=>array('admin'=>'1234', 'demo'=>'abc'),
//                              'authFailURL'=>'/admin/fail');
                            'authFail'=>'Please login to the admin site!');


//parameters matching example
$route['*']['/news/:year/:month'] = array('NewsController', 'show_news_by_year_month',
                                            'match'=>array(
                                                        'year'=>'/^\d{4}$/',
                                                        'month'=>'/^\d{2}$/'
                                                     )
                                         );

//almost identical routes examples, must assigned a matching pattern to the parameters
//if no pattern is assigned, it will match the route defined first.
$route['*']['/news/:id'] = array('NewsController', 'show_news_by_id',
                                    'match'=>array('id'=>'/^\d+$/'));
$route['*']['/news/id/:id'] = $route['*']['/news/:id']; //here's how you do redirection to an existing route internally

$route['*']['/news/:title'] = array('NewsController', 'show_news_by_title',
                                    'match'=>array('title'=>'/[a-z0-9]+/'));



?>