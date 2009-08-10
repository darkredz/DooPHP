<?php
/**
 * DooFileCache class file.
 *
 * @author David Shieh <mykingheaven@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * This class is for file cache using.
 * 
 * @author David Shieh <mykingheaven@gmail.com>
 *
 */
class DooFileCache extends DooCache {
	
	private static $_cache;
	
	private $_directory = false;

	/**
	 * Chdir to cache directory
	 * @return unknown_type
	 */
	protected function __construct() {
		// If user set a path for cache, save it
		if (isset(Doo::conf()->CACHE_PATH)) {
			$this->_directory = Doo::conf()->CACHE_PATH;
		}
		// If user set a path, change the current directory to user's
		if ($this->_directory !== false) {
			if (is_dir(Doo::conf()->SITE_PATH . 'protected/cache/' . $this->_directory))
				chdir(Doo::conf()->SITE_PATH . 'protected/cache/' . $this->_directory);
			else {
				mkdir(Doo::conf()->SITE_PATH . 'protected/cache/' . $this->_directory);
				chdir(Doo::conf()->SITE_PATH . 'protected/cache/' . $this->_directory);
			}
		}
	}
	
	/**
	 * Get the cache instance
	 * @return unknown_type
	 */
	static function cache() {
		if (self::$_cache == null) {
			self::$_cache = new DooFileCache();
		}
		return self::$_cache;
	}
	
	/**
	 * Get value from file by $id
	 * @see dooframework/cache/DooCache#get($id)
	 */
	public function get($id) {
		if (file_exists($this->generateKey($id)))
			return unserialize(file_get_contents($this->generateKey($id)));
		else
			return null;
	}
	
	/**
	 * Set value into file by $id
	 * If $duration = 0, set it to a year
	 * @see dooframework/cache/DooCache#set($id, $value, $duration)
	 */
	public function set($id, $value, $duration = 0) {
		if ($duration < 0)
			$duration = 31536000;
		$duration = $duration + time();
		if (file_put_contents($this->generateKey($id), serialize($value), LOCK_EX)) {
			@chmod($this->generateKey($id),0777);
			return @touch($this->generateKey($id),$duration);
		}
		else
			return false;
	}
	
	/**
	 * Delete a cache file by $id
	 * @param $id
	 * @return unknown_type
	 */
	public function flush($id) {
		if ($id !== null) {
			@unlink($this->generateKey($id));
			return true;
		}
		return false;
	}
	
	public function flushAll() {
		$handle = opendir(Doo::conf()->SITE_PATH . 'protected/cache');
		
		while(($file = readdir($handle)) !== false) {
			if (is_file($file))
				@unlink($file);
		}
		return true;
	}
}
