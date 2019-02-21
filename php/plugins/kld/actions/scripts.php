<?php
function script_queue($hook) {
  // echo 'HOOK: '.$hook;
	
	//REGISTRATION
	wp_register_script( 'transit-js', fm_this_plugin().'js/transit.js', array('jquery'), '0.9.12');
	wp_register_script( 'fm-library', fm_this_plugin().'js/library.js', array('transit-js'), '0.1');
	wp_register_style( 'admin-styles', fm_this_plugin().'css/style.css', false, '1.0.0' );

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
		if($pg == 'page' || $pg == 'post') {
			wp_register_script('page-ops', fm_this_plugin().'js/post-page-options.js', array('fm-library'), '0.1');
			wp_enqueue_script('page-ops');
		}
	}
	else if($hook == 'options-general.php') {
		wp_enqueue_script('gen-ops');
	}
	else if($hook == 'options-reading.php') {
		wp_enqueue_script('red-ops');
	}
	else if($hook == 'pages_page_form-submissions') {
		wp_enqueue_script('sub-ops');
	}
	else if ($hook == 'toplevel_page_cpt') {
		wp_enqueue_script('fm-library');
	}
	
	wp_enqueue_media();
	wp_enqueue_style('admin-styles');
}
?>