<?php
namespace Ozraid\Includes\Templates;

/**
 * HTML <head> template.
 *
 * Type:         Template
 * Description:  Website configuration data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

// <!DOCTYPE>, <html>, and <head> start tags.
$output 	 = $html->start_tag( '!DOCTYPE html' );
$output 	.= $html->start_tag( 'html', $this->head->html_attributes );
$output 	.= $html->start_tag( 'head', NULL, $html->tab );

// <base> element.
$output 	.= $html->block( 'base', $this->head->base_attributes, NULL, TRUE, $html->tab_2 );

// <meta> elements.
if ( isset ( $this->head->meta_attributes ) ) {
	$output .= $html->iterate_attributes( 'block', 'meta', $this->head->meta_attributes, NULL, TRUE, $html->tab_2 );
}

// <title> element.
if ( ! isset ( $this->title ) ) {
	$this->title = $this->config->site_byline;
}
$output 	.= $html->block( 'title', NULL, $this->config->site_title .' | ' .$this->title, TRUE, $html->tab_2 );

// <link> elements.
if ( isset ( $this->head->link_attributes ) ) {
	$output .= $html->iterate_attributes( 'block', 'link', $this->head->link_attributes, NULL, NULL, $html->tab_2 );
}
if ( isset ( $this->head->css_attributes ) ) {
	$output .= $html->iterate_attributes( 'block', 'link', $this->head->link_attributes, NULL, TRUE, $html->tab_2 );
}

// External <script> elements.
if ( isset ( $this->head->script_attributes ) ) {
	$output .= $html->iterate_attributes( 'block', 'script', $this->head->script_attributes, '', TRUE, $html->tab_2 );
}

// Internal <script> elements.
if ( isset ( $this->head->script_content ) ) {
	$output .= $html->iterate_content( 'container', 'script', NULL, $this->head->script_content, TRUE, $html->tab_2 );
}


// Internal CSS3 <style> element.
if ( isset ( $this->head->css_content ) ) {
	$output .= $html->container( 'style', NULL, $this->head->css_content, 'internal css3', $html->tab_2 );
}

// </head> end tag.
$output 	.= $html->end_tag( 'head', TRUE, $html->tab );
