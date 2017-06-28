<?php
namespace Ozraid\Includes\Traits;

/**
 * Validate trait.
 *
 * Type:         Trait
 * Description:  Validates variables. All methods return TRUE if variable is valid, otherwise FALSE.
 *               match() returns an array of regular expression matches if argument 3 is 'match'.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Trait methods:
 *                - email()            Checks an email address.
 *                - float()            Checks a float (a number with at least one decimal place).
 *                - html()             Checks for HTML5 tags.
 *                - int() or integer() Checks an integer (a whole number with no decimal places).
 *                - ip_address()       Checks an IP address.
 *                - match()            Checks a regular expression against a string.
 *                                     Returns an array of regular expression matches if argument 3 is 'match'.
 *                - number()           Checks any type of number.
 *                - set()              Checks if a variable exists.
 *                - string()           Checks a string of characters.
 *                - url()              Checks a URL with the FTP, HTTP, HTTPS, or mailto schemas.
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

trait Validate {
	
	/**
	 * Checks if a variable is an email address.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function email( $variable ) {
		if ( filter_var ( $variable, FILTER_VALIDATE_EMAIL ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable is a float (a number with at least one decimal place).
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function float( $variable ) {
		if ( is_float ( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable contains HTML5 tags.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function html( $variable ) {
		if ( $variable == strip_tags( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable is an integer (a whole number with no decimal places).
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function int( $variable ) {
		if ( is_int ( $variable ) || ctype_digit( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	public function integer( $variable ) {
		return $this->int( $variable );
	}
	
	/**
	 * Checks if a variable is an IP address.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function ip_address( $variable ) {
		if ( filter_var ( $variable, FILTER_VALIDATE_IP ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks a regular expression against a string.
	 *
	 * @param string $pattern  Regular expression. The start and end forward slashes (/) for preg_match are optional.
	 * @param string $variable Variable to be validated.
	 * @param string $return   'match' returns an array of regular expression matches, otherwise FALSE.
	 *                          NULL returns TRUE if the expression is matched, otherwise FALSE.
	 */
	public function match( $pattern, $string, $return = NULL ) {
		if ( substr ( $pattern, 0, 1 ) != '/' ) {
			$pattern = '/' .$pattern;
		}
		if ( substr ( $pattern, -1, 1 ) != '/' ) {
			$pattern = $pattern .'/';
		}
		if ( preg_match_all ( $pattern, $string, $matches ) ) {
			if ( $return == 'match' ) {
				return $matches[1];
			}
			else {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable is a number (either a float or integer).
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function number( $variable ) {
		if ( is_numeric ( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable exists.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function set( $variable ) {
		if ( isset( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable is a string.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function string( $variable ) {
		if ( is_string ( $variable ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Checks if a variable is a URL with the FTP, HTTP, HTTPS, or mailto schemas.
	 *
	 * @param var $variable Variable to be validated.
	 */
	public function url( $variable ) {
		$schema = array ( 'ftp', 'http', 'https', 'mailto' );
		$scheme = explode ( ':', $variable );
		if ( filter_var ( $variable, FILTER_VALIDATE_URL ) && in_array ( $scheme[0], $schema ) ) {
			return TRUE;
		}
		return FALSE;
	}
	
}
