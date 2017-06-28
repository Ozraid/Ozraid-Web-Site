<?php
namespace Ozraid\Includes\Classes;

/**
 * Encryption/Decryption class.
 *
 * Type:         Class
 * Dependencies: Config
 * Description:  Encrypts and decrypts data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class properties:
 *                - $delimiter      Characters used to separate values stored in a string.
 *                - $iv             Random initialisation vector generated after data is encrypted via the encrypt() method.
 *
 *               Public class methods:
 *                - hash()          Returns hashed data.
 *                - validate_hash() Returns TRUE if the data is the same as the hashed data, otherwise FALSE.
 *                - encrypt()       Returns an encrypted string by generating a random initialisation vector
 *                                  and using the $this->crypt_method algorithm.
 *                - decrypt()       Returns a decrypted string using the random initialisation vector injected
 *                                  and the $this->crypt_method algorithm, otherwise NULL.
 */

// Loads and initialises Status Error class and exception handler that blocks direct access to the PHP file.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Crypt {
	
	/**
	 * PUBLIC PROPERTIES
	 *
	 * @var string $delimiter Characters used to separate values stored in a string.
	 * @var bytes  $iv        Random initialisation vector generated after data is encrypted via the encrypt() method.
	 */
	public $delimiter = ' | ';
	public $iv;
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $config       Configuration class.
	 *
	 * @var string $key
	 * @var string $crypt_method Encryption/decryption protocol.
	 * @var string $hash_method  Hash protocol.
	 */
	private $config;
	
	private $key;
	private $crypt_method = 'AES-256-CBC';
	private $hash_method  = 'SHA256';
	
	/**
	 * Class constructor.
	 */
	public function __construct( $args ) {
		$this->config = $args->Config;
		$this->key = $this->hash ( $this->config->private_key );
	}
	
	/**
	 * Returns hashed data.
	 *
	 * @param string $data Data to be hashed.
	 */
	public function hash( $data ) {
		return password_hash ( $data, PASSWORD_DEFAULT, array ( 'cost' => 12 ) );
	}
	
	/**
	 * Returns TRUE if the data is the same as the hashed data, otherwise FALSE.
	 *
	 * @param string $data Data to be validated.
	 * @param string $hash Hashed data.
	 */
	public function validate_hash( $data, $hash ) {
		return password_verify ( $data, $hash );
	}
	
	/**
	 * Returns hashed data using the $this->hash algorithm as either a string or in binary hexidecimal format.
	 *
	 * @param string  $data   Data to be hashed.
	 * @param boolean $output FALSE returns data as a string, otherwise TRUE returns data in binary hexidecimal format.
	 * @return var Hashed data as a string or in binary hexidecimal format.
	 */
	private function ssl_hash( $data, $output = TRUE ) {
		return openssl_digest ( $data, $this->hash_method, $output );
	}
	
	/**
	 * Returns an encrypted string by generating a random initialisation vector and using the $this->crypt_method algorithm.
	 *  - The initialisation vector is stored in $this->iv property so the encrypted string can be decrypted in the future
	 *  - The data to be encrypted has $this->delimiter and the hashed initialisation vector added prior to encryption.
	 *
	 * @param string  $data    Data to be encrypted.
	 * @param boolean $decrypt Optional. TRUE if data is to be decrypted, otherwise FALSE.
	 * @return string Encrypted data.
	 */
	public function encrypt( $data, $decrypt = TRUE ) {	
		$this->iv = openssl_random_pseudo_bytes ( 16 );
		return openssl_encrypt ( $data .$this->delimiter .$this->hash( $this->iv ), $this->crypt_method, $this->key, 0, $this->iv );
	}
	
	/**
	 * Returns a decrypted string using the random initialisation vector injected and the $this->crypt_method algorithm, otherwise NULL.
	 *  - If the hashed initialisation vector is the same as the hashed initialisation vector at the end of the decrypted string,
	 *    the decrypted string is returned, otherwise FALSE.
	 *
	 * @param string $data Data to be encrypted.
	 * @param bytes  $iv   Initialisation vector used to encrypt the data.
	 * @return var Decrypted data, otherwise FALSE if the hashed $iv is not equivalent to the hashed initialisation vector
	 *                appended to the end of the decrypted data.
	 */
	public function decrypt( $data, $iv ) {
		$decrypt_data = openssl_decrypt ( $data, $this->crypt_method, $this->key, 0, $iv );
		$decrypt_iv_hash = end ( explode ( $this->delimiter, $decrypt_data ) );
		if ( $this->validate_hash( $iv, $decrypt_iv_hash ) ) {
			return rtrim ( $decrypt_data, $this->delimiter .$decrypt_iv_hash );
		}
		return FALSE;
	}
	
}
