<?php
include_once('config.php');
include_once(DOCROOT.'/_data/collate.php');
include_once('php/server.php');

//args for building:
/*
$scripts = array(
	$libraries: array, list of all scripts. refer to lib folder
	$exts: array, list of all scripts. refer to ext folder
	$scripts: array, list of all scripts. refer to scr folder
	$externals: array, list of all scripts. refer to external links
	$inlines: array, list of all scripts. refer to ext folder
);

$styles: array - style urls
*/

_build(array(
	'libraries' => array(
		'modernizr.js',
		'jquery.js',
		'angular.js',
		'angular-maps.js',
		'transit.js'
	),
	'extensions' => array(
		'html.js',
		'comms.js'
	),
	'scripts' => array(
		array(
			'predeployed' => true,
			'src' => 'views.js'
		),
		array(
			'predeployed' => true,
			'src' => 'modes.js'
		),
		array(
			'predeployed' => true,
			'src' => 'main.js'
		),
		array(
			'predeployed' => false,
			'src' => 'seo.js'
		)
	),
	'externals' => array(
		// array(
		// 	'predeployed' => false,
		// 	'src' => 'https://app.lassocrm.com/analytics.js'
		// )
	),
	'inlines' => array(
		// array(
		// 	'predeployed' => false,
		// 	'src' => '--[JS SCRIPT]--'
		// )
	)
), array(
	//styles
));
?>