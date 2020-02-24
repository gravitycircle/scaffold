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

$apis = json_decode(stripslashes(str_replace('&quot;', '"', get_option('api-keys'))), true);
$gapi = '';
foreach($apis as $api) {
	if($api['api'] == 'Google Cloud Platform'){
		$gapi = $api['key'];
		break;
	}
}

$js = array(
	'libraries' => array(
		'polyfills.js',
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
		// 	'predeployed' => true,
		// 	'src' => 'https://maps.googleapis.com/maps/api/js?key='.$gapi,
		// 	'attributes' => array(
		// 	)
		// )
	),
	'inlines' => array()
);

$extfetch = get_field('ext-js', 'option');

if(!is_array($extfetch)) {
	$extfetch = array();
}

foreach($extfetch as $ext) {
	$availability = $ext['js-resource']['availability'];

	if(!is_array($availability)) {
		$availability = array();
	}

	if(in_array('front', $availability)) {
		$exbuild = array(
			'predeployed' => !in_array('production', $availability),
			'src' => $ext['js-resource']['is-file'] ? wp_get_attachment_url($ext['js-resource']['js-script']['file']) : $ext['js-resource']['js-script']['url'],
			'attributes' => array()
		);

		if(is_array($ext['js-attributes']) && sizeof($ext['js-attributes']) > 0) {
			foreach($ext['js-attributes'] as $exat) {
				$attv = $exat['value'];
				if($attv == '') {
					$attv = null;
				}
				$exbuild['attributes'][$exat['key']] = $attv;
			}
		}

		array_push($js['externals'], $exbuild);
	}
}


$inlfetch = get_field('inline-js', 'option');

if(!is_array($inlfetch)) {
	$inlfetch = array();
}

foreach($inlfetch as $inl) {
	$inbuild = array(
		'predeployed' => $inl['predeployed']  == '' ? false : true,
		'src' => $inl['src']
	);

	array_push($js['inlines'], $inbuild);
}

$cssfetch = get_field('ext-css', 'option');
if(!is_array($cssfetch)) {
	$cssfetch = array();
}
$css = array();

foreach($cssfetch as $c) {
	if(in_array('front', $c['availability'])) {
		if(!$c['is-file']) {
			array_push($css, $c['css-src']['url']);
		}
		else {
			array_push($css, wp_get_attachment_url($c['css-src']['file']));
		}
	}
}

_build($js, $css);
?>