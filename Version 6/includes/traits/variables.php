<?php
namespace Ozraid\Includes\Traits;

/**
 * Variables trait.
 *
 * Type:         Trait
 * Description:  Validates or sorts variables. All methods return TRUE if variable is valid, otherwise FALSE.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Trait methods:
 *                - numeric_keys_array() Checks if a variable is an array with numeric keys.
 *                - multi_array()        Checks if a variable is a multi-dimensional array.
 *                - multi_array_flip()   Flips the column and row keys in a multi-dimensional array.
 *                - multi_object()       Checks if a variable is an array or object containing objects.
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

trait Variables {
	
	/**
	 * Checks if a variable is an array with numeric keys.
	 *
	 * @param var $variable Variable to be validated.
	 */
	 private function numeric_keys_array( $variable ) {
		 if ( !is_array ( $variable ) ) {
			 return FALSE;
		 }
		 foreach ( $variable as $key => $value ) {
			 if ( ! is_int ( $key ) && ! ctype_digit( $key ) ) {
				 return FALSE;
			 }
		 }
		 return TRUE;
	 }
	
	/**
	 * Checks if a variable is a multi-dimensional array.
	 *
	 * @param var $variable Variable to be validated.
	 */
	private function multi_array( $variable ) {
		if ( ! is_array ( $variable ) ) {
			return FALSE;
		}
		foreach ( $variable as $array ) {
			if ( ! is_array ( $array ) ) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Flips the column and row keys in a multi-dimensional array.
	 *
	 * @param array $array Multi-dimensional array. {
	 *   @type array column key {
	 *     @type row key Value.
	 *   }
	 * }
	 * @return array Multi-dimensional array. {
	 *   @type array row key {
	 *     @type column key Value.
	 *   }
	 * }
	 */
	private function multi_array_flip( array $array ) {
		foreach ( $array as $column => $rows ) {
			foreach ( $rows as $row => $value ) {
				$result[$row][$column] = $value;
			}
		}
		return $result;
	}
	
	/**
	 * Checks if a variable is a multi-dimensional object.
	 *
	 * @param var $variable Variable to be validated.
	 */
	private function multi_object( $variable ) {
		if ( ! is_array ( $variable ) && ! is_object ( $variable ) ) {
			return FALSE;
		}
		foreach ( $variable as $array ) {
			if ( ! is_object ( $array ) ) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
}
