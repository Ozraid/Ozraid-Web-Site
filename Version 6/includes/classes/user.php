<?php
namespace Ozraid\Includes\Classes;

/**
 * User class (implements the Validate trait).
 *
 * Type:         Singleton Class
 * Dependencies: Cookie, Crypt, Database, Date_Time_Interface, Session
 * Description:  Contains data about the current user if they are logged into the website, otherwise 'Public User' data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class methods:
 *                - __set()        Magic method that stores new user data in $this->user property, otherwise returns NULL
 *                                 if user data variable name exists.
 *                - __get()        Magic method that returns user data from $this->user property, otherwise NULL.
 *                - __isset()      Returns TRUE if user data exists in $this->user property, otherwise FALSE.
 *                - get_instance() Returns a singleton instance of this class.
 */

class User {
	
	// Use the Cases and Variables traits.
	use \Ozraid\Includes\Traits\Validate;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $cookie           Cookie class.
	 * @var object $crypt            Crypt class.
	 * @var object $database         MySQL PHP PDO Database class.
	 * @var object $dt               Date/Time API class.
	 * @var object $session          Session class.
	 *
	 * @var object $user   User data. {
	 *   @var int     id             Database 'users' table 'id' field.
	 *   @var boolean logged_in      TRUE if user is logged in to the website, otherwise FALSE.
	 *   @var object  table_id       Database table variable IDs. {
	 *     @var int role         Database 'roles' table 'id' field.
	 *     @var int status       Database 'status' table 'id' field.
	 *     @var int login_expiry Database 'login_expiry' table 'id' field.
	 *     @var int country      Database 'countries' table 'id' field.
	 *   }
	 *   @var string  username       User's username.
	 *   @var string  role           User's role.
	 *   @var string  status         User's status.
	 *   @var string  login_expiry   User's login expiry period.
	 *   @var string  country        User's country.
	 *   @var object  timezone       Timezone data. {
	 *     @var int    id   Database 'timezones' tables 'id' field.
	 *     @var string name User's timezone.
	 *     @var string php  User's PHP timezone.
	 *   }
	 *   @var var      characters     Array of character data or NULL. {
	 *     @var object database 'characters' table 'id' field. {
	 *       @var string  name       Character name.
	 *       @var string  race       Character's race.
	 *       @var string  class      Character's class.
	 *       @var boolean raid_ready TRUE if the character is Tier 2 Challenge raid ready, otherwise FALSE.
	 *     }
	 *   }
	 *   @var boolean mic            TRUE if user uses a headset or microphone, otherwise FALSE.
	 *   @var string  email          User's email address.
	 *   @var string  facebook       User's Facebook URL.
	 *   @var string  twitter_handle User's Twitter handle.
	 *   @var boolean contact        TRUE if the user wishes to be contacted, otherwise FALSE.
	 *   @var boolean image          TRUE if the user has uploaded a profile image, otherwise FALSE.
	 *   @var object  date_time      Dates and times. {
	 *     @var string application Date and time in 'yyyy-mm-dd hh:mm:ss' format user submitted their application (UTC).
	 *     @var string review      Date and time in 'yyyy-mm-dd hh:mm:ss' format user's application was reviewed (UTC).
	 *   }
	 * }
	 * @var array  $login_expiry     Array of database 'login_expiry' table 'id' => 'name' fields.
	 * @var array  $roles            Array of database 'roles' table 'id' => 'name' fields.
	 * @var array  $status           Array of database 'status' table 'id' => 'name' fields.
	 */
	private $cookie;
	private $crypt;
	private $database;
	private $dt;
	private $session;
	
	private $user;
	private $login_expiry;
	private $roles;
	private $status;
	
	/**
	 * Class constructor.
	 *
	 * @see set_vars(), set_user_id(), and set_user() methods.
	 */
	private function __construct( $args ) {
		$this->cookie   = $args->Cookie;
		$this->crypt    = $args->Crypt;
		$this->database = $args->Database;
		$this->dt       = $args->Date_Time_Interface;
		$this->session  = $args->Session;
		
		$this->user = new \stdClass();
		
		$this->session->start();
		$this->set_vars();
		$this->set_user_id();
		$this->set_user();
		$this->session->end();
	}
	
	/**
	 * Magic method that stores new user data in $this->user property, otherwise returns NULL if user data variable name exists.
	 *
	 * @param string $name  User data variable name.
	 * @param string $value User data.
	 * @return NULL if user data variable name exists..
	 */
	public function __set( $name, $value ) {
		if ( ! isset ( $this->user->{$name} ) ) {
			$this->user->{$name} = $value;
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Magic method that returns user data from $this->user property, otherwise NULL.
	 *
	 * @param string $name User data variable name.
	 * @return var User data, otherwise NULL.
	 */
	public function __get( $name ) {
		if ( isset ( $this->user->{$name} ) ) {
			return $this->user->{$name};
		}
		return NULL;
	}
	
	/**
	 * Returns TRUE if user data exists in $this->user property, otherwise FALSE.
	 *
	 * @param string $name User data variable name.
	 * @return boolean TRUE if variable exists in $this->user property, otherwise FALSE.
	 */
	public function __isset( $name ) {
		if ( isset ( $this->user->{$name} ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	// Magic methods that ensure only a singleton instance of this class can be initialised.
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
	 * @return object Singleton instance of the Configuration class.
	 */
	public static function get_instance( $args ) {
		static $class = NULL;
		if ( NULL === $class ) {
			$class_name = __CLASS__;
			$class = new $class_name( $args );
		}
		return $class;
	}
	
	/**
	 * Returns an array of database table key to value column values.
	 *
	 * @var string $table Database table name.
	 * @var string $key   Database table column name.
	 * @var string $value Database table column name.
	 * @return array Database table column values. {
	 *   @type string $key value => $value value.
	 * }
	 */
	private function key_to_value( $table, $key = 'id', $value = 'name' ) {
		return $this->database->query( 'SELECT `' .$key .'`, `' .$value .'` FROM `' .$table .'`', \PDO::FETCH_KEY_PAIR );
	}
	
	/**
	 * Stores arrays of database table 'id' column => other column fields:
	 *  - $private_properties = Stores each value in $this->{$value} property
	 *  - $session_vars = Stores each array value in $_SESSION[$value] variable
	 *  - Stores $_SESSION['timezones'] object variable.
	 */
	private function set_vars() {
		$private_properties = array ( 'login_expiry', 'roles', 'status' );
		foreach ( $private_properties as $property ) {
			$this->{$property} = $this->key_to_value( $property );
		}
		
		$session_vars = array ( 'classes', 'countries', 'races' );
		foreach ( $session_vars as $variable ) {
			if ( ! isset ( $this->session->{$variable} ) ) {
				$this->session->{$variable} = $this->key_to_value( $variable );
			}
		}
		if ( ! isset ( $this->session->timezones ) ) {
			$this->session->timezones = $this->database->query( 'SELECT * FROM timezones', \PDO::FETCH_UNIQUE | \PDO::FETCH_OBJ );
		}
	}
	
	/**
	 * Stores user's ID and login status in $this->user property.
	 *
	 *  1. If $_COOKIE['oz_token'] and $_COOKIE['oz_user'] are set:
	 *      - $login_token = Database 'login_tokens' data retrieved via $_COOKIE['oz_token'] value
	 *      - $expiry_date_time = Date/Time object created from login expiry date and time (UTC).
	 *  2. If $expiry_date_time is in 'yyyy-mm-dd hh:mm:ss' format and valid (i.e. prior to the current date and time (UTC)):
	 *      - User's ID and login status is stored
	 *      - Otherwise User ID = 1 ('Public' User) and login status = FALSE.
	 */
	private function set_user_id() {
		if ( isset ( $this->cookie->oz_token ) && isset ( $this->cookie->oz_user ) ) {
			$this->database->parameters = array ( 'token' => $this->cookie->oz_token );
			$login_token = $this->database->query( 'SELECT `id`, `user`, `expiry_date_time`, `iv` FROM `login_tokens` WHERE `token` = :token', \PDO::FETCH_OBJ )[0];
			$user_token = explode ( $this->crypt->delimiter, $this->crypt->decrypt( $this->cookie->oz_user, $login_token->iv ) );
			if ( $login_token->expiry_date_time == $user_token[1] ) {
				$expiry_date_time = $this->dt->create( 'date_time',  $login_token->expiry_date_time, $this->dt->timezone() );
				if ( $this->dt->validate( $expiry_date_time ) ) {
					$this->user->id        = $user_token[0];
					$this->user->logged_in = TRUE;
					return;
				}
			}
		}
		$this->user->id        = 2;
		$this->user->logged_in = FALSE;
	}
	
	/**
	 * Stores user data from database 'users' table in $this->user property.
	 *
	 * @see $this->user property.
	 */
	private function set_user() {
		$this->database->parameters = array ( 'id' => $this->user->id );
		$user = $this->database->query( 'SELECT * FROM `users` WHERE `id` = :id', \PDO::FETCH_OBJ )[0];
		
		$this->user->table_id       = (object) array (
			'role'         => $user->role,
			'status'       => $user->status,
			'login_expiry' => $user->login_expiry,
			'country'      => $user->country
		);
		$this->user->username       = $user->username;
		$this->user->role           = $this->roles[$user->role];
		$this->user->status         = $this->status[$user->status];
		$this->user->login_expiry   = $this->login_expiry[$user->login_expiry];
		$this->user->country        = $this->session->countries[$user->country];
		
		if ( isset ( $this->cookie->oz_timezone ) ) {
			$timezone = $this->cookie->oz_timezone;
			$this->user->timezone     = (object) array (
				'id'   => $timezone,
				'name' => $this->session->timezones[$timezone]->name,
				'php'  => $this->session->timezones[$timezone]->php
			);
		}
		else {
			$this->user->timezone     = (object) array (
				'id'   => $user->timezone,
				'name' => $this->session->timezones[$user->timezone]->name,
				'php'  => $this->session->timezones[$user->timezone]->php
			);
		}
		
		if ( $this->user->id > 2 ) {
			$this->database->parameters = array ( 'user', $this->user->id );
			$characters = $this->database->query( 'SELECT * FROM `characters` WHERE `user` = :user', \PDO::FETCH_OBJ );
			if ( $this->set( $characters ) ) {
				foreach ( $characters as $character ) {
					$this->user->characters[$character->id] = (object) array (
						'name'       => $character->name,
						'race'       => $this->session->races[$character->race],
						'class'      => $this->session->classes[$character->class],
						'raid_ready' => $character->raid_ready
					);
				}
				return;
			}
			$this->user->characters = NULL;
		}
		
		$this->user->mic            = $user->mic;
		$this->user->email          = $user->email;
		$this->user->facebook       = $user->facebook;
		$this->user->twitter_handle = $user->twitter_handle;
		$this->user->contact        = $user->contact;
		$this->user->image          = $user->image;
		$this->user->date_time = (object) array (
			'application' => $user->application_date_time,
			'review'      => $user->review_date_time
		);
	}
	
	public function get_user() {
		return $this->user;
	}
	
}
