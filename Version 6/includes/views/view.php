<?php
namespace Ozraid\Includes\Views;

/**
 * View class.
 *
 * Type:         Class
 * Dependencies: Config, Config_Head, Database_Interface, Html
 * Description:  MVC View class.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class View {
	
	// Use the Cases trait.
	use \Ozraid\Includes\Traits\Cases;
	
	/**
	 * PRIVATE VARIABLES: PHP classes.
	 *
	 * @var object $config        Configuration class.
	 * @var object $db            Database API class.
	 * @var object $head          HTML5 <head> elements configuration class.
	 * @var object $html          HTML5 class.
	 *
	 * @var string $title         HTML5 page title.
	 */
	private $config;
	private $db;
	private $head;
	private $html;
	
	private $title;
	
	public function __construct( $args ) {
		$this->config = $args->Config;
		$this->head   = $args->Config_Head;
		$this->html   = $args->Html;
		$this->db     = $args->Database_Interface;
		
		$css  = "html, body::after { width: 100%; height: 100%; }" .$this->html->crlf;
		$css .= "body { width: 100%; margin: 0 auto; text-align: center; }" .$this->html->crlf;
		$css .= "body::after { content: ''; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #ffffff; background-image: url('images/website/overlay-white.png'), url('images/background-0001.jpg'); background-position: left top, center center; background-size: auto, cover; background-repeat: repeat, no-repeat; background-attachment: fixed, fixed; z-index: -1; }" .$this->html->crlf;
		$css .= "#site-container { width: calc(100% - 80px); margin: 20px auto; padding: 20px; background-color: #ffffff; text-align: left; }" .$this->html->crlf;
		$css .= "#site-header { position: relative; width: 100%; height: 360px; margin: 0 0 20px 0; color: #ffffff; background-image: url('images/background-0001.jpg'); background-position: center center; background-size: cover; background-repeat: no-repeat; z-index: 1; }" .$this->html->crlf;
		$css .= "h1 { margin-top: 0; padding-top: 0; }";
		$this->set_css( $css );
	}
	
	public function set_template( $file ) {
		$this->html_templates[] = $file;
	}
	
	public function set_title( $title ) {
		$this->title = $title;
	}
	
	public function set_meta( array $attributes ) {
		$this->head->meta_attributes[] = $attributes;
	}
	
	public function set_link( array $attributes ) {
		$this->head->link_attributes[] = $attributes;
	}
	
	public function set_stylesheet( $href ) {
		$this->head->css_attributes[] = array ( 'rel' => 'stylesheet', 'href' => $href );
	}
	
	public function set_script( $src, $type = 'text/javascript' ) {
		$this->head->script_attributes[] = array ( 'type' => $type, 'src' => $src );
	}
	public function set_external_script( $src, $type = 'text/javascript' ) {
		$this->head->set_script( $src, $type );
	}
	
	public function set_internal_script( $content ) {
		$this->head->internal_script_content[] = $content;
	}
	
	public function set_css( $content ) {
		if ( isset ( $this->head->css_content ) ) {
			$crlf = $this->html->crlf;
		}
		$this->head->css_content .= $crlf .$content;
	}
	
	public function get_html_output( $content = NULL ) {
		$html = $this->html;
		require_once constant ( 'AB_PATH' ) .'templates/head.php';
		require_once constant ( 'AB_PATH' ) .'templates/header.php';
		$output .= $tab .$html->tab .$html->content( $content, $tab ) .$html->crlf;
		require_once constant ( 'AB_PATH' ) .'templates/footer.php';
		return $output;
	}
	
}
