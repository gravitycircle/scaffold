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
		'views.js',
		'modes.js',
		'main.js'
	),
	'externals' => array(
	),
	'inlines' => array(
	)
), array(
	//styles
));
?>	