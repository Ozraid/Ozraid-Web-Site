<?php
namespace Ozraid\Includes\Models;

/**
 * Model class.
 *
 * Type:         Class
 * Dependencies: Config, Head_Tags
 * Description:  Primary Model class that is extended by other models.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public methods:
 *                - get_random_string() Returns a random string of letters, numbers, and dashes.
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Model {
	
	use \Ozraid\Includes\Traits\Cases, \Ozraid\Includes\Traits\Validate;
	
	/**
	 * PUBLIC PROPERTIES.
	 *
	 * @var object $head     HTML 5 Head Tags class.
	 * @var object $validate Validation class.
	 */
	public $head;
	public $errors;
	public $fields;
	
	/**
	 * PRIVATE PROPERTIES.
	 *
	 * @var object $config Configuration class.
	 */
	private $config;
	private $head;
	
	
	/**
	 * Class constructor.
	 *
	 */
	public function __construct( $args ) {
		$this->config   = $args->Config;
		$this->head     = $args->Head;
	}
	
	
	
	/**
	 * Returns a random string of letters, numbers, and dashes.
	 *
	 * @param int $length Number of characters in the string to be returned. Default 12 characters in length.
	 */
	public function get_random_string( $length = 12 ) {
		return substr ( str_shuffle ( str_repeat ( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-', mt_rand ( 1 , $length ) ) ), 1, $length );
	}
	
	
}
