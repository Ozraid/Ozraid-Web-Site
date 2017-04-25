<?php
$config = new stdClass();
$config->charset = 'UTF-8';

$meta_tags = array {
	array (
		'charset' => $config->charset
	),
	array (
		'name'    => 'msapplication-config',
		'content' => 'images/favicons/browserconfig.xml'
	),
	array (
		'name'    => 'theme-color',
		'content' => '#000000'
	),
	array (
		'name' => 'viewport',
		'content' => 'width => device-width, initial-scale => 1.0'
	)
};

$link_tags = array (
	array (
		'rel' => 'apple-touch-icon',
		'href' => 'images/favicons/apple-touch-icon.png',
		'sizes' => '180x180'
	),
	array (
		'rel'   => 'icon',
		'type'  => 'image/png',
		'href'  => 'images/favicons/favicon-32x32.png',
		'sizes' => '32x32'
	),
	array (
		'rel'   => 'icon',
		'type'  => 'image/png',
		'href'  => 'images/favicons/favicon-16x16.png',
		'sizes' => '16x16'
	),
	array (
		'rel'  => 'manifest',
		'href' => 'images/favicons/manifest.json'
	),
	array (
		'rel'   => 'mask-icon',
		'href'  => 'images/favicons/safari-pinned-tab.svg',
		'color' => '#000000'
	),
	array (
		'rel'  => 'shortcut icon',
		'href' => 'images/favicons/favicon.ico'
	)
);

$script_tags = array (
	array (
		'type' => 'text/javascript',
		'src'  => 'js/jquery.js'
	)
);
