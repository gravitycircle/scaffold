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
			'user' => 'no.reply@auberryliving.com',
			'pass' => 'H=pQZ*TC#!?o',
			'host' => 'mail.auberryliving.com',
			'port' => 465
		),
		'sitename' => get_bloginfo('name'),
		'base' => BASE,
		'logo' => get_field('main-logo', 'option'),
		'backdrop' => get_field('loading-bg', 'option'),
		'complete' => get_field('loading-complete', 'option'),
		'ornament' => isset($_GET['svg']) && $_GET['svg'] == 'true' ? BASE.'img/svg/ornament.svg' : BASE.'img/png/ornament.png'
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