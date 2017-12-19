<?php
include_once('collect.php');
include_once('content.php');
include_once(DOCROOT.'/php/keygen.php');
function main($json = true) {

	$output = array();
	
	$output['nav'] = array(
		array(
			'name' => 'Home',
			'path' => ''
		)
	);

	$output['preload'] = scan_imgs();
	$output['on_init'] = array(

	); // pre-preloaded images
	$output['contents'] = build_content('Angular Based Scaffolding & Bootstrap');
	if($json) {
		return json_encode($output);
	}
	else{
		return $output;
	}
}
?>