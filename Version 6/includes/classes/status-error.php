<?php
namespace Ozraid\Includes\Classes;

/**
 * Status Error Exception class (extension of Exception class).
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Handles HTTP status code browser and server errors.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

class Status_Error extends \Exception {
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var array $this->status HTTP status codes that indicate a browser or server error. {
	 *   @type array http status code {
	 *     @type string type    HTTP status code type.
	 *     @type string message Error message.
	 *   }
	 * }
	 * @var array $this->status_codes HTTP status codes. {
	 *   @type int key HTTP status code.
	 * }
	 */
	private $status = array (
		400 => array (
			'type'    => 'Bad Request',
			'message' => 'An error has occurred with your browser. Please try again, or contact us if the error occurs again.'
		),
		401 => array (
			'type'    => 'Authorisation Required',
			'message' => 'You do not have permission to access the directory, file, or page requested.'
		),
		402 => array (
			'type'    => 'Payment Required',
			'message' => 'An error has occurred with your browser. Please try again, or contact us if the error occurs again.'
		),
		403 => array (
			'type'    => 'Forbidden',
			'message' => 'You do not have permission to access the directory, file, or page requested.'
		),
		404 => array (
			'type'    => 'Directory, File, or Page Not Found',
			'message' => 'The directory, file, or page requested can not be found.'
		),
		405 => array (
			'type'    => 'Method Not Allowed',
			'message' => 'Your browser has submitted data in an incorrect format. Please try again, or contact us if the error occurs again.'
		),
		406 => array (
			'type'    => 'Not Acceptable',
			'message' => 'The directory, file, or page requested cannot be displayed by your browser in the format requested. Please try again, or contact us if the error occurs again.'
		),
		407 => array (
			'type'    => 'Proxy Authentication Required',
			'message' => 'You do not have permission to access the directory, file, or page requested.'
		),
		408 => array (
			'type'    => 'Request Timeout',
			'message' => 'The directory, file, or page requested can not be displayed as your browser timed out. Please try again, or contact us if the error occurs again.'
		),
		409 => array (
			'type'    => 'Conflict',
			'message' => 'The directory, file, or page requested can not be displayed due to a conflict. Please try again, or contact us if the error occurs again.'
		),
		410 => array (
			'type'    => 'Gone',
			'message' => 'The directory, file, or page requested is no longer available..'
		),
		411 => array (
			'type'    => 'Length Required',
			'message' => 'An error has occurred with your browser. Please try again, or contact us if the error occurs again.'
		),
		412 => array (
			'type'    => 'Precondition Failed',
			'message' => 'The directory, file, or page requested can not be displayed as the server does not meet one of the conditions requested by your browser. Please try again, or contact us if the error occurs again.'
		),
		413 => array (
			'type'    => 'Payload Too Large',
			'message' => 'An error has occurred due to your browser supplying too much data. Our server can\'t handle that much information! Please try again, or contact us if the error occurs again.'
		),
		414 => array (
			'type'    => 'Request ULI Too Long',
			'message' => 'An error has occurred as the URL to the directory, file, or page is too long. We recommend using our search engine to find for what you\'re looking for.'
		),
		415 => array (
			'type'    => 'Unsupported Media Type',
			'message' => 'The file requested can not be displayed as the server does not support the file format.'
		),
		416 => array (
			'type'    => 'Request Range Not Satisfiable',
			'message' => 'The file requested can not be displayed due to an error with your browser. Please try again, or contact us if the error occurs again.'
		),
		417 => array (
			'type'    => 'Expectation Failed',
			'message' => 'The directory, file, or page requested can not be displayed as the server does not meet your browser\'s expectations. We are sorry to disappoint you, and your browser! Please try again, or contact us if the error occurs again.'
		),
		422 => array (
			'type'    => 'Unprocessable Entity',
			'message' => 'The directory, file, or page requested can not be displayed due to an error with the URL. Please try again, or contact us if the error occurs again.'
		),
		423 => array (
			'type'    => 'Locked',
			'message' => 'The directory, file, or page requested is currently locked. Please try again at a later time, or contact us if the error occurs again.'
		),
		424 => array (
			'type'    => 'Failed Dependency',
			'message' => 'An error has occurred. Please try again, or contact us if the error occurs again.'
		),
		500 => array (
			'type'    => 'Internal Server Error',
			'message' => 'A server error has occurred. Please try again later, or contact us if the error occurs again.'
		),
		501 => array (
			'type'    => 'Not Implemented',
			'message' => 'Our server does not recognise the request method. Please try again, or contact us if the error occurs again.'
		),
		502 => array (
			'type'    => 'Bad Gateway',
			'message' => 'Our database or another part of our website is currently unavailable. Please try again later, and contact us if the error continues to occur.'
		),
		503 => array (
			'type'    => 'Service Unavailable',
			'message' => 'Our server is currently unavailable. Many hungry Hobbits are currently working behind-the-scenes on maintenance, or repairing a piece of hardware that\'s exploded! Please try again later.'
		),
		504 => array (
			'type'    => 'Gateway Timeout',
			'message' => 'Our database or another part of our website timed out. Please try again later, and contact us if the error continues to occur.'
		),
		505 => array (
			'type'    => 'HTTP Version Not Supported',
			'message' => 'Our server does not support your browser\'s HTTP version. We\'re sorry about that!'
		),
		506 => array (
			'type'    => 'Variant Also Negotiates',
			'message' => 'Our server has become confused, and generated an error due to a circular loop. Please try again, or contact us if the error occurs again.'
		),
		507 => array (
			'type'    => 'Insufficient Storage',
			'message' => 'Our server can not store the amound of data you\'ve provided. It\'s just too much information for it to handle! Please upload a smaller file.'
		),
		510 => array (
			'type'    => 'Not Extended',
			'message' => 'Our server requires more information to process your request. Please try again later, and contact us if the error continues to occur.'
		)
	);
	private $status_codes;
	
	/**
	 * Class constructor.
	 *
	 *  1. Stores HTTP status codes from $this->status in $this->status_codes property.
	 *  2. $message = Exception class error message.
	 *  3. Extended Exception class constructor.
	 *
	 * @param integer $code Exception class error code. HTTP status code that indicate a browser or server error value.
	 */
	public function __construct( $code = 0 ) {
		$this->status_codes = array_keys ( $this->status );
		$message = $this->status[$code]['message'];
		parent::__construct( $message, $code, NULL );
	}
	
	// Sets a HTTP response status code and returns $output if Exception->code property is in $this->status_codes property.
	public function get_error_message() {
		if ( in_array ( $this->getCode(), $this->status_codes ) ) {
			http_response_code( $this->getCode() );
			$crlf = "\r\n";
			
			$output  = '<h1>' .$this->status[$this->getCode()]['type'] .'</h1>' .$crlf;
			$output .= '<p>' .$this->getMessage() .'</p>';
			return $output;
		}
	}
	
}

// Automatically loads PHP classes when initialised.

/**
 * Sets a HTTP response status code, echoes an error message, and exits if:
 *  - The $_SERVER['REDIRECT_STATUS'] HTTP redirect status code is not contained within $status_codes
 *  - The 'AB_PATH' constant is undefined
 *  - The Front Controller PHP file is accessed directly.
 *
 * @var array $status_codes Common HTTP status codes that do not indicate a browser or server error.
 */
$status_codes = array ( 200, 300, 301, 302, 303, 307, 308 );
try {
	if ( ! in_array( $_SERVER['REDIRECT_STATUS'], $status_codes ) ) {
		throw new Status_Error( $_SERVER['REDIRECT_STATUS'] );
	}
	else if ( ! defined ( 'AB_PATH' ) || strtolower ( $_SERVER['REQUEST_URI'] ) == '/includes/front-controller.php' ) {
		throw new Status_Error( 403 );
	}
}
catch ( Status_Error $e ) {
	require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/autoload.php';
	$container = new DI_Container();
	$view = $container->View;
	echo $view->get_html_output( $e->get_error_message() );
	exit();
}
