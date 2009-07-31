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
$route['*']['/about'] = $route['*']['/home'] = $route['*']['/'];
$route['*']['/example'] = array('MainController', 'example');
$route['*']['/error'] = array('ErrorController', 'index');

//generate Models automatically
//$route['*']['/gen_model'] = array('MainController', 'gen_models', 'authName'=>'Model Generator', 'auth'=>array('admin'=>'1234'), 'authFail'=>'Unauthorized!');


?>