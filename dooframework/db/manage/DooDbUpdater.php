<?php

abstract class DooDbUpdater {

	/**
	 * This represents the newest version update avaliable
	 * This should reflect the id of the newest upgrade_* function
	 * @var int
	 */
	protected $latestVersion = 0;

	/**
	 * This stores the current databases version
	 * @var int
	 */
	protected $currentDbVersion = null;

	/**
	 * A DooDbAdmin adapater for the active DB engine
	 * @var DooManageDb
	 */
	public $db = null;

	/**
	 * This is the mode of DB being used and relates to the application mode
	 * This will be 'dev', 'prod' etc.
	 * @var string
	 */
	protected $dbMode = null;

	/**
	 * This is the folder in which database update tracking files will be stored
	 * There will be one file [APP_MODE].version created for each DooConfig::APP_MODE setting
	 * The value in here indicates the current DB version for the current app mode
	 * @var string
	 */
	protected $versionTrackingLocation = null;

	/**
	 * Sets up the DooDbUpdater
	 * @param string $versionTrackingLocation folder path where tracking information files should be stored
	 * @param array $dbConfig Database connection settings (db_host, db_name, db_user, db_pwd, db_driver, db_connection_cache)
	 * @param string $appMode The mode the site is running in. DooDbUpdater supports different database versions for each app mode
	 */
	public function __construct($versionTrackingLocation, $dbConfig, $appMode) {

		$this->versionTrackingLocation = $versionTrackingLocation;

		$this->dbMode = $appMode;

		// Load the correct Db Manager adapter based on the db_engine/driver being used
		$this->db = $this->getDbEngineManager($dbConfig[4]);	// element 4 is the db_driver

		// Configure the database connection settings
		$this->db->setDb($dbConfig);
		$this->db->connect();
	}

	public function enableDebugging($enable) {
		$this->db->enableSqlHistory($enable);
	}

	public function getCurrentDbVersion() {
		$version = $this->retrieveCurrentDbVersion();
		if ($version === false) {
			return 0;
		} else {
			return (int) $version;
		}
	}

	/**
	 * Will run all of the updates since the current version upto and including the
	 * DooDbUpdater::latestVersion. eg. if the current version is 5 and the latestVersion
	 * is set to 10 then updates 6, 7, 8, 9 and 10 will be run
	 * @return void
	 */
	public function updateToLatestVersion() {
		$this->updateToVersion($this->latestVersion);
	}

	/**
	 * Will run all of the downgrade steps from the current version to the very start
	 * This will leave the database as it was before any upgrades
	 * @return
	 */
	public function revertToStart() {
		$this->downgradeToVersion(0);
	}

	/**
	 * Will run all of the required updates since the current version upto and including
	 * the the version specified. eg. A call to updateToVersion(10) when the current version
	 * is 5 will run updates 6, 7, 8, 9 and 10
	 * @param int $version Version to update to
	 * @return
	 */
	public function updateToVersion($version) {

		if ($this->getCurrentDbVersion() >= $version) {
			return;
		}

		$updateTo = null;

		try {
			for ($updateTo = $this->getCurrentDbVersion() + 1; $updateTo <= $version; $updateTo++) {
				$this->executeVersionUpdate($updateTo);
				$this->storeCurrentDbVersion($updateTo);
			}
		} catch (Exception $ex) {
			throw new DooDbUpdateException("Error Running Database Update : $updateTo", $ex->getMessage());
		}
	}

	/**
	 * Will run all of the required downgrades from the current version down to and including
	 * the the version specified. eg. A call to downgradeToVersion(5) when the current version
	 * is 10 will run downgrades 10, 9, 8, 7 and 6
	 * @param int $version Version to downgrade to
	 * @return
	 */
	public function downgradeToVersion($version) {

		if ($this->getCurrentDbVersion() <= $version) {
			return;
		}

		$downgradeTo = null;

		try {
			for ($downgradeTo = $this->getCurrentDbVersion(); $downgradeTo > $version; $downgradeTo--) {
				$this->executeVersionDowngrade($downgradeTo);
				$this->storeCurrentDbVersion($downgradeTo);
			}
			$this->storeCurrentDbVersion($downgradeTo);
		} catch (Exception $ex) {
			throw new DooDbUpdateException("Error Running Database Downgrade : $downgradeTo", $ex->getMessage());
		}
	}


	/**
	 * Runs a single update step so a call to executeVersionUpdate(12) will run the function
	 * upgrade_12() in the extending class. It will throw an exception on an error running the
	 * update or if the update function is missing
	 * @param int $version The update version to run
	 * @return
	 */
	public function executeVersionUpdate($version) {
		$methodName = 'upgrade_' . $version;

		if (method_exists($this, $methodName) == true) {
			$this->$methodName();
		} else {
			throw new DooDbUpdateException("Database upgrade function $methodName could not be found");
		}
	}

	/**
	 * Runs a single downgrade step so a call to executeVersionDowngrade(12) will run the function
	 * downgrade_12() in the extending class. It will throw an exception on an error running the
	 * downgrade or if the downgrade function is missing
	 * @param int $version The downgrade version to run
	 * @return
	 */
	public function executeVersionDowngrade($version) {
		$methodName = 'downgrade_' . $version;

		if (method_exists($this, $methodName) == true) {
			$this->$methodName();
		} else {
			throw new DooDbUpdateException("Database downgrade function $methodName could not be found");
		}
	}

	/**
	 * Returns the current version of the database. This is taken from the file:
	 * DooDbUpdater::$versionTrackingLocation . DooDbUpdater::$dbMode . '.version'
	 * @return int
	 */
	protected function retrieveCurrentDbVersion() {
		$version = DooFile::readFileContents($this->versionTrackingLocation . $this->dbMode . '.version');
		if ($version === false) {
			return 0;
		} else {
			return (int) $version;
		}
	}

	/**
	 * Stores the current version the database has been upgraded to in the file
	 * DooDbUpdater::$versionTrackingLocation . DooDbUpdater::$dbMode . '.version'
	 * @param int $version the current version which the db has been updated to
	 * @return void
	 */
	protected function storeCurrentDbVersion($version) {
		DooFile::createFileAndWriteContents($this->versionTrackingLocation . $this->dbMode . '.version', $version);
	}

	/**
	 * Gets an instance of a Database Admin Adapter for the current DB's Engine
	 * @param object $engine
	 * @return
	 */
	private function getDbEngineManager($engine) {

		$engine = strtolower($engine);

		if ($engine == 'mysql') {
			Doo::loadCore('db/manage/adapters/DooManageMySqlDb');
			return new DooManageMySqlDb();
		} else if ($engine == 'pgsql') {
			Doo::loadCore('db/manage/adapters/DooManagePgSqlDb');
			return new DooManagePgSqlDb();
		} else if ($engine == 'sqlite') {
			Doo::loadCore('db/manage/adapters/DooManageSqliteDb');
			return new DooManageSqliteDb();
		} else {
			throw new DooDbUpdateException("Unsupported Database Engine : $engine");
		}
	}
}



// TODO: Move DooFile into its own folder so it can be used elsewhere
class DooFile {

	/**
	 * Writes content to a file and will create the folder / file if it does not exist
	 * @param string $fullFilePath Full path to the file
	 * @param string $contents Content to be written to the file
	 * @param int $flags Standard flags allowed by file_put_contents
	 * @return bool True on successful write
	 */
	static function createFileAndWriteContents($fullFilePath, $contents, $flags = 0) {
		$parts = explode('/', $fullFilePath);
    	$file = array_pop($parts); // We dont create a folder for the actual file!
    	$dir = '';
		if (file_exists(dirname($fullFilePath)) == false) {
			foreach($parts as $part) {
	            if(is_dir($dir .= "/$part") == false) {
	            	mkdir($dir);
	            }
			}
		}
		file_put_contents($fullFilePath, $contents, $flags);
		return true;
	}

	/**
	 * Read contents from a file
	 * @param string $fullFilePath Path to the file to be read
	 * @return mixed The files contents or FALSE
	 */
	static function readFileContents($fullFilePath) {
		if (file_exists($fullFilePath)) {
			return file_get_contents($fullFilePath);
		} else {
			return false;
		}
	}

	// TODO: Atomic Safe File Read
	// TODO: Atomic Safe File Write
}


class DooDbUpdateException extends Exception {
	/**
	 * An exception thrown by the DooUpdater
	 * @param string $error The error which occured
	 * @param string $reason [optional] Reason for the error
	 * @return
	 */
	function __construct($error, $info = "Unknown") {
		parent::__construct($error . "\n" . $info);
	}
}
