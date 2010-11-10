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
    public $SITE_PATH;

    /**
     * Name of the protected folder where all the application scripts located. Default 'protected/'
     * @var string
     */
    public $PROTECTED_FOLDER = 'protected/';


    /**
     * Path to the location of Doo framework directory. eg. /var/lib/dooframework/
     * @var string
     */
    public $BASE_PATH;

    /**
     * Path to store the log files.
     * Recommended to put outside the web root directory where others cannot access through the web .eg. /var/mylogs/
     * @var string
     */
    public $LOG_PATH;

    /**
     * URL of your app. eg. http://localhost/doophp/
     * @var string
     */
    public $APP_URL;

    /**
     * Please define SUBFOLDER if your app is not in the root directory of the domain. eg. http://localhost/doophp , you should set '/doophp/'
     * @var string
     */
    public $SUBFOLDER;

    /**
     * Application mode(<b>dev</b>, <b>prod</b>). In dev mode, view templates are always checked and compiled
     * @var string
     */
    public $APP_MODE;

    /**
     * Enable/disable Auto routing.
     *
     * <p>Every controller can deny being accessed by auto routes
     * just by setting <code>public $autoroute = false;</code> in the Controller class.</p>
     * @var bool
     */
    public $AUTOROUTE;

    /**
     * Enable/disable debug mode. If debug mode is on, debug trace will be logged.
     * Debug tool can be viewed if <code>Doo::logger()->showDebugger()</code> is called
     * @var bool
     */
    public $DEBUG_ENABLED;

    /**
     * If defined, the document specified will be included when a 404 header is sent (route not found).
     * @var string
     */
    public $ERROR_404_DOCUMENT;

    /**
     * If defined, the route specified will be executed when a 404 header is sent (route not found).
     * If ERROR_404_DOCUMENT is defined, then the document would be loaded instead.
     * @var string
     */
    public $ERROR_404_ROUTE;

    /**
     * Path where the cache files are stored. If not defined, caches are stored in SITE_PATH/protected/cache/
     * @var string
     */
    public $CACHE_PATH;

    /**
     * Auto view render path based on URI(defined route) or resource and action string parts in URI(autoroute)
     * @var string|array
     */
    public $AUTO_VIEW_RENDER_PATH;

    /**
     * Settings for Memcache servers, defined in arrays: array(host, port, persistent, weight)
     * <code>
     * // host, port, persistent, weight
     * $config['MEMCACHE'] = array(
     *                       array('192.168.1.31', '11211', true, 40),
     *                       array('192.168.1.23', '11211', true, 80)
     *                     );
     * </code>
     * @var array
     */
    public $MEMCACHE;

	/**
	 * The template engine to use by default
	 * Options are: DooView and DooBasicView
	 */
	public $TEMPLATE_ENGINE;

    /**
     * Output/processed comments block in the template files.
     * @var bool
     */
    public $TEMPLATE_SHOW_COMMENT;

    /**
     * Allow, deny or parse native PHP usage in templates.
     * @var bool
     */
    public $TEMPLATE_ALLOW_PHP;

    /**
     * Always compile template files if true
     * @var bool
     */
    public $TEMPLATE_COMPILE_ALWAYS;

    /**
     * List of template tags to be used globally in DooView template engine ( global function names and PHP functions )
     * @var array
     */
    public $TEMPLATE_GLOBAL_TAGS;

	/**
	 * Path to model files. Allows you to share models between apps
	 * @param string
	 */
	public $MODEL_PATH;

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

		if ($this->TEMPLATE_ENGINE==NULL)
			$this->TEMPLATE_ENGINE='DooView';

		if ($this->MODEL_PATH==NULL)
			$this->MODEL_PATH=$this->SITE_PATH . $this->PROTECTED_FOLDER . 'model/';

    }

    /**
     * Add data to be retrieved later on. Served as a dummy storage.
     *
     * Example usage:
     * <code>
     * //Store
     * Doo::conf()->add('latestItem', $item);
     *
     * //Retrieve
     * echo Doo::conf()->latestItem;
     * </code>
     *
     * @param mixed $key Key o
     * @param  $value
     */
    public function add($key, $value){
        $this->{$key} = $value;
    }
}
