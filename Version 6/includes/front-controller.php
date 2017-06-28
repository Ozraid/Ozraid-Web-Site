<?php
namespace Ozraid\Includes;

/**
 * Front Controller PHP file.
 *
 * Type:         PHP
 * Description:  Front Controller.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

/**
 *  1. Defines the 'AB_PATH' constant = The absolute path to the website '/includes/' directory.
 *  2. Defines the 'BASE_NS' constant = Base namespace of files in the '/includes/' directory.
 */
define ( 'AB_PATH', __DIR__ .'/' );
define ( 'BASE_NS', __NAMESPACE__ );

/**
 *  1. Loads and initialises Status Error class and exception handler that blocks access to the PHP file.
 *  2. Automatically loads PHP classes when initialised.
 */
require_once constant ( 'AB_PATH' ) .'classes/status-error.php';
require_once constant ( 'AB_PATH' ) .'autoload.php';

/**
 * Loads and initialises:
 *  - $container = Dependency Injection (DI) Container class
 *  - $config    = Configuration class
 *  - $db        = Database API class.
 */
$container = new Classes\DI_Container( 'reset' );
$config = $container->Config;
$db = $container->Database_Interface;
$view = $container->View;

$subdomains = $db->key_to_value( 'subdomains' );
$base_paths = $db->key_to_value( 'base_paths' );
$public_directories = $db->key_to_value( 'public_directories' );
if ( in_array ( $config->subdomain, $subdomains ) ) {
	if ( in_array ( $config->subdomain, $base_paths ) ) {
		$output = $view->get_html_output( '<h1>' .ucfirst ( $config->subdomain ) .'</h1>' ."\r\n" .'<p>This is the Ozraid website <strong>' .$config->subdomain .'</strong> page accessed via a subdomain.</p>' );
	}
	else {
		$output = $view->get_html_output( '<h1>' .ucfirst ( $config->subdomain ) .' Unique Subdomain Page</h1>' ."\r\n" .'<p>This is the Ozraid website <strong>' .$config->subdomain .'</strong> subdomain page. It will link up to the appropriate Ozraid website page.</p>' );
	}
}
else if ( in_array ( $config->base_path, $base_paths ) ) {
	$output = $view->get_html_output( '<h1>' .ucfirst ( $config->base_path ) .'</h1>' ."\r\n" .'<p>This is the Ozraid website <strong>' .$config->base_path .'</strong> page.</p>' );
}
else if ( in_array ( $config->base_path, $public_directories ) ) {
	$output = $view->get_html_output( '<h1>' .ucfirst ( $config->base_path ) .' Directory</h1>' ."\r\n" .'<p>This is the Ozraid website <strong>' .$config->base_path .'</strong> directory.</p>' );
}
else if ( $config->path == NULL ) {
	$output = $view->get_html_output( '<h1>Home</h1>' ."\r\n" .'<p>This is the Ozraid website <strong>Home</strong> page.</p>' );
}
else {
	$e = new Classes\Status_Error( 404 );
	echo $view->get_html_output( $e->get_error_message() );
	exit();
}

echo $output;
