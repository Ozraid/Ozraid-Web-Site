<?php
namespace Ozraid\Includes\Config;

/**
 * HTML5 <head> elements configuration class.
 *
 * Type:         Class
 * Dependencies: Config
 * Description:  HTML5 page <head> data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Config_Head {
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $config Configuration class.
	 */
	private $config;
	
	/**
	 * PUBLIC PROPERTIES
	 *
	 * @var array $html_attributes          HTML5 <html> element attributes.
	 * @var array $base_attributes          HTML5 <base> element attributes.
	 * @var array $meta_attributes          Multi-dimensional array of HTML5 <meta> element attributes.
	 * @var array $link_attributes          Multi-dimensional array of HTML5 <link> element attributes.
	 * @var array $css_attributes           Multi-dimensional array of HTML5 <link> CSS3 element attributes.
	 * @var array $script_attributes        Multi-dimensional array of HTML5 external <script> element attributes.
	 *
	 * @var array  $internal_script_content Internal scripts contained within a HTML <head><script>$script</script> element.
	 * @var string $css_content             Internal CSS3 contained within the HTML5 <head><style>$css</style> element.
	 */
	public $html_attributes;
	public $base_attributes;
	public $meta_attributes;
	public $link_attributes;
	public $script_attributes;
	
	public $internal_script_content;
	public $css_content;
	
	/**
	 * Class constructor.
	 */
	public function __construct( $args ) {
		$this->config = $args->Config;
		
		$this->html_attributes = array ( 'lang' => $this->config->language, 'xml:lang' => $this->config->language );
		$this->base_attributes = array ( 'href' => $this->config->primary_domain );
		$this->meta_attributes = array (
			array ( 'charset' => $this->config->charset ),
			array ( 'name' => 'msapplication-config', 'content' => '/images/favicons/browserconfig.xml' ),
			array ( 'name' => 'theme-color', 'content' => '#000000' ),
			array ( 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0' )
		);
		$this->link_attributes = array (
			array ( 'rel' => 'apple-touch-icon', 'href' => '/images/favicons/apple-touch-icon.png', 'sizes' => '180x180' ),
			array ( 'rel' => 'icon', 'type' => 'image/png', 'href' => '/images/favicons/favicon-32x32.png', 'sizes' => '32x32' ),
			array ( 'rel' => 'icon', 'type' => 'image/png', 'href' => '/images/favicons/favicon-16x16.png', 'sizes' => '16x16' ),
			array ( 'rel' => 'manifest', 'href' => '/images/favicons/manifest.json' ),
			array ( 'rel' => 'mask-icon', 'href' => '/images/favicons/safari-pinned-tab.svg', 'color' => '#000000' ),
			array ( 'rel' => 'shortcut icon', 'href' => '/images/favicons/favicon.ico' )
		);
		$this->css_attributes = array (
			array ( 'rel' => 'stylesheet', 'href' => '\css\fonts.css' ),
			array ( 'rel' => 'stylesheet', 'href' => '\css\icons.css' )
		);
		$this->script_attributes = array (
			array ( 'type' => 'text/javascript', 'src' => '/js/jquery.js' ),
			array ( 'type' => 'text/javascript', 'src' => '/js/page.js' )
		);
	}
	
}
