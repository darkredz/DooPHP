<?php
include './protected/config/common.conf.php';
include './protected/config/routes.conf.php';
//include './protected/config/db.conf.php';


include $config['BASE_PATH'].'Doo.php';
include $config['BASE_PATH'].'app/DooConfig.php';

# Uncomment for auto loading the framework classes.
//spl_autoload_register('Doo::autoload');

Doo::conf()->set($config);

# remove this if you wish to see the normal PHP error view.
include $config['BASE_PATH'].'diagnostic/debug.php';

Doo::loadConstants();
spl_autoload_register(array('Doo','loader'));

Doo::app()->route = $route;

Doo::app()->run();
?>