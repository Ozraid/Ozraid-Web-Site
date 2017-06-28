<?php
namespace Ozraid\Includes\Templates;

/**
 * HTML page <footer> template.
 *
 * Type:         Template
 * Description:  Website configuration data.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 */

$output 	.= $html->end_tag( 'div', '#site-container', $html->tab_2 );

// </body> and </html> end tags.
$output 	.= $html->end_tag( 'body', TRUE, $html->tab );
$output 	.= $html->end_tag( 'html', TRUE, NULL, NULL );
