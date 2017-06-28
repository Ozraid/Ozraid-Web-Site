<?php
namespace Ozraid\Includes;

/**
 * Autoload function.
 *
 * Type:         Function
 * Description:  Registers autoload_class() function as a PHP Autoloader to automatically load PHP classes when initialised.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

/**
 * Autoloads a PHP class file when the class is initiated, and any PHP trait files used by the class.
 *
 * Traits need to use a global namespace prefix when included in a class.
 *  i.e. 'use \Ozraid\Includes\Traits\Cases;' is the correct syntax.
 *       'use Ozraid\Includes\Traits\Cases' will generate an error as the class namespace will be prefixed.
 *
 * @var string $pathname Relative path to the PHP class file generated from the class namespace and name.
 *
 * @param string $class PHP class name (with namespace).
 */
function autoload_class( $class ) {
	$search = array ( constant ( 'BASE_NS' ) .'\\', '\\', 'Controller_', 'Model_', 'View_', '_' );
	$replace = array ( '', '/', '', '', '', '-' );
	$pathname = strtolower ( str_ireplace ( $search, $replace, $class ) .'.php' );
	require_once constant ( 'AB_PATH' ) .$pathname;
}

// Registers autoload_class() function as a PHP Autoloader.
spl_autoload_register( 'Ozraid\Includes\autoload_class' );
