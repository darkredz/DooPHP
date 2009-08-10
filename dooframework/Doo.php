<?php
/**
 * Doo class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 * @version $Id: DooWebApp.php 1000 2009-06-22 18:27:22
 * @package doo
 * @since 1.0
 */

/**
 * Doo is a singleton class serving common framework functionalities.
 *
 * You can access Doo in every class to retrieve configuration settings,
 * DB connections, application properties, logging, loader utilities and etc.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: Doo.php 1000 2009-07-7 18:27:22
 * @package doo
 * @since 1.0
 */
class Doo{
    private static $_app;
    private static $_conf;
    private static $_logger;
    private static $_db;
	private static $_cache;

	/**
	 * @return DooConfig configuration settings defined in <i>common.conf.php</i>, auto create if the singleton has not been created yet.
	 */
    public static function conf(){
        if(self::$_conf===NULL){
            self::$_conf = new DooConfig;
        }
        return self::$_conf;
    }

	/**
	 * @return DooWebApp the application singleton, auto create if the singleton has not been created yet.
	 */
    public static function app(){
        if(self::$_app===NULL){
            self::loadCore('app/DooWebApp');
            self::$_app = new DooWebApp;
        }
        return self::$_app;
    }

	/**
	 * @return DooSqlMagic the database singleton, auto create if the singleton has not been created yet.
	 */
    public static function db(){
        if(self::$_db===NULL){
            self::loadCore('db/DooSqlMagic');
            self::$_db = new DooSqlMagic;
        }

        if(!self::$_db->connected)
            self::$_db->connect();

        return self::$_db;
    }

	/**
	 * @return DooLog logging tool for logging, tracing and profiling, singleton, auto create if the singleton has not been created yet.
	 */
	public static function logger(){
        if(self::$_logger===NULL){
            self::loadCore('logging/DooLog');
            self::$_logger = new DooLog(self::conf()->DEBUG_ENABLED);
        }
        return self::$_logger;
	}

	/**
	 * @param string $cacheType Cache type, file, apc, memcache. Default is file based cache.
	 * @return DooCache file/apc/memcache caching tool, singleton, auto create if the singleton has not been created yet.
	 */
	public static function cache($cacheType='file') {
		if($cacheType=='file'){
			if(isset(self::$_cache['file']))
				return self::$_cache['file'];
				
			self::loadCore('cache/DooCache');
			self::loadCore('cache/DooFileCache');
			self::$_cache['file'] = DooFileCache::cache();
			return self::$_cache['file'];
		}
		else if($cacheType=='apc'){
			if(isset(self::$_cache['apc']))
				return self::$_cache['apc'];
				
			self::loadCore('cache/DooCache');
			self::loadCore('cache/DooApcCache');
			self::$_cache['apc'] = DooApcCache::cache();
			return self::$_cache['apc'];
		}
		//settings for cache done in common.conf.php
		/*
		 * $config['CACHE_PATH'] = '/var/cache/';   //This is for file based cache only.
		 * $config['APCCACHE'] = array('settings'=>'some values');   //This is for APC cache
		 * $config['MEMCACHE'] = array('settings'=>'some values');   //This is for fMemcache.
		 * use in code. Doo::cache()->set()    Doo::cache('apc')->set()     Doo::cache('memcache')->set()
		 */
	}
	
	/**
     * Imports the definition of class(es) and tries to create an object/a list of objects of the class.
     * @param string|array $class_name Name(s) of the class to be imported
     * @param string $path Path to the class file
     * @param bool $createObj Determined whether to create object(s) of the class
	 * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object of the class name passed in.
	 */
	protected static function load($class_name, $path, $createObj=FALSE){
        if(is_string($class_name)){
    		require_once($path . "$class_name.php");
            if($createObj)
                return new $class_name;
        }else{
            //if not string, then a list of Class name, require them all.
            if($createObj)
                $obj=array();

            foreach ($class_name as $one) {
            	require_once($path . "$one.php");
                if($createObj)
                    $obj[] = new $one;
            }

            if($createObj)
                return $obj;
        }
	}

	/**
     * Imports the definition of User defined class(es). Class file is located at <b>SITE_PATH/protected/class/</b>
     * @param string|array $class_name Name(s) of the class to be imported
     * @param bool $createObj Determined whether to create object(s) of the class
	 * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
	 */
	public static function loadClass($class_name, $createObj=FALSE){
        return self::load($class_name, self::conf()->SITE_PATH ."protected/class/", $createObj);
	}

	/**
     * Imports the definition of Controller class. Class file is located at <b>SITE_PATH/protected/controller/</b>
     * @param string $class_name Name of the class to be imported
	 */
    public static function loadController($class_name){
		require_once(self::conf()->SITE_PATH ."protected/controller/$class_name.php");
	}

	/**
     * Imports the definition of Model class(es). Class file is located at <b>SITE_PATH/protected/model/</b>
     * @param string|array $class_name Name(s) of the Model class to be imported
     * @param bool $createObj Determined whether to create object(s) of the class
	 * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
	 */
	public static function loadModel($class_name, $createObj=FALSE){
		return self::load($class_name, self::conf()->SITE_PATH ."protected/model/", $createObj);
	}

	/**
     * Imports the definition of Helper class(es). Class file is located at <b>BASE_PATH/protected/helper/</b>
     * @param string|array $class_name Name(s) of the Helper class to be imported
     * @param bool $createObj Determined whether to create object(s) of the class
	 * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
	 */
	public static function loadHelper($class_name, $createObj=FALSE){
        return self::load($class_name, self::conf()->BASE_PATH ."helper/", $createObj);
	}

	/**
     * Imports the definition of Doo framework core class. Class file is located at <b>BASE_PATH</b>.
     * @example If the file is in a package, called <code>loadCore('auth/DooLog')</code>
     * @param string $class_name Name of the class to be imported
	 */
	public static function loadCore($class_name){
		require_once(self::conf()->BASE_PATH ."$class_name.php");
	}
	
	/**
	 * Provides auto loading feature. To be used with the Magic method __autoload
	 * @param string $classname Class name to be loaded.
	 */
	public static function autoload($classname){
		$class['DooSiteMagic'] = 'app/DooSiteMagic';
		$class['DooWebApp'] = 'app/DooWebApp';
		$class['DooConfig'] = 'app/DooConfig';
		$class['DooDigestAuth'] = 'auth/DooDigestAuth';
		$class['DooCache'] = 'cache/DooCache';
		$class['DooFileCache'] = 'cache/DooFileCache';
		$class['DooController'] = 'controller/DooController';
		$class['DooDbExpression'] = 'db/DooDbExpression';
		$class['DooModelGen'] = 'db/DooModelGen';
		$class['DooSqlMagic'] = 'db/DooSqlMagic';
		$class['DooRestClient'] = 'helper/DooRestClient';
		$class['DooUrlBuilder'] = 'helper/DooUrlBuilder';
		$class['DooLog'] = 'helper/DooLog';
		$class['DooLoader'] = 'uri/DooLoader';
		$class['DooUriRouter'] = 'uri/DooUriRouter';
		$class['DooView'] = 'view/DooView';
		
		if(isset($class[$classname]))
			self::loadCore($class[$classname]);
	}

    /**
     * Simple benchmarking. To used this, set <code>$config['START_TIME'] = microtime(true);</code> in <i>common.conf.php</i> .
     * @param bool $html To return the duration as string in HTML comment.
     * @return mixed Duration(sec) of the benchmarked process. If $html is True, returns string <!-- Generated in 0.002456 seconds -->
     */
    public static function benchmark($html=false){
        if(!isset(self::conf()->START_TIME)){
            return 0;
        }
        $duration = microtime(true) - self::conf()->START_TIME;
        if($html)
            return '<!-- Generated in ' . $duration . ' seconds -->';
        return $duration;
    }

	public static function powerby(){
		return 'Powered by <a href="http://www.doophp.com/">DooPHP Framework</a>.';
	}

	public static function version(){
		return '1.1';
	}
}

?>