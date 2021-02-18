<?php
include_once('fields.php');
include_once('content.php');
include_once(DOCROOT.'/php/keygen.php');
include_once(DOCROOT.'/_bin/wp-load.php');
include_once(DOCROOT.'/_data/directives.php');

function main($json = true) {

	$output = array();
	
	$teasermode = get_field('teaser-active', 'option');

	$nav = get_field('site_navigation', 'option');
	
	$output['nav'] = array();

	$indices = array();

	if(!$teasermode && $nav != false && sizeof($nav) > 0) {
		foreach($nav as $n) {
			if(get_option('page_on_front') == $n['target']->ID) {
				array_push($output['nav'], array(
					'name' => 'Home',
					'path' => '',
					'visible' => $n['visible'],
					'directive' => ng_template(get_option('page_on_front'))
				));
				array_push($indices, $n['target']->ID);
			}
			else{
				array_push($output['nav'], array(
					'name' => $n['target']->post_title,
					'path' => $n['target']->post_name,
					'visible' => $n['visible'],
					'directive' => ng_template($n['target']->ID)
				));
				array_push($indices, $n['target']->ID);
			}
		}
	}
	else{
		if($teasermode) {
			array_push($output['nav'], array(
				'name' => 'Home',
				'path' => '',
				'visible' => $n['visible'],
				'directive' => 'v-teaser'
			));
		}
	}

	if(sizeof($output['nav']) < 1) {
		array_push($output['nav'], array(
			'name' => 'Home',
			'path' => '',
			'visible' => false,
			'directive' => ng_template(get_option('page_on_front'))
		));
		array_push($indices, get_option('page_on_front'));
	}
	
	array_push($output['nav'], array(
		'name' => 'Lost',
		'path' => 'lost',
		'visible' => false,
		'directive' => ng_template(get_option('page_for_lost'))
	));
	array_push($indices, get_option('page_for_lost'));

	$contentinfo = build_content($indices);

	$output['preload'] = $contentinfo['preload'];
	$output['on_init'] = $contentinfo['init']; // pre-preloaded images
	$output['contents'] = $contentinfo['content'];

	if($json) {
		return json_encode($output);
	}
	else{
		return $output;
	}
}
?>