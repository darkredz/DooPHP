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
 * $route['*']['/products/promotion/:month'] = array('ProductController', 'promo', 'extension'=>'.html');
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
 * <p>If you do not want case sensetive routing you can force all routes to lowercase. Note this will also result in
 * All parmeters being converted to lowercase as well.
 * <code>
 * $route['force_lowercase'] = true;	// Setting this to false or not defining it will keep routes case sensetive.
 * </code>
 * </p>
 *
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

		if (isset($route['params'])) {
			$params = array_merge((array)$params, $route['params']);
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
            for($i=0;$i<sizeof($headerAfter);$i++){
                header($headerAfter[$i]);
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
	private function connect($routes, $subfolder) {

		$skipNormalRoutes = false;	// Used to allow for parse through to check root and catchall
									// routes if * and type routes do not meet / criteria

		//$this->log('Routes: ', $routes);
		//$this->log('Subfolder: ' . $subfolder);

		$type = strtolower($_SERVER['REQUEST_METHOD']);
		$requestedUri = $_SERVER['REQUEST_URI'];

		if (isset($routes['force_lowercase']) && $routes['force_lowercase'] === true) {
			$requestedUri = strtolower($requestedUri);
		}

		//$this->log('Type: ' . $type);
		//$this->log('Requested Uri: ' . $requestedUri);

		// Remove Subfolder
		$requestedUri = substr($requestedUri, strlen($subfolder)-1);
		//$this->log('Trimmed off subfolder from Request Uri to give: ' . $requestedUri);

		// Remove index.php from URL if it exists
		if (0 === strpos($requestedUri, '/index.php')) {
			$requestedUri = substr($requestedUri, 10);
			//$this->log('Trimmed off the /index.php from Request Uri to give: ' . $requestedUri);
			if ($requestedUri == '') {
				$requestedUri = '/';
			}
		}

		// Remove get part of url (eg example.com/test/?foo=bar trimed to example.com/test/)
		if (false !== ($getPosition = strpos($requestedUri, '?'))) {
			$requestedUri = substr($requestedUri, 0, $getPosition);
			//$this->log('Trimmed off get (?) to give Request Uri: ' . $requestedUri);
		}

		// Remove any trailing slashes from Uri except the first / of a uri (Root)
		$this->strip_slashes($requestedUri);
		//$this->log('Trimmed off trailing slashes from Request Uri: ' . $requestedUri);

		
		// Got a root url (ie. Homepage)
		if ($requestedUri == '/') {
			//$this->log('Got a root URL');
			if(isset($routes[$type]['/']))
				return array($routes[$type]['/'], null);
			elseif(isset($routes['*']['/']))
				return array($routes['*']['/'], null);
			elseif(isset($routes['*']['catchall']))
				$skipNormalRoutes = true;
			else
				return;
		}

		if (!$skipNormalRoutes) {
			// Not got root url so we need to get possible routes
			// First look for routes for specific route type and then try for * routes
			// We will merge the 2 together to prevent duplicate checking
			if(isset($routes[$type]))
				$possibleRoutes = $routes[$type];
			else
				$possibleRoutes = null;

			if(isset($routes['*'])){
				if($possibleRoutes != null)
					$possibleRoutes = array_merge($possibleRoutes, $routes['*']);
				else
					$possibleRoutes = $routes['*'];
			}

			//$this->log('Possible Routes: ', $possibleRoutes);

			// We if we simply have the full route (ie. No params needed)
			if (isset($possibleRoutes[$requestedUri])) {
				// Ensure the url does not contain : in it
				if (false === strpos($requestedUri, ':')) {
					//$this->log('Got Perfect Match');
					return array($possibleRoutes[$requestedUri], null);
				}
			}
		}


		/* Not got a match so now we will loop over all possibleRoutes and see
		 * if we have a matching route using parameters. We carry out some quick checks first
		 * in an attempt to skip past a route which does not match the current route.
		 *
		 * Once we have a route which might work we must then test the route against any
		 * regex (matches) which are to be applied to parameters. This allows for identical uri's to be used
		 * but with each expecting different parameter formats for example
		 * /news/:title     - to show a news by passing the title which will maybe call the controller action News->show_by_title
		 * /news/:id     - to show a news by passing the ID which will maybe call the controller action News->show_by_id
		 * 
		 * Note that Identical Routes MUST have different REQUIREMENT (match) for the param,
		 * if not the first which is defined will matched, therefore preventing any others being matched
		 */

		$uriPartsOrig = explode('/', $requestedUri);
		$uriPartsSize = sizeof($uriPartsOrig);

		$uriExtension = false;
		if (false !== ($pos = strpos($uriPartsOrig[$uriPartsSize-1], '.')) ) {
			$uriExtension = substr($uriPartsOrig[$uriPartsSize-1], $pos);
			$uriLastPartNoExtension = substr($uriPartsOrig[$uriPartsSize-1], 0, $pos);
			//$this->log('URI Extension is: ' . $uriExtension);
		}

		if (!$skipNormalRoutes) {
			foreach($possibleRoutes as $routeKey=>&$routeData) {
				//$this->log('Trying routeKey: ' . $routeKey);
				$uriParts = $uriPartsOrig;
				$routeParts = explode('/', $routeKey);

				if ($uriPartsSize !== sizeof($routeParts)) {
					//$this->log('Not Enought Parts: ' . $routeKey);
					continue;	// Not enough parts in route to match our current uri?
				}

				// If first part of uri not match first part of route then skip.
				// We expect ALL routes at this stage to begin with a static segment.
				// Note: We exploded with a leading / so element 0 in both arrays is an empty string
				if ($uriParts[1] !== $routeParts[1]) {
					//$this->log('First path not match');
					continue;
				}

				// If the route allows extensions check that the extension provided is a correct match
				if (isset($routeData['extension'])) {
					if ($uriExtension === false) {
						continue;		// We need an extension for this to match so can't be a match
					} else {
						$routeExtension = $routeData['extension'];
						if (is_string($routeExtension) && ($uriExtension !== $routeExtension)) {
							continue;	// Extensions do not match so can't be a match
						} elseif (is_array($routeExtension) && !in_array($uriExtension, $routeExtension)) {
							continue;	// Extension not in allowed extensions so can't be a match
						}
					}
				}

				// Now check the other statics parts of the url (we deal with parameters later
				foreach ($routeParts as $i=>&$routePart) {
					if ($i < 2)
						continue;

					if ($routePart[0] === ':')
						continue;	// This routePart is a parameter in the Uri

					if ($routePart !== $uriParts[$i])
						continue 2; // The static part of this route does not match the route part
				}

				//$this->log('Got a route match. RouteKey: ' . $routeKey);
				if (isset($routeData['extension']) && $uriExtension !== false) {
					$uriParts[$uriPartsSize - 1] = $uriLastPartNoExtension;
				}

				$params = $this->parse_params($uriParts, $routeParts);
				//$this->log('Got Parameter Values:', $params);

				if (isset($routeData['match'])) {
					//$this->log('Checking Parameter Matches');
					foreach($routeData['match'] as $paramName=>&$pattern) {
						if (preg_match($pattern, $params[$paramName]) == 0) {
							continue 2;
						}
					}
				}
				if ($uriExtension !== false) {
					$params['__extension'] = $uriExtension;
				}
				$params['__routematch'] = $routeData;
				//$this->log('Got a Match');
				return array($routeData, $params);
			}

			if (isset($routes['*']['root'])) {

				// Note: Root Routes should always start with a parameter ie. ['*']['root']['/:param']
				// Therefore we wont look at running some checks used by non root routes
				//$this->log('No Route Yet Found. Trying Root routes');

				foreach($routes['*']['root'] as $routeKey=>&$routeData) {
					$uriParts = $uriPartsOrig;
					$routeParts = explode('/', $routeKey);

					if ($uriPartsSize !== sizeof($routeParts)) {
						//$this->log('Not Enought Parts: ' . $routeKey);
						continue;	// Not enough parts in route to match our current uri?
					}

					// If the route allows extensions check that the extension provided is a correct match
					if (isset($routeData['extension'])) {
						if ($uriExtension === false) {
							continue;		// We need an extension for this to match so can't be a match
						} else {
							$routeExtension = $routeData['extension'];
							if (is_string($routeExtension) && ($uriExtension !== $routeExtension)) {
								continue;	// Extensions do not match so can't be a match
							} elseif (is_array($routeExtension) && !in_array($uriExtension, $routeExtension)) {
								continue;	// Extension not in allowed extensions so can't be a match
							}
						}
					}

					// Now check the other statics parts of the url (we deal with parameters later
					foreach ($routeParts as $i=>&$routePart) {
						if ($i == 0)
							continue;	// The first item is empty

						if ($routePart[0] === ':')
							continue;	// This routePart is a parameter in the Uri

						if ($routePart !== $uriParts[$i])
							continue 2; // The static part of this route does not match the route part
					}

					//$this->log('Got a route match. RouteKey: ' . $routeKey);
					if (isset($routeData['extension']) && $uriExtension !== false) {
						$uriParts[$uriPartsSize - 1] = $uriLastPartNoExtension;
					}

					$params = $this->parse_params($uriParts, $routeParts);
					//$this->log('Got Parameter Values:', $params);

					if (isset($routeData['match'])) {
						//$this->log('Checking Parameter Matches');
						foreach($routeData['match'] as $paramName=>&$pattern) {
							if (preg_match($pattern, $params[$paramName]) == 0) {
								continue 2;
							}
						}
					}
					if ($uriExtension !== false) {
						$params['__extension'] = $uriExtension;
					}
					$params['__routematch'] = $routeData;
					//$this->log('Got a Match');
					return array($routeData, $params);
				}
			}
		}


		if(isset($routes['*']['catchall'])) {

			//$this->log('No Route Yet Found. Trying Catch All Routes');

			foreach($routes['*']['catchall'] as $routeKey=>&$routeData) {

				// If the route allows extensions check that the extension provided is a correct match
				if (isset($routeData['extension'])) {
					if ($uriExtension === false) {
						continue;		// We need an extension for this to match so can't be a match
					} else {
						$routeExtension = $routeData['extension'];
						if (is_string($routeExtension) && ($uriExtension !== $routeExtension)) {
							continue;	// Extensions do not match so can't be a match
						} elseif (is_array($routeExtension) && !in_array($uriExtension, $routeExtension)) {
							continue;	// Extension not in allowed extensions so can't be a match
						}
					}
				}

				$uriParts = $uriPartsOrig;
				if ($routeKey === '/'){
					$routeParts = array('');
				} else {
					$routeParts = explode('/', $routeKey);

					// Now check the other statics parts of the url (we deal with parameters later
					foreach ($routeParts as $i=>&$routePart) {
						if ($i == 0)
							continue;	// The first item is empty

						if (isset($routePart[0]) && $routePart[0] === ':')
							continue;	// This routePart is a parameter in the Uri

						if ($routePart !== $uriParts[$i]) {
							continue 2; // The static part of this route does not match the route part
						}
					}
				}

				if (isset($routeData['extension']) && $uriExtension !== false) {
					$uriParts[$uriPartsSize - 1] = $uriLastPartNoExtension;
				}

				$params = $this->parse_params_catch($uriParts, $routeParts);

				if (isset($routeData['match'])) {
					foreach($routeData['match'] as $paramName=>&$pattern) {
						if (preg_match($pattern, $params[$paramName]) == 0) {
							continue 2;
						}
					}
				}

				if ($uriExtension !== false) {
					$params['__extension'] = $uriExtension;
				}

				$params['__routematch'] = $routeData;

				return array($routeData, $params);
			}
		}

		//$this->log('Failed to find a matching route');
	}

	private function log($msg, $var=null) {
		if (true) {
			echo "{$msg}<br />\n";
			if ($var !== null)
				echo "<pre>" . print_r($var,true) . "</pre>";
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
        }else if($pos = strpos($uri, '?')) {
            $tmp = explode('?', $uri);
            $uri = $tmp[0];
        }

        if($uri!='/')
            $this->strip_slashes($uri);

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

        if(isset($controller_name[0])===FALSE){
            return;
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
     * @param array $defined_route Route defined by the user
     * @return array An array of parameters found in the requested URI
     */
    protected function parse_params($req_route, $defined_route){
        $params = array();
		$size = sizeof($req_route);
        for($i=0; $i<$size; $i++){
            $param_key = $defined_route[$i];
			if ($param_key == '') {
				continue;
			} elseif($param_key[0]===':'){
                $param_key = str_replace(':', '', $param_key);
                $params[$param_key] = $req_route[$i];
            }
        }
        return $params;
    }

    /**
     * Get the parameter list found in the URI (unlimited)
     *
     * @param array $req_route The requested route
     * @param array $defined_route Route defined by the user
     * @return array An array of parameters found in the requested URI
     */
    protected function parse_params_catch($req_route, $defined_route){
        $params = array();
        for($i=0;$i<sizeof($req_route);$i++){
            if(isset($defined_route[$i])){
                $param_key = $defined_route[$i];
                if ($param_key == '') {
					continue;
				} elseif($param_key[0]===':'){
                    $param_key = str_replace(':', '', $param_key);
                    $params[$param_key] = $req_route[$i];
                }
            }else{
                $params[] = $req_route[$i];
            }
        }
        return $params;
    }

    /**
     * Strip out the additional slashes found at the end. If first character is / then leaves it alone
     *
     * @param string $str Requested URI
     */
    protected function strip_slashes(&$str){
		for ($end = strlen($str) - 1; $end > 0 && $str[$end] === '/'; $end--) {}
		$str = substr($str, 0, $end+1);
    }

}
