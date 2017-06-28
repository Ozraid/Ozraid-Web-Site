<?php
namespace Ozraid\Includes\Classes;

/**
 * Text class (implements the Cases trait).
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Uses the Cases trait to include methods that return a string in a specific format.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Text {
	
	// Use the Cases trait.
	use \Ozraid\Includes\Traits\Cases;
	
}
