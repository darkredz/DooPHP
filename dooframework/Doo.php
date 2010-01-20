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
    protected static $_app;
    protected static $_conf;
    protected static $_logger;
    protected static $_db;
    protected static $_useDbReplicate;
    protected static $_cache;
    protected static $_acl;
	protected static $_session;
	protected static $_translator;

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
     * @return DooAcl the application ACL singleton, auto create if the singleton has not been created yet.
     */
    public static function acl(){
        if(self::$_acl===NULL){
            self::loadCore('auth/DooAcl');
            self::$_acl = new DooAcl;
        }
        return self::$_acl;
    }

    /**
     * Call this method to use database replication instead of a single db server.
     */
    public static function useDbReplicate(){
        self::$_useDbReplicate = true;
    }

    /**
     * @return DooSqlMagic the database singleton, auto create if the singleton has not been created yet.
     */
    public static function db(){
        if(self::$_db===NULL){
            if(self::$_useDbReplicate===NULL){
                self::loadCore('db/DooSqlMagic');
                self::$_db = new DooSqlMagic;
            }else{
                self::loadCore('db/DooMasterSlave');
                self::$_db = new DooMasterSlave;
            }
        }

        if(!self::$_db->connected)
            self::$_db->connect();

        return self::$_db;
    }

    /**
     * @return DooSession
     */
    public static function session($namespace = null){
        if(self::$_session===NULL){
            self::loadCore('session/DooSession');
            self::$_session = new DooSession($namespace);
        }
        return self::$_session;
    }
	
	/**
     * @return true/false according to cache system being installed
     */
    public static function cacheSession($prefix = 'dooSession/', $type='file'){
		$cache = self::cache($type);
		self::loadCore('session/DooCacheSession');
		return DooCacheSession::installOnCache($cache, $prefix);
    }

	 /**
	  * @return DooTranslator
	  */
    public static function translator($adapter, $data, $options=array()) {
        if(self::$_translator===NULL){
            self::loadCore('translate/DooTranslator');
            self::$_translator = new DooTranslator($adapter, $data, $options);
        }
        return self::$_translator;
    }

	/**
	 * Simple accessor to Doo Translator class. You must be sure you have initialised it before calling. See translator(...)
	 * @return DooTranslator
	 */
	public static function getTranslator() {
		return self::$_translator;
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
     * @param string $cacheType Cache type: file, php, front, apc, memcache, xcache, eaccelerator. Default is file based cache.
     * @return DooFileCache|DooPhpCache|DooFrontCache|DooApcCache|DooMemCache|DooXCache|DooEAcceleratorCache file/php/apc/memcache/xcache/eaccelerator & frontend caching tool, singleton, auto create if the singleton has not been created yet.
     */
    public static function cache($cacheType='file') {
        if($cacheType=='file'){
            if(isset(self::$_cache['file']))
                return self::$_cache['file'];

            self::loadCore('cache/DooFileCache');
            self::$_cache['file'] = new DooFileCache;
            return self::$_cache['file'];
        }
        else if($cacheType=='php'){
            if(isset(self::$_cache['php']))
                return self::$_cache['php'];

            self::loadCore('cache/DooPhpCache');
            self::$_cache['php'] = new DooPhpCache;
            return self::$_cache['php'];
        }
        else if($cacheType=='front'){
            if(isset(self::$_cache['front']))
                return self::$_cache['front'];

            self::loadCore('cache/DooFrontCache');
            self::$_cache['front'] = new DooFrontCache;
            return self::$_cache['front'];
        }
        else if($cacheType=='apc'){
            if(isset(self::$_cache['apc']))
                return self::$_cache['apc'];

            self::loadCore('cache/DooApcCache');
            self::$_cache['apc'] = new DooApcCache;
            return self::$_cache['apc'];
        }
        else if($cacheType=='xcache'){
            if(isset(self::$_cache['xcache']))
                return self::$_cache['xcache'];

            self::loadCore('cache/DooXCache');
            self::$_cache['xcache'] = new DooXCache;
            return self::$_cache['xcache'];
        }
        else if($cacheType=='eaccelerator'){
            if(isset(self::$_cache['eaccelerator']))
                return self::$_cache['eaccelerator'];

            self::loadCore('cache/DooEAcceleratorCache');
            self::$_cache['eaccelerator'] = new DooEAcceleratorCache;
            return self::$_cache['eaccelerator'];
        }
        else if($cacheType=='memcache'){
            if(isset(self::$_cache['memcache']))
                return self::$_cache['memcache'];

            self::loadCore('cache/DooMemCache');
            self::$_cache['memcache'] = new DooMemCache(Doo::conf()->MEMCACHE);
            return self::$_cache['memcache'];
        }
    }

    /**
     * Imports the definition of class(es) and tries to create an object/a list of objects of the class.
     * @param string|array $class_name Name(s) of the class to be imported
     * @param string $path Path to the class file
     * @param bool $createObj Determined whether to create object(s) of the class
     * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object of the class name passed in.
     */
    protected static function load($class_name, $path, $createObj=FALSE){
        if(is_string($class_name)===True){
            class_exists($class_name)===True || require_once($path . "$class_name.php");
            if($createObj)
                return new $class_name;
        }else if(is_array($class_name)===True){
            //if not string, then a list of Class name, require them all.
            //make sure the class_name has array with is_array
            if($createObj)
                $obj=array();

            foreach ($class_name as $one) {
                class_exists($class_name)===True || require_once($path . "$one.php");
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
        return self::load($class_name, self::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "class/", $createObj);
    }

    /**
     * Imports the definition of Controller class. Class file is located at <b>SITE_PATH/protected/controller/</b>
     * @param string $class_name Name of the class to be imported
     */
    public static function loadController($class_name){
        require_once self::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'controller/'.$class_name.'.php';
    }

    /**
     * Imports the definition of Model class(es). Class file is located at <b>SITE_PATH/protected/model/</b>
     * @param string|array $class_name Name(s) of the Model class to be imported
     * @param bool $createObj Determined whether to create object(s) of the class
     * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
     */
    public static function loadModel($class_name, $createObj=FALSE){
        return self::load($class_name, self::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "model/", $createObj);
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
        require_once self::conf()->BASE_PATH ."$class_name.php";
    }

    /**
     * Imports the definition of Model class(es) in a certain module or from the main app.
     *
     * @param string|array $class_name Name(s) of the Model class to be imported
     * @param string $path module folder name. Default is the main app folder.
     * @param bool $createObj Determined whether to create object(s) of the class
     * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
     */
    public static function loadModelAt($class_name, $moduleFolder=Null, $createObj=FALSE){
        $moduleFolder = ($moduleFolder===Null) ? Doo::conf()->PROTECTED_FOLDER_ORI : Doo::conf()->PROTECTED_FOLDER_ORI . 'module/' . $moduleFolder;
        return self::load($class_name, self::conf()->SITE_PATH . $moduleFolder . "/model/", $createObj);
    }

    /**
     * Imports the definition of Controller class(es) in a certain module or from the main app.
     *
     * @param string|array $class_name Name(s) of the Controller class to be imported
     * @param string $path module folder name. Default is the main app folder.
     */
    public static function loadControllerAt($class_name, $moduleFolder=Null){
        $moduleFolder = ($moduleFolder===Null) ? Doo::conf()->PROTECTED_FOLDER_ORI : Doo::conf()->PROTECTED_FOLDER_ORI . 'module/' . $moduleFolder;
        require_once self::conf()->SITE_PATH . $moduleFolder . '/controller/'.$class_name.'.php';
    }

    /**
     * Imports the definition of User defined class(es) in a certain module or from the main app.
     *
     * @param string|array $class_name Name(s) of the class to be imported
     * @param string $path module folder name. Default is the main app folder.
     * @param bool $createObj Determined whether to create object(s) of the class
     * @return mixed returns NULL by default. If $createObj is TRUE, it creates and return the Object(s) of the class name passed in.
     */
    public static function loadClassAt($class_name, $moduleFolder=Null, $createObj=FALSE){
        $moduleFolder = ($moduleFolder===Null) ? Doo::conf()->PROTECTED_FOLDER_ORI : Doo::conf()->PROTECTED_FOLDER_ORI . 'module/' . $moduleFolder;
        return self::load($class_name, self::conf()->SITE_PATH . $moduleFolder. "/class/", $createObj);
    }

    /**
     * Loads template tag class from plugin directory for both main app and modules
     * 
     * @param string $class_name Template tag class name
     * @param string $moduleFolder Folder name of the module. If Null, the class will be loaded from main app.
     */
    public static function loadPlugin($class_name, $moduleFolder=Null){
        if($moduleFolder===Null){
            if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===True)
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'plugin/'. $class_name .'.php';
            else
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/'. $class_name .'.php';
        }else{
            require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI .'module/'. $moduleFolder .'/plugin/'. $class_name .'.php';
        }
    }
	
    /**
     * Provides auto loading feature. To be used with the Magic method __autoload
     * @param string $classname Class name to be loaded.
     */
    public static function autoload($classname){
        if(class_exists($classname)===True) return;
        $class['DooController'] = 'controller/DooController';
		$class['DooSiteMagic'] = 'app/DooSiteMagic';
        $class['DooDigestAuth'] = 'auth/DooDigestAuth';
        $class['DooAuth'] = 'auth/DooAuth';
        $class['DooAcl'] = 'auth/DooAcl';
        $class['DooFileCache'] = 'cache/DooFileCache';
        $class['DooPhpCache'] = 'cache/DooPhpCache';
        $class['DooFrontCache'] = 'cache/DooFrontCache';
        $class['DooApcCache'] = 'cache/DooApcCache';
        $class['DooMemCache'] = 'cache/DooMemCache';
        $class['DooXCache'] = 'cache/DooXCache';
        $class['DooEAcceleratorCache'] = 'cache/DooEAcceleratorCache';
        $class['DooDbExpression'] = 'db/DooDbExpression';
        $class['DooModelGen'] = 'db/DooModelGen';
        $class['DooSqlMagic'] = 'db/DooSqlMagic';
        $class['DooModel'] = 'db/DooModel';
        $class['DooSmartModel'] = 'db/DooSmartModel';
        $class['DooMasterSlave'] = 'db/DooMasterSlave';
        $class['DooRestClient'] = 'helper/DooRestClient';
        $class['DooUrlBuilder'] = 'helper/DooUrlBuilder';
        $class['DooTextHelper'] = 'helper/DooTextHelper';
        $class['DooValidator'] = 'helper/DooValidator';
        $class['DooForm'] = 'helper/DooForm';
        $class['DooMailer'] = 'helper/DooMailer';
        $class['DooPager'] = 'helper/DooPager';
        $class['DooGdImage'] = 'helper/DooGdImage';
		$class['DooBenchmark'] = 'helper/DooBenchmark';
        $class['DooLog'] = 'logging/DooLog';
        $class['DooLoader'] = 'uri/DooLoader';
        $class['DooView'] = 'view/DooView';
		$class['DooViewBasic'] = 'view/DooViewBasic';
		$class['DooSession'] = 'session/DooSession';
		$class['DooTranslator'] = 'translate/DooTranslator';

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
        return '1.3';
    }
}

?>