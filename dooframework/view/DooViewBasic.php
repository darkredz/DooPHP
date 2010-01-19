<?php

class DooViewBasic {

	public $controller;
    public $data;
    protected $tags;
    protected $mainRenderFolder;
    protected $tagClassName;
    protected $tagModuleName;
	protected $tagBlock_start = '{%';
	protected $tagBlock_end   = '%}';
	protected $tagVariable_start = '{{';
	protected $tagVariable_end = '}}';
	protected $tagComment_start = '{#';
	protected $tagComment_end = '#}';

	protected $rootViewPath = null;
	protected $defaultRootViewPath = null;
	protected $rootCompiledPath = null;
	protected $relativeViewPath = '';

	protected $fileManager = null;

	public function __construct() {
		Doo::loadHelper('DooFile');
		$this->fileManager = new DooFile(0777);
		$this->defaultRootViewPath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'view/';
		$this->rootViewPath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'view/';
		$this->rootCompiledPath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'viewc/';
	}

	/**
	 * Specify the root folder where default views should reside. Will look here if view not found in main folder
	 * This must be the FULL PATH and not relative
	 * @param string $path The path to the folder where the views should be found. Should include / at the end
	 */
	public function setDefaultRootViewPath($path) {
		$this->defaultRootViewPath = $path;
	}

	/**
	 * Specify the root folder where views should reside. Will look here first for a view before trying default path
	 * This must be the FULL PATH and not relative
	 * @param string $path The path to the folder where the views should be found. Should include / at the end
	 */
	public function setRootViewPath($path) {
		$this->rootViewPath = $path;
	}

	/**
	 * Specify the root folder where compiled views should reside.
	 * This must be the FULL PATH and not relative
	 * @param string $path The root path where the compiled files should be stored
	 */
	public function setRootCompiledPath($path) {
		$this->rootCompiledPath = $path;
	}

	/**
	 * Set the start and end tags for blocks. These are things like if, else, for, endif, endfor etc...
	 * @param string $startTag The start tag. Defaults to {%
	 * @param string $endTag The end tag. Defaults to %}
	 */
	public function setBlockTags($startTag, $endTag) {
		$this->tagBlock_start = $startTag;
		$this->tagBlock_end = $endTag;
	}

	/**
	 * Set the start and end tags for variable and funcction output. These are things like {{foo.bar}}
	 * @param string $startTag The start tag. Defaults to {{
	 * @param string $endTag The end tag. Defaults to }}
	 */
	public function setVariableTags($startTag, $endTag) {
		$this->tagVariable_start = $startTag;
		$this->tagVariable_end = $endTag;
	}

	/**
	 * Set the start and end tags for comments. For example {# I am a comment #}
	 * @param string $startTag The start tag. Defaults to {#
	 * @param string $endTag The end tag. Defaults to #}
	 */
	public function setCommentTags($startTag, $endTag) {
		$this->tagComment_start = $startTag;
		$this->tagComment_end = $endTag;
	}

	/**
     * Determine which class to use as template tag.
     *
     * If $module is equal to '/', the main app's template tag class will be used.
     *
     * @param string $class Template tag class name
     * @param string $module Folder name of the module. Define this module name if the tag class is from another module.
     */
    public function setTagClass($class, $module=null){
        $this->tagClassName = $class;
        $this->tagModuleName = $module;
    }

    /**
     * Includes the native PHP template file to be output.
     *
     * @param string $file PHP template file name without extension .php
     * @param array $data Associative array of the data to be used in the template view.
     * @param object $controller The controller object, pass this in so that in views you can access the controller.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     */
    public function renderc($file, $data=NULL, $controller=NULL, $includeTagClass=TRUE){
        $this->data = $data;
        $this->controller = $controller;
        if($includeTagClass===TRUE)
            $this->loadTagClass();
        include $this->rootCompiledPath . $file . '.php';
    }

    /**
     * Include template view files
     * @param string $file File name without extension (.php)
     */
    public function inc($file){
        include $this->rootCompiledPath . $file . '.php';
    }

    public function  __call($name,  $arguments) {
        if($this->controller!=NULL){
            return call_user_func_array(array(&$this->controller, $name), $arguments);
        }
    }

    public function  __get($name) {
        if($this->controller!=NULL){
            return $this->controller->{$name};
        }
    }

    /**
     * Writes the generated output produced by render() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @return string|false The file name of the rendered output saved (html).
     */
    public function saveRendered($path, $templatefile, $data=NULL){
        ob_start();
        $this->render($templatefile, $data, null, true);
        $data = ob_get_contents();
        ob_end_clean();
        if(file_put_contents($path, $data)>0){
            $filename = explode('/',$path);
            return $filename[sizeof($filename)-1];
        }
        return false;
    }

    /**
     * Writes the generated output produced by renderc() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param object $controller The controller object, pass this in so that in views you can access the controller.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     * @return string|false The file name of the rendered output saved (html).
     */
    public function saveRenderedC($path, $templatefile, $data=NULL, $controller=NULL, $includeTagClass=TRUE){
        ob_start();
        $this->renderc($templatefile, $data, $controller, $includeTagClass);
        $data = ob_get_contents();
        ob_end_clean();
        if(file_put_contents($path, $data)>0){
            $filename = explode('/',$path);
            return $filename[sizeof($filename)-1];
        }
        return false;
    }

    /**
     * Renders the view file, generates compiled version of the view template if necessary
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function render($file, $data=NULL, $process=NULL, $forceCompile=false){

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

		$this->mainRenderFolder = $file;
		
        if($process == false){
            $this->loadTagClass();
            include $this->rootCompiledPath . $file . '.php';
        } else {
            $cfilename = $this->rootCompiledPath . $file . '.php';

			if (file_exists($this->rootViewPath . $file . '.html')) {
				$vfilename = $this->rootViewPath . $file . '.html';
			} else {
				$vfilename = $this->defaultRootViewPath . $file . '.html';
			}

            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }
            $this->data = $data;
            $this->compile($file, $vfilename, $cfilename);
            include $cfilename;
        }
    }

	/**
	 * Recursivly searches for $file.html in relative folder. If not found in there
	 * it goes up a directory in relative path and tries there. It will repeat this until
	 * it reaches the root view folder. Should this fail it will then try in the default
	 * root folder. If it still has no luck will render an error notice in compiled view
	 *
	 * @param string $relativeFolder relative path from view root where we should start looking for $file (excluding /)
	 * @param string $file file name to look for excluding extension
	 * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
	 * @param bool Ignores last modified time checking and force compile the template everytime it is visited.
	 */
	public function renderFileRecursive($relativeFolder, $file, $data=NULL, $forceCompile = false) {
		if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $forceCompile = true;
        }
        
		$this->mainRenderFolder = $relativeFolder;

        $file = '/' . $file . '.html';
		$path =  $relativeFolder;
		$cfilename = $this->rootCompiledPath . $path . $file . '.php';
		
		while(($found = file_exists($this->rootViewPath . $path . $file)) == false) {
			if ($path == '.')
				break;
			$path = dirname($path);
		}

		if ($found == true) {
			$vfilename = $this->rootViewPath . $path . $file;
		} else {
			$path = $relativeFolder;
			while(($found = file_exists($this->defaultRootViewPath . $path . $file)) == false) {
				if ($path == '.')
					break;
				$path = dirname($path);
			}
			$vfilename = $this->rootViewPath . $path . $file;
		}

		//if file exist and is not older than the html template file, include the compiled php instead and exit the function
		if(!$forceCompile){
			if(file_exists($cfilename)){
				if(filemtime($cfilename)>=filemtime($vfilename)){
					$this->setTags();
					include $cfilename;
					return;
				}
			}
		}
		$this->data = $data;
		$this->compile($file, $vfilename, $cfilename);
		include $cfilename;
	}

	/**
     * Parse and compile the template file. Templates generated in protected/viewc folder
     * @param string $file Template file name without extension .html
     * @param string $vfilename Full path of the template file
     * @param string $cfilename Full path of the compiled file to be saved
     */
    protected function compile($file, $vfilename, $cfilename){
		if (($viewContent = $this->fileManager->readFileContents($vfilename)) == false) {
			$viewContent = '<span style="color:#ff0000;">View Not Found: ' . $file . "</span>\n";
		}
        $compiledContent = $this->compileTags($viewContent);
		$this->fileManager->create($cfilename, $compiledContent);
    }

    /**
     * Renders layouts
     * @param string $viewFolder Path where all template files to be found for the active layout
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function renderLayout($viewFolder, $data=NULL, $process=NULL, $forceCompile=false) {

		if (empty($viewFolder)){
			echo "View folder can not be empty. Must be a folder within the view path";
			exit;
		}

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }

        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

		$this->mainRenderFolder = $viewFolder;

        //just include the compiled file if process is false
        if($process==false){
			//includes user defined template tags for template use
            $this->loadTagClass();
            include $this->rootCompiledPath . $viewFolder . '/index.php';
        }
        else{
            $lfilename = $this->rootViewPath . "/layout.html";
			$cfilename = $this->rootCompiledPath . $viewFolder . '/index.php';

            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($lfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }
            $this->data = $data;
            $this->compileLayout($lfilename, $cfilename);
            include $cfilename;
        }

    }


    /**
     * Parses and compiled a view into a layout to fill in placeholders and
     * stores the resulting view file to then be processed as normal by DooView::compile
     * @param string $viewFile The original location of the view without extension .html
     * @param string $lfilename Full path to the layout file
     * @param string $vfilename Full path to the view to be merged into the layout
     * @param string $cfilename Full path of the compiled file to be saved
     */
    protected function compileLayout($layoutFilePath, $compiledFilePath) {

		if (($layout = $this->fileManager->readFileContents($layoutFilePath)) == false) {
			echo "Layout File Not Found: {$layoutFilePath}";
			exit;
		}
        $viewContent = preg_replace_callback('/<!-- placeholder:([a-zA-Z0-9\_\-]+?) -->([\s\S]*?)<!-- endplaceholder -->/', array( &$this, 'replacePlaceholder'), $layout);
        $compiledContent = $this->compileTags($viewContent);

		$this->fileManager->create($compiledFilePath, $compiledContent);
    }

	protected function replacePlaceholder($matches) {
		$fileName = '/' . $matches[1] . '.html';
		$path = $this->mainRenderFolder;
		$content = false;

		while($content === false) {
			$content = $this->fileManager->readFileContents($this->rootViewPath . $path . $fileName);
			if ($path == '.')
				break;
			$path = dirname($path);
		}
		if ($content === false) {
			$content = $matches[2];
		}

        return $content;
    }


    /**
     * Load the template class and returns the class name.
     * @return string Name of the class that is loaded.
     */
    public function loadTagClass(){
        /* if include tag class is not defined load TemplateTag for main app
         * else if render() is called from a module, load ModulenameTag */
        if( !isset($this->tagClassName) ){
            if( !isset(Doo::conf()->PROTECTED_FOLDER_ORI) ){
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/TemplateTag.php';
                $tagcls = 'TemplateTag';
            }else{
                $tagcls = explode('/', Doo::conf()->PROTECTED_FOLDER);
                $tagcls = ucfirst($tagcls[sizeof($tagcls)-2]) . 'Tag';
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/' . $tagcls .'.php';
            }
        }else{
            //load the main app's TemplateTag if module is '/'
            if($this->tagModuleName=='/'){
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'plugin/'. $this->tagClassName .'.php';
            }
            else if($this->tagModuleName===Null){
                require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/'. $this->tagClassName .'.php';
            }
            else{
                if(isset(Doo::conf()->PROTECTED_FOLDER_ORI))
                    require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI .'module/'. $this->tagModuleName . '/plugin/'. $this->tagClassName .'.php';
                else
                    require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER .'module/'. $this->tagModuleName . '/plugin/'. $this->tagClassName .'.php';
            }
            $tagcls = $this->tagClassName;
        }
        return $tagcls;
    }

    private function setTags(){
        $tagcls = $this->loadTagClass();

        $tagMethod = get_class_methods($tagcls);

        if(!empty($tagMethod)){
            if( !empty(Doo::conf()->TEMPLATE_GLOBAL_TAGS) )
                $template_tags = array_merge(Doo::conf()->TEMPLATE_GLOBAL_TAGS, $tagMethod);
            else
                $template_tags = $tagMethod;

            $template_tags['_methods'] = $tagMethod;
            $template_tags['_class'] = $tagcls;
        }
        else if( !empty(Doo::conf()->TEMPLATE_GLOBAL_TAGS) ){
            $template_tags = Doo::conf()->TEMPLATE_GLOBAL_TAGS;
        }
        else{
            $template_tags = array();
        }

        foreach($template_tags as $k=>$v ){
            if(is_int($k))
                $template_tags[$k] = strtolower($v);
            else
                $template_tags[$k] = $v;
        }
        Doo::conf()->add('TEMPLATE_TAGS', $template_tags);
        return $template_tags;
    }

	


    /**
     * Processes a string containing DooPHP Template tags and replaces them with the relevant PHP code required
     * @param string $str This is the html template markup from View files
     * @return string The PHP markedup version of the View file
     */
    protected function compileTags($str) {

		// Includes user defined template tags and checks for the tag and compile.
        if($this->tags===NULL){
            if(!isset(Doo::conf()->TEMPLATE_TAGS)){
                $this->tags = $this->setTags();
            }else{
                $this->tags = Doo::conf()->TEMPLATE_TAGS;
            }
        }

		// Reduce our workload and remove commented parts before we do anything else
        if(!isset(Doo::conf()->TEMPLATE_SHOW_COMMENT) || Doo::conf()->TEMPLATE_SHOW_COMMENT!=true){
			$str = preg_replace('/' . $this->tagComment_start . '[\s\S]*' . $this->tagComment_end . '/', '', $str);
        }

		// Handel any PHP Tags based on configured rules
		if( isset(Doo::conf()->TEMPLATE_ALLOW_PHP) ){
            if( Doo::conf()->TEMPLATE_ALLOW_PHP === false ){
                $str = preg_replace('/<\?(php|\=|\+)?([\S|\s]*)\?>/Ui', '', $str);
            }
        }else{
            $str = preg_replace_callback('/<\?(php|\=|\+)?([\S|\s]*)\?>/Ui', array( &$this, 'convertPhpFunction'), $str);
        }

		// Handle Variable output in the form of {{foo}}, {{foo.bar}}, {{foo.@bar}}, {{func(foo, 'bar'}}, {{+foo}}, {{+func(foo)}}
		$str = preg_replace_callback('/' . $this->tagVariable_start . '[ ]*([^\t\r\n]+?)[ ]*' . $this->tagVariable_end . '/', array( &$this, 'convertOutputVariable'), $str);

		// Find and replace blocks in the form of {% blockHanlerFunction DATA %}
		$str = preg_replace_callback('/' . $this->tagBlock_start . '[ ]*([a-zA-Z0-9\-\_]+)[ ]*([\s\S]*?)[ ]*'	. $this->tagBlock_end . '/', array( &$this, 'convertBlock'), $str);

        return $str;
    }

	// Inherited stuff from DooView
	private function convertPhpFunction($matches){
        if(stripos($matches[0], '<?php')!==0 && strpos($matches[0], '<?=')!==0 && strpos($matches[0], '<?+')!==0  && strpos($matches[0], '<? ')!==0 ){
            return $matches[0];
        }

        $str = preg_replace_callback('/([^ \t\r\n\(\)}]+)([\s\t]*?)\(/', array( &$this, 'parsePHPFunc'), $matches[2]);
        if(strpos($str, 'php')===0)
            $str = substr($str, 3);

        //if short tag <?=, convert to <?php echo
        if($matches[2][0]=='='){
            $str = substr($str, 1);
            return '<?php echo ' . $str .' ?>';
        }
        //write the variable value
        else if($matches[2][0]=='+'){
            $str = substr($str, 1);
            return eval('return ' . $str);
        }

        return '<?php ' . $str .' ?>';
    }
	private function parsePHPFunc($matches){
        //matches and check function name against template tag
        if(!empty($matches[1])){
            $funcName = trim(strtolower($matches[1]));
            if($funcName[0]=='+' || $funcName[0]=='=')
                $funcName = substr($funcName, 1);

            $controls = array('if','elseif','else if','while','switch','for','foreach','switch','return','include','require','include_once','require_once','declare','define','defined','die','constant','array');

            //skip checking static method usage: TemplateTag::go(), Doo::conf()
            if(stripos($funcName, $this->tags['_class'] . '::')===False && stripos($funcName, 'Doo')===False){
                //$funcname = str_ireplace($this->tags['_class'] . '::', '', $funcname);
                if(!in_array($funcName, $controls)){
                    if(!in_array($funcName, $this->tags)) {
                        return 'function_deny(';
                    }
                }
            }
        }
        return $matches[1].'(';
    }


	// Handel variable/function return value outputting
	private function convertOutputVariable($matches){
		if ($matches[1][0] == '+') {
			$matches[1] = substr($matches[1], 1);
			$writeStaticValue = true;
		}

		$dataIndex = $this->strToStmt($matches[1]);

		if (!empty($writeStaticValue)) {
			$dataIndex = str_replace('$data', '$this->data', $dataIndex);
			return eval('return ' . $dataIndex .';');
		} else {
			return '<?php echo ' . $dataIndex .'; ?>';
		}
    }


	// BLOCK HANDLERS
	protected function convertBlock($matches) {
		$blockName = $matches[1];
		$blockParams = isset($matches[2]) ? $matches[2] : '';

		// Remove any PHP tags from outputting
		$blockParams = $this->stripPHPTags($blockParams);
		$blockName = 'block_' . $blockName;

		if (method_exists($this, $blockName)) {
			return $this->$blockName($blockParams);
		} else {
			return '<span style="color:#ff0000;">Unknown Template Tag : ' . $blockName . '</span>';
		}
	}

	protected function block_set($params) {
		preg_match('/([a-zA-Z0-9\-\_]+) as (.*)/', $params, $matches);
		return '<?php $data[\'' . $matches[1] . '\'] = ' . $this->strToStmt($matches[2]) . '; ?>';
	}

	protected function block_if($params) {
		return '<?php if(' . $this->strToStmt($params) . '): ?>';
	}

	protected function block_elseif($params) {
		return '<?php elseif(' . $this->strToStmt($params) . '): ?>';
	}

	protected function block_else($params) {
		return '<?php else: ?>';
	}

	protected function block_endif($params) {
		return '<?php endif; ?>';
	}

	protected function block_for($params) {

		$metaIdentifer = false;
		$preForStatement = '';
		$forStmt = '';
		$postForStatement = '';

        //for: i from 0 to 10
		if (preg_match('/([a-z0-9\-_]+) from ([^\t\r\n]+) to ([^\t\r\n]+?) step ([^\t\r\n]+?)( with meta)?$/i', $params, $matches)){
			$step = $this->strToStmt($matches[4]);
			$metaIdentifer = isset($matches[5]) ? $matches[1] : false;
			if ($metaIdentifer !== false) {
				$tmp = rand(1000, 9999);
				$preForStatement .= '$_dooTemplateRangeForLoop_' . $tmp . ' = range(' . $this->strToStmt($matches[2]) . ', ' . $this->strToStmt($matches[3]) . ', ' . $step . ");\n";
				$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['length'] = count(\$_dooTemplateRangeForLoop_{$tmp});\n";
				$forStmt .= 'foreach($_dooTemplateRangeForLoop_' . $tmp . ' as $data[\'' . $matches[1] . '\']):';
			} else {
				$forStmt = 'foreach(range(' . $this->strToStmt($matches[2]) . ', ' . $this->strToStmt($matches[3]) . ', ' . $step . ') as $data[\'' . $matches[1] . '\']):';
			}
		}
		elseif (preg_match('/([a-z0-9\-_]+) from ([^\t\r\n]+) to ([^\t\r\n]+?)( with meta)?$/i', $params, $matches)){
			$metaIdentifer = isset($matches[4]) ? $matches[1] : false;
			if ($metaIdentifer !== false) {
				$tmp = rand(1000, 9999);
				$preForStatement .= '$_dooTemplateRangeForLoop_' . $tmp . ' = range(' . $this->strToStmt($matches[2]) . ', ' . $this->strToStmt($matches[3]) . ", 1);\n";
				$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['length'] = count(\$_dooTemplateRangeForLoop_{$tmp});\n";
				$forStmt .= 'foreach($_dooTemplateRangeForLoop_' . $tmp . ' as $data[\'' . $matches[1] . '\']):';
			} else {
				$forStmt = 'foreach(range(' . $this->strToStmt($matches[2]) . ', ' . $this->strToStmt($matches[3]) . ', 1) as $data[\'' . $matches[1] . '\']):';
			}
		}
		// for: 'myArray as key=>val'
		else if (preg_match('/([a-zA-Z0-9\-_]+) as ([a-zA-Z0-9\-_]+)[ ]?=>[ ]?([a-zA-Z0-9\-_]+)( with meta)?/', $params, $matches)) {
			$metaIdentifer = isset($matches[4]) ? $matches[3] : false;
			if ($metaIdentifer !== false) {
				$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['length'] = count(" . $this->strToStmt($matches[1]) . ");\n";
			}
			$forStmt = 'foreach(' . $this->strToStmt($matches[1]) .' as $data[\''.$matches[2].'\']=>$data[\''.$matches[3].'\']):';
		}
		// for: 'myArray as val'
		else if (preg_match('/([a-zA-Z0-9\-_]+) as ([a-zA-Z0-9\-_]+)( with meta)?/', $params, $matches)) {
			$metaIdentifer = isset($matches[3]) ? $matches[2] : false;
			if ($metaIdentifer !== false) {
				$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['length'] = count(" . $this->strToStmt($matches[1]) . ");\n";
			}
			$forStmt = 'foreach(' . $this->strToStmt($matches[1]) .' as $data[\''.$matches[2].'\']):';
		}

		if ($metaIdentifer !== false) {
			$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['index']  = 0;\n";
			$preForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['index0'] = -1;\n";
			$postForStatement  = "\$data['doo']['for']['{$metaIdentifer}']['index']++;\n";
			$postForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['index0']++;\n";
			$postForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['first'] = (\$data['doo']['for']['{$metaIdentifer}']['index0']) == 0 ? true : false;\n";
			$postForStatement .= "\$data['doo']['for']['{$metaIdentifer}']['last']  = (\$data['doo']['for']['{$metaIdentifer}']['index']) == \$data['doo']['for']['{$metaIdentifer}']['length'] ? true : false;\n";
			return '<?php ' . $preForStatement . $forStmt . "\n" . $postForStatement . '?>';
		} else {
			return '<?php ' . $forStmt . '?>';
		}
	}

	protected function block_endfor($params) {
		return '<?php endforeach; ?>';
	}

	protected function block_continue($params) {
		return '<?php continue; ?>';
	}

	protected function block_break($params) {
		return '<?php break; ?>';
	}

	protected function block_cache($params) {
		if(preg_match('/\((.+)(([,][ ]*)([0-9]+))?\)/', $params, $matches)) {
			$matches[1] = $this->strToStmt($matches[1]);
			if (count($matches) == 5) {
				return "<?php if (!Doo::cache('front')->getPart({$matches[1]}, {$matches[4]})): ?>\n<?php Doo::cache('front')->start({$matches[1]}); ?>";
			} else {
				return "<?php if (!Doo::cache('front')->getPart({$matches[1]})): ?>\n<?php Doo::cache('front')->start({$matches[1]}); ?>";
			}
		}
		return "<span style=\"color:#ff0000\">Invalid cache parameters specified.</span>";
	}

	protected function block_endcache($params) {
		return "\n<?php Doo::cache('front')->end(); ?>\n<?php endif; ?>";
	}

	protected function block_include($params) {
		$params = trim($params);
		if (preg_match('/^[\'|\"](.+)[\'|\"]$/', $params, $matches)){
			$file = $matches[1];
		} elseif (preg_match('/([a-zA-Z0-9\-\_]+)/', $params, $matches)) {
			if (isset($this->data[$matches[1]])) {
				$file = $this->data[$matches[1]];
			} else {
				return "<span style=\"color:#ff0000\">Invalid include path. Could not find \$data[{$matches[1]}]</span>";
			}
		} else {
			return "<span style=\"color:#ff0000\">Unrecognised include path. Must be string OR \$data index.</span>";
		}

		if(substr($file, 0,1)=='/'){
            $file = substr($file, 1);
            $cfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            $vfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";
        }
        else{
            $folders = explode('/', $this->mainRenderFolder);
            $file = implode('/', array_splice($folders, 0, -1)).'/'.$file;
            $cfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            $vfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";
        }

        if(!file_exists($vfilename)){
            return "<span style=\"color:#ff0000\">Include view file <strong>$file.html</strong> not found</span>";
        }else{
            if(file_exists($cfilename)){
                if(filemtime($vfilename)>filemtime($cfilename)){
                    $this->compile($file, $vfilename, $cfilename);
                }
            }else{
                $this->compile($file, $vfilename, $cfilename);
            }
        }

        return "<?php include '{$cfilename}'; ?>";
	}


	// UTILITY STUFF
	protected function strToStmt($str) {
		$result = '';
		$currentToken = '';
		$numChars = strlen($str);
		$arrayDepth = 0;
		$inSingleQuoteString = false;
		$inDoubleQuoteString = false;

		for($i = 0; $i < $numChars; $i++) {
			$char = $str[$i];

			switch($char) {
				case '(':	// Moving into a function
					if (!$inSingleQuoteString && !$inDoubleQuoteString && !strpos($currentToken, '->')) {
						if (trim($currentToken) != ''){
							$result .= $this->convertToFunction($currentToken) . '(';
						} else {
							$result .= $currentToken . $char;
						}
						$currentToken = '';
						break;
					}
					$currentToken .= $char;
					break;
				case '[':
					if (!$inSingleQuoteString && !$inDoubleQuoteString) {
						if ($currentToken != '') {		// We have an index like foo.bar[abc] to become $data['foo']['bar'][$data['abc']]
							//$result .= $this->extractArgument($currentToken) . '[';
							$currentToken .= $char;
						} else {
							$result .= 'array(';
							$currentToken .= '';
							$arrayDepth++;
						}
						break;
					}
					$currentToken .= $char;
					break;
				case ']':
					if (!$inSingleQuoteString && !$inDoubleQuoteString) {
						if ($arrayDepth > 0) {
							$result .= $this->extractArgument($currentToken) . ')';
							$currentToken = '';
							$arrayDepth--;
						} else {
							//$result .= $this->extractArgument($currentToken) . ']';
							$currentToken .= $char;
						}
						break;
					}
					$currentToken .= $char;
				case'\\':	// Handle any escaped quotes
					$currentToken = $char;
					if ($inSingleQuoteString || $inDoubleQuoteString) {
						if (isset($char[$i+1])) {
							$nextChar = $char[$i+1];
							if ($nextChar == '\'' || $nextChar == '"') {
								$currentToken .= $nextChar;
								$i++;
							}
						}
					}
					break;
				case '"':
					if ($inDoubleQuoteString) {
						$currentToken .= $char;
						$inDoubleQuoteString = false;
					} else {
						$currentToken .= $char;
						$inDoubleQuoteString = true;
					}
					break;
				case'\'':
					if($inSingleQuoteString){
						$currentToken .= $char;
						$inSingleQuoteString = false;
					} else {
						$currentToken .= $char;
						$inSingleQuoteString = true;
					}
					break;
				case '=':	// If = appears in an array then its used for associatve array
							// Otherwise its a delimiter for example <= == etc..
					if ($arrayDepth > 0) {
						if (!$inSingleQuoteString && !($inDoubleQuoteString)) {
							if ($currentToken != null && $currentToken[0] != '\'' && $currentToken[0] != '"') {
								$currentToken = '\'' . $currentToken . '\'';
							}
							$result .= strtolower($currentToken) . '=>';
							$currentToken = '';
							break;
						}
					}
					$currentToken .= $char;
					break;
				case ')': // Reached the end of a parameter
				case ',': // Reached the end of a parameter
					if (!$inSingleQuoteString && !$inDoubleQuoteString && !strpos($currentToken, '->')) {
						$result .= $this->extractArgument($currentToken) . $char;
						$currentToken = '';
					} else {
						$currentToken .= $char;
					}
					break;
				case ' ':
					if ($inSingleQuoteString || $inDoubleQuoteString) {
						$currentToken .= $char;
					} else {
						$result .= $this->extractArgument($currentToken);
						$currentToken = '';
					}
					break;
				default:	// Not doing anything - keep moving
					$currentToken .= $char;
			}
		}
		$result .= $this->extractArgument($currentToken);

		return $result;
	}

	protected function extractArgument($arg) {

		if (in_array($arg, array('', '&&', '||', '<=', '==', '>=', '!=', '===', '!==', '<', '>', '+', '-', '*', '/'))) {
			return $arg;
		}

		// Is a number
		if (preg_match('/^[0-9]*\\.?[0-9]{0,}$/', $arg)) {
			return $arg;
		}
		// Is a string 'anything' OR "anything"
		elseif (preg_match('/^[\'\"].*[\'\"]$/', $arg)) {
			return str_replace('\/\.\;', ',', $arg);
		}
		elseif (strtolower($arg)=='true' || strtolower($arg)=='false') {
			return $arg;
		}
		// Got parameter values to handle
		else {
			return $this->extractDataPath($arg);
		}
	}

	protected function extractDataPath($str) {
		$result = '$data';
		$currentToken = '';
		$numChars = strlen($str);
		$isNumericIndex = false;
		$processingArrayIndex = true;		// True if an array index. False for object index
		for($i = 0; $i < $numChars; $i++) {
			$char = $str[$i];

			switch($char) {
				case '(':
					$depth = 1;
					$functionContentToken = '';
					for($j=$i+1; $j < $numChars && $depth != 0; $j++) {
						switch ($str[$j]) {
							case '(':
								$depth++;
								$functionContentToken .= $str[$j];
								break;
							case ')':
								$depth--;
								if ($depth != 0) {
									$functionContentToken .= $str[$j];
								}
								break;
							default:
								$functionContentToken .= $str[$j];
						}
					}
					$i = $j - 1;
					$currentToken .= '(' . $this->strToStmt($functionContentToken) . ')';
					break;
				case '[':
					$depth = 1;
					$arrayIndexContentToken = '';
					for($j=$i+1; $j < $numChars && $depth != 0; $j++) {
						switch ($str[$j]) {
							case '[':
								$depth++;
								$arrayIndexContentToken .= $str[$j];
								break;
							case ']':
								$depth--;
								if ($depth != 0) {
									$arrayIndexContentToken .= $str[$j];
								}
								break;
							default:
								$arrayIndexContentToken .= $str[$j];
						}
					}
					$i = $j - 1;
					$currentToken .= '[' . $this->strToStmt($arrayIndexContentToken) . ']';
					break;
				case '-':
					if (isset($str[$i+1])) {
						if($str[$i+1] == '>') {
							if ($processingArrayIndex) {
								if (is_numeric($currentToken))
									$result .= '[' . $currentToken . ']';
								else
									$result .= '[\'' . $currentToken . '\']';
							} else {
								$result .= '->' . $currentToken;
							}

							$processingArrayIndex = false;
							$currentToken = '';
							$i++;
							break;
						}
					}
					$currentToken .= $char;
					break;
				case '.':
					if ($processingArrayIndex) {
						if (is_numeric($currentToken))
							$result .= '[' . $currentToken . ']';
						else
							$result .= '[\'' . $currentToken . '\']';
					} else {
						$result .= '->' . $currentToken;
					}
					$currentToken = '';
					$processingArrayIndex = true;
					break;
				default:
					$currentToken .= $char;

			}
		}
		if ($currentToken != '') {
			if ($processingArrayIndex) {
				if (is_numeric($currentToken))
					$result .= '[' . $currentToken . ']';
				else
					$result .= '[\'' . $currentToken . '\']';
			} else {
				$result .= '->' . $currentToken;
			}
		}
		return $result;
	}

	private function convertToFunction($funcName) {
		if(!in_array(strtolower($funcName), $this->tags)) {
            return 'function_deny';
        }

        if(isset($this->tags['_methods']) && in_array($funcName, $this->tags['_methods'])===True){
            $funcName = $this->tags['_class'] . '::' . $funcName;
        }
		return $funcName;
	}

	private function stripCommaStr($matches) {
		$str = implode('\/\.\;', explode(',', $matches[0]) );
        $str = substr($str, 1, strlen($str)-2);
        return "'".$str."'";
	}

	private function stripPHPTags($str) {
		$str = str_replace('<?php echo ', '', $str);
        return str_replace('; ?>', '', $str);
	}

}
?>