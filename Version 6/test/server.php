<?php
foreach ( $_SERVER as $name => $value ) {
	$output .= $name .' = ' .$value .'<br />';
}
rtrim ( $output, '<br />' );
echo $output;