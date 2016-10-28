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

	$output['site_name'] = 'Angular: Site Scaffolding & Bootstrap';

	$output['preload'] = scan_imgs();

	$output['contents'] = build_content();
	if($json) {
		return json_encode($output);
	}
	else{
		$output['meta'] = array(
			'lost' => array(
				'title' => $output['site_name'].' - Page Not Found',
				'description' => '---',
				'og' => array(
					'title' => $output['site_name'].' - Page Not Found',
					'description' => '---',
					'site_name' => 'Angular: Site Scaffolding & Bootstrap',
					'url' => BASE,
					'image' => BASE.'img/non-render/og-logo.jpg' 
				),
				'tw' => array(
					'card' => 'summary',
					'title' => $output['site_name'].' - Page Not Found',
					'description' => '---',
					'image' => BASE.'img/non-render/og-logo.jpg'
				)
			)
		);
		return $output;
	}
}
?>