<?php
namespace Ozraid\Includes\Traits;

/**
 * Cases trait.
 *
 * Type:         Trait
 * Description:  Methods to return a string in a specific format.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Trait methods:
 *                - attribute_case() Returns HTML5 attribute case.   i.e. 'attribute-case-text'.
 *                - class_case()     Returns PHP class case.         i.e. 'Class_Case_Text'.
 *                - lower_case()     Returns lower case text.        i.e. 'lower case text'.
 *                - title_case()     Returns title case text.        i.e. 'Title Case Text'.
 *                - upper_case()     Returns upper case text.        i.e. 'UPPER CASE TEXT'.
 *                - variable_case()  Returns PHP variable case text. i.e. 'variable_case_text'.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

trait Cases {
	
	/**
	 * Returns a string in HTML5 attribute case.
	 *  - All words are lower case, with a dash (-) separating words. i.e. 'example-attribute-name'.
	 *
	 * @param string $text
	 * @return string HTML5 attribute name case.
	 */
	public function attribute_case( $text ) {
		$search = array ( '_', ' ' );
		return str_replace ( $search, '-', strtolower ( $text ) );
	}
	
	/**
	 * Returns a string in PHP class case.
	 *  - First letter of each word is capitalised, with an underscore (_) separating words. i.e. 'Example_Model_Class_Name'.
	 *
	 * @param string $text
	 * @return string PHP class name case.
	 */
	public function class_case( $text ) {
		$search = array ( '-', '_' );
		$text = str_replace ( $search, '', $text );
		return str_replace ( ' ', '_', ucwords ( strtolower ( $text ) ) );
	}
	
	/**
	 * Returns a string in lower case.
	 *  - All letters lower case, with spaces ( ) separating words. i.e. 'this is a lower case string'.
	 *
	 * @param string $text
	 * @return string Lower case text.
	 */
	public function lower_case( $text ) {
		$search = array ( '-', '_' );
		return str_replace ( $search, ' ', strtolower ( $text ) );
	}
	
	/**
	 * Returns a string in title case.
	 *  - First letter of each word capitalised. i.e. 'The Name Of A Thing'.
	 *
	 * @param string $text
	 * @return string Title case text.
	 */
	public function title_case( $text ) {
		$search = array ( '-', '_' );
		return ucwords ( str_replace ( $search, ' ', $text ) );
	}
	
	/**
	 * Returns a string in upper case.
	 *  - All letters upper case, with spaces ( ) separating words. i.e. 'THIS IS AN UPPER CASE STRING'.
	 *
	 * @param string $text
	 * @return string Upper case text.
	 */
	public function upper_case( $text ) {
		$search = array ( '-', '_' );
		return str_replace ( $search, ' ', strtoupper ( $text ) );
	}
	
	
	/**
	 * Returns a string in PHP variable case.
	 *  - All words lower case, with an underscore (_) separating words. i.e. 'example_php_variable'.
	 *
	 * @param string $text
	 * @return string PHP variable name case.
	 */
	public function variable_case( $text ) {
		$search = array ( '-', ' ' );
		return str_replace ( $search, '_', strtolower ( $text ) );
	}
	
}
