<?php

class View {
	
	/**
	 * HTML 5 <head> tag properties.
	 *
	 * @var array $meta_tags, $link_tags, $script_tags {
	 *   @type array key {
	 *     @type html5 attribute Attribute value.
	 *   }
	 * }
	 * @var array $scripts {
	 *   @type key Script content.
	 * }
	 */
	public $meta_tags;
	public $link_tags;
	public $script_tags;
	public $scripts;
	
	
	/**
	 * @var object $config Configuration class.
	 */
	private $config;
	
	
	// @inject Config
	public function __construct( $config ) {
		$this->config = $config;
		
		$this->set_head_tags();
	}
	
	public function set_head_tags() {
		$this->meta_tags[] = array ( 'charset' => $this->config->charset );
		$this->meta_tags[] = array ( 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0' );
		
		/**
		 * @todo: Define these tags somewhere...
		$this->meta_tags[] = array ( 'name' => 'description', 'content' => '' );
		$this->meta_tags[] = array ( 'name' => 'keywords', 'content' => '' );
		$this->meta_tags[] = array ( 'name' => 'author', 'content' => '' );
		 */
		 
		 
		$this->script_tags[] = array ( 'type' => 'text/javascript', 'href' => 'js/jquery.js?ver=3.2.0' );
	}
	
}