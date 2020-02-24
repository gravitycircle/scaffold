<?php
function get_lasso() {
	return array(
		'ProjectID' => '',
		'ClientID' => '',
		'LassoUID' => ''
	);
}

function get_processed_vectors($vector_field) {
	$s = explode('-', $vector_field);
	return wp_get_attachment_url(isset($_GET['svg']) && $_GET['svg'] == 'true' ? $s[1] : $s[0]);
}

function get_configs() {
	$x = get_post(get_option('page_on_front'));

	//get form data
	$recap = array(
		'key' => get_field('recap-key', $id->ID),
		'fail-message' => get_field('recap-fail', $id->ID)
	);

	return array(
		'smtp' => array(
			'user' => 'no.reply@thehillcrest.ca',
			'pass' => 'H=pQZ*TC#!?o',
			'host' => 'mail.thehillcrest.ca',
			'port' => 465
		),
		'sitename' => get_bloginfo('name'),
		'base' => BASE
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
		'preload' => array_values(array_unique($preload)),
		'init' => array_values(array_unique($initd))
	);
}



?>