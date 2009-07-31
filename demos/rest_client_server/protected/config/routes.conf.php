<?php
/**
 * Define your URI routes here.
 *
 * $route[Request Method][Uri ] = array( Controller class, action method, other options, etc. )
 *
 * RESTful api support, *=any request method, GET PUT POST DELETE
 * POST 	Create
 * GET      Read
 * PUT      Update, Create
 * DELETE 	Delete
 */
$route['*']['/'] = array('MainController', 'index');
$route['*']['/url'] = array('MainController', 'url');
$route['*']['/example'] = array('MainController', 'example');
$route['*']['/error'] = array('ErrorController', 'index');
$route['*']['/about'] = $route['*']['/'];

//----------- REST client routes ---------
$route['*']['/client/twitter/daily_trends'] = array('RestClientController', 'twitter_daily');
$route['*']['/client/twitter/follower'] = array('RestClientController', 'twitter_follower');
$route['*']['/client/twitter/post/:username/:password/:message'] = array('RestClientController', 'twitter_post');

$route['*']['/client/food/list/:id'] = array('RestClientController', 'foodById',
                                          'match'=>array('id'=>'/^\d+$/'),
                                          'extension'=>array('.json','.xml')
                                       );
$route['*']['/client/food/list/all.xml'] = array('RestClientController', 'foodAllXml');
$route['*']['/client/food/list/all.json'] = array('RestClientController', 'foodAllJson');
$route['*']['/client/food/new/:food/:type/:rating'] = array('RestClientController', 'foodCreateNew');
$route['*']['/client/food/edit/:id/:food/:type/:rating'] = array('RestClientController', 'foodUpdate');
$route['*']['/client/food/admin/:username/:password'] = array('RestClientController', 'foodAdmin');



//------ REST Server routes -------
$route['*']['/api/food/list/all.xml'] = array('RestServerController', 'listFood_xml');
$route['*']['/api/food/list/all.json'] = array('RestServerController', 'listFood_json');
$route['*']['/api/food/list/:id'] = array('RestServerController', 'listFoodById',
                                          'match'=>array('id'=>'/^\d+$/'),
                                          'extension'=>array('.json','.xml')
                                    );
                                    
$route['post']['/api/food/create'] = array('RestServerController', 'createFood');         //post only
$route['put']['/api/food/update'] = array('RestServerController', 'updateFood');         //put only
$route['delete']['/api/food/delete/:id'] = array('RestServerController', 'deleteFood');  //delete only

$route['*']['/api/failed/:msg'] = array('RestServerController', 'api_fail');         

//Http digest auth with Rest
$route['post']['/api/admin/dostuff'] = array('RestServerController', 'admin');

?>