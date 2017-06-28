<?php
namespace Ozraid\Includes\Classes;

/**
 * Cookie class (implements the Cases trait).
 *
 * Type:         Class
 * Dependencies: Config, Date_Time_Interface
 * Description:  Sets, retrieves, and destroys HTTP cookies.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Cookies are set on all website domains, and accessible via all subdomains.
 *
 *               Public class methods:
 *                - __get()   Magic method that returns a cookie's value, otherwise NULL.
 *                - __isset() Magic method that returns TRUE if a cookie is set, otherwise FALSE.
 *                - __unset() Magic method that deletes a cookie for each website domain.
 *                - delete()  Alias for __unset() magic method.
 *                - set()     Sets a cookie for each website domain.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Cookie {
	
	// Use the Cases trait.
	use \Ozraid\Includes\Traits\Cases;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object  $config    Configuration class.
	 * @var object  $dt        Date/Time API class.
	 *
	 * @var boolean $https     TRUE cookie should only be transmitted over a secure HTTPS connection, otherwise FALSE.
	 * @var boolean $http_only TRUE cookie is only accessible via the HTTP protocol, otherwise FALSE.
	 */
	private $config;
	private $dt;
	
	private $https     = FALSE;
	private $http_only = TRUE;
	
	/**
	 * Class constructor
	 */
	public function __construct( $args ) {
		$this->config = $args->Config;
		$this->dt     = $args->Date_Time_Interface;
	}
	
	/**
	 * Magic method that returns a cookie's value, otherwise NULL.
	 *
	 * @param string $name Cookie name, with or without Config class $prefix website prefix.
	 * @return string Cookie value or NULL.
	 */
	public function __get( $name ) {
		$name = $this->prefix( $name );
		if ( isset ( $_COOKIE[$name] ) ) {
			return $_COOKIE[$name];
		}
		return NULL;
	}
	
	/**
	 * Magic method that returns TRUE if a cookie is set, otherwise FALSE.
	 *
	 * @param string $name Cookie name, with or without Config class $prefix website prefix.
	 */
	public function __isset( $name ) {
		$name = $this->prefix( $name );
		if ( isset ( $_COOKIE[$name] ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Magic method that deletes a cookie for each website domain.
	 *
	 * @param string $name Cookie name, with or without Config class $prefix website prefix.
	 */
	public function __unset( $name ) {
		$name = $this->prefix( $name );
		$expiry = time() - 3601;
		foreach ( $this->config->domain_names as $domain_name ) {
			setcookie ( $name, '', $expiry, '/', $domain_name, $this->https, $this->http_only );
		}
	}
	
	// @see __unset() method.
	public function delete( $name ) {
		$this->__unset( $name );
	}
	
	/**
	 * Returns a cookie name with Config class $prefix website prefix.
	 *
	 * @param string $name Cookie name, with or without Config class $prefix website prefix.
	 * @return Cookie name with website prefix.
	 */
	private function prefix( $name ) {
		$length = strlen ( $this->config->prefix );
		if ( substr ( $name, 0, $length ) == $this->config->prefix ) {
			return $this->variable_case( $name );
		}
		else {
			return $this->config->prefix .$this->variable_case( $name );
		}
	}
	
	/**
	 * Sets a cookie for each website domain.
	 *
	 * @param string $name   Cookie name, with or without Config class $prefix website prefix.
	 * @param var    $value  Cookie value.
	 * @param var    $expiry Optional. DateInterval class date and time parameters or NULL. See Date_Time_Interval class interval() method.
	 *                       Default NULL value ensures the cookie is valid only for the current browser session.
	 * @param string $path   Optional. URL path the cookie will be available on. Default '/' (all website paths).
	 */
	public function set( $name, $value, $expiry = NULL, $path = '/' ) {
		$name = $this->prefix( $name );
		if ( isset ( $expiry ) ) {
			$date_time = $this->dt->plus( $expiry );
			$expiry = $date_time->getTimestamp();
		}
		foreach ( $this->config->domain_names as $domain_name ) {
			setcookie ( $name, $value, $expiry, $path, $domain_name, $this->https, $this->http_only );
		}
	}
	
}
