<?php
namespace Ozraid\Includes\Templates;

// @TODO: FOR OTHER TEMPLATES AND TESTING PURPOSES ONLY.
$output 	.= $html->start_tag( 'body', NULL, $html->tab );
$output 	.= $html->start_tag( 'div', array ( 'id' => 'site-container' ), $html->tab_2 );
$output 	.= $html->start_tag( 'div', array ( 'id' => 'site-header' ), $html->tab_3 );

$pages = $this->db->index_object( 'base_paths' );
$subdomains = $this->db->key_to_value( 'subdomains' );
foreach ( $pages as $page ) {
	if ( in_array ( $page->name, $subdomains ) ) {
		$url = 'http://' .$page->name .'.webeatthegame.com';
	}
	else if ( $page->name == 'home' ) {
		$url = $this->config->primary_domain;
	}
	else {
		$url = $this->config->primary_domain .'/' .$page->name .'/';
	}
	$menu_attributes = array (
		'href'   => $url,
		'target' => '_self',
		'style'  => 'color: #ffffff; text-decoration: none;'
	);
	$menu .= $html->inline( 'a', $menu_attributes, $page->label ) .' | ';
}
$output 	.= $html->block( 'p', array ( 'style' => 'position: absolute; bottom: -20px; right: 0; width: calc(100% - 20px); padding: 10px; font-weight: bold; background-color: #000000; text-align: right;' ), rtrim ( $menu, ' | ' ), 'primary-menu', $html->tab_4 );
$output 	.= $html->end_tag( 'div', '#site-header', $html->tab_3 );
$tab = $html->tab_2;
