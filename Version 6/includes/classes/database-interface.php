<?php
namespace Ozraid\Includes\Classes;

/**
 * Database API class (implements the Cases and Variables traits).
 *
 * Type:         Singleton Class
 * Dependencies: Config, Database, DateTime, User
 * Description:  Simplifies PDO database queries.
 *               Inserts a record into the database 'audit_trail' table for any changes to a table or table data.
 *               Logs historical data affected by 'INSERT', 'UPDATE', and 'DELETE' queries in the database 'database_log' table.
 *               Logs table structure and data affected by 'DELETE', 'ALTER', or 'DROP' queries to a PHP log file.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class methods:
 *                - get_instance()   Returns a singleton instance of this class.
 *
 *                - set_parameters() Stores PHP PDO parameters in Database class $parameters property.
 *                - parameters()     Alias for parameters() method.
 *                - p()              Alias for parameters() method.
 *
 *                - select()         Returns data from a database table.
 *                - index_array()    Returns a multi-dimensional array indexed by a database table column values.
 *                - index_object()   Returns an array of objects indexed by a database table column value.
 *                - key_to_value()   Returns an array of key to value column values.
 *                - column()         Returns an array of column values.
 *                - row()            Returns the first row that matches the SQL statement.
 *                - single()         Returns a single value that matches the SQL statement.
 *                - show_tables()    Returns an array of database table names that match a search term.
 *                - retrieve()       Returns a custom SQL 'SELECT' or 'SHOW' query.
 *
 *                - insert()         Inserts new data into a database table.
 *                - update()         Updates existing data in a database table, and saves historical data in 'database_log' table.
 *                - delete()         Deletes data from a database table, and saves historical data in 'database_log' table.
 *                                   USE truncate() METHOD TO DELETE ALL DATA FROM A DATABASE TABLE.
 *
 *                - delete_table()   Alias for truncate() method.
 *                - truncate()       Deletes all data from a database table and saves it to a log file.
 *                - create()         Creates a new database table.
 *                - alter()          Alters the structure of an existing database table.
 *                - drop()           Deletes a database table and saves its structure and data to a log file.
 *                - restore()        Recreates a deleted database table from a log file.
 *
 *                - audit_trail()  Inserts a record into the 'audit_trail' database table.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Database_Interface {
	
	// Use the Cases and Variables traits.
	use \Ozraid\Includes\Traits\Cases, \Ozraid\Includes\Traits\Variables;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object  $config            Configuration class.
	 * @var object  $db                Database class.
	 * @var object  $datetime          PHP Date/Time class (UTC timezone).
	 * @var object  $user              User class.
	 *
	 * @var array   $actions           Database 'actions' table object indexed by 'name' column. {
	 *   @type object table 'name' column value {
	 *     @type string id  Database 'actions' table 'id' field.
	 *     @type string sql TRUE or FALSE.
	 *   }
	 * }
	 * @var string  $crlf              Carriage return and linefeed / new line.
	 * @var array   $exclude_tables    Tables to exclude logging historical data in 'database_log' table.
	 * @var array   $log_data          Historical data prior to being modified by an SQL 'INSERT', 'UPDATE' or 'DELETE' query. {
	 *   @var array table index column value {
	 *     @var string table column name Column value.
	 *   }
	 * }
	 * @var string  $log_path          Relative path to the '/logs/database/' directory.
	 * @var var    $parameters         PDO named (:$named) or question mark(?) parameters for 'UPDATE' or 'DELETE' queries.
	 * @var array  $parram_columns     Named parameter database table columns. {
	 *   @type string numeric key Table column name.
	 * }
	 * @var boolean $param_multi_array TRUE if Database class $parameters property is a multi-dimensional array, otherwise FALSE.
	 * @var string  $query             SQL query type.
	 */
	private $config;
	private $db;
	private $datetime;
	private $user;
	
	private $actions;
	private $crlf = "\r\n";
	private $exclude_tables;
	private $log_data;
	private $log_path;
	private $parameters;
	private $param_columns;
	private $param_multi_array = FALSE;
	private $query;
	
	/**
	 * Class constructor.
	 */
	private function __construct( $args ) {
		$this->config   = $args->Config;
		$this->db       = $args->Database;
		$this->datetime = $args->DateTime;
		$this->user     = $args->User;
		
		$this->actions        = $this->index_object( 'actions', array ( 'name', 'id', 'sql' ), 'ORDER BY `id` ASC' );
		$this->log_path       = constant ( 'AB_PATH' ) .'logs/database/';
		$this->exclude_tables = array ( 'audit_trail', 'database_log', 'pass', 'articles', 'pages' );
	}
	
	// Ensures only a singleton instance of this class can be initialised.
	private function __clone() {
	}
	private function __wakeup() {
	}
	
	/**
	 * Initialises a singleton instance of this class.
	 *
	 * @static object $class Singleton instance of the class.
	 *
	 * @param var $args Class dependencies or NULL.
	 * @return object Singleton instance of the Database API class.
	 */
	public static function get_instance( $args ) {
		static $class = NULL;
		if ( NULL === $class ) {
			$class_name = __CLASS__;
			$class = new $class_name( $args );
		}
		return $class;
	}
	
	// Stores User class in $this->user property.
	public function set_user( $user ) {
		$this->user = $user;
	}
	
	// EXCEPTION HANDLING METHODS.
	
	/**
	 * Throws an exception if a variable is NULL or does not exist.
	 *
	 * @param var    $variable Variable.
	 * @param string $error    Exception error.
	 */
	private function validate( $variable, $error ) {
		if ( ! isset ( $variable ) ) {
			echo $error;
			exit();
		}
	}
	
	
	// COMMON METHODS.
	
	/**
	 * Stores PHP PDO parameters in Database class $parameters property.
	 * Stores $this->param_colums and $this->param_multi_array properties based on PHP PDO parameters.
	 *
	 * @param array $parameters PHP PDO parameters.
	 */
	public function set_parameters( array $parameters ) {
		$this->db->parameters = $parameters;
		if ( $this->multi_array( $parameters ) ) {
			$key = key ( current ( $parameters ) );
			$this->param_columns = array_keys ( $parameters[$key] );
			$this->param_multi_array = TRUE;
		}
		else {
			$this->param_columns = array_keys ( $parameters );
			$this->param_multi_array = FALSE;
		}
		foreach ( $this->param_columns as $column ) {
			if ( ! is_int ( $coulmn ) ) {
				return;
			}
		}
		$count = count ( $this->param_columns );
		$this->param_columns = array_fill ( 0, $count, '?' );
	}
	
	// @see parameters() method.
	public function parameters( array $parameters ) {
		$this->parameters( $parameters );
	}
	public function p( array $parameters ) {
		$this->parameters( $parameters );
	}
	
	/**
	 * Returns SQL statement fields.
	 *  i.e. 'SELECT $fields FROM $table $condition'.
	 *
	 * @param var $columns String or array containing a database table column name.
	 * @return string SQL statement fields.
	 */
	private function get_columns( $columns ) {
		if ( $columns == NULL || $columns == '*' ) {
			return '*';
		}
		else if ( is_string ( $columns ) ) {
			return '`' .$columns .'`';
		}
		else if ( is_array ( $columns ) ) {
			return '`' .implode ( '`, `', $columns ) .'`';
		}
	}
	
	/**
	 * Stores historical data prior to being modified by an SQL 'UPDATE' or 'DELETE' query in $this->log_data property.
	 *
	 *  1. $this->parameters = Database class $parameters property.
	 *  2. If SQL statement condition is NULL, stores database table contents in $this->log_data.
	 *  3. If there is a SQL statement condition with named (:named) parameters:
	 *      - $matches[1] = Matches named (:named) parameters using a regular expression
	 *      - If any matches are found, removes these parameters from $this->param_columns property.
	 *  4. If there is a SQL statement condition with question mark (?) parameters:
	 *      - $matches[1] = Matches question mark (?) parameters using a regular expression
	 *      - If any matches are found, removes these parameters from $this->param_columns property.
	 *  5. $select_param_columns = Array of matched named (:named) column names or question mark (?) parameters.
	 *  6. Performs a SQL 'SELECT' query for each change to the database and stores the historical data in $this->log_data.
	 *
	 * @param string $table     Database table name.
	 * @param string $condition Optional. SQL statement condition.
	 */
	private function set_log_data( $table, $condition = NULL ) {
		$this->parameters = $this->db->parameters;
		$this->db->parameters = NULL;
		if ( ! $condition ) {
			$this->log_data = $this->select( $table );
		}
		else {
			if ( preg_match_all ( '/\s:([.*\S]+)/', $condition, $matches ) ) {
				$select_param_columns = $matches[1];
				foreach ( $this->param_columns as $param_key => $param_column ) {
					if ( in_array ( $param_column, $select_param_columns ) ) {
						unset ( $this->param_columns[$param_key] );
					}
				}
			}
			else if ( preg_match_all ( '/[^a-zA-Z0-9](\?)/', $condition, $matches ) ) {
				$params_count = count ( $this->param_columns );
				$select_params_count = count ( $matches[1] );
				$difference = $params_count - $select_params_count;
				if ( $difference == 0 ) {
					$this->param_columns = NULL;
				}
				else {
					$this->param_columns = array_fill ( 0, $difference, '?' );
				}
				$select_param_columns = array_fill ( $difference, $select_params_count, '?' );
			}
			else {
				$select_param_columns = NULL;
			}
			
			if ( $param_multi_array == TRUE ) {
				foreach ( $this->parameters as $data ) {
					if ( $select_param_columns != NULL ) {
						foreach ( $data as $column => $value ) {
							if ( in_array ( $column, $select_param_columns ) ) {
								$this->db->parameters[$column] = $value;
							}
						}
					}
					$this->log_data = array_merge ( $this->log_data, $this->index_array( $table, NULL, $condition ) );
				}
			}
			else if ( $param_multi_array == FALSE ) {
				foreach ( $this->parameters as $column => $value ) {
					if ( in_array ( $column, $select_param_columns ) ) {
						$this->db->parameters[$column] = $value;
					}
				}
				$this->log_data = $this->index_array( $table, NULL, $condition );
			}
		}
	}
	
	/**
	 * Stores data inserted into a database table into $this->log_data property.
	 *
	 * @param array $insert_ids Database table index ids. i.e. Array of last insert IDs.
	 * @param array $parameters Database class $parameters property.
	 */
	private function set_insert_log_data( array $index_ids, array $parameters ) {
		if ( $this->param_multi_array == TRUE ) {
			$x = 0;
			foreach ( $parameters as $data ) {
				$this->log_data[$index_ids[$x]] = $data;
				$x++;
			}
		}
		else if ( $this->param_multi_array == FALSE ) {
			$this->log_data[$index_ids[0]] = $parameters;
		}
	}
	
	
	// SQL 'SELECT' AND 'SHOW' QUERY METHODS.
	
	/**
	 * Returns data from a database table.
	 *
	 * @param string $table      Database table name.
	 * @param var    $columns    Optional. String '*' or table column name, or array of table column names.
	 * @param string $condition  Optional. SQL statement condition.
	 * @param object $fetch_mode Optional. PHP PDO fetch mode. @see Database class query() method.
	 * @param string $class      Optional. PHP class name (with namespace). Used in conjunction with $fetch_mode = '\PDO::FETCH_INTO'.
	 *
	 */
	public function select( $table, $columns = NULL, $condition = NULL, $fetch_mode = NULL, $class = NULL ) {
		$sql = 'SELECT ' .$this->get_columns( $columns ) .' FROM `' .$table .'` ' .$condition;
		return $this->db->query( $sql, $fetch_mode, $class );
	}
	
	/**
	 * Returns a multi-dimensional array indexed by a database table column values from a database table.
	 *
	 *  i.e. '$result[ index column value ][ column name ] = column value'.
	 *
	 *  1. The index table column depends on the $columns argument:
	 *      - @type string Table column name, or first database table column if $columns = '*'
	 *      - @type array  First column name in the array
	 *      - @type NULL   First database table column.
	 *  2. Values in the index table column should be unique, else the last matching value will be returned.
	 *
	 * @params See select() for a list of arguments.
	 * @return array SQL query result. {
	 *   @type array index column value {
	 *     @type string column Value.
	 *   }
	 * }
	 */
	public function index_array( $table, $columns = NULL, $condition = NULL ) {
		return $this->select( $table, $columns, $condition, \PDO::FETCH_UNIQUE );
	}
	
	/**
	 * Returns an array of objects indexed by a database table column values from a database table.
	 *
	 *  i.e. '$result[ index column value ]->{ column name } = column value'.
	 *
	 *  1. The index table column depends on the $columns argument:
	 *      - @type string Table column name, or first database table column if $columns = '*'
	 *      - @type array  First column name in the array
	 *      - @type NULL   First database table column.
	 *  2. Values in the index table column should be unique, else the last matching value will be returned.
	 *
	 * @params See select() for a list of arguments.
	 * @return array SQL query result. {
	 *   @type object index column value {
	 *     @type string column Value.
	 *   }
	 * }
	 */
	public function index_object( $table, $columns = NULL, $condition = NULL ) {
		return $this->select( $table, $columns, $condition, \PDO::FETCH_UNIQUE | \PDO::FETCH_OBJ );
	}
	
	/**
	 * Returns an array of key to value column values.
	 *
	 *  i.e. 'array ( $key => $value )'.
	 *
	 * @see select() for a list of arguments.
	 * @param string $key        Table column name.
	 * @param string $value      Table column name.
	 * @return array Database table column values. {
	 *   @type string $key value => $value value.
	 * }
	 */
	public function key_to_value( $table, $key = 'id', $value = 'name', $condition = NULL ) {
		return $this->select( $table, array ( $key, $value ), $condition, \PDO::FETCH_KEY_PAIR );
	}
	
	/**
	 * Returns an array of column values from a database table.
	 *
	 * @see select() for a list of arguments.
	 * @param string $column     Table column name.
	 * @return array Database table column values. {
	 *   @type string numeric key Value.
	 * }
	 */
	public function column( $table, $column, $condition = NULL ) {
		$sql = 'SELECT `' .$column .'` FROM `' .$table .'` ' .$condition;
		$data = $this->db->query( $sql, \PDO::FETCH_NUM );
		foreach ( $data as $row ) {
			$result[] = $row[0];
		}
		return $result;
	}
	
	/**
	 * Returns the first row of data that matches the SQL statement from a database table.
	 *
	 * @see select() for a list of arguments.
	 * @return var First database table row that matches the SQL statement.
	 */
	public function row( $table, $columns = NULL, $condition = NULL, $fetch_mode = NULL, $class = NULL ) {
		return $this->select( $table, $columns, $condition, $fetch_mode, $class )[0];
	}
	
	/**
	 * Returns the first value that matches the SQL statement from a database table.
	 *
	 * @see select() for a list of arguments.
	 * @param string $column Table column name.
	 * @return var First value that matches the SQL statement.
	 */
	public function single( $table, $column, $condition ) {
		return $this->select( $table, $column, $condition )[0];
	}
	
	/**
	 * Returns an array of database table names that match a search term.
	 *
	 * Search pattern can contain the following special characters:
	 *  - Percentage (%) matches any number of characters
	 *  - Underscore (_) matches one character
	 *  - Backslash (\) escapes the percentage, underscore, and backslash characters.
	 *
	 * @param string $pattern Table name or search pattern.
	 * @return var Database table names that match the $search term, or NULL. Array {
	 *   @type numeric key Database table name. 
	 * }
	 */
	public function show_tables( $pattern ) {
		$data = $this->db->query( 'SHOW TABLES LIKE "' .$pattern .'"', \PDO::FETCH_NUM );
		if ( ! isset ( $data ) ) {
			return NULL;
		}
		foreach ( $data as $column ) {
			$result[] = $column[1];
		}
		return $result;
	}
	
	/**
	 * Returns a custom SQL 'SELECT' or 'SHOW' query.
	 *
	 * @param string $sql        SQL statement.
	 * @param object $fetch_mode Optional. PHP PDO fetch mode. @see Database class query() method.
	 * @param string $class      Optional. PHP class name (with namespace). Used in conjunction with $fetch_mode = '\PDO::FETCH_INTO'.
	 * @return var Database table data.
	 */
	public function retrieve( $sql, $fetch_mode = NULL, $class = NULL ) {
		$this->query = current ( explode ( ' ', strtoupper ( $sql ) ) );
		if ( $this->query != 'SELECT' && $this->query != 'SHOW' ) {
			return FALSE;
		}
		return $this->db->query( $sql, $fetch_mode, $class );
	}
	
	
	// OTHER SQL QUERY METHODS.
	
	/**
	 * Inserts data into a database table.
	 * Inserts data in database 'audit_trail' and 'database_log' tables unless the table is in $this->exclude_tables property.
	 *
	 * @param string $table Database table name.
	 * @return int Number of records inserted into the database table.
	 */
	public function insert( $table ) {
		$this->query = $this->upper_case( __FUNCTION__ );
		$this->validate( $this->db->parameters, 'Parameters have not been set for this ' .$this->query .' query.' );
		$sql  = 'INSERT INTO `' .$table .'` ( `' .implode ( '`, `', $this->param_columns ) .'` ) ';
		$sql .= 'VALUES ( :' .implode ( ', :', $this->param_columns ) .' )';
		$parameters = $this->db->parameters;
		$index_ids = $this->db->query( $sql );
		$this->validate( $index_ids, 'There was an error processing this ' .$this->query .' query. SQL: "' .$sql .'"' );
		$this->set_insert_log_data( $index_ids, $parameters );
		$this->audit_trail( $this->query, $table );
		return count ( $index_ids );
	}
	
	/**
	 * Inserts data into a database table.
	 * Inserts data in database 'audit_trail' and 'database_log' tables unless the table is in $this->exclude_tables property.
	 *
	 * @param string $table     Database table name.
	 * @param string $condition SQL statement condition.
	 * @return int Number of records updated in the database table.
	 */
	public function update( $table, $condition ) {
		$this->query = $this->upper_case( __FUNCTION__ );
		$this->validate( $this->db->parameters, 'Parameters have not been set for this ' .$this->query .' query.' );
		$this->validate( $condition, 'No condition was specified for this ' .$this->query .' query.' );
		$this->set_log_data( $table, $condition );
		$this->audit_trail( $this->query, $table );
		$this->db->parameters = $this->parameters;
		$sql  	= 'UPDATE `' .$table .'` SET ';
		foreach ( $this->param_columns as $column ) {
			$sql .= '`' .$column .'` = :' .$column .' ';
		}
		$sql	 .= $condition;
		return $this->db->query( $sql );
	}
	
	/**
	 * Deletes data from a database table.
	 * Inserts data in database 'audit_trail' and 'database_log' tables unless the table is in $this->exclude_tables property.
	 *
	 * @param string $table     Database table name.
	 * @param string $condition SQL statement condition.
	 * @return int Number of records deleted from the database table.
	 */
	public function delete( $table, $condition ) {
		$this->query = $this->upper_case( __FUNCTION__ );
		$this->validate( $this->db->parameters, 'Parameters have not been set for this ' .$this->query .' query.' );
		$this->validate( $condition, 'No condition was specified for this ' .$this->query .' query.' );
		$this->set_log_data( $table, $condition );
		$this->audit_trail( $this->query, $table );
		$this->db->parameters = $this->parameters;
		$sql = 'DELETE FROM `' .$table .'` ' .$condition;
		return $this->db->query( $sql );
	}
	
	// @see truncate() method.
	public function delete_table( $table ) {
		$this->truncate( $table );
	}
	
	/**
	 * Deletes all data from a database table and saves it to a log file.
	 *
	 * @param string $table     Database table name.
	 * @return TRUE if successful.
	 */
	public function truncate( $table ) {
		$this->query = $this->upper_case( __FUNCTION__ );
		$this->db->parameters = NULL;
		$data = '$table_data = ' .var_export ( $this->db->query( 'SELECT * FROM `' .$table .'`' ) ) .';';
		$this->set_php( $table, $data );
		return $this->db->query( 'TRUNCATE TABLE `' .$table .'`' );
	}
	
	/**
	 * Creates a new database table.
	 *
	 * @param string $table          Database table name.
	 * @param array  $columns        Table column data. {
	 *   @type array numeric key Column SQL statement.
	 * }
	 * @param var    $auto_increment Starting auto increment value.
	 * @return TRUE if successful, otherwise FALSE if table already exists.
	 */
	public function create( $table, $columns, $auto_increment = NULL ) {
		$sql	  = 'CREATE TABLE `' .$table .'` IF NOT EXISTS (' .$this->crlf;
		$sql	 .= $columns .') ENGINE=InnoDB ';
		if ( isset ( $auto_increment ) && is_int ( $auto_increment ) ) {
			$sql .= 'AUTO_INCREMENT=' .$auto_increment .' ';
		}
		$sql	 .= 'DEFAULT CHARSET=utf8';
		return $this->db->query( $sql );
	}
	
	public function alter( $table ) {
		
	}
	
	public function drop( $table ) {
		$this->query = $this->upper_case( __FUNCTION__ );
		$this->db->parameters = NULL;
		$data  = '$structure = ' .var_export ( $this->db->query( 'SHOW CREATE TABLE `' .$table .'`' )[0]['Create Table'] ) .';' .$this->crlf .$this->crlf;
		$data .= '$data = ' .var_export ( $this->db->query( 'SELECT * FROM `' .$table .'`' ) ) .';';
		$this->set_php( $table, $data );
		return $this->db->query( 'DROP TABLE `' .$table .'`' );
	}
	
	public function restore( $table, $action ) {
		
	}
	
	
	// LOG METHODS.
	
	/**
	 * Inserts an entry into the 'audit_trail' database table.
	 *
	 * @param string $action Database 'actions' table 'name' field.
	 * @param string $table  Database table name. Default 'site_log' table.
	 * @return TRUE if an entry is inserted into the 'audit_trail' table, last insert ID if $table = 'site_log', otherwise FALSE.
	 */
	public function audit_trail( $action, $table = 'site_log' ) {
		if ( in_array ( $table, array ( 'audit_trail', 'database_log' ) ) ) {
			return FALSE;
		}
		if ( ( $this->query == 'INSERT' || $this->query == 'UPDATE' || $this->query == 'DELETE' ) && ! isset ( $this->log_data ) ) {
			$this->validate( NULL, "Log entry was for the '" .$table ."' table was not recorded in the 'audit_trail' table due to an error with this " .$this->query ." query." );
		}
		$this->db->parameters = array (
			'user'     => $this->user->id,
			'action'   => $this->actions[$action]->id,
			'table'    => $table,
			'datetime' => $this->datetime->format( 'Y-m-d H:i:s' )
		);
		$audit_id = $this->db->query( 'INSERT INTO `audit_trail` ( `user`, `action`, `table`, `datetime` ) VALUES ( :user, :action, :table, :datetime )' )[0];
		$this->validate( $audit_id, "Log entry was for the '" .$table ."' table was not recorded in the 'audit_trail' table for " .$this->query ." query." );
		if ( $table == 'site_log' ) {
			return $this->site_log( $audit_id );
		}
		else if ( in_array ( $table, $this->exclude_tables ) ) {
			return TRUE;
		}
		else if ( $this->actions[$action]->sql === TRUE ) {
			return $this->database_log( $audit_id );
		}
		return TRUE;
	}
	
	/**
	 * Inserts historical data into the 'database_log' database table.
	 *
	 * @param array $audit_id Database 'audit_trail' table 'id' field. i.e. Last insert ID.
	 * @return TRUE if an entry is inserted into the 'database_log' table.
	 */
	private function database_log( $audit_id ) {
		foreach ( $this->log_data as $table_index => $data ) {
			foreach ( $data as $column => $value ) {
				$this->db->parameters[] = array (
					'audit_trail' => $audit_id,
					'table_index' => $table_index,
					'column'      => $column,
					'value'       => $value
				);
			}
		}
		$log_ids = $this->db->query( 'INSERT INTO `database_log` ( `audit_trail`, `table_index`, `column`, `value` ) VALUES ( :audit_trail, :table_index, :column, :value )' );
		$this->validate( $log_ids, "Log entry was not recorded in the 'database_log' table for " .$this->query ." query." );
		$this->log_data = NULL;
		$this->parameters = NULL;
		return TRUE;
	}
	
	/**
	 * Inserts an entry into the 'site_log' database table.
	 *
	 * @param array $audit_id Database 'audit_trail' table 'id' field. i.e. Last insert ID.
	 * @return TRUE if an entry is inserted into the 'site_log' table.
	 */
	private function site_log( $audit_id ) {
		$this->db->parameters = array (
			'audit_trail' => $audit_id,
			'url'         => $this->config->url,
			'prev_url'    => $this->config->prev_url,
			'ip_address' => $this->config->ip_address
		);
		$site_log_id = $this->db->query( 'INSERT INTO `site_log` ( `audit_trail`, `url`, `prev_url`, `ip_address` ) VALUES ( :audit_trail, :url, :prev_url, :ip_address )' )[0];
		$this->validate( $site_log_id, "Log entry was was not recorded in the database 'site_log' table." );
		return TRUE;
	}
	
	/**
	 * Saves a deleted table's structure and/or data to a PHP log file.
	 *
	 * @param string $table     Database table name.
	 * @param string $data      Database PHP log data.
	 */
	private function set_php( $table, $data ) {
		if ( ! file_exists ( $this->log_path ) ) {
			mkdir ( $this->log_path, 0755 );
		}
		$content  = '<?php' .$this->crlf;
		$content .= 'namespace ' .constant ( 'BASE_NS' ) .'\Logs\Database;' .$this->crlf .$this->crlf;
		$content .= '/**' .$this->crlf;
		$content .= ' * Database PHP log file.' .$this->crlf;
		$content .= ' *' .$this->crlf;
		$content .= ' * Database Table: ' .$table .$this->crlf;
		$content .= ' * Date/Time:      ' .$this->datetime->format( 'Y-m-d H:i:s' ) .$this->crlf;
		$content .= ' * User:           ' .$this->user->id .$this->crlf;
		$content .= ' * Username:       ' .$this->user->username .$this->crlf;
		$content .= ' * Action:         ' .$this->query .$this->crlf;
		$content .= ' */' .$this->crlf .$this->crlf;
		$content .= $data .$this->crlf;
		$file = fopen ( $this->log_path .$this->datetime->format( 'Y-m-d H-i-s' ) .' ' .$this->attribute_case( $table ) .'.php', 'w' );
		fwrite ( $file, $content );
		fclose ( $file );
	}
	
}
