<?php
/**
 * DooConfig class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * DooConfig contains all the configuration for the system. This class should not be called directly.
 *
 * <p>Framework settings are all in capital letters. Settings can be accessed via <code>Doo::conf()->SOME_SETTINGS</code></p>
 *
 * <p>You can defined your own setting properties and access them throughout the application.
 * It is recommended to defined your properties in <b>lower case</b> for future compatibility.
 * eg. <code>$config['my_setting_here'] = 'example';</code>
 * </p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooConfig.php 1000 2009-07-7 18:27:22
 * @package doo.app
 * @since 1.0
 */
class DooConfig{
    /**
     * Path to the location of your project directory.eg. /var/www/myproject/
     * @var string
     */
    var $SITE_PATH;

    /**
     * Path to the location of Doo framework directory. eg. /var/lib/dooframework/
     * @var string 
     */
    var $BASE_PATH;

    /**
     * Path to store the log files.
     * Recommended to put outside the web root directory where others cannot access through the web .eg. /var/mylogs/
     * @var string
     */
    var $LOG_PATH;

    /**
     * URL of your app. eg. http://localhost/doophp/
     * @var string 
     */
    var $APP_URL;

    /**
     * Please define SUBFOLDER if your app is not in the root directory of the domain. eg. http://localhost/doophp , you should set '/doophp/'
     * @var string 
     */
    var $SUBFOLDER;

    /**
     * Application mode(<b>dev</b>, <b>prod</b>). In dev mode, view templates are always checked and compiled
     * @var string 
     */
    var $APP_MODE;

    /**
     * Enable/disable Auto routing.
     * 
     * <p>Every controller can deny being accessed by auto routes
     * just by setting <code>public $autoroute = false;</code> in the Controller class.</p>
     * @var bool 
     */
    var $AUTOROUTE;

    /**
     * Enable/disable debug mode. If debug mode is on, debug trace will be logged.
     * Debug tool can be viewed if <code>Doo::logger()->showDebugger()</code> is called
     * @var bool 
     */
    var $DEBUG_ENABLED;

    /**
     * If defined, the document specified will be included when a 404 header is sent (route not found).
     * @var string 
     */
    var $ERROR_404_DOCUMENT;

    /**
     * If defined, the route specified will be executed when a 404 header is sent (route not found).
     * If ERROR_404_DOCUMENT is defined, then the document would be loaded instead.
     * @var string
     */
    var $ERROR_404_ROUTE;

    /**
     * Path where the cache files are stored. If not defined, caches are stored in SITE_PATH/protected/cache/
     * @var string
     */
    var $CACHE_PATH;

    /**
     * Set the configurations. SITE_PATH, BASE_PATH and APP_URL is required
     * @param array $confArr associative array of the configs.
     */
    public function set($confArr){
        foreach($confArr as $k=>$v){
            $this->{$k} = $v;
        }
        
        if($this->SUBFOLDER==NULL)
           $this->SUBFOLDER='/';

        if($this->AUTOROUTE==NULL)
           $this->AUTOROUTE=FALSE;

        if($this->DEBUG_ENABLED==NULL)
           $this->DEBUG_ENABLED=FALSE;

    }
}
?>