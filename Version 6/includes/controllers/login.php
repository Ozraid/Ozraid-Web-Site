<?php
namespace Ozraid\Includes\Controllers;

/**
 * Login Controller class (extends Controller class).
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Website login Controller class.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Controller_Home extends Controller {
	
	public function __construct( $args ) {
		if ( isset ( $_POST ) ) {
			// Filter and validate $_POST variables.
			// Check username and password.
			// 
		}
		
		parent::__construct( $args );
	}
	
}
