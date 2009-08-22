<?php
/**
 * DooCache class file.
 *
 * @author David Shieh <mykingheaven@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * 
 * A abstract class for all cache classes.
 * This class defines all usable parameters and methods.
 * All cache classes must inherit this class to support uniform methods.
 * 
 * @author David Shieh <mykingheaven@gmail.com>
 * @version $Id: DooCache.php 1000 2009-08-22 18:38:42
 * @package doo.cache
 * @since 1.1
 */
abstract class DooCache {
	/**
	 * Set datas to cache
	 * @param $name
	 * @return true or false
	 */
	public function set($id, $value, $duration=0) {}
	
	/**
	 * Get datas from cache
	 * @param $name
	 * @return $data or false
	 */
	public function get($id) {}
	
	/**
	 * Clear datas in cache
	 * @param $name
	 * @return true or false
	 */
	public function flush($id) {}
	
	/**
	 * Clear all datas in all caches
	 * @return true or false
	 */
	public function flushAll() {}
	
	/**
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	protected function generateKey($id) {
		return $id !== null ? md5($id) : false;
	}
}