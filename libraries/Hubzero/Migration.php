<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
* HUBzero Database migrations class
* 
* @TODO: add flag to ignore development scripts?
*/
class Hubzero_Migration
{
	/**
	 * Document root in which to search for migration scripts
	 *
	 * @var string
	 **/
	private $docroot = '';

	/**
	 * Array holding paths to migration scripts
	 *
	 * @var array
	 **/
	private $files = array();

	/**
	 * Variable holding database object
	 *
	 * @var string
	 **/
	private $db = null;

	/**
	 * Logging type (ex: stdout, php error log)
	 *
	 * @var string
	 **/
	private $log_type = array('error_log');

	/**
	 * Log messages themselves (stored as array to return to browser, or something that won't stream output)
	 *
	 * @var array
	 **/
	private $internal_log = array();

	/**
	 * Date of last migrations run - implicit by last log entry
	 *
	 * @var array
	 **/
	private $last_run = array('up'=>null, 'down'=>null);

	/**
	 * Constructor
	 *
	 * @param $docroot - default null, which should then resolve to hub docroot
	 * @param $log_type - additional location to log updates/errors
	 * @return void
	 **/
	public function __construct($docroot=null, $log_type=null)
	{
		// Try to determine the document root if none provided
		if (is_null($docroot))
		{
			if (is_file(dirname(dirname(dirname(__FILE__))) . '/configuration.php'))
			{
				$this->docroot = dirname(dirname(dirname(__FILE__)));
			}
			else
			{
				$conf = '/etc/hubzero.conf';
				if (is_file($conf) && is_readable($conf))
				{
					$content = file_get_contents($conf);
					preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);
					if (isset($matches[1]))
					{
						$this->docroot = $matches[1];
					}
				}
				else
				{
					// Can't retrieve default docroot
					$this->log('Could not detect default document root, and none provided.');
					return false;
				}
			}
		}
		else
		{
			$this->docroot = rtrim($docroot, '/');
		}

		// Set the log type if one is given
		if (is_string($log_type))
		{
			$this->log_type[] = $log_type;
		}
		elseif (is_array($log_type))
		{
			$this->log_type = array_merge($log_type, $this->log_type);
		}

		// If log type includes error log and stdout, but no cli error log destination is set, only use one of the two
		// If no error_log path is set, it seems to just echo instead of writing to a file, effectively printing messages twice
		$path = ini_get('error_log');
		if (empty($path)
			&& in_array('stdout', $this->log_type)
			&& in_array('error_log', $this->log_type)
			&& php_sapi_name() == 'cli')
		{
			$key = array_search('error_log', $this->log_type);
			if ($key !== false)
			{
				unset($this->log_type[$key]);
			}
		}

		// Setup joomla environment
		if (php_sapi_name() == 'cli' && !defined('DS'))
		{
			$this->joomlaInit();
		}

		// Setup the database connection
		if (!$this->db = $this->getDBO())
		{
			$this->log('Error: database connection failed.');
			return false;
		}

		// Try to figure out the date of the last file run
		try
		{
			$this->db->setQuery('SELECT `file` FROM `migrations` WHERE `direction` = \'up\' ORDER BY `date` DESC LIMIT 1');
			$rowup = $this->db->loadAssoc();

			$this->db->setQuery('SELECT `file` FROM `migrations` WHERE `direction` = \'down\'ORDER BY `date` DESC LIMIT 1');
			$rowdown = $this->db->loadAssoc();

			if (count($rowup) > 0)
			{
				$this->last_run['up'] = substr($rowup['file'], 9, 14);
			}
			if (count($rowdown) > 0)
			{
				$this->last_run['down'] = substr($rowdown['file'], 9, 14);
			}
		}
		catch (PDOException $e)
		{
			$this->log('Error: failed to look up last migrations log entry.');
			return false;
		}
	}

	/**
	 * Setup Joomla environment
	 *
	 * @return class var
	 **/
	private function joomlaInit()
	{
		// See if this looks like a valid joomla document root
		if (file_exists($this->docroot . '/configuration.php'))
		{
			$docroot = $this->docroot;
		}
		else
		{
			if (is_file(is_file(dirname(dirname(dirname(__FILE__))) . '/configuration.php')))
			{
				$docroot = dirname(dirname(dirname(__FILE__)));
			}
			else
			{
				// We couldn't find a config file in the provided doc root, so try to find one another way
				$conf = '/etc/hubzero.conf';
				if (is_file($conf) && is_readable($conf))
				{
					$content = file_get_contents($conf);
					preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);

					if (isset($matches[1]))
					{
						$docroot = rtrim($matches[1], '/');
					}
					else
					{
						$this->log('Could not find a reasonable Joomla document root.');
						return false;
					}
				}
				else
				{
					$this->log('Could not find a HUBzero configuration file');
					return false;
				}
			}
		}

		define('DS', '/');
		define('JPATH_ROOT', $docroot);
		define('JPATH_BASE', JPATH_ROOT);
		define('JPATH_SITE', JPATH_ROOT);
		define('JPATH_CONFIGURATION', JPATH_ROOT);
		define('JPATH_INSTALLATION', JPATH_ROOT . DS . 'installation');
		define('JPATH_ADMINISTRATOR', JPATH_ROOT . DS . 'administrator');
		define('JPATH_LIBRARIES', JPATH_ROOT . DS . 'libraries');
		define('JPATH_XMLRPC', JPATH_ROOT . DS . 'xmlrpc');

		require(JPATH_LIBRARIES.DS.'loader.php');

		JLoader::import('joomla.error.error');
		JLoader::import('joomla.factory');
		JLoader::import('joomla.base.object');
		JLoader::import('joomla.database.database');

		if (!defined('JVERSION'))
		{
			JLoader::import('joomla.version');
			$version = new JVersion();
			define('JVERSION', $version->getShortVersion());
		}
	}

	/**
	 * Getter for class private variables
	 *
	 * @param $var - var to retrieve
	 * @return class var
	 **/
	public function get($var)
	{
		if (property_exists($this, $var))
		{
			return $this->$var;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Setup database connect, test, return object
	 *
	 * @return database object
	 **/
	public function getDBO()
	{
		// Include the joomla configuration file
		if (file_exists($this->docroot . '/configuration.php'))
		{
			// If there's one in the provided doc root, use that
			require_once $this->docroot . '/configuration.php';
		}
		else
		{
			if (is_file(is_file(dirname(dirname(dirname(__FILE__))) . '/configuration.php')))
			{
				$docroot = dirname(dirname(dirname(__FILE__)));
			}
			else
			{
				// We couldn't find a config file in the provided doc root, so try to find one another way
				$conf = '/etc/hubzero.conf';
				if (is_file($conf) && is_readable($conf))
				{
					$content = file_get_contents($conf);
					preg_match('/.*DocumentRoot\s*=\s*(.*)\n/i', $content, $matches);

					if (isset($matches[1]))
					{
						require_once $matches[1] . '/configuration.php';
					}
					else
					{
						$this->log('Could not find a Joomla configuration file');
						return false;
					}
				}
				else
				{
					$this->log('Could not find a HUBzero configuration file');
					return false;
				}
			}
		}

		// Instantiate a config object
		$config = new JConfig();

		$db = JDatabase::getInstance(
			array(
				'driver'   => 'pdo',
				'host'     => $config->host,
				'user'     => $config->user,
				'password' => $config->password,
				'database' => $config->db,
				'prefix'   => 'jos_'
			)
		);

		// Test the connection
		if (!$db->connected())
		{
			$this->log('PDO connection failed');
			return false;
		}

		// Check for the existance of the migrations table
		try
		{
			$db->setQuery("SELECT `id` FROM `migrations` LIMIT 1");
			$db->query();
		}
		catch (PDOException $e)
		{
			if ($this->createMigrationsTable($db) === false)
			{
				return false;
			}
		}

		return $db;
	}

	/**
	 * Find all migration scripts
	 *
	 * @param $extension - only look for migrations for this extension
	 * @return array of file paths
	 **/
	public function find($extension=null)
	{
		// Exclude certain thiings from our search
		$exclude = array(".", "..", "__migration_template.php");

		$files = array_diff(scandir($this->docroot . DS . 'migrations'), $exclude);
		$ext   = '';

		if (!is_null($extension))
		{
			$parts = explode('_', $extension);
			foreach ($parts as $part)
			{
				$ext .= ucfirst($part);
			}
		}

		foreach ($files as $file)
		{
			// Make sure they have a php extension and proper filename format
			if (preg_match('/^Migration[0-9]{14}[a-zA-Z]+\.php$/', $file))
			{
				// If an extension was provided...match against it...
				if (empty($ext) || (!empty($ext) && preg_match('/Migration[0-9]{14}'.$ext.'\.php/', $file)))
				{
					$this->files[] = $file;
				}
			}
		}

		return true;
	}

	/**
	 * Migrate up/down on all files gathered via 'find'
	 *
	 * @param $direction   - direction to migrate (up or down)
	 * @param $force       - run the update, even if the database says it's already been run
	 * @param $dryrun      - run the udpate, but only display what would be changed, wihthout actually doing anything
	 * @param $ignoreDates - run the update on all files found lacking entries in the db, not just those after the last run date
	 * @param $logOnly     - run the update, and mark as run, but don't actually run sql (usefully to mark changes that had already been made manually)
	 * @param $print       - print the contents of effected files
	 * @return bool - success
	 **/
	public function migrate($direction='up', $force=false, $dryrun=false, $ignoreDates=false, $logOnly=false, $print=false)
	{
		// Make sure we have files
		if (empty($this->files))
		{
			$this->log("There were no migrations to run");
			return true;
		}

		// Notify if we're making a dry run
		if ($dryrun)
		{
			$this->log("Dry run: no changes will be made!");
		}

		// Notify if we're ignoring dates
		if ($ignoreDates)
		{
			$this->log("Ignore dates: all eligible files will be included!");
		}

		if ($print)
		{
			$contents = array();
		}

		// Loop through files and run their '$direction' method
		foreach ($this->files as $file)
		{
			// Create a hash of the file (not using this at the moment)
			$hash = hash('md5', $file);

			// Don't compare dates if we're doing a full scan
			if (!$ignoreDates)
			{
				// Get date from file (would use last modified date, but git changes that)
				// Running up, down, and up again won't do anything, because the file is now older than the last run.
				// Currently, one should run $force or $ignoreDates and include an extension to override that behavior
				$date = substr($file, 9, 14);
				if (is_numeric($date))
				{
					if (is_numeric($this->last_run[$direction]) && $date <= $this->last_run[$direction] && !$force)
					{
						continue;
					}
				}
				else
				{
					// Filename did not contain a valid date
					$this->log("File did not contain a valid date ({$file}).");
					continue;
				}
			}

			// Initialize ignore
			$ignore = false;

			// Check to see if this file has already been run
			try
			{
				// Look to the database log to see the last run on this file
				$this->db->setQuery("SELECT `direction` FROM migrations WHERE `file` = " . $this->db->Quote($file) . " ORDER BY `date` DESC LIMIT 1");
				$row = $this->db->loadResult();

				// If the last migration for this file doesn't exist, or, it was the opposite of $direction, we can go ahead and run it.
				// But, if the last run was the same direction as is currently being run, we shouldn't run it again
				// Also, don't run down first...it's should come after up
				if ($row == $direction || (!$row && $direction == 'down'))
				{
					$ignore = true;
				}
			}
			catch (PDOException $e) 
			{
				$ignore = false;
			}

			// Ignore this file, if we've already run it (unless it's being forced)
			if ($ignore && !$force)
			{
				if ($dryrun)
				{
					$this->log("Would ignore {$direction}() {$file}");
					continue;
				}
				elseif (!$row)
				{
					$this->log("Ignoring {$direction}() - you should run up first ({$file})");
					continue;
				}
				else
				{
					$this->log("Ignoring {$direction}() {$file}");
					continue;
				}
			}

			// Get the file name
			$info = pathinfo($file);

			$fullpath = $this->docroot . DS . 'migrations' . DS . $file;

			// Include the file
			if (!is_file($fullpath))
			{
				$this->log("{$fullpath} is not a valid file");
				continue;
			}
			else
			{
				require_once $fullpath;
			}

			// Set classname
			$class = $info['filename'];

			// Make sure file and classname match
			if (!class_exists($class))
			{
				$this->log("{$info['filename']} does not have a class of the same name");
				continue;
			}

			// Check if we're making a dry run, or only logging changes
			if ($dryrun || $logOnly || $print)
			{
				if ($dryrun)
				{
					$this->log("Would run {$direction}() {$file}");
				}
				elseif ($logOnly)
				{
					$this->recordMigration($file, $hash, $direction);
					$this->log("Marking as run: {$direction}() in {$file}");
				}
				elseif ($print)
				{
					$contents[] = array('filename'=>$info['filename'], 'content'=>file_get_contents($fullpath));
				}
			}
			else
			{
				// Check and run 'pre' if necessary
				if (method_exists($class, 'pre'))
				{
					try
					{
						if ($class::pre($this->db) === false)
						{
							$this->log("Running pre() returned false {$file}");
							continue;
						}

						$this->log("Running pre() {$file}");
					}
					catch (PDOException $e)
					{
						$this->log("Error: running pre() resulted in\n\n{$e}\n\nin {$file}");
						return false;
					}
				}

				// Try running the '$direction' SQL
				if(method_exists($class, $direction))
				{
					try
					{
						$class::$direction($this->db);

						$this->recordMigration($file, $hash, $direction);
						$this->log("Running {$direction}() in {$file}");
					}
					catch (PDOException $e)
					{
						$this->log("Error: running {$direction}() resulted in\n\n{$e}\n\nin {$file}");
						return false;
					}
				}

				// Check and run 'post'
				if (method_exists($class, 'post'))
				{
					try
					{
						if ($class::post($this->db) === false)
						{
							$this->log("Running post() returned false {$file}");
							continue;
						}

						$this->log("Running post() {$file}");
					}
					catch (PDOException $e)
					{
						$this->log("Error: running post() resulted in\n\n{$e}\n\nin {$file}");
						return false;
					}
				}
			}
		}

		return ($print) ? $contents : true;
	}

	/**
	 * Record migration in migrations table
	 *
	 * @param $file - path to file being recorded
	 * @param $hash - hash of file
	 * @param $direction - up or down
	 * @return void
	 **/
	protected function recordMigration($file, $hash, $direction)
	{
		// Try inserting a migration record into the database
		try
		{
			// Craete our object to insert
			$obj = (object) array(
					'file'      => $file,
					'hash'      => $hash,
					'direction' => $direction,
					'date'      => date("Y-m-d H:i:s"),
					'action_by' => (php_sapi_name() == 'cli') ? exec("whoami") : JFactory::getUser()->get('id')
				);

			$this->db->insertObject('migrations', $obj);
		}
		catch (PDOException $e)
		{
			$this->log("Failed inserting migration record: {$e}");
			return false;
		}
	}

	/**
	 * Logging mechanism
	 *
	 * @param $message - message to log
	 * @return log messages
	 **/
	public function log($message)
	{
		if (in_array('stdout', $this->log_type))
		{
			fwrite(STDOUT, $message . "\n");
		}
		if (in_array('internal_log', $this->log_type))
		{
			$this->internal_log[] = $message;
		}
		if (in_array('error_log', $this->log_type))
		{
			error_log($message);
		}
	}

	/**
	 * Attempt to create needed migrations table
	 *
	 * @return bool
	 **/
	private function createMigrationsTable(&$db)
	{
		$this->log('Migrations table did not exist...attempting to create it now');

		$query = "CREATE TABLE `migrations` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`file` varchar(255) NOT NULL DEFAULT '',
					`hash` char(32) NOT NULL DEFAULT '',
					`direction` varchar(10) NOT NULL DEFAULT '',
					`date` datetime NOT NULL,
					`action_by` varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		// Try creating the migrations table now
		try
		{
			$db->setQuery($query);
			$db->query();
			$this->log('Migrations table successfully created');
			return true;
		}
		catch (PDOException $e)
		{
			$this->log('Unable to create needed migrations table');
			return false;
		}
	}
}
