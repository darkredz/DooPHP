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
 $admin = array('admin'=>'1234');
 
$route['*']['/'] = array('MainController', 'index');

//generate Controllers automatically
$route['*']['/gen_site'] = array('MainController', 'gen_site', 'authName'=>'Model Generator', 'auth'=>$admin, 'authFail'=>'Unauthorized!');

//generate Models automatically
$route['*']['/gen_model'] = array('MainController', 'gen_model', 'authName'=>'Model Generator', 'auth'=>$admin, 'authFail'=>'Unauthorized!');


?>
