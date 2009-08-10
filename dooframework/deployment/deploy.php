<?php
class Doo{
    private static $_app;
    private static $_conf;
    private static $_logger;
    private static $_db;
    public static function conf(){
        if(self::$_conf===NULL){
            self::$_conf = new DooConfig;
        }
        return self::$_conf;
    }

    public static function app(){
        if(self::$_app===NULL){
            self::$_app = new DooWebApp;
        }
        return self::$_app;
    }

    public static function db(){
        if(self::$_db===NULL){
            self::loadCore('db/DooSqlMagic');
            self::$_db = new DooSqlMagic;
        }

        if(!self::$_db->connected)
            self::$_db->connect();

        return self::$_db;
    }

	public static function logger(){
        if(self::$_logger===NULL){
            self::loadCore('logging/DooLog');
            self::$_logger = new DooLog(self::conf()->DEBUG_ENABLED);
        }
        return self::$_logger;
	}
	
	public static function cache() {
		if(self::$_cache === null) {
			self::loadCore('cache/DooCache');
			self::loadCore('cache/DooFileCache');
			self::$_cache = DooFileCache::cache();
		}
		return self::$_cache;
	}
	
	protected static function load($class_name, $path, $createObj=FALSE){
        if(is_string($class_name)){
    		require_once($path . "$class_name.php");
            if($createObj)
                return new $class_name;
        }else{
            if($createObj)
                $obj=array();

            for($i=0;$i<sizeof($class_name);$i++){
                require_once($path . "$class_name.php");
                if($createObj)
                    $obj[] = new $class_name;
            }

            if($createObj)
                return $obj;
        }
	}

	public static function loadClass($class_name, $createObj=FALSE){
        return self::load($class_name, self::conf()->SITE_PATH ."protected/class/", $createObj);
	}

    public static function loadController($class_name){
		require_once(self::conf()->SITE_PATH ."protected/controller/$class_name.php");
	}

	public static function loadModel($class_name, $createObj=FALSE){
		return self::load($class_name, self::conf()->SITE_PATH ."protected/model/", $createObj);
	}

	public static function loadHelper($class_name, $createObj=FALSE){
        return self::load($class_name, self::conf()->BASE_PATH ."helper/", $createObj);
	}
	public static function loadCore($class_name){
		require_once(self::conf()->BASE_PATH ."$class_name.php");
	}
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
		return 'Powered by <a href="http://www.doophp.com/">Doo PHP Framework</a>.';
	}

	public static function version(){
		return '1.0';
	}
}

class DooConfig{
    var $SITE_PATH;
    var $BASE_PATH;
    var $LOG_PATH;
    var $APP_URL;
    var $SUBFOLDER;
    var $APP_MODE;
    var $AUTOROUTE;
    var $DEBUG_ENABLED;
    var $ERROR_404_DOCUMENT;
    var $ERROR_404_ROUTE;
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
class DooWebApp{
    public $route;
    public function run(){
        $this->throwHeader( $this->route_to() );
    }

    private function route_to(){
        $router = new DooUriRouter;
        $routeRs = $router->execute($this->route,Doo::conf()->SUBFOLDER);

        if($routeRs[0]!=NULL && $routeRs[1]!=NULL){
            require_once(Doo::conf()->BASE_PATH ."controller/DooController.php");
            require_once(Doo::conf()->SITE_PATH ."protected/controller/{$routeRs[0]}.php");
            if(strpos($routeRs[0], '/')!==FALSE){
                $clsname = explode('/', $routeRs[0]);
                $routeRs[0] = $clsname[ sizeof($clsname)-1 ];
            }
            $controller = new $routeRs[0];
            $controller->params = $routeRs[2];
            
            if(isset($controller->params['__extension'])){
                $controller->extension = $controller->params['__extension'];
                unset($controller->params['__extension']);
            }
            if($_SERVER['REQUEST_METHOD']==='PUT')
                $controller->init_put_vars();
            return $controller->$routeRs[1]();
        }
         else if(Doo::conf()->AUTOROUTE){
            
            list($controller_name, $method_name, $params )= $router->auto_connect(Doo::conf()->SUBFOLDER);
            $controller_file = Doo::conf()->SITE_PATH ."protected/controller/{$controller_name}.php";

            if(file_exists($controller_file)){
                require_once(Doo::conf()->BASE_PATH ."controller/DooController.php");
                require_once($controller_file);
                $controller = new $controller_name;

                if(!$controller->autoroute)
                    $this->throwHeader(404);

                if($params!=NULL)
                    $controller->params = $params;
                
                if($_SERVER['REQUEST_METHOD']==='PUT')
                    $controller->init_put_vars();

                if(method_exists($controller, $method_name))
                    return $controller->$method_name();
                else
                    $this->throwHeader(404);
            }
            else{
                $this->throwHeader(404);
            }
        }
        else{
            $this->throwHeader(404);
        }
    }

    public function reroute($routeuri, $is404=FALSE){
        if(Doo::conf()->SUBFOLDER!='/')
            $_SERVER['REQUEST_URI'] = substr(Doo::conf()->SUBFOLDER, 0, strlen(Doo::conf()->SUBFOLDER)-1) . $routeuri;
        else
            $_SERVER['REQUEST_URI'] = $routeuri;

        if($is404===TRUE)
            header('HTTP/1.1 404 Not Found');
        $this->route_to();
    }

    public function throwHeader($code){
        if(headers_sent()){
            return;
        }
        if($code!=NULL){
            if(is_int($code)){
                if($code===404){
                    header('HTTP/1.1 404 Not Found');
                    if(!empty(Doo::conf()->ERROR_404_DOCUMENT)){
                        include Doo::conf()->SITE_PATH . Doo::conf()->ERROR_404_DOCUMENT;
                    }
                    elseif(!empty(Doo::conf()->ERROR_404_ROUTE)){
                        $this->reroute(Doo::conf()->ERROR_404_ROUTE, true);
                    }
                    exit;
                }
                else{
                    DooUriRouter::redirect(NULL,true, $code);
                }
            }
            elseif(is_string($code)){
                DooUriRouter::redirect($code);
            }
            elseif(is_array($code)){
                if($code[1]=='internal'){
                    $this->reroute($code[0]);
                    exit;
                }
                elseif($code[1]===404){
                    $this->reroute($code[0],true);
                    exit;
                }
                elseif($code[1]===302){
                    DooUriRouter::redirect($code[0],true, $code[1], array("HTTP/1.1 302 Moved Temporarily"));
                }
                else{
                    DooUriRouter::redirect($code[0],true, $code[1]);
                }
            }
        }
    }
}

class DooUriRouter{

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
        
        return array($route[0],$route[1],$params);
    }

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

    private function connect($route,$subfolder){
        $type = strtolower($_SERVER['REQUEST_METHOD']);

        if(strpos($_SERVER['REQUEST_URI'],'/index.php')!==FALSE){
            $requri = $_SERVER['REQUEST_URI'];
        }else if($subfolder=='/'){
            $requri = '/index.php' . $_SERVER['REQUEST_URI'];
        }
        else{
            $requri = substr_replace($_SERVER['REQUEST_URI'], '/index.php/', 0, strlen($subfolder));
        }
        if($requri=='/' || $requri=='/index.php' || $requri=='/index.php/' || $requri==$subfolder.'index.php/' || $requri==$subfolder.'index.php'){
                $uri='/';
        }else{
            $uri = $requri;
            if(strpos($uri, '/index.php')===0)
                $uri = str_replace('/index.php','', $uri);
            elseif(strpos($uri, $subfolder.'index.php')===0)
                $uri = str_replace($subfolder.'index.php','', $uri);
        }

        if($pos = strpos($uri, '/?')){
            $uri = substr($uri,0,$pos);
        }
        
        if($uri!='/'){
            $this->strip_slash($uri);
        }
        if(isset($route[$type]))
            $uris_data = $route[$type];
        else
            $uris_data=NULL;

        if(isset($route['*'])){
            if($uris_data!=NULL)
                $uris_data = array_merge($uris_data, $route['*']);
            else
                $uris_data = $route['*'];
        }

        $match = NULL;

        if(isset($uris_data[$uri]))
            $match = $uris_data[$uri];

        if($match!=NULL){
            return array($match, NULL);
        }else{
            $uri = substr($uri,1);
            $uri_parts = explode('/', $uri);

            foreach($uris_data as $ukey=>$udata){
                $ukey = substr($ukey,1);
                $uparts = explode('/', $ukey);
                if(sizeof($uri_parts) !== sizeof($uparts) )
                    continue;

                if($uri_parts[0]!==$uparts[0])
                    continue;

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
                if(substr($uri,0,strlen($static_part))!==$static_part)
                    continue;

                $param = $this->parse_params($uri_parts, $uparts);
                if(isset($udata['match'])){
                    foreach($udata['match'] as $var_name=>$pattern){
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
    public function auto_connect($subfolder='/'){
        $uri = $_SERVER['REQUEST_URI'];

        if( $subfolder!='/' )
            $uri = substr($uri, strlen($subfolder));

        if(strpos($uri, 'index.php/')===0)
            $uri = substr($uri, strlen('index.php/'));

        if($pos = strpos($uri, '/?')){
            $uri = substr($uri,0,$pos);
        }

        if($uri!='/')
            $this->strip_slash($uri);

        if($uri[0]=='/')
            $uri = substr($uri, 1);

        $uri = explode('/',$uri);
        $controller_name = $uri[0];

        $camelpos = strpos($controller_name, '-');
        if($camelpos!==FALSE){
            $controller_name = substr_replace($controller_name, strtoupper($controller_name[$camelpos+1]), $camelpos, 2) ;
        }

        $controller_name = substr_replace($controller_name, strtoupper($controller_name[0]), 0, 1) . 'Controller';

        if(isset($uri[1]) && $uri[1]!=NULL && $uri[1]!='')
            $method_name = $uri[1];
        else
            $method_name = 'index';

        $params = NULL;
        if(sizeof($uri)>2){
            $params=array_slice($uri, 2);
        }

        return array($controller_name, $method_name, $params);
    }
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
