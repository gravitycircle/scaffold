<?php
function script_queue($hook) {
  // echo 'HOOK: '.$hook;
	$deps = array();
	$cdeps = array();
	//REGISTRATION
	wp_register_script( 'transit-js', fm_this_plugin().'js/transit.js', array('jquery'), '0.9.12');
	wp_register_script( 'fm-library', fm_this_plugin().'js/library.js', array('transit-js'), '0.1');
	

	wp_register_script('gen-ops', fm_this_plugin().'js/general-options.js', array('fm-library'), '0.1');

	wp_register_script('red-ops', fm_this_plugin().'js/reading-options.js', array('fm-library', 'jquery-ui-core'), '0.1');

	wp_register_script('sub-ops', fm_this_plugin().'js/submissions.js', array('fm-library'), '0.1');
	//ACTIVATION
	if($hook == 'post-new.php' || $hook == 'post.php'){
		global $post;
		if($hook == 'post-new.php'){
			if(!isset($_GET['post_type'])){
				$pg = 'post';
			}
			else {
				$pg = $_GET['post_type'];
			}
		}
		else{
			$pg = $post->post_type;
		}
	}
	else if($hook == 'options-general.php') {
		array_push($deps, 'gen-ops');
	}
	else if($hook == 'options-reading.php') {
		array_push($deps, 'red-ops');
	}
	else if($hook == 'pages_page_form-submissions') {
		array_push($deps, 'sub-ops');
	}
	else if ($hook == 'toplevel_page_cpt') {
		array_push($deps, 'fm-library');
	}

	//fetch from local files - JS
	$extfetch = get_field('ext-js', 'option');

	$dbm = kld_debug_mode();
	if(!is_array($extfetch)) {
		$extfetch = array();
	}

	if(sizeof($extfetch) > 0) {
		foreach($extfetch as $ext) {
			$availability = $ext['js-resource']['availability'];

			if(!is_array($availability)) {
				$availability = array();
			}

			if(in_array('admin', $availability) || in_array($hook, $availability)) {
				$source = $ext['js-resource']['is-file'] ? wp_get_attachment_url($ext['js-resource']['js-script']['file']) : $ext['js-resource']['js-script']['url'];

				if(in_array('production', $availability)){ // predeployed 
					if(!$dbm) {
						wp_register_script('data-script-'.sizeof($deps), $source);
						array_push($deps, 'data-script-'.sizeof($deps));
					}
				}
				else{
					wp_register_script('data-script-'.sizeof($deps), $source);
					array_push($deps, 'data-script-'.sizeof($deps));
				}
			}
		}
	}

	$cssfetch = get_field('ext-css', 'option');
	if(!is_array($cssfetch)) {
		$cssfetch = array();
	}
	
	if(sizeof($cssfetch) > 0) {
		foreach($cssfetch as $c) {
			if(in_array('admin', $c['availability']) || in_array($hook, $c['availability'])) {
				if(!$c['is-file']) {
					wp_register_style('data-style-'.sizeof($cdeps), $c['css-src']['url']);
					array_push($cdeps, 'data-style-'.sizeof($cdeps));
				}
				else {
					wp_register_style('data-style-'.sizeof($cdeps), wp_get_attachment_url($c['css-src']['file']));
					array_push($cdeps, 'data-style-'.sizeof($cdeps));
				}
			}
		}
	}

	//register manifests
	wp_register_script('manifest', fm_this_plugin().'js/manifest.js', $deps, '1.00');
	wp_register_style( 'admin-styles', fm_this_plugin().'css/style.css', $cdeps, '1.0.0' );
	
	wp_enqueue_media();
	wp_enqueue_script('manifest');
	wp_enqueue_style('admin-styles');
}
?>