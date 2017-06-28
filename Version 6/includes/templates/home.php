<?php
namespace Ozraid\Includes\Templates;

/**
 * HTML Home page template.
 *
 * Type:         Template
 * Description:  Website configuration data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

$output 	.= $html->start_tag( 'body', NULL, $html->tab );
$output 	.= $html->start_tag( 'div', array ( 'id' => 'site_container' ), $html->tab_2 );
$output 	.= $html->start_tag( 'main', array ( 'id' => 'site-content' ), $html->tab_3 );
$output 	.= $html->block( 'div', array ( 'id' => 'site-title' ), $this->config->site_title, '#site-title', $html->tab_4 );
$output 	.= $html->block( 'div', array ( 'id' => 'site-byline' ), $this->config->site_byline, '#site-byline', $html->tab_4 );
$output 	.= $html->start_tag( 'nav', array ( 'id' => 'primary-menu' ), $html->tab_4 );
$output 	.= $html->start_tag( 'ul', NULL, $html->tab_5 );

foreach ( $ as $page ) {
	$menu_li_attributes = array (
		'class' => 'background-hover-' .$page->colour
	);
	$menu_link_attributes = array (
		'href'   => $this->config->primary_domain .$page->path,
		'title'  => $this->config->site_title .' | ' .$page->label,
		'target' => '_self'
	);
	$menu_image_attributes = array (
		'src' => '/images/backgrounds/400x225/' .$page->image .'-400x225.' .$page->image_extension,
		'alt' => $this->config->site_title .' | ' .$page->label
	);
	
}
$output 	.= $html->end_tag( 'ul', NULL, $html->tab_5 );
$output 	.= $html->end_tag( 'nav', '#primary-menu', $html->tab_4 );
$output 	.= $html->end_tag( 'main', TRUE, $html->tab_3 );
$output 	.= $html->end_tag( 'div', '#site_container', $html->tab_2 );
