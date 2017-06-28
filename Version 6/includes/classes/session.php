<?php
namespace Ozraid\Includes\Classes;

/**
 * PHP Session class.
 *
 * Type:         Class
 * Dependencies: Config
 * Description:  Manages PHP Sessions.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class properties:
 *                - $status     TRUE if a PHP Session is active, otherwise FALSE.
 *
 *               Public class methods:
 *                - __set()   Magic method that stores a variable in the current PHP Session.
 *                            Starts a PHP Session if one is not currently active.
 *                - __get()   Magic method that returns a PHP Session variable, otherwise NULL.
 *                - __isset() Magic method that returns TRUE if a PHP Session variable exists, otherwise FALSE.
 *                - __unset() Magic method that destroys a PHP Session variable.
 *                - start()   Starts a new PHP Session and sets a PHP Session cookie.
 *                - end()     Ends the current PHP Session.
 *                - destroy() Destroys all PHP Session variables, the current PHP Session, and the PHP Session cookie.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Session {
	
	/**
	 * PUBLIC PROPERTIES
	 *
	 * @var boolean $status Session status TRUE if a PHP Session is active, otherwise FALSE.
	 */
	public $status = FALSE;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $config       Configuration class.
	 * @var string $session_name PHP Session name. Configuration class $prefix property + 'session'. i.e. 'oz_session'.
	 */
	private $config;
	private $session_name;
	
	/**
	 * Class constructor.
	 */
	public function __construct( $args ) {
		$this->config = $args->Config;
		$this->session_name = $this->config->prefix .'session';
	}
	
	/**
	 * Magic method that stores a variable in the current PHP Session.
	 * Starts a PHP Session if one is not currently active.
	 *
	 * @param string $name  Variable name.
	 * @param var    $value Variable value.
	 */
	public function __set( $name, $value ) {
		if ( ! $this->status ) {
			$this->start();
		}
		$_SESSION[$name] = $value;
	}
	
	/**
	 * Magic method that returns a PHP Session variable, otherwise NULL.
	 *
	 * @param string $name Variable name.
	 * @return var PHP Session variable, or NULL if no session has been started or the variable does not exist.
	 */
	public function __get( $name ) {
		if ( $this->status && isset ( $_SESSION[$name] ) ) {
			return $_SESSION[$name];
		}
		return NULL;
	}
	
	/**
	 * Magic method that returns TRUE if a PHP Session variable exists, otherwise FALSE.
	 *
	 * @param string $name Variable name.
	 */
	public function __isset( $name ) {
		if ( $this->status && isset ( $_SESSION[$name] ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Magic method that destroys a PHP Session variable.
	 *
	 * @param string $name Variable name.
	 */
	public function __unset( $name ) {
		if ( $this->status && isset ( $_SESSION[$name] ) ) {
			unset ( $_SESSION[$name] );
		}
	}
	
	// Starts a new PHP Session and sets a PHP Session cookie.
	public function start() {
		session_start ( array ( 'name' => $this->session_name ) );
		$this->status = TRUE;
	}
	
	// Ends the current PHP Session.
	public function end() {
		session_destroy();
		$this->status = FALSE;
	}
	
	// Destroys all PHP Session variables, the current PHP Session, and the PHP Session cookie.
	public function destroy() {
		session_unset();
		$session = (object) session_get_cookie_params();
		setcookie ( $this->session_name, '', time() - 3600, $session->path, $session->domain, $session->secure, $session->httponly );
		$this->close();
	}
	
}
