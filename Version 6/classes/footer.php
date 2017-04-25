<?php
/**
 * HTML5 document page <footer> output template.
 */
 
$output 	.= $html->tab_3 .'<footer id="site-footer">' .$this->crlf;

$output 	.= $html->tab_3 .$html->endtag( 'footer', 'site-footer' );
$output 	.= $html->tab_2 .$html->endtag( 'div', 'site-container' );

// </body> and </html> tags.
$output 	.= $html->endbody;
$output 	.= $html->endhtml;