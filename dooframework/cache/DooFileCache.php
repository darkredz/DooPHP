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
 * @version $Id: DooFileCache.php 1000 2009-08-22 18:38:42
 * @package doo.cache
 * @since 1.1
 *
 */

Doo::loadCore('cache/DooCache');

class DooFileCache extends DooCache {
	
	private static $_cache;
	
	private $_directory;

	public function __construct($path='') {
		if ( $path=='' ) {
			if(isset(Doo::conf()->CACHE_PATH))
				$this->_directory = Doo::conf()->CACHE_PATH;
			else
				$this->_directory = Doo::conf()->SITE_PATH . 'protected/cache/';
		}else{
			$this->_directory = $path;
		}
	}
	
	/**
	 * Get the cache instance
	 * @return DooFileCache
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
			chmod($this->generateKey($id),0777);
			return touch($this->generateKey($id),$duration);
		}
		else
			return false;
	}
	
	/**
	 * Delete a cache file by Id
	 * @param $id Id of the cache
	 * @return mixed
	 */
	public function flush($id) {
		if ($id !== null) {
			unlink($this->generateKey($id));
			return true;
		}
		return false;
	}

        /**
         * Deletes all data cache files
         * @return bool
         */
	public function flushAll() {
		$handle = opendir($this->_directory);
		
		while(($file = readdir($handle)) !== false) {
			if (is_file($file))
                            unlink($file);
		}
		return true;
	}
}
?>