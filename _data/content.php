<?php
function get_configs($internal = false) {	
	if(!$internal) {
		$x = get_post(get_option('page_on_front'));

		return array(
			'teasermode' => get_field('teaser-active', 'option'),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'sitename' => get_bloginfo('name'),
			'base' => BASE
		);
	}
	else{
		return array(
			'temp_page' => array(
				'seo-title'
			)
		);
	}
}

function get_processed_vectors($vector_field, $type = false) {
	$s = explode('-', $vector_field);
	if(!$type){
		return wp_get_attachment_url(isset($_GET['svg']) && $_GET['svg'] == 'true' ? $s[1] : $s[0]);
	}
	else{
		if($type == 'raster') {
			return wp_get_attachment_url($s[0]);
		}
		else if($type == 'vector') {
			return wp_get_attachment_url($s[1]);
		}
		else{
			return wp_get_attachment_url(isset($_GET['svg']) && $_GET['svg'] == 'true' ? $s[1] : $s[0]);
		}
	}
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

	if(get_field('teaser-active', 'option')) {
		//add teaser
		$cfg = get_configs(true);

		$x = new REST_optionset($cfg['temp_page'], true);

		$output['home'] = $x->toObject();

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