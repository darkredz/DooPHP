
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
      <div class="content">
	  	<h1>Example of Defining Routes</h1>
		<p class="normal">The list below are defined routes.</p>
        <p class="normal">For auto routing, your controller should NOT have <pre>$autoroute=FALSE;</pre> </p>
        <p class="normal">You need to enable auto route in <em>common.conf.php</em> as well <pre>$config['AUTOROUTE'] = TRUE;</pre></p>
        <p class="normal">Auto routes are accessed as http://domain.com/classname/methodname/param1/param2. If your class is named in a camel Case convention,
        you access it with a '-'. Parameters in auto route are accessed with integer Index instead of a key. Example:</p>
        <pre>
class HelloWorldController extends DooController{
    public function sayhi(){
        if(!empty($this->params)){
            print_r($this->params);
            echo 'Bye! '. $this->params[2];
        }
    }
}
# You access it via: http://domain.com/hello-world/sayhi/I am DooPHP/Second-name-here/Darkredz
# You will get a result like this:
Array
(
    [0] => I am DooPHP
    [1] => Second-name-here
    [2] => Darkredz
)
Bye! Darkredz
        </pre>
        <p class="normal">Defining Fixed routes. The action method needs to be a public non-static function.
            <pre>

 // Define your URI routes here.
 // $route[Request Method][Uri] = array( class, action method, [options, etc.])
 
 $route['*']['/'] = array('MainController', 'index');
 $route['get']['/url'] = array('MainController', 'url');
 $route['post']['/news/update'] = array('NewsController', 'update');

</pre>
		</p>
        <p class="normal">A route with parameters.
            <pre>
$route['*']['/news/:id'] = array('NewsController', 'getNews');
$route['*']['/archive/:year/:month'] = array('NewsController', 'archive');
</pre>
		</p>
        <p class="normal">Then you access the params in the Controller via:
            <pre>
public function getNews(){
    echo $this->params['id'];
}

public function archive(){
    echo $this->params['year'];
    echo $this->params['month'];
}
</pre>
		</p>
        <p class="normal">Here's how you define with extension name.
            <pre>
$route['*']['/simple.rss'] = array('FeedController', 'getRss');
$route['*']['/simple.atom'] = array('FeedController', 'getAtom');
</pre>
		</p>
        <p class="normal">Parameters &amp; extension name.
            <pre>
$route['*']['/food/list/:id'] = array('RestController',
                                      'listFood',
                                      'extension'=>'.json'
                                     );
$route['post']['/food/create/:id'] = array('RestController',
                                           'createFood',
                                           'extension'=>'.json');
</pre>
		</p>
        <p class="normal">Here's how you define with extension name.
            <pre>
$route['*']['/simple.rss'] = array('FeedController', 'rss');
$route['*']['/simple.atom'] = array('FeedController', 'atom');
</pre>
		</p>
        <p class="normal">Routes redirection:
            <pre>
// here's how you do redirection to an existing route internally
// http status code is optional, default 302 Moved Temporarily
$route['*']['/about'] = $route['*']['/home'] = $route['*']['/'];
$route['*']['/easy'] = array('redirect', '/simple');
$route['*']['/easier'] = array('redirect', '/simple', 301);

// External redirect
$route['*']['/doophp'] = array('redirect', 'http://doophp.com/');
</pre>
		</p>
        <p class="normal">If your Controller is in a sub folder. For example, it's in protected/controller/admin/AdminController.php
            <pre>
$route['*']['/admin'] = array('admin/AdminController', 'index');
</pre>
		</p>
        <p class="normal">If you need Authentication for it quickly:
            <pre>
//Http digest auth and subfolder example
$route['*']['/admin'] = array('admin/AdminController', 'index',
                              'authName'=>'Food Api Admin',
                              'auth'=>array('admin'=>'1234', 'demo'=>'abc'),
                              'authFailURL'=>'/admin/fail');

//Instead of redirect when fail, just show a message, authFail
$route['*']['/admin'] = array('admin/AdminController', 'index',
                              'authName'=>'Food Api Admin',
                              'auth'=>array('admin'=>'1234', 'demo'=>'abc'),
                              'authFail'=>'Please login to the admin site!');
</pre>
		</p>

        <p class="normal">If you want to filter the parameters, you can use regular expressions.
            <pre>
// This will only match value of Year 4 digits and Month 2 digits
$route['*']['/news/:year/:month'] = array('NewsController', 'show_by_date',
                                            'match'=>array(
                                                        'year'=>'/^\d{4}$/',
                                                        'month'=>'/^\d{2}$/'
                                                     )
                                         );
</pre>
		</p>

        <p class="normal">Almost identical routes are supported if you ever need them.
            <pre>
/**
 * almost identical routes examples, must assigned a matching pattern to the parameters
 * if no pattern is assigned, it will match the route defined first.
 */
$route['*']['/news/:id'] = array('NewsController', 'show_news_by_id',
                                 'match'=> array('id'=>'/^\d+$/')
                                );

$route['*']['/news/:title'] = array('NewsController', 'show_news_by_title',
                                    'match'=>array('title'=>'/[a-z0-9]+/')
                                   );

</pre>
		</p>
       <span class="totop"><a href="#top">BACK TO TOP</a></span>  
       </div>
	</body>
</html>
