<?php
include './protected/config/common.conf.php';
include './protected/config/routes.conf.php';
include './protected/config/db.conf.php';

#Just include this for production mode
//include $config['BASE_PATH'].'deployment/deploy.php';
include $config['BASE_PATH'].'Doo.php';
include $config['BASE_PATH'].'app/DooConfig.php';

# Uncomment for auto loading the framework classes.
/*function __autoload($classname){
	Doo::autoload($classname);
}*/
Doo::conf()->set($config);
include $config['BASE_PATH'].'diagnostic/debug.php';

# database usage
//Doo::useDbReplicate();	#for db replication master-slave usage
Doo::db()->setMap($dbmap);
Doo::db()->setDb($dbconfig, $config['APP_MODE']);
Doo::db()->sql_tracking = true;	#for debugging/profiling purpose

Doo::app()->route = $route;

# Uncomment for DB profiling
//Doo::logger()->beginDbProfile('doowebsite');
Doo::app()->run();
//Doo::logger()->endDbProfile('doowebsite');
//Doo::logger()->rotateFile(20);
//Doo::logger()->writeDbProfiles();
?>