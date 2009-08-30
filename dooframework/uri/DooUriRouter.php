<?php
/**
 * DooUriRouter class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * DooUriRouter parse the server request URI
 *
 * <p>The uri router parse the URI and returns the matching controller, action and parameters as defined in the routes configuration.
 * Auto routing is also handled if <b>AUTOROUTE</b> is on. A controller can disable autorouting request by writing the
 * <code>public $autoroute = false;</code>
 * </p>
 *
 * <p>The Uri Router is tested and should work in most modern web servers such as <b>Apache</b> and <b>Cherokee</b>
 * in both mod_php or FastCGI mode. Please refer to http://doophp.com/tutorial/setup to see how to used DooPHP with Cherokee web server</p>
 *
 * <p>HTTP digest authentication can be used with the URI router.
 * HTTP digest is much more recommended over the use of HTTP Basic auth which doesn't provide any encryption.
 * If you are running PHP on Apache in CGI/FastCGI mode, you would need to
 * add the following line to your .htaccess for digest auth to work correctly.</p>
 * <code>RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]</code>
 *
 * <p>Almost identical routes can be parsed by setting the matching URI pattern for each of the identical routes.</p>
 * <p>URI ends with an extension name such as .html .php .aspx will also be parsed by setting a extension value in the route definitions. eg.
 * <code>
 * //static route, just add a .html
 * $route['*']['/products/promotion.html'] = array('ProductController', 'promo');
 *
 * //dynamic route, add 'extension'=>'.html'
 * $route['*']['/products/promotion/:month'] = array('ProductController', 'promo', array('extension'=>'.html'));
 * </code>
 * </p>
 *
 * <p><b>RESTful API</b>s are supported natively. You can mocked up <b>RESTful API</b>s easily with DooUriRouter.
 * Just defined the routes to be accessed through the specified request method and handles them in different
 * controller or action method. eg. GET/POST/PUT/DELETE/etc.
 * <code>
 * $route['*']['/news/:id']     #can be accessed through any method
 * $route['get']['/news/:id']   #only accessed through GET
 * $route['post']['/news/:id']  #only accessed through POST
 * $route['delete']['/news/:id']#only accessed through DELETE
 * </code>
 * </p>
 *
 * <p>Routes can be redirect either to an external URL or an internal route.
 * <code>
 * //internal redirect
 * $route['*']['/some/route2'] = $route['*']['/some/route1'];
 *
 * //----- external redirect -----
 * $route['*']['/google/go'] = array('redirect', 'http://localhost/index.html');
 * //sends a 301 Moved Permenantly header
 * $route['*']['/google/go2'] = array('redirect', 'http://localhost/index.html', 301);
 * //redirect to a file called error.html on the same domain.
 * $route['*']['/some/error'] = array('redirect', '/error.html');
 * </code>
 * </p>
 *
 * <p>Defining HTTP authentication
 * <code>
 * //authFail can also be a URL for redirection
 * $route['*']['/admin'] = array('AdminController', 'index',
 *                               'authName'=>'My ABC',
 *                               'auth'=>array('admin'=>'1234', 'moderator'=>'123456'),
 *                               'authFail'=>'login first to view!');
 * </code>
 * </p>
 *
 * <p>See http://doophp.com/doc/guide/uri-routing for information in configuring Routes</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: Doo.php 1000 2009-07-7 18:27:22
 * @package doo.uri
 * @since 1.0
 */
class DooUriRouter{

    /**
     * Main function to be called in order to parse the requested URI.
     *
     * <p>The returned parameter list can be accessed as an assoc array.</p>
     *
     * <code>
     * #Defined in routes.conf.php
     * $route['*']['/news/:year/:month'] = array('NewsController', 'show_news_by_year'
     * </code>
     *
     * @param array $routeArr Routes defined in <i>routes.conf.php</i>
     * @param string $subfolder Relative path of the sub directory where the app is located. eg. http://localhost/doophp, the value should be '/doophp/'
     * @return array returns an array consist of the Controller class, action method and parameters of the route
     */
    public function execute($routeArr,$subfolder='/'){
        list($route, $params) = $this->connect($routeArr,$subfolder);

        if($route[0]=='redirect'){
            if(sizeof($route)===2)
                self::redirect($route[1]);
            else
                self::redirect($route[1],true,$route[2]);
        }

        if(isset($route['auth'])){
            $route['authFailURL'] = (!isset($route['authFailURL']))?NULL:$route['authFailURL'];
            $route['authFail'] = (!isset($route['authFail']))?NULL:$route['authFail'];
            Doo::loadCore('auth/DooDigestAuth');
            DooDigestAuth::http_auth($route['authName'],$route['auth'], $route['authFail'], $route['authFailURL']);
        }
		
        if(isset($route['className']))
			return array($route[0],$route[1],$params,$route['className']);
		
        //return Controller class, method, parameters of the route
        return array($route[0],$route[1],$params);
    }

    /**
     * Redirect to an external URL with HTTP 302 header sent by default
     *
     * @param string $location URL of the redirect location
     * @param bool $exit to end the application
     * @param code $code HTTP status code to be sent with the header
     * @param array $headerBefore Headers to be sent before header("Location: some_url_address");
     * @param array $headerAfter Headers to be sent after header("Location: some_url_address");
     */
    public static function redirect($location, $exit=true, $code=302, $headerBefore=NULL, $headerAfter=NULL){
        if($headerBefore!=NULL){
            for($i=0;$i<sizeof($headerBefore);$i++){
                header($headerBefore[$i]);
            }
        }
        header("Location: $location", true, $code);
        if($headerAfter!=NULL){
            for($i=0;$i<sizeof($headerBefore);$i++){
                header($headerBefore[$i]);
            }
        }
        if($exit)
            exit;
    }

    /**
     * Matching the route array with the request URI
     *
     * <p>Avoids preg_match for most cases to gain more performance.
     * Trailing slashes '/' are ignored and stripped out. It can be used with or without the <b>index.php</b> in the URI.</p>
     * <p>To use DooUriRouter without index.php, add the following code to your .htaccess file if Apache mod_rewrite is enabled.</p>
     * <code>
     * RewriteEngine On
     *
     * # if a directory or a file exists, use it directly
     * RewriteCond %{REQUEST_FILENAME} !-f
     * RewriteCond %{REQUEST_FILENAME} !-d

     * # otherwise forward it to index.php
     * RewriteRule .* index.php
     * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
     * </code>
     */
    private function connect($route,$subfolder){
        $type = strtolower($_SERVER['REQUEST_METHOD']);
        
        #echo 'type = ' . $type . '<br/>';
        #echo 'request URI = ' . $_SERVER['REQUEST_URI'] . '<br/>';
        #echo 'requested file = ' . $_SERVER['SCRIPT_NAME'] . '<br/>';

        if(strpos($_SERVER['REQUEST_URI'],'/index.php')!==FALSE){
            $requri = $_SERVER['REQUEST_URI'];
        }else if($subfolder=='/'){
            $requri = '/index.php' . $_SERVER['REQUEST_URI'];
        }
        else{
            //if subfolder exists add index.php after /subfolder/index.php/news/2009
    #		echo 'subfolder at '. strpos($_SERVER['REQUEST_URI'],$subfolder) .'<br>';
            $requri = substr_replace($_SERVER['REQUEST_URI'], '/index.php/', 0, strlen($subfolder));
    #		echo $requri;
        }
        #echo '<h1>G - '.$requri.'</h1>';
        /*Uri is the server Request_Uri - Script_name, replace on first occurence only(the filename)
         * Works for Apache and Cheroke - sub folder, sub domain and domain
         */
        if($requri=='/' || $requri=='/index.php' || $requri=='/index.php/' || $requri==$subfolder.'index.php/' || $requri==$subfolder.'index.php'){
               # || ($requri==str_replace('index.php','',$_SERVER['SCRIPT_NAME'])  && strpos($_SERVER['SCRIPT_NAME'], 'index.php')!==FALSE)
               # || ($requri==$_SERVER['SCRIPT_NAME'] && strpos($_SERVER['SCRIPT_NAME'], 'index.php')!==FALSE)){
                //echo strpos($_SERVER['SCRIPT_NAME'], 'index.php');

                $uri='/';
        }else{
            //support ?p=123 GET variable on root
            if(strpos($_SERVER['REQUEST_URI'], $subfolder.'?')===0 || strpos($_SERVER['REQUEST_URI'], $subfolder.'index.php?')===0){
                if(isset($route[$type]['/']))
                    return array($route[$type]['/'], null);
                if(isset($route['*']['/']))
                    return array($route['*']['/'], null);
                return;
            }
            #echo '<h2>NOt Root</h2>';
            $uri = $requri;
            //strip out the index.php part
            #if($uri!=$_SERVER['SCRIPT_NAME'])
            #    $uri = substr_replace($uri, '', 0, strlen($_SERVER['SCRIPT_NAME']));
            //echo $uri;
            #echo '<h1>'.$subfolder.'index.php</h1>';
            if(strpos($uri, '/index.php')===0)
                $uri = str_replace('/index.php','', $uri);
            elseif(strpos($uri, $subfolder.'index.php')===0)
                $uri = str_replace($subfolder.'index.php','', $uri);
            //echo strpos($uri, '/index.php');

            #echo '<h1>'.$uri.'</h1>';
        }

        //strip out the GET variable part if start with /?
        if($pos = strpos($uri, '/?')){
            $uri = substr($uri,0,$pos);
        }
        
        //strip slashes at the end
        if($uri!='/'){
            $this->strip_slash($uri);
        }
     #   echo 'URI = '.$uri .'<br>';

        if(isset($route[$type]))
            $uris_data = $route[$type];
        else
            $uris_data=NULL;

        /* search in both * and other request methods, array merge them if * exists,else just search in GET/POST/PUT or others */
        if(isset($route['*'])){
            /* so it will search for the request type other than * first, speed things up, also solve the repeat in * problem */
            if($uris_data!=NULL)
                $uris_data = array_merge($uris_data, $route['*']);
            else
                $uris_data = $route['*'];
        }

        $match = NULL;

        if(isset($uris_data[$uri]))
            $match = $uris_data[$uri];

        if($match!=NULL){
    #        echo '<h1>Match perfect!</h1>';
            /*These are perfect match, no dynamic route invote, so NO NEED check for requirement!
             *go ahead to call and dispatch to controller->action,
             *probably just return the array of controller and action, let other function handle the dispatch for code reusability
             */
            return array($match, NULL);
        }else{
            /* Add all match Routes to an array, as there might be Identical URIs defined by the developer, eg:
             * /news/:title     - to show a news by passing the title which will maybe call the controller action News->show_by_title
             * /news/:id     - to show a news by passing the ID which will maybe call the controller action News->show_by_id
             * will then check up those identical routes IF the array length > 1
             * if only 1 result, return the array, check up the params requirement in another function before dispatch them
             * take note that Identical Routes MUST have different REQUIREMENT for the param, if not the first which is defined will matched, resulting the other will never be matched
             */
            $uri = substr($uri,1);
            $uri_parts = explode('/', $uri);
    		#echo '<br>------------------------------<br><h3>URI parts from browser:</h3><br>';
    		#print_r($uri_parts);

            foreach($uris_data as $ukey=>$udata){
                $ukey = substr($ukey,1);
                $uparts = explode('/', $ukey);
                if(sizeof($uri_parts) !== sizeof($uparts) )
                    continue;

                //if even the first part of the URI doesn't match, don't even bother to check down this route, continue searching...
                if($uri_parts[0]!==$uparts[0])
                    continue;


                //if extension is set, remove the part of the extension from the URI if found
                if(isset($udata['extension'])){
                    $lpindex = sizeof($uri_parts)-1;
                    $lastpart = $uri_parts[$lpindex];

                    if(is_string($udata['extension'])){
                        $ext = $udata['extension'];
                        $lastpart = explode($udata['extension'], $lastpart);
                    }else{
                        foreach ($udata['extension'] as $ext){
                            if($ext!='' && strpos($lastpart,$ext)!==FALSE)
                                break;
                        }
                        $lastpart = explode($ext, $lastpart);
                    }
                    
                    $lplength = sizeof($lastpart);
                    if($lplength<2)
                        continue;
                    if($lastpart[$lplength-1]!=='')
                        continue;
                    if($lplength===2)
                        $uri_parts[$lpindex] = $lastpart[0];
                    else
                        $uri_parts[$lpindex] = $lastpart[0] . str_repeat($udata['extension'], $lplength-2);
                }

                $static_part = explode('/:', $ukey,2);
                $static_part = $static_part[0];
    			#echo '<br/>---------------------<br/><h3>Static part:</h3><br/>';
    			#print_r($static_part);

                //if the static part doesn't match any existing routes' static part... skip, continue the search
                if(substr($uri,0,strlen($static_part))!==$static_part)
                    continue;

    			#echo '<br/>---------------------<br/><h3>Route parts:</h3><br>';
    			#print_r($uparts);
    #			echo '<h1>MATCHED found!</h1><hr/><br/>';

                //set parameters list
                $param = $this->parse_params($uri_parts, $uparts);
                #echo '<pre>';
                #print_r($param);
                #print_r($udata['match']);

                //check for matching requirement for each match defined by user
                //match for the requirement of these Identical Routes
                if(isset($udata['match'])){
                    foreach($udata['match'] as $var_name=>$pattern){
                        #echo $var_name .' = '. $pattern;
                        #echo preg_match($pattern, $param[$var_name]);
                        if( preg_match($pattern, $param[$var_name])==0 ){
                            continue 2;
                        }
                    }
                }
                if(isset($ext)){
                    $param['__extension'] = $ext;
                }
                return array($udata, $param);
            }

        }
    }

    /**
     * Handles auto routing.
     *
     * <p>If AUTOROUTE is on, you can access a controller action method by
     * accessing the URL http://localhost/controllername/methodname</p>
     *
     * <p>If your controller class has a Camel Case naming convention for the class name,
     * access it through http://localhost/camel-case/method for a class name CamelCaseController</p>
     *
     * <p>If no method is specified in the URL, <i>index()</i> will be executed by default if available.
     * If no matching controller/method is found, a 404 status will be sent in the header.</p>
     *
     * <p>The returned parameter list is access through an indexed array ( $param[0], $param[1], $param[2] ) instead of a assoc array in the Controller class.</p>
     *
     * @param string $subfolder Relative path of the sub directory where the app is located. eg. http://localhost/doophp, the value should be '/doophp/'
     * @return array returns an array consist of the Controller class, action method and parameters of the route
     */
    public function auto_connect($subfolder='/'){
        $uri = $_SERVER['REQUEST_URI'];

        //remove Subfolder from the URI if exist
        if( $subfolder!='/' )
            $uri = substr($uri, strlen($subfolder));

        //remove index.php/ from the URI if exist
        if(strpos($uri, 'index.php/')===0)
            $uri = substr($uri, strlen('index.php/'));

        //strip out the GET variable part if start with /?
        if($pos = strpos($uri, '/?')){
            $uri = substr($uri,0,$pos);
        }

        if($uri!='/')
            $this->strip_slash($uri);

        //remove the / in the first char in REQUEST URI
        if($uri[0]=='/')
            $uri = substr($uri, 1);

        //spilt out GET variable first
        $uri = explode('/',$uri);
        $controller_name = $uri[0];

        $camelpos = strpos($controller_name, '-');
        if($camelpos!==FALSE){
            $controller_name = substr_replace($controller_name, strtoupper($controller_name[$camelpos+1]), $camelpos, 2) ;
        }

        $controller_name = substr_replace($controller_name, strtoupper($controller_name[0]), 0, 1) . 'Controller';

        //if method is empty, make it access index
        if(isset($uri[1]) && $uri[1]!=NULL && $uri[1]!='')
            $method_name = $uri[1];
        else
            $method_name = 'index';

        //the first 2 would be Controller and Method, the others will be params if available, access through Array arr[0], arr[1], arr[3]
        $params = NULL;
        if(sizeof($uri)>2){
            $params=array_slice($uri, 2);
        }

        return array($controller_name, $method_name, $params);
    }

    /**
     * Get the parameter list found in the URI which matched a user defined route
     *
     * @param array $req_route The requested route
     * @param  $defined_route Route defined by the user
     * @return array An array of parameters found in the requested URI
     */
    protected function parse_params($req_route, $defined_route){
        $params = NULL;
        for($i=0;$i<sizeof($req_route);$i++){
            $param_key = $defined_route[$i];
            if(strpos($param_key,':')===0){
                $param_key = str_replace(':', '', $param_key);
                $params[$param_key] = $req_route[$i];
            }
        }
        return $params;
    }
    
    /**
     * Strip out the additional slashes found at the end of an URI
     * 
     * This method is called recursively to removed all slashes repeatedly
     * 
     * @param string $str Requested URI
     */
    protected function strip_slash(&$str){
        if($str[strlen($str)-1]==='/'){
            $str = substr($str,0,-1);
            $this->strip_slash($str);
        }else{
            return;
        }
    }

}

?>