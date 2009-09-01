<?php
/**
 * DooController class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * Base class of all controller
 *
 * <p>Provides a few shorthand methods to access commonly used component during development. e.g. DooLoader, DooLog, DooSqlMagic.</p>
 *
 * <p>Parameter lists and extension type defined in routes configuration can be accessed through <b>$this->params</b> and <b>$this->extension</b></p>
 *
 * <p>If a client sends PUT request to your controller, you can retrieve the values sent through <b>$this->puts</b></p>
 *
 * <p>GET and POST variables can still be accessed via php $_GET and $_POST. They are not handled/process by Doo framework.</p>
 *
 * <p>Auto routing can be denied from a Controller by setting <b>$autoroute = false</b></p>
 *
 * Therefore, the following class properties & methods is reserved and should not be used in your Controller class.
 * <code>
 * $params
 * $puts
 * $extension
 * $autoroute
 * init_put_vars()
 * load()
 * language()
 * accept_type()
 * render()
 * renderc()
 * setContentType()
 * is_SSL()
 * view()
 * db()
 * cache()
 * acl()
 * beforeRun()
 * </code>
 *
 * You still have a lot of freedom to name your methods and properties other than the 16 names mentioned.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooController.php 1000 2009-07-7 18:27:22
 * @package doo.controller
 * @since 1.0
 */
class DooController {
    /**
     * Associative array of the parameter list found matched in a URI route.
     * @var array
     */
    public $params;

    /**
     * Associative array of the PUT values sent by client.
     * @var array
     */
    public $puts;

    /**
     * Extension name (.html, .json, .xml ,...) found in the URI. Routes can be specified with a string or an array as matching extensions
     * @var string
     */
    public $extension;

    /**
     * Deny or allow auto routing access to a Controller. By default auto routes are allowed in a controller.
     * @var bool
     */
    public $autoroute = TRUE;
    
    protected $_load;
    protected $_view;

    /**
     * Set PUT request variables in a controller. This method is to be used by the main web app class.
     */
    public function init_put_vars(){
        parse_str(file_get_contents('php://input'), $this->puts);
    }

    /**
     * The loader singleton, auto create if the singleton has not been created yet.
     * @return DooLoader
     */
    public function load(){
        if($this->_load==NULL){
            Doo::loadCore('uri/DooLoader');
            $this->_load = new DooLoader;
        }

        return $this->_load;
    }

    /**
     * Returns the database singleton, shorthand to Doo::db()
     * @return DooSqlMagic
     */
    public function db(){
        return Doo::db();
    }

    /**
     * Returns the Acl singleton, shorthand to Doo::acl()
     * @return DooAcl
     */
    public function acl(){
        return Doo::acl();
    }

    /**
     * This will be called before the actual action is executed
     */
    public function beforeRun($resource, $action){}	

    /**
     * Returns the cache singleton, shorthand to Doo::cache()
     * @return DooFileCache|DooFrontCache|DooApcCache|DooMemCache|DooXCache|DooEAcceleratorCache
     */
    public function cache($cacheType='file'){
        return Doo::cache($cacheType);
    }

    /**
     * The view singleton, auto create if the singleton has not been created yet.
     * @return DooView
     */
    public function view(){
        if($this->_view==NULL){
            Doo::loadCore('view/DooView');
            $this->_view = new DooView;
        }

        return $this->_view;
    }

    /**
     * Short hand for $this->view()->render() Renders the view file.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function render($file, $data=NULL, $process=NULL, $forceCompile=false){
        $this->view()->render($file, $data, $process, $forceCompile);
    }

    /**
     * Short hand for $this->view()->renderc() Renders the view file(php) located in viewc.
     *
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the php template.
     * @param bool $enableControllerAccess Enable the view scripts to access the controller property and methods.
     */
    public function renderc($file, $data=NULL, $enableControllerAccess=False){
        if($enableControllerAccess===true){
            $this->view()->renderc($file, $data, $this);
        }else{
            $this->view()->renderc($file, $data);
        }
    }

    /**
     * Get the client accept language from the header
     *
     * @param bool $countryCode to return the language code along with country code
     * @return string The language code. eg. <b>en</b> or <b>en-US</b>
     */
    public function language($countryCode=FALSE){
        $langcode = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $langcode = (!empty($langcode)) ? explode(';', $langcode) : $langcode;
        $langcode = (!empty($langcode[0])) ? explode(',', $langcode[0]) : $langcode;
        if(!$countryCode)
            $langcode = (!empty($langcode[0])) ? explode('-', $langcode[0]) : $langcode;
        return $langcode[0];
    }

    /**
     * Get the client specified accept type from the header sent
     * 
     * <p>Instead of appending a extension name like '.json' to a URL,
     * clients can use 'Accept: application/json' for RESTful APIs.</p>
     * @return string Client accept type
     */
    public function accept_type(){
        $type = array(
            '*/*'=>'*',
            'html'=>'text/html,application/xhtml+xml',
            'xml'=>'application/xml,text/xml,application/x-xml',
            'json'=>'application/json,text/x-json,application/jsonrequest,text/json',
            'js'=>'text/javascript,application/javascript,application/x-javascript',
            'css'=>'text/css',
            'rss'=>'application/rss+xml',
            'yaml'=>'application/x-yaml,text/yaml',
            'atom'=>'application/atom+xml',
            'pdf'=>'application/pdf',
            'text'=>'text/plain',
            'png'=>'image/png',
            'jpg'=>'image/jpg,image/jpeg,image/pjpeg',
            'gif'=>'image/gif',
            'form'=>'multipart/form-data',
            'url-form'=>'application/x-www-form-urlencoded',
            'csv'=>'text/csv'
        );

        $matches = array();

        //search and match, add 1 priority to the key if found matched
        foreach($type as $k=>$v){
            if(strpos($v,',')!==FALSE){
                $tv = explode(',', $v);
                foreach($tv as $k2=>$v2){
                    if (stristr($_SERVER["HTTP_ACCEPT"], $v2)){
                        if(isset($matches[$k]))
                            $matches[$k] = $matches[$k]+1;
                        else
                            $matches[$k]=1;
                    }
                }
            }else{
                if (stristr($_SERVER["HTTP_ACCEPT"], $v)){
                    if(isset($matches[$k]))
                        $matches[$k] = $matches[$k]+1;
                    else
                        $matches[$k]=1;
                }
            }
        }

        if(sizeof($matches)<1)
            return NULL;

        //sort by the highest priority, keep the key, return the highest
        arsort($matches);

        foreach ($matches as $k=>$v){
            return ($k==='*/*')?'html':$k;
        }
    }

    /**
     * Sent a content type header
     *
     * <p>This can be used with your REST api if you allow clients to retrieve result format
     * by sending a <b>Accept type header</b> in their requests. Alternatively, extension names can be
     * used at the end of an URI such as <b>.json</b> and <b>.xml</b></p>
     *
     * <p>NOTE: This method should be used before echoing out your results.
     * Use accept_type() or $extension to determined the desirable format the client wanted to accept.</p>
     *
     * @param string $type Content type of the result. eg. text, xml, json, rss, atom
     * @param string $charset Charset of the result content. Default utf-8.
     */
    public function setContentType($type, $charset='utf-8'){
        if(headers_sent())return;
        
        $extensions = array('html'=>'text/html',
                            'xml'=>'application/xml',
                            'json'=>'application/json',
                            'js'=>'application/javascript',
                            'css'=>'text/css',
                            'rss'=>'application/rss+xml',
                            'yaml'=>'text/yaml',
                            'atom'=>'application/atom+xml',
                            'pdf'=>'application/pdf',
                            'text'=>'text/plain',
                            'png'=>'image/png',
                            'jpg'=>'image/jpeg',
                            'gif'=>'image/gif',
                            'csv'=>'text/csv'
						);
        if(isset($extensions[$type]))
            header("Content-Type: {$extensions[$type]}; charset=$charset");
    }

    /**
     * Check if the connection is a SSL connection
     * @return bool determined if it is a SSL connection
     */
    public function is_SSL(){
        if(!isset($_SERVER['HTTPS']))
            return FALSE;
            
        //Apache
        if($_SERVER['HTTPS'] === 1) {
            return TRUE;
        }
        //IIS
        elseif ($_SERVER['HTTPS'] === 'on') {
            return TRUE;
        }
        //other servers
        elseif ($_SERVER['SERVER_PORT'] == 443){
            return TRUE;
        }
        return FALSE;
    }

}

?>