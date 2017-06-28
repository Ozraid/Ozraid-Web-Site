<?php
namespace Ozraid\Includes\Classes;

/**
 * cPanel API class (implements the Cases trait).
 *
 * Type:         Class
 * Dependencies: Config_Cpanel
 * Description:  Performs cPanel mailbox (email account) and subdomain functions.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class methods:
 *                - set_domain_name() Stores a domain name in cPanel Configuration class $config-> property.
 *
 *                - get_mailbox()      Checks if mailbox exists (email account).
 *                - set_mailbox()      Adds a new mailbox (email account).
 *                - delete_mailbox()   Deletes an existing mailbox (email account).
 *                - mailbox_password() Changes a mailbox password (email account password).
 *                - mailbox_quota()    Changes a mailbox quota limit (email account).
 *                - mailbox_usage()    Returns a mailbox's usage in megabytes (email account data usage).
 *                - get_forward()      Returns an array of existing mailbox forwarding addresses (email account forward email addresses),
 *                                     otherwise NULL.
 *                - set_forward()      Adds a mailbox forwarding address (email account forward).
 *                - delete_forward()   Deletes an existing mailbox forwarding address (email account forward).
 *
 *                - subdomains()       Returns all subdomains set in cPanel.
 *                - get_subdomain()    Checks if a subdomain is set in cPanel.
 *                - set_subdomain()    Adds a subdomain to cPanel.
 *                - delete_subdomain() Deletes a subdomain from cPanel.
 *                - subdomain_path()   Changes a subdomain's relative path.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Cpanel_Interface {
	
	// Use the Cases trait.
	use \Ozraid\Includes\Traits\Cases;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $cpanel cPanel Configuration class.
	 * @var string $crlf   x
	 */
	private $cpanel;
	private $crlf = "\r\n";
	
	/**
	 * Class constructor.
	 */
	public function __construct( $args ) {
		$this->cpanel = $args->Config_Cpanel;
	}
	
	/**
	 * Stores a domain name in cPanel Configuration class $config property.
	 *
	 * @param string $domain_name Website domain name.
	 */
	public function set_domain_name( $domain_name ) {
		$this->cpanel->set_domain_name( $domain_name  );
	}
	
	// cPANEL METHODS
	
	/**
	 * Validates a cPanel CURL query response.
	 *
	 * @param object $response cPanel CURL query response.
	 * @return TRUE if query was successful, otherwise FALSE.
	 */
	private function validate( $response ) {
		if ( isset ( $response->data[0]->result ) || isset ( $response->data[0]->status ) ) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Adds cPanel user, module, function, and API version arguments to injected arguments, and returns a cPanel CURL query.
	 *
	 * @param string $user
	 * @param string $module   cPanel module.
	 * @param string $function cPanel module function.
	 * @param string $type     API version. '1' or '2'.
	 * @param array  $args     cPanel module function arguments. {
	 *   @type parameter name Parameter value.
	 * }
	 * @return object cPanel CURL query response.
	 *
	 * @see https://documentation.cpanel.net/display/SDK/Software+Development+Kit+Home for a list of cPanel API 1 and API 2 modules and functions.
	 */
	private function get_query_args( $user, $module, $function, $type, $args ) {
		$args['cpanel_jsonapi_user']       = $user;
		$args['cpanel_jsonapi_module']     = $module;
		$args['cpanel_jsonapi_func']       = $function;
		$args['cpanel_jsonapi_apiversion'] = $type;
		return $this->query( 'cpanel', $args );
	}
	
	/**
	 * Executes a cPanel CURL query, and returns a response.
	 *
	 * @param string $function Function appended to the URI string. Typically 'cpanel'.
	 * @param array  $args     cPanel module function arguments. {
	 *   @type parameter name Parameter value.
	 * }
	 * @return object cPanel CURL query response.
	 */
	private function query( $function, $args ) {
		$url           = 'https://' .$this->cpanel->host .':' .$this->cpanel->port .'/json-api/' .$function;
		$query_string  = http_build_query( $args, '', '&' );
		$header[0]     = 'Authorization: Basic ' .base64_encode ( $this->cpanel->username .':'. $this->cpanel->password ) .$this->crlf;
		$header[0]    .= 'Content-Type: application/x-www-form-urlencoded' .$this->crlf;
		$header[0]    .= 'Content-Length: ' .strlen ( $query_string ) .$this->crlf .$this->crlf;
		$header[0]    .= $query_string;
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_BUFFERSIZE, 131072 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		$result = curl_exec( $curl );
		if ( $result == FALSE ) {
			echo 'curl_exec threw error: \\ ' .curl_error( $curl ) . ' \\ for ' .$url .'?' .$query_string;
		}
		curl_close( $curl );
		return json_decode ( $result )->cpanelresult;
	}
	
	
	// EMAIL / MAILBOX METHODS
	
	/**
	 * Checks if mailbox exists (email account).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @return boolean TRUE if mailbox exists (email account), otherwise FALSE.
	 */
	public function get_mailbox( $username ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'accountname', '2', array ( 'account' => $username ) );
		if ( $query->data[0]->account == $username ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Adds a new mailbox (email account).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @param string $password User's password.
	 * @param int    $quota    Optional. Mailbox quota limit (megabytes). Default '2000 (MG)'.
	 *                         If $quota is '0', the mailbox is unlimited (does not possess a quota limit).
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function set_mailbox( $username, $password, $quota = '2000' ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'addpop', '2', array ( 'domain' => $this->cpanel->domain_name, 'email' => $username, 'password' => $password, 'quota' => $quota ) );
		return $this->validate( $query );
	}
	
	/**
	 * Deletes an existing mailbox (email account).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function delete_mailbox( $username ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'delpop', '2', array ( 'domain' => $this->cpanel->domain_name, 'email' => $username ) );
		return $this->validate( $query );
	}
	
	/**
	 * Changes a mailbox password (email account password).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @param string $password User's password.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function mailbox_password( $username, $password ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'passwdpop', '2', array ( 'domain' => $this->cpanel->domain_name, 'email' => $username, 'password' => $password ) );
		return $this->validate( $query );
	}
	
	/**
	 * Changes a mailbox quota limit (email account).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @param int    $quota    Mailbox quota limit (megabytes).
	 *                         If $quota is '0', the mailbox is unlimited (does not possess a quota limit).
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function mailbox_quota( $username, $quota ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'editquota', '2', array ( 'domain' => $this->cpanel->domain_name, 'email' => $username, 'quota' => $quota ) );
		return $this->validate( $query );
	}
	
	/**
	 * Returns a mailbox's usage in megabytes (email account data usage).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @return var Integer of mailbox usage in megabytes, otherwise FALSE.
	 */
	public function mailbox_usage( $username ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'getdiskusage', '2', array ( 'domain' => $this->cpanel->domain_name, 'user' => $username ) );
	return $query->data[0]->diskused;
	}
	
	/**
	 * Returns an array of existing mailbox forwarding addresses (email account forward email addresses), otherwise NULL.
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @return var An array of forward email address, otherwise NULL. {
	 *   @var numeric key Forward email address (text format).
	 * }
	 */
	public function get_forward( $username ) {
		$query = $this->get_query_args( 'serverusername', 'Email', 'listforwards', '2', array ( 'domain' => $this->cpanel->domain_name ) );
		if ( $query->data[0]->forward ) {
			foreach ( $query->data as $key => $data ) {
				$output[$key] = $data->forward;
			}
			return $output;
		}
		return NULL;
	}
	
	/**
	 * Adds a mailbox forwarding address (email account forward).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @param string $email    Forwarding email address. Must be a valid email address, otherwise returns FALSE.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function set_forward( $username, $email ) {
		if ( strpos ( $email, '@' ) === FALSE ) {
			return FALSE;
		}
		$query = $this->get_query_args( 'serverusername', 'Email', 'addforward', '2', array ( 'domain' => $this->cpanel->domain_name, 'email' => $username, 'fwdopt' => 'fwd', 'fwdemail' => $email, 'failmsgs' => $username .'@' .$this->cpanel->domain_name .' cannot be reached due to an invalid forwarding email address.' ) );
		if ( $query->data[0]->forward == $email ) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Deletes an existing mailbox forwarding address (email account forward).
	 *
	 * @param string $username User's mailbox name (the email suffix). i.e. 'test' = 'test@ozraid.com'.
	 * @param string $email    Forwarding email address. Must be a valid email address, otherwise returns FALSE.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function delete_forward( $username, $email ) {
		if ( strpos ( $email, '@' ) === FALSE ) {
			return FALSE;
		}
		$query = $this->get_query_args( 'serverusername', 'Email', 'delforward', '2', array ( 'email' => $username .'@' .$this->cpanel->domain_name, 'emaildest' => $email ) );
		return $this->validate( $query );
	}
	
	
	// SUBDOMAIN METHODS
	
	/**
	 * Returns all subdomains set in cPanel.
	 *
	 * @return var Array of subdomains, otherwise NULL. {
	 *   @type string numeric key Subdomain.
	 * }
	 */
	public function subdomains() {
		$query = $this->get_query_args( 'serverusername', 'SubDomain', 'listsubdomains', '2', array () );
		if ( isset ( $query->data[0]->subdomain ) ) {
			foreach ( $query->data as $key => $data ) {
				$output[$key] = $data->subdomain;
			}
			return $output;
		}
		return NULL;
	}
	
	/**
	 *  Checks if a subdomain is set in cPanel.
	 *
	 * @param string $subdomain Subdomain.
	 * @return var Object containing subdomain data, otherwise NULL. {
	 *   @type string subdomain x
	 *   @type string domain    x
	 * }
	 */
	public function get_subdomain( $subdomain ) {
		$subdomain = $this->lower_case( $subdomain );
		$query = $this->get_query_args( 'serverusername', 'SubDomain', 'listsubdomains', '2', array ( 'regex' => $subdomain ) );
		if ( $query->data[0]->subdomain == $subdomain ) {
			return (object) array (
				'subdomain'   => $query->data[0]->subdomain,
				'domain_name' => $query->data[0]->rootdomain,
				'domain'      => $query->data[0]->domain,
				'base_dir'    => $query->data[0]->dir
			);
		}
		return NULL;
	}
	
	/**
	 * Adds a subdomain to cPanel.
	 *
	 * @param string $subdomain Subdomain.
	 * @param string $path      Optional. Relative base path requests to the subdomain will be routed to. Default = 'public_html' directory.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function set_subdomain( $subdomain, $path = NULL ) {
		$subdomain = $this->lower_case( $subdomain );
		if ( ! isset ( $dir ) ) {
			$path = $_SERVER['DOCUMENT_ROOT'];
		}
		$query = $this->get_query_args( 'serverusername', 'SubDomain', 'addsubdomain', '2', array ( 'domain' => $subdomain, 'rootdomain' => $this->cpanel->domain_name, 'canoff' => 0, 'dir' => $path, 'disallowdot' => 0 ) );
		return $this->validate( $query );
	}
	
	/**
	 * Deletes a subdomain from cPanel.
	 *
	 * @param string $subdomain Subdomain.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function delete_subdomain( $subdomain ) {
		$subdomain = $this->lower_case( $subdomain );
		$query = $this->get_query_args( 'serverusername', 'SubDomain', 'delsubdomain', '2', array ( 'domain' => $subdomain .'.' .$this->cpanel->domain_name  ) );
		return $this->validate( $query );
	}
	
	/**
	 * Changes a subdomain's relative base path.
	 *
	 * @param string $subdomain Subdomain.
	 * @param string $path      Relative base path requests to the subdomain will be routed to starting from the 'public_hemtl' directory.
	 *                          i.e. '/home/webeatth/public_html' prefixes the path specified.
	 * @return boolean TRUE if successful, otherwise FALSE.
	 */
	public function subdomain_path( $subdomain, $path ) {
		$subdomain = $this->lower_case( $subdomain );
		$base_path = $_SERVER['DOCUMENT_ROOT'] .$path;
		$query = $this->get_query_args( 'serverusername', 'SubDomain', 'changedocroot', '2', array ( 'subdomain' => $subdomain, 'rootdomain' => $this->cpanel->domain_name, 'dir' => $base_path ) );
		return $this->validate( $query );
	}
	
}
