<?php

// @class singleton
class Config {

	private $config;
	
	private function __construct() {
		$this->set_configuration();
	}
	
	public function __set( $name, $value ) {
		$this->config->{$name} = $value;
	}
	
	public function __get( $name ) {
		if ( isset( $this->config->{$name} ) ) {
			return $this->config->{$name};
		}
	}
	
	private function set_configuration() {
		// Website configuration values.
		$this->site_title   = 'Ozraid';
		$this->site_byline  = 'A Lord of the Rings Online Raid Group &amp; Resource';
		$this->charset      = 'UTF-8';
		
		// Organisation URIs.
		$this->email        = 'ozraid@ozraid.org';
		$this->lotro_url    = 'http://www.lotro.com/';
		$this->twitter_url  = 'https://twitter.com/Ozraid';
		$this->youtube_url  = 'https://www.youtube.com/channel/UCF0GgKPJQRsvCBG40nX86Aw';
		
		// Website URIs.
		$this->domain       = 'http://' .$_SERVER['SERVER_NAME'] .'/';
		$this->url          = 'http://' .$_SERVER['SERVER_NAME'] .$_SERVER['REQUEST_URI'];
		$this->path         = trim ( $_SERVER['REQUEST_URI'], '/' );
		
		// Local configuration values.
		$this->host         = 'localhost';
		$this->base_dir     = $_SERVER['DOCUMENT_ROOT'] .'/';
		
		// User data.
		$this->ip_address   = $_SERVER['REMOTE_ADDR'];
		$this->prev_url     = $_SERVER['HTTP_REFERER'];
	}
	
	public static function get_instance() {
		static $config = NULL;
		if ( NULL === $config ) {
			$config = new Config();
		}
		return $config;
	}
}