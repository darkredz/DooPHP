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

//----------- DB routes ---------
//$route['*']['/gen_model'] = array('MainController', 'gen_models', 'authName'=>'Model Generator', 'auth'=>array('admin'=>'1234'), 'authFail'=>'Unauthorized!');

$route['*']['/enhanced'] = array('MainController', 'test');

$route['*']['/food/all'] =  array('DbController', 'allFood');
$route['*']['/food_with_type'] =  array('DbController', 'all_food_with_type');
$route['*']['/food_with_ingredients'] =  array('DbController', 'food_with_ingredients');
$route['*']['/ingredients_with_food'] =  array('DbController', 'ingredients_with_food');
$route['*']['/type_with_food'] =  array('DbController', 'foodtype_with_its_food');
$route['*']['/type_with_food/matched'] =  array('DbController', 'foodtype_with_its_food_matched');

$route['*']['/nasilemak_type_&_article'] =  array('DbController', 'nasilemak_type_article');

$route['*']['/food_&_type_by_name/:foodname'] =  array('DbController', 'food_with_type_by_name');
$route['*']['/recipe/:foodname'] =  array('DbController', 'get_recipe_using_model');
$route['*']['/food_by_id/:id'] =  array('DbController', 'get_food_by_id_using_model');
$route['*']['/article_by_food/:foodname'] =  array('DbController', 'get_articles_by_foodname');
$route['*']['/article_by_food_desc/:foodname'] =  array('DbController', 'get_articles_by_foodname_desc');

$route['*']['/article_by_food_desc_published/:foodname'] =  array('DbController', 'get_articles_by_foodname_desc_not_draft');


$route['*']['/food/insert/:foodtype/:foodname/:location/:desc'] =  array('DbController', 'insertFood');
$route['*']['/food/insert/:foodtype/:foodname/:location/:desc/:ingredient'] =  array('DbController', 'insert_food_with_ingredient');

$route['*']['/food_location_ingredient_edit/:foodname/:location/:ingredient'] =  array('DbController', 'food_ingredients_update');


$route['*']['/db/failed/:msg'] = array('DbController', 'db_error');

?>