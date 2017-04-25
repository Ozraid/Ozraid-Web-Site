<?php
/**
 * HTML5 document <head> output template.
 */

// <!DOCTYPE>, <html>, and <head> tags.
$output 	 = $html->doctype;
$output 	.= $html->html;
$output 	.= $html->head;

// <meta> tags.
$output 	.= $html->head_tags( 'meta', $view->meta_tags );

// <title> tag.
$output 	.= $html->tab_2 .'<title>' .$config->site_title .' | ' .$view->page_title .'</title>' .$html->crlf;

// <base> tag.
$output 	.= '<base href="' .$config->domain .'" />' .$html->crlf;

// <link> tags.
if ( isset ( $view->link_tags ) ) {
	$output .= $html->head_tags( 'link', $view->link_tags );
}

// <script> tags.
if ( isset ( $view->script_tags ) ) {
	$output .= $html->head_tags( 'script', $view->script_tags );
}

// Inline <script> tags.
if ( isset ( $view->scripts ) ) {
	$output .= $html->inline_content( 'script', $view->scripts );
}

// Inline CSS3 <style> tags.
if ( isset ( $view->css ) ) {
	$output .= $html->inline_content( 'style', $view->css );
}

// </head> tag.
$output 	.= $html->endhead;