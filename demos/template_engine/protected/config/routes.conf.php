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

//----------- Template routes ---------
$route['*']['/template.html'] = array('MainController', 'template_source');

?>