<?php
/**
* @version		$Id: mysql.php 16385 2010-04-23 10:44:15Z ian $
* @package		Joomla.Framework
* @subpackage	Database
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * PDO database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
class JDatabasePDO extends JDatabase
{
	var $timer = 0;

	/**
	 * The database driver name
	 *
	 * @var string
	 */
	var $name			= 'pdo';

	/**
	 *  The null/zero date string
	 *
	 * @var string
	 */
	var $_nullDate		= '0000-00-00 00:00:00';

	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	var $_nameQuote		= '`';

	/**
	* Database object constructor
	*
	* @access	public
	* @param	array	List of options used to configure the connection
	* @since	1.5
	* @see		JDatabase
	*/
	function __construct( $options )
	{
		$host		= array_key_exists('host', $options)	? $options['host']		: 'localhost';
		$user		= array_key_exists('user', $options)	? $options['user']		: '';
		$password	= array_key_exists('password',$options)	? $options['password']	: '';
		$database	= array_key_exists('database',$options)	? $options['database']	: '';
		$prefix		= array_key_exists('prefix', $options)	? $options['prefix']	: 'jos_';
		$select		= array_key_exists('select', $options)	? $options['select']	: true;

		// perform a number of fatality checks, then return gracefully
		if (!class_exists( 'PDO' )) {
			$this->_errorNum = 1;
			$this->_errorMsg = 'The PDO adapter "pdo" is not available.';
			return;
		}

		// connect to the server
		if (!($this->_resource = new PDO("mysql:host=${host}", $user, $password))) {
			$this->_errorNum = 2;
			$this->_errorMsg = 'Could not connect to MySQL';
			return;
		}

		$this->_resource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// finalize initialization
		parent::__construct($options);

		// select the database
		if ( $select ) {
			$this->select($database);
		}
	}

	/**
	 * Database object destructor
	 *
	 * @return boolean
	 * @since 1.5
	 */
	function __destruct()
	{
		$return = false;
		if (is_object($this->_resource)) {
			$this->_resource = null;
			$return = true;
		}
		return $return;
	}

	/**
	 * Test to see if the MySQL connector is available
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public static function test()
	{
		return class_exists('PDO');
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @access	public
	 * @return	boolean
	 * @since	1.5
	 */
	function connected()
	{
		try 
		{
			$this->_resource->query('SELECT 1');
		} 
		catch (PDOException $e) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Select a database for use
	 *
	 * @access	public
	 * @param	string $database
	 * @return	boolean True if the database has been successfully selected
	 * @since	1.5
	 */
	function select($database)
	{
		if ( ! $database )
		{
			return false;
		}
		
		if ($this->_resource->exec('USE ' . $database) === false) {
			$this->_errorNum = 3;
			$this->_errorMsg = 'Could not connect to database';
			var_dump($result);
			return false;
		}

		// if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
		if ($this->_resource->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
			$this->setQuery( "SET sql_mode = 'MYSQL40'" );
			$this->query();
		}

		return true;
	}

	public function quote($text, $escape = true)
	{
		return ($escape ? $this->escape($text) : '\'' . $text . '\'');
	}

	public function escape($text, $extra = false)
	{
		$result = $this->connection->quote($text);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Determines UTF support
	 *
	 * @access	public
	 * @return boolean True - UTF is supported
	 */
	function hasUTF()
	{
		$verParts = explode( '.', $this->getVersion() );
		return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int)$verParts[2] >= 2));
	}

	/**
	 * Custom settings for UTF support
	 *
	 * @access	public
	 */
	function setUTF()
	{
		$this->_resource->exec("SET NAMES 'utf8'");
	}

	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * @access	public
	 * @abstract
	 */
	function getEscaped( $text, $extra = false )
	{
		$result = substr($this->_resource->quote($text), 1, -1);
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}

	/**
	 * Execute the query
	 *
	 * @access	public
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	function query()
	{
		if (!is_object($this->_resource)) {
			return false;
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->_sql;
		if ($this->_limit > 0 || $this->_offset > 0) {
			$sql .= ' LIMIT ' . max($this->_offset, 0) . ', ' . max($this->_limit, 0);
		}
		// MOVED ABOVE if/LOG SECTION
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$starttime = microtime(true); // ADDED
		$this->_cursor = $this->_resource->query($sql);
		$endtime = microtime(true); // ADDED
		if ($this->_debug) {
			$this->_ticker++;
			// ADDED ALL THE timediff LOGIC
			$timediff = ($endtime - $starttime);
			$this->timer += $timediff;
			if($timediff > 1000)
			{
				$timediff = "!!!!! ".$timediff;
			}
			$this->_log[] = "(".$timediff."): ".$sql;
			//$this->_log[] = $sql;
		}

		if (!$this->_cursor)
		{
			$this->_errorNum .= $this->_resource->errorCode();
			$this->_errorMsg .= $this->_resource->errorInfo()." SQL=$sql";

			if ($this->_debug) {
				JError::raiseError(500, 'JDatabaseMySQL::query: '.$this->_errorNum.' - '.$this->_errorMsg );
			}
			return false;
		}
		return $this->_cursor;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	function getAffectedRows()
	{
		return $this->_cursor->rowCount();
	}

	/**
	 * Execute a batch query
	 *
	 * @access	public
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	function queryBatch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$this->_sql = rtrim($this->_sql, "; \t\r\n\0");
			$this->_resource->beginTransaction();
		}
		$query_split = $this->splitSql($this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
				$this->_cursor = $this->_resource->query( $command_line );
				if ($this->_debug) {
					$this->_ticker++;
					$this->_log[] = $command_line;
				}
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= $this->_resource->errorCode();
					$this->_errorMsg .= $this->_resource->errorInfo()." SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function
	 *
	 * @access	public
	 * @return	string
	 */
	function explain()
	{
		$temp = $this->_sql;
		$this->_sql = "EXPLAIN $this->_sql";

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
		while ($row = $cur->fetch(PDO::FETCH_ASSOC)) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		$cur->closeCursor();

		$this->_sql = $temp;

		return $buffer;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return int The number of rows returned from the most recent query.
	 */
	function getNumRows( $cur=null )
	{
		return $cur ? $cur->rowCount() : $this->_cursor->rowCount();
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @access	public
	 * @return The value returned in the query or null if the query failed.
	 */
	function loadResult()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = $cur->fetch(PDO::FETCH_NUM)) {
			$ret = $row[0];
		}
		$cur->closeCursor();
		return $ret;
	}

	/**
	 * Load an array of single field results into an array
	 *
	 * @access	public
	 */
	function loadResultArray($numinarray = 0)
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $cur->fetch(PDO::FETCH_NUM)) {
			$array[] = $row[$numinarray];
		}
		$cur->closeCursor();
		return $array;
	}

	/**
	* Fetch a result row as an associative array
	*
	* @access	public
	* @return array
	*/
	function loadAssoc()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($array = $cur->fetch(PDO::FETCH_ASSOC)) {
			$ret = $array;
		}
		$cur->closeCursor();
		return $ret;
	}

	/**
	* Load a assoc list of database rows
	*
	* @access	public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadAssocList( $key='', $column = null )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $cur->fetch(PDO::FETCH_ASSOC)) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		$cur->closeCursor();
		return $array;
	}

	/**
	* This global function loads the first row of a query into an object
	*
	* @access	public
	* @return 	object
	*/
	function loadObject($class = 'stdClass')
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($object = $cur->fetch(PDO::FETCH_OBJ)) {
			$ret = $object;
		}
		$cur->closeCursor();
		return $ret;
	}

	/**
	* Load a list of database objects
	*
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*
	* @access	public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadObjectList($key = '', $class = 'stdClass')
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $cur->fetch(PDO::FETCH_OBJ)) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
		$cur->closeCursor();
		return $array;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return The first row of the query.
	 */
	function loadRow()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = $cur->fetch(PDO::FETCH_NUM)) {
			$ret = $row;
		}
		$cur->closeCursor();
		return $ret;
	}

	/**
	* Load a list of database rows (numeric column indexing)
	*
	* @access public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	function loadRowList( $key=null )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $cur->fetch(PDO::FETCH_NUM)) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		$cur->closeCursor();
		return $array;
	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access	public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	public function insertObject( $table, $object, $keyName = NULL )
	{
		$fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->nameQuote( $k );
			$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		if (!$this->query()) {
			return false;
		}
		$id = $this->insertid();
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return true;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	function updateObject( $table, &$object, $keyName, $updateNulls=true )
	{
		$fmtsql = 'UPDATE '.$this->nameQuote($table).' SET %s WHERE %s';
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v)
		{
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === null)
			{
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	function insertid()
	{
		return $this->_resource->lastInsertId();
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	function getVersion()
	{
		return $this->_resource->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	/**
	 * Assumes database collation in use by sampling one text field in one table
	 *
	 * @access	public
	 * @return string Collation in use
	 */
	function getCollation ()
	{
		if ( $this->hasUTF() ) {
			$this->setQuery( 'SHOW FULL COLUMNS FROM #__content' );
			$array = $this->loadAssocList();
			return $array['4']['Collation'];
		} else {
			return "N/A (mySQL < 4.1.2)";
		}
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return array A list of all the tables in the database
	 */
	function getTableList()
	{
		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();
	}

	/**
	 * Shows the CREATE TABLE statement that creates the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @return 	array A list the create SQL for the tables
	 */
	function getTableCreate( $tables )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
			$rows = $this->loadRowList();
			foreach ($rows as $row) {
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}

	/**
	 * Retrieves information about the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @param	boolean			Only return field types, default true
	 * @return	array An array of fields by table
	 */
	function getTableFields( $tables, $typeonly = true )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval)
		{
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();

			if($typeonly)
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
				}
			}
			else
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = $field;
				}
			}
		}

		return $result;
	}

	/**
	 * Check for the existance of a table
	 *
	 * @access	public
	 * @param 	string $table - table we're looking for
	 * @return 	bool
	 */
	public function tableExists( $table )
	{
		$this->setQuery( 'SHOW TABLES LIKE ' . str_replace('#__', $this->_table_prefix, $this->Quote($table)) );
		$this->query();

		return ($this->getAffectedRows() > 0) ? true : false;
	}

	/**
	 * Returns whether or not the given table has a given field
	 *
	 * @access	public
	 * @param 	string $table - A table name
	 * @param	string $field - A field name
	 * @return	bool          - true if table has field, false otherwise
	 */
	public function tableHasField( $table, $field )
	{
		$this->setQuery( 'SHOW FIELDS FROM ' . $table );
		$fields = $this->loadObjectList('Field');

		return (in_array($field, array_keys($fields))) ? true : false;
	}

	/**
	 * Returns whether or not the given table has a given key
	 *
	 * @access	public
	 * @param 	string $table - A table name
	 * @param	string $key   - A key name
	 * @return	bool          - true if table has key, false otherwise
	 */
	public function tableHaskey( $table, $key )
	{
		$this->setQuery( 'SHOW KEYS FROM ' . $table );
		$keys = $this->loadObjectList('Key_name');

		return (in_array($key, array_keys($keys))) ? true : false;
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$result = array();

		// Set the query to get the table fields statement.
		$this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($table));
		$fields = $this->loadObjectList();

		// If we only want the type as the value add just that to the list.
		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		}
		// If we want the whole field data object add that to the list.
		else
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field;
			}
		}

		return $result;
	}

	public function dropTable($table, $ifExists = true) {}
	public function fetchArray($cursor = null) {}
	public function fetchAssoc($cursor = null) {}
	public function fetchObject($cursor = null, $class = 'stdClass') {}
	public function freeResult($cursor = null) {}
	public function getQuery($new = false) {}
	public function getTableKeys($tables) {}
	public function lockTable($tableName) {}
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null) {}
	public function transactionCommit() {}
	public function transactionRollback() {}
	public function transactionStart() {}
	public function unlockTables() {}
}
