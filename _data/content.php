<?php
function get_lasso() {
	return array(
		'ProjectID' => '',
		'ClientID' => '',
		'LassoUID' => ''
	);
}

function get_configs() {
	return array(
		'smtp' => array(
			'user' => false,
			'pass' => false,
			'host' => false,
			'port' => false
		),
		'sitename' => get_bloginfo('name'),
		'api' => array(

		)
	);
}

function build_content($data){
	$output = array(
		'config' => get_configs()
	);

	$preload = array();
	$initd = array();

	foreach($data as $d) {
		$x = new REST_output($d, true);
		$output[$x->__toString()] = $x->toObject();

		foreach($x->toPreload() as $pl) {
			array_push($preload, $pl);
		}

		foreach($x->toInit() as $pl) {
			array_push($initd, $pl);
		}

		
	}

	return array(
		'content' => $output,
		'preload' => array_unique($preload),
		'init' => array_unique($initd)
	);
}



?>