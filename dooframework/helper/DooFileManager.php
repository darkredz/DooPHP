<?php
/**
 * DooFileManager class file
 * @package doo.helper
 * @author Richard Myers <richard.myers@hotmail.co.uk>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * Provides functions for managing File System
 */
class DooFileManager {

	/**
	 * If the folder does not exist creates it (recursively)
	 * @param string $path Absolute path to folder to be created
	 * @param int Chmod code used for new folders
	 */
	public static function createFolder($path, $mode = Null) {
		if($mode!=Null)
			$result = mkdir($path, $mode, true);
		else
			$result = mkdir($path);
		if ($result === false) {
			throw new DooFileManagerException('Could not create folder: ' . $path);
		}
	}


	/**
	 * Delete a folder (and all files and folders below it)
	 * @param string $path Absoloute path to folder to be deleted
	 * @param bool $deleteMe true if the folder should be deleted. false if just its contents.
	 */
	public static function deleteFolder($path, $deleteMe = true) {
		if (is_dir($filepath) && !is_link($filepath)) {
			if ($dh = opendir($filepath)) {
				while (($sf = readdir($dh)) !== false) {
					if ($sf == '.' || $sf == '..' ) {
						continue;
					}
					if (!self::deleteFolder($filepath.'/'.$sf, true)) {
						closedir($dh);
						throw new DooFileManagerException($filepath.'/'.$sf.' could not be deleted.');
					}
				}
				closedir($dh);
			}
			if ($deleteMe)
				return rmdir($filepath);
		}
		if ($deleteMe)
			return unlink($filepath);
	}

}

/**
 * Exception thrown by DooFileManager on errors
 */
class DooFileManagerException extends Exception {
	
}

?>