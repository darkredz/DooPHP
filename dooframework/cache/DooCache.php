<?php
/**
 * DooRestClient class file.
 *
 * @author David Shieh <mykingheaven@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * A abstract class for all cache classes.
 * This class defines all usable parameters and methods.
 * All cache classes must inherit this class to support uniform methods.
 * 
 * @author David Shieh <mykingheaven@gmail.com>
 *
 */
abstract class DooCache {
	private $_id;
	
	/**
	 * Set datas to cache
	 * @param $name
	 * @return true or false
	 */
	public function setCache($name) {}
	
	/**
	 * Get datas from cache
	 * @param $name
	 * @return $data or false
	 */
	public function getCache($name) {}
	
	/**
	 * Clear datas in cache
	 * @param $name
	 * @return true or false
	 */
	public function flushCache($name) {}
	
	/**
	 * Clear all datas in all caches
	 * @return true or false
	 */
	public function flushAllCache() {}
}