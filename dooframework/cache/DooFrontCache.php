<?php
/**
 * DooFrontCache class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooFrontCache provides frontend caching utilities.
 * <p>It can be retrieved with the shorhand Doo::cache('front'). The frontend cache supports full page and partial page caching mechanism.
 * Cache files are store in the path defined in $config['CACHE_PATH'].</p>
 *
 * <p>You can start the caching before displaying the view output.</p>
 * <code>
 * //Display cache if exist and exit the script(full page cache)
 * Doo::cache('front')->get();	
 * 
 * //Start recording and cache the page.
 * Doo::cache('front')->start();
 * $this->view()->render('index',$data);
 * Doo::cache('front')->end();
 * </code>
 *
 * <p>Partial cache can be used with the template engine:</p>
 * <code>
 * $cacheOK = Doo::cache('front')->testPart('latestuser');
 * //if cache exist, skip retrieving from DB
 * if(!$cacheOK){
 *     //get list from DB if cache not exist.
 * }
 * $this->view()->render('index',$data);
 *
 * //in the Template file
 * <!-- cache('latestuser', 60) -->
 * <li>The list of results to be loop</li>
 * <!-- endcache -->
 * </code>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooFrontCache.php 1000 2009-08-11 18:28:42
 * @package doo.cache
 * @since 1.1
 */
class DooFrontCache{
	
	private $_directory;
	private $_cachefile;
	
	public function __construct($path='') {
		if ( $path=='' ) {
			if(isset(Doo::conf()->CACHE_PATH))
				$this->_directory = Doo::conf()->CACHE_PATH . 'frontend/';
			else
				$this->_directory = Doo::conf()->SITE_PATH . 'protected/cache/frontend/';
		}else{
			$this->_directory = $path;
		}
	}
	
	public function setPath($path){
		$this->_directory = $path;
	}
	
	/**
	 * Retrieve the full page cache.
	 * @param int $secondsCache Duration till the cache expired 
	 */
	public function get($secondsCache=60){
		if($id==''){
			$this->_cachefile = str_replace('/','-',$_SERVER['REQUEST_URI']).'.html';
		}else{
			$this->_cachefile  = $id.'.html';
		}
		
		$this->_cachefile  = $this->_directory.$this->_cachefile;
		
		// If the cache has not expired, include it.
		if (file_exists($this->_cachefile) && time() - $secondsCache < filemtime($this->_cachefile)) {
			include $this->_cachefile;
			echo "<h1> Cached copy, generated ".date('H:i', filemtime($this->_cachefile ))." </h1>\n";
			exit;
		}
	}

	/**
	 * Retrieve the partial page cache.
	 * @param string $id ID of the partial cache.
	 * @param int $secondsCache Duration till the cache expired 
	 */
	public function getPart($id, $secondsCache=60){
		$this->_cachefile  = $this->_directory.$id.'.html';
		
		// If the cache has not expired, include it.
		if (file_exists($this->_cachefile) && time() - $secondsCache < filemtime($this->_cachefile)) {
			include $this->_cachefile;
			//echo "<h1> Cached loaded, generated time".date('H:i', filemtime($this->_cachefile ))." </h1>\n";
			return true;
		}
	}
	
	/**
	 * Frontend cache start. Start the output buffer.
	 * @param string $id ID of the cache. To be used with partial cache
	 */
	public function start($id=''){
		if($id!=''){
			$this->_cachefile  = $this->_directory.$id.'.html';
		}
		ob_start(); 
	}
	
	/**
	 * Frontend cache ending. Cache the output to a file in the defined cache folder.
	 */
	public function end(){
		$fp = fopen($this->_cachefile, 'w+');
		fwrite($fp, ob_get_contents());
		fclose($fp);
		ob_end_flush(); 
	}
	
	/**
	 * Check if the partial cache exists and is not expire
	 * @param string $id ID of the partial cache.
	 * @param int $secondsCache Duration till the cache expired 
	 * @return bool Returns true if the cache exists and is not yet expire.
	 */
	public function testPart($id, $secondsCache=60){
		if($id!=''){
			$this->_cachefile  = $this->_directory.$id.'.html';
			return (file_exists($this->_cachefile) && time() - $secondsCache < filemtime($this->_cachefile));
		}
		return false;
	}
}

?>