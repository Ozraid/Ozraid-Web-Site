<?php
namespace Ozraid\Includes\Classes;

/**
 * MySQL PHP PDO Database class.
 *
 * Type:         Class
 * Dependencies: Config
 * Description:  PDO database class.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class properties:
 *                - $parameters PDO named (:$named) or question mark(?) parameters.
 *
 *               Public class methods:
 *                - query()     Executes a PDO database query.
 *                - quote()     Quotes a string for use in a query. Typically used for database table column information.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Database {
	
	/**
	 * PUBLIC PROPERTIES
	 *
	 * @var string $column     Table index column name.
	 * @var var    $parameters PDO named (:$named) or question mark(?) parameters.
	 */
	public $column = NULL;
	public $parameters = NULL;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object  $config Configuration class.
	 8 @var var     $pdo    Database PDO class or NULL.
	 * @var boolean $status Database connection status. TRUE if connected, otherwise FALSE.
	 */
	private $config;
	private $pdo;
	private $status = FALSE;
	
	/**
	 * Class constructor.
	 */
	public function __construct( $args ) {
		$this->config = $args->Config;
		if ( $this->status === FALSE ) {
			$this->set_connection();
		}
	}
	
	// Sets the $this->pdo and $this->parameters properties to NULL, and $this->status propterty to FALSE when the class is destroyed.
	public function __destruct() {
		$this->pdo = NULL;
		$this->paramters = NULL;
		$this->status = FALSE;
	}
	
	/**
	 * Initialises and stores the database PDO class in $this->pdo, and sets $this->status to TRUE.
	 *
	 * @var string $dsn     Database connection information.
	 * @var array  $options Database PDO options. {
	 *   @type array option Value. See PHP PDO manual online for more information.
	 * }
	 */
	private function set_connection() {
		$dsn = 'mysql:host=' .$this->config->host .';dbname=' .$this->config->prefix .$this->config->database_name .';charset=utf8';
		$options = [
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES   => FALSE,
		];
		$this->pdo = new \PDO( $dsn, $this->config->prefix .$this->config->database_username, $this->config->database_key, $options );
		$this->status = TRUE;
	}
	
	/**
	 * Executes a PDO database query.
	 *
	 * @param string $sql        SQL statement.
	 * @param object $fetch_mode Optional. PHP PDO fetch mode. Default '\PDO::FETCH_ASSOC'.
	 * @param string $class      Optional. PHP class name (with namespace). Used in conjunction with $fetch_mode = '\PDO::FETCH_INTO'.
	 * @return var Returns data as per $fetch_mode for 'SELECT' or 'SHOW' queries,
	 *             Returns an array of table index column values for 'INSERT' queries.
	 *             Returns number of rows affected for 'UPDATE', or 'DELETE' queries.
	 *             Returns TRUE for all other queries.
	 *             Returns NULL if incorrect query specified.
	 */
	public function query( $sql, $fetch_mode = \PDO::FETCH_ASSOC, $class = NULL ) {
		$action = current ( explode ( ' ', strtoupper ( $sql ) ) );
		$query = $this->pdo->prepare( trim ( $sql ) );
		if ( isset ( $this->parameters[0] ) && is_array ( $this->parameters[0] ) ) {
			foreach ( $this->parameters as $parameters ) {
				$query->execute( $parameters );
				if ( $action == 'INSERT' ) {
					$insert_ids[] = $this->pdo->lastInsertId( $this->column );
				}
			}
		}
		else {
			$query->execute( $this->parameters );
			if ( $action == 'INSERT' ) {
				$insert_ids[] = $this->pdo->lastInsertId( $this->column );
			}
		}
		$this->parameters = NULL;
		switch ( $action ) {
			case 'SELECT':
			case 'SHOW':
				if ( $class ) {
					return $query->fetchALL( $fetch_mode, $class );
				}
				else {
					return $query->fetchALL( $fetch_mode );
				}
				break;
			case 'INSERT':
				$this->column = NULL;
				return $insert_ids;
				break;
			case 'UPDATE':
			case 'DELETE':
				return $query->rowCount();
				break;
			case 'TRUNCATE':
			case 'CREATE':
			case 'ALTER':
			case 'DROP':
				return TRUE;
				break;
			default:
				return NULL;
				break;
		}
	}
	
	/**
	 * Quotes a string for use in a query. Typically used for database table column information.
	 *
	 * @param string $string Data.
	 * @return string Data with quotes around the string and escaped special characters.
	 */
	public function quote( $string ) {
		return $this->pdo->quote( $string );
	}
	
}
