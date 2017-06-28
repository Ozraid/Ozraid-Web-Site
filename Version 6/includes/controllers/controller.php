<?php
namespace Ozraid\Includes\Controllers;

/**
 * Controller class.
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Primary Controller class that is extended by other controllers.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Controller {
	
	public $logged_in;
	
	
	
	// Stores
	protected function is_logged_in() {
		
	}
	
	
	
}
