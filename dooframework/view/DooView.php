<?php
/**
 * DooView class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooView is a class for working with the "view" portion of the model-view-controller pattern.
 *
 * <p>That is, it exists to help keep the view script separate from the model and controller scripts.
 * It provides a system of helpers, output filters, and variable escaping which is known as Template tags where you can defined them in <i>SITE_PATH/protected/plugin/template_tags.php</i>.</p>
 *
 * <p>DooView is a compiling template engine. It parses the HTML templates and convert them into PHP scripts which will then be included and rendered.
 * In production mode, DooView will not processed the template but include the compiled PHP file instead. Otherwise,
 * it will compare the last modified time of both template and compiled file, then regenerate the compiled file if the template file is newer.
 * Compiled files are located at <i>SITE_PATH/protected/viewc</i> while HTML templates should be placed at <i>SITE_PATH/protected/view</i></p>
 *
 * <p>Loops, variables, function calls with parameters, include files are supported in DooView. DooView only allows a template to use functions defined in <i>template_tags.php</i>.
 * The first parameter needs to be the variable passed in from the template file and it should return a value to be printed out. Functions are case insensitive.
 * </p>
 * <code>
 * //register functions to be used with your template files
 * $template_tags = array('upper', 'toprice');
 *
 * function upper($str){
 *     return strtoupper($str);
 * }
 * function toprice($str, $currency='$'){
 *     return $currency . sprintf("%.2f", $str);
 * }
 *
 * //usage in template
 * Welcome, Mr. {{upper(username)}}
 * Your account has: {{TOPRICE(amount, 'RM')}}
 * </code>
 *
 * <p>Included files in template are automatically generated if it doesn't exist. To include other template files in your template, use:</p>
 * <code>
 * <!-- include "header" -->
 * //Or you can use a variable in the include tag too. say, $data['file']='header'
 * <!-- include "{{file}}" -->
 * </code>
 *
 * <p>Since 1.1, If statement supported. Function used in IF can be controlled.</p>
 * <code>
 * <!-- if -->
 * <!-- elseif -->
 * <!-- else -->
 * <!-- endif -->
 *
 * //Examples
 * <!-- if {{MyVar}}==100 -->
 *
 * //With function
 * <!-- if {{checkVar(MyVar)}} -->
 *
 * //&& || and other operators
 * <!-- if {{isGender(gender, 'female')}}==true && {{age}}>=14 -->
 * <!-- if {{isAdmin(user)}}==true && ({{age}}>=14 || {{age}}<54) -->
 *
 * //Write in one line
 * <!-- if {{isAdmin(username)}} --><h2>Success!</h2><!-- endif -->
 * </code>
 *
 * <p>Since 1.1, Partial caching in view:</p>
 * <code>
 * <!-- cache('mydata', 60) --> 
 * <ul>{{userList}}</ul>
 * <!-- endcache -->
 * </code>
 * <p>DooView can be called from the controller: </p>
 * <code>
 * class SampleController extends DooController{
 *      public viewme(){
 *          $data['myname'] = 'My name is Doo';
 *          $this->view()->render('viewmepage', $data);
 *          //viewmepage instead of viewmepage.html
 *          //files in subfolder, use: render('foldername/viewmepage')
 *      }
 * }
 * </code>
 *
 * <p>You can use <b>native PHP</b> as view templates in DooPHP. Use DooView::renderc() instead of render.</p>
 * <p>In your Controller:</p>
 * <code>
 * $data['phone'] = 012432456;
 * $this->view()->abc = 'ABCDE';
 *
 * //pass in true to enable access to controller if you need it.
 * $this->renderc('example', $data, true);
 * </code>
 *
 * <p>In your view scrips located in <b>SITE_PATH/protected/viewc/</b></p>
 * <code>
 * echo $this->data['phone'];     //012432456
 * echo $this->abc;             //ABC
 *
 * //call controller methods if enabled.
 * $this->thisIsFromController();
 * echo $this->thisIsControllerProperty;
 * </code>
 *
 * <p>To include a template script in viewc folder</p>
 * <code>
 * $this->inc('this_is_another_view_php');
 * </code>
 * 
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooView.php 1000 2009-07-7 18:27:22
 * @package doo.view
 * @since 1.0
 */
class DooView {
    protected $tags;
    public $controller;
    public $data;

    /**
     * Includes the native PHP template file to be output.
     * 
     * @param string $file PHP template file name without extension .php
     * @param array $data Associative array of the data to be used in the template view.
     * @param object $controller The controller object, pass this in so that in views you can access the controller.
     */
    public function renderc($file, $data=NULL, $controller=NULL){
        $this->data = $data;
        $this->controller = $controller;
        //includes user defined template tags for template use
        include Doo::conf()->SITE_PATH . 'protected/plugin/template_tags.php';
        include Doo::conf()->SITE_PATH . "protected/viewc/$file.php";
    }

    /**
     * Include template view files
     * @param string $file File name without extension (.php)
     */
    public function inc($file){
        include Doo::conf()->SITE_PATH . "protected/viewc/$file.php";
    }

    public function  __call($name,  $arguments) {
        if($this->controller!=NULL){
            call_user_func_array(array(&$this->controller, $name), $arguments);
        }
    }

    public function  __get($name) {
        if($this->controller!=NULL){
            return $this->controller->{$name};
        }
    }
    
    /**
     * Renders the view file, generates compiled version of the view template if necessary
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function render($file, $data=NULL, $process=NULL, $forceCompile=false){

        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        if($process===NULL)
            $process = (Doo::conf()->APP_MODE!='prod');

        //just include the compiled file if process is false
        if($process!=true){
            //includes user defined template tags for template use
            include Doo::conf()->SITE_PATH . 'protected/plugin/template_tags.php';
            include Doo::conf()->SITE_PATH . "protected/viewc/$file.php";
        }
        else{
            $cfilename = Doo::conf()->SITE_PATH . "protected/viewc/$file.php";
            $vfilename = Doo::conf()->SITE_PATH . "protected/view/$file.html";
            
            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename)){
                        include Doo::conf()->SITE_PATH . 'protected/plugin/template_tags.php';
                        include $cfilename;
                        return;
                    }
                }
            }
            $this->compile($file, $vfilename, $cfilename);
            include $cfilename;
        }
    }

    /**
     * Parse and compile the template file. Templates generated in protected/viewc folder
     * @param string $file Template file name without extension .html
     * @param string $vfilename Full path of the template file
     * @param string $cfilename Full path of the compiled file to be saved
     */
    protected function compile($file, $vfilename, $cfilename){
        //includes user defined template tags and checks for the tag and compile.
        if($this->tags===NULL){
            include Doo::conf()->SITE_PATH . 'protected/plugin/template_tags.php';
            foreach($template_tags as $k=>$v ){
                $template_tags[$k] = strtolower($v);
            }
            $this->tags = $template_tags;
        }

        //--------------------------- Parsing -----------------------------
        //if no compiled file exist or compiled file is older, generate new one
        $str = file_get_contents($vfilename);

        //convert end loop
        $str = str_replace('<!-- endloop -->', '<?php endforeach; ?>', $str);

        //convert variables {{username}}
        $str = preg_replace('/{{([^ \t\r\n\(\)\.}]+)}}/', "<?php echo \$data['$1']; ?>", $str);

        //convert function with variables passed in {{upper(username)}}
        /*$str = preg_replace('/{{([^ \t\r\n\(\)}]+)\(([^ \t\r\n\(\)}]+)\)}}/', "<?php echo $1(\$data['$2']); ?>", $str);
        */
        $str = preg_replace_callback('/{{([^ \t\r\n\(\)}]+)\(([^\t\r\n\(\)}@]+)\)}}/', array( &$this, 'convertFuncWithVar'), $str);

        //convert function with Object variables passed in {{upper(user.@gender)}}
        $str = preg_replace_callback('/{{([^ \t\r\n\(\)}]+)\(([^\t\r\n\(\)}]+)\)}}/', array( &$this, 'convertFuncWithObjectVar'), $str);

        //convert key variables {{user.john}} {{user.total.male}}
        /*$str = preg_replace('/{{([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)\.}]+)}}/', "<?php echo \$data['$1']['$2']; ?>", $str);
        */
        $str = preg_replace_callback('/{{([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)}]+)}}/', array( &$this, 'convertVarKey'), $str);

        //convert start loop <!--# loop users --> <!--# loop users' value --> <!--# loop users' value' value -->
        $str = preg_replace_callback('/<!-- loop ([^ \t\r\n\(\)}\']+).* -->/', array( &$this, 'convertLoop'), $str);

        //convert variable in loop {{user' value}}  {{user' value' value}}
        $str = preg_replace_callback('/{{([^ \t\r\n\(\)\.}\']+)([^\t\r\n\(\)}{]+)}}/', array( &$this, 'convertVarLoop'), $str);


        //convert else
        $str = str_replace('<!-- else -->', '<?php else: ?>', $str);

        //convert end if
        $str = str_replace('<!-- endif -->', '<?php endif; ?>', $str);
		
        //convert if and else if condition <!-- if expression --> <!-- elseif expression -->  only functions in template_tags are allowed
        $str = preg_replace_callback('/<!-- (if|elseif) ([^\t\r\n}]+) -->/', array( &$this, 'convertCond'), $str);


        //convert end cache <!-- endcache -->
        $str = str_replace('<!-- endcache -->', "\n<?php Doo::cache('front')->end(); ?>\n<?php endif; ?>", $str);

		//convert cache <!-- cache('partial_id', 60) -->
        $str = preg_replace_callback('/<!-- cache\(([^\t\r\n}\)]+)\) -->/', array( &$this, 'convertCache'), $str);

        //convert include to php include and parse & compile the file, if include file not exist Echo error and exit application
        // <?php echo $data['file']; chars allowed for the grouping
        $str = preg_replace_callback('/<!-- include [\'\"]{1}([^\t\r\n\"]+).*[\'\"]{1} -->/', array( &$this, 'convertInclude'), $str);

        //-------------------- Compiling -------------------------
        //write to compiled file in viewc and include that file in.
        $folders = explode('/', $file);
        array_splice($folders, -1);

        //if a subfolder is specified, search for it, if not exist then create the folder
        $pathsize = sizeof($folders);
        if($pathsize>0){
            $path = Doo::conf()->SITE_PATH . "protected/viewc/";
            for($i=0;$i<$pathsize;$i++){
                $path .= $folders[$i] .'/';
                if(!file_exists($path)){
                    mkdir($path);
                }
            }
        }
        $fh = fopen($cfilename, 'w+');
        fwrite($fh, $str);
        fclose($fh);
    }

    private function convertCache($matches){
		$data = explode(',', $matches[1]);
		if(sizeof($data)==2){
			$data[1] = intval($data[1]);
			return "<?php if (!Doo::cache('front')->getPart({$data[0]}, {$data[1]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
		}else{
			return "<?php if (!Doo::cache('front')->getPart({$data[0]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
		}
    }

    private function checkCondFunc($matches){
        //print_r( $matches );
        if(!in_array(strtolower($matches[1]), $this->tags))
            return 'function_deny('. $matches[2] .')';
        return $matches[1].'('. $matches[2] .')';
    }

    private function convertCond($matches){
        //echo '<h1>'.str_replace('>', '&gt;', str_replace('<', '&lt;', $matches[2])).'</h1>';
        $stmt = str_replace('<?php echo ', '', $matches[2]);
        $stmt = str_replace('; ?>', '', $stmt);
        //echo '<h1>'.$stmt.'</h1>';

        //prevent malicious HTML designers to use function with spaces
        //eg. unlink        ( 'allmyfiles.file'  ), php allows this to happen!!
        $stmt = preg_replace_callback('/([a-z0-9\-_]+)[ ]*\([ ]*([^ \t\r\n}]+)\)/i', array( &$this, 'checkCondFunc'), $stmt);

        //echo '<h1>'.$stmt.'</h1>';
        switch($matches[1]){
            case 'if':
                return '<?php if( '.$stmt.' ): ?>';
            case 'elseif':
                return '<?php elseif( '.$stmt.' ): ?>';
        }
    }

    private function convertLoop($matches){
        $looplevel = sizeof(explode('\' ', $matches[0]));
        if(strpos($matches[0], "' ")!==FALSE){
            $strValue = str_repeat("' value", $looplevel-1);
            $loopStr = "<!-- loop {$matches[1]}$strValue.";
            if( strpos($matches[0], $loopStr)===0){
                $loopStr = substr($matches[0], strlen($loopStr));
                $loopStr = str_replace(' -->', '', $loopStr);
                $param = explode('.', $loopStr);
                $varBck ='';
                foreach($param as $pa){
                    if(strpos($pa, '@')===0){
                        $varBck .= '->' . substr($pa, 1);
                    }else{
                        $varBck .= "['$pa']";
                    }
                }
                $thislvl = $looplevel-1;
                $loopname = "\$v$thislvl$varBck";
            }else{
                $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
            }
        }
        else if(strpos($matches[1], '.@')!==FALSE){
            $varname = str_replace('.@', '->', $matches[1]);
            $varname = explode('->', $varname);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname.'\']->' . implode('->', $varname) ;
        }
        else if(strpos($matches[1], '.')!==FALSE){
            $varname = explode('.',$matches[1]);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname .'\'][\''. implode("']['", $varname) .'\']';
        }
        else{
            $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
        }
        return '<?php foreach('.$loopname.' as $k'.$looplevel.'=>$v'.$looplevel.'): ?>';
    }


    private function convertInclude($matches){
        $file = $matches[1];

        /*include file is a Variable <!-- include "{{file}}" -->,
         *modify the converted string <?php echo $data['file']; ?> to $data['file']; and return it to be written to file
         * <!-- include "<?php echo $data['file']; ?>" --> after convert to var */
        $includeVarPos = strpos($file, '<?php echo $data');
        if($includeVarPos===0){
            $file = str_replace('<?php echo ', '', $file);
            $file = str_replace('; ?>', '', $file);
            return '<?php include "{'.$file.'}.php"; ?>';
        }

        $cfilename = Doo::conf()->SITE_PATH . "protected/viewc/$file.php";
        $vfilename = Doo::conf()->SITE_PATH . "protected/view/$file.html";

        if(!file_exists($vfilename)){
            echo "<span style=\"color:#ff0000\">Include view file <strong>$file.html</strong> not found</span>";
            exit;
        }else{
            if(file_exists($cfilename)){
                if(filemtime($vfilename)>filemtime($cfilename)){
                    $this->compile($file, $vfilename, $cfilename);
                }
            }else{
                $this->compile($file, $vfilename, $cfilename);
            }
        }

        return '<?php include "'.$file.'.php"; ?>';
    }


    private function convertFuncWithVar($matches){
        if(!in_array(strtolower($matches[1]), $this->tags))
            return '<span style="color:#ff0000;">Function Denied</span>';

        $varname = '';
        $args = '';

        //if using dots, got variables, eg. users.name, users.total.pdf, users' value' value.something, , users' value.something.some
        if(strpos($matches[2], '.')!==FALSE){
            //remove , for the section to be passed into the function as another argument
            if(strpos($matches[2], ',')!==FALSE){
                $params = explode(',', $matches[2]);
                //first part is the variable to be passed in, removed it from parameter
                $matches[2] = array_splice($params, 0, 1);
                $properties = explode('.', $matches[2][0]);

                //join arguments back ,'hihi', 100
                $args = implode(',', $params);
            }else{
                $properties = explode('.', $matches[2]);
            }

            //if the function found used with a key or value in a loop, then use $k1,$k2 or $v1,$v2 instead of $data
            if(strpos($properties[0], "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $properties[0]));

                //if ' key found that it's a key $k1
                if(strpos($properties[0],"' key")!==FALSE || strpos($properties[0],"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);

                    //remove the variable part with the ' key or  ' value
                    array_splice($properties, 0, 1);

                    //join it up as array $v1['attachment']['pdf']   from  {{upper(msgdetails' value.attachment.pdf)}}
                    $varname .= "['". implode("']['", $properties) ."']";
                }
            }else{
                $varname .= "\$data['". implode("']['", $properties) ."']";
            }
        }
        //no dots, only variable or key or value, users.john,  users' value, users' key, users' value' value
        else{

            //remove , for the section to be passed into the function as another argument
            if(strpos($matches[2], ',')!==FALSE){
                $params = explode(',', $matches[2]);
                $matches[2] = implode(',', array_splice($params, 0, 1));
                $args = implode(',', $params);
            }

            //if the function found used with a key or value in a loop, then use $k1,$k2 or $v1,$v2 instead of $data
            if(strpos($matches[2], "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $matches[2]));

                //if ' key found that it's a key $k1
                if(strpos($matches[2],"' key")!==FALSE || strpos($matches[2],"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);
                }
            }else{
                $varname = "\$data['".$matches[2]."']";
            }
        }

        if($args==='')
            return "<?php echo {$matches[1]}($varname); ?>";
        else
            return "<?php echo {$matches[1]}($varname,$args); ?>";
    }

    private function convertFuncWithObjectVar($matches){
        if(!in_array(strtolower($matches[1]), $this->tags))
            return '<span style="color:#ff0000;">Function Denied</span>';

        $varname = '';
        $args = '';

        //if using dots, got variables, eg. users.@name, users.@total.@pdf, users' value' value.@something, , users' value.@something.@some
        if(strpos($matches[2], '.@')!==FALSE){
            //remove , for the section to be passed into the function as another argument
            if(strpos($matches[2], ',')!==FALSE){
                $params = explode(',', $matches[2]);
                //first part is the variable to be passed in, removed it from parameter
                $matches[2] = array_splice($params, 0, 1);
                $properties = explode('.@', $matches[2][0]);

                //join arguments back ,'hihi', 100
                $args = implode(',', $params);
            }else{
                $properties = explode('.@', $matches[2]);
            }

            //if the function found used with a key or value in a loop, then use $k1,$k2 or $v1,$v2 instead of $data
            if(strpos($properties[0], "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $properties[0]));

                //if ' key found that it's a key $k1
                if(strpos($properties[0],"' key")!==FALSE || strpos($properties[0],"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);

                    //remove the variable part with the ' key or  ' value
                    array_splice($properties, 0, 1);

                    //join it up as array $v1['attachment']['pdf']   from  {{upper(msgdetails' value.attachment.pdf)}}
                    $varname .= "->". implode("->", $properties);
                }
            }else{
                $objname = $properties[0];
                array_splice($properties, 0, 1);
                $varname .= "\$data['$objname']->". implode("->", $properties);
            }
        }

        if($args==='')
            return "<?php echo {$matches[1]}($varname); ?>";
        else
            return "<?php echo {$matches[1]}($varname,$args); ?>";
    }

    private function convertVarKey($matches){
        $varname = '';
        //if more than 1 dots, eg. users.total.pdf
        if(strpos($matches[2], '@')!==FALSE){
            $varname = str_replace('@', '->', $matches[2]);
            $varname = str_replace('.', '', $varname);
        }
        else if(strpos($matches[2], '.')!==FALSE){
            $properties = explode('.', $matches[2]);
            $varname .= "['". implode("']['", $properties) ."']";
        }
        //only 1 dot, users.john
        else{
            $varname = "['".$matches[2]."']";
        }
        return "<?php echo \$data['{$matches[1]}']$varname; ?>";
    }

    private function convertVarLoop($matches){
        $looplevel = sizeof(explode('\' ', $matches[0]));
        
        //if ' key found that it's a key $k1
        if(strpos($matches[0],"' key")!==FALSE || strpos($matches[0],"' k")!==FALSE)
            $varname = 'k' . ($looplevel-1);
        else{
            $varname = 'v' . ($looplevel-1);
            //remove the first variable if the ' is found, we dunwan the loop name
            if(strpos($matches[2], "' ")!==FALSE){
                $matches[2] = explode("' ", $matches[2]);
                array_splice($matches[2], 0, 1);
                $matches[2] = "' ".implode("' ", $matches[2] );
            }

            //users' value.uname  becomes  $v1['uname']
            //users' value.posts.latest  becomes  $v1['posts']['latest']
            //users' value.@uname  becomes  $v1->uname
            //users' value.@posts.@latest  becomes  $v1->posts->latest
            if(strpos($matches[2], '.@')!==FALSE){
                $varname .= str_replace('.@', '->', $matches[2]);
                $varname = str_replace("' value",'', $varname);
                $varname = str_replace("' v",'', $varname);
            }
            else if(strpos($matches[2], '.')!==FALSE){
                $properties = explode('.', $matches[2]);
                if(sizeof($properties)===2)
                    $varname .= "['".$properties[1]."']";
                else{
                    array_splice($properties, 0, 1);
                    $varname .= "['". implode("']['", $properties) ."']";
                }
            }
        }
        return '<?php echo $'.$varname.'; ?>';
    }

}
?>