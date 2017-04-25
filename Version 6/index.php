<?php
$path = trim ( $_SERVER['REQUEST_URI'], '/' );
$segment = explode ( '/', $path );

$pages = array ( 'about', 'login', 'logout', 'member', 'admin', 'articles', 'resources', 'contact', 'apply' );

if ( ! in_array ( $segment[0], $pages ) ) {
	$basename = basename ( $path );
	echo 'Cannot find error<br />Basename = ' .$basename .'<br />HTTP Response Code = ' .http_response_code();
}
else {
	echo $segment[0];
}