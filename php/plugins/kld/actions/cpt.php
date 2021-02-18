<?php
acf_add_options_page(array(
	'page_title' => 'Data & Defaults',
	'menu_title' => '',
	'menu_slug' => 'cpt',
	'capability' => 'setup_network',
	'position' => 2,
	'parent_slug' => '',
	'icon_url' => 'dashicons-welcome-widgets-menus'
));

acf_add_options_page(array(
	'page_title' => 'Temporary Page',
	'menu_title' => 'Temporary Page',
	'menu_slug' => 'temp-page',
	'capability' => 'edit_posts',
	'position' => 4,
	'parent_slug' => 'edit.php?post_type=page',
	'icon_url' => 'dashicons-admin-plugins',
	'update_button' => 'Save Changes',
	'update_message' => 'Changes to this page are successfully saved. Make sure to set the "State" to "Active" and save again if you wish to use this feature.'
));

function acf_js_includes() {
	if(class_exists('cstm_acf_plugin_kld_post_type') && class_exists('dshicn_acf_plugin_kld_dashicon')) {
		wp_enqueue_script( 'acf-js', fm_this_plugin().'js/acf.js', array(), '1.0.0', true );	
	}
	
	if(class_exists('vctr_acf_plugin_vector_image')) {
		wp_enqueue_script( 'vctr-js', fm_this_plugin().'js/vector.js', array(), '1.0.0', true );	
	}
}

function kld_greek_alpha($ind) {
	$alp = array('alpha', 'beta', 'gamma', 'delta', 'epsilon', 'zeta', 'eta', 'theta', 'iota', 'kappa', 'lambda', 'mu', 'nu', 'xi', 'omicron', 'pi', 'rho', 'sigma', 'tau', 'upsilon', 'phi', 'chi', 'psi', 'omega', 'varepsilon', 'varkappa', 'varphi', 'varpi', 'vartheta', 'varsigma');
	return !isset($alp[$ind]) ? $ind : $alp[$ind];
}

function kld_analyzecap($task, $caparray) {
	$analyze = array();
	foreach($caparray as $c) {
		$analyze[$c['capability']] = $c['role'];
	}

	if(isset($analyze[$task])) {
		return $analyze[$task];
	}
	else{
		if(isset($analyze['all'])) {
			return $analyze['all'];
		}
		else{
			return 'setup_network';
		}
	}
}

function create_post_type()
{	
	
	if(class_exists('cstm_acf_plugin_kld_post_type') && class_exists('dshicn_acf_plugin_kld_dashicon')) {
		$cpt = get_field('post-type', 'option');

		if(!(!$cpt) && sizeof($cpt) > 0) {
			$names_taken = [];
			foreach($cpt as $it => $pt) {
				if(!in_array($pt['slug'], $names_taken)) {
					array_push($names_taken, $pt['slug']);
					$slug = $pt['slug'];
				}
				else{
					$slug = $pt['slug'][kld_greek_alpha($it)];
				}

				$rw = $pt['rewrite'];
				if($pt['rewrite'] == '') {
					$rw = $slug;
				}

				register_post_type($slug, array(	
					'label' => $pt['label']['plural'],
					'description' => 'Past, Present, Future '.$pt['label']['plural'],
					'public' => $pt['public'],
					'menu_icon' => 'dashicons-'.$pt['dashicon'],
					'show_ui' => true,
					'show_in_menu' => true,
					'capability_type' => 'post',
					'capabilities' => array(
						'edit_post' => kld_analyzecap('edit_post', $pt['capability']),
						'read_post' => kld_analyzecap('read_post', $pt['capability']),
						'delete_post' => kld_analyzecap('delete_post', $pt['capability']),
						'delete_posts' => kld_analyzecap('delete_posts', $pt['capability']),
						'edit_posts' => kld_analyzecap('edit_posts', $pt['capability']),
						'edit_others_posts'	=> kld_analyzecap('edit_others_posts', $pt['capability']),
						'publish_posts' => kld_analyzecap('publish_posts', $pt['capability']),
						'read_private_posts' => kld_analyzecap('read_private_posts', $pt['capability'])
					),
					'hierarchical' => $pt['hierarchical'],
					'rewrite' => array('slug' => $rw),
					'query_var' => true,
					'menu_position' => $pt['position'],
					'supports' => array('title'),
					'labels' => array(
						'name' => $pt['label']['plural'], 
						'singular_name' => $pt['label']['singular'],
						'menu_name' => $pt['label']['menu'] == '' ? $pt['label']['plural'] : $pt['label']['menu'],
						'add_new' => 'Create '.$pt['label']['singular'],
						'add_new_item' => 'Add New '.$pt['label']['singular'],
						'edit' => 'Edit',
						'edit_item' => 'Edit '.$pt['label']['singular'],
						'new_item' => 'New '.$pt['label']['singular'],
						'view' => 'View '.$pt['label']['plural'],
						'view_item' => 'View '.$pt['label']['singular'],
						'search_items' => 'Search '.$pt['label']['plural'],
						'not_found' => 'No '.$pt['label']['plural'].' Found',
						'not_found_in_trash' => 'No '.$pt['label']['plural'].' Found in Trash',
						'parent' => 'Parent '.$pt['label']['singular']
					)
				));
			}
		}

		$ctx = get_field('taxonomy', 'option');

		if(!(!$ctx) && sizeof($ctx) > 0) {
			foreach($ctx as $ci => $ct) {
				$targets = array();
				foreach($ct['target'] as $t) {
					array_push($targets, $t['type']);
				}

				register_taxonomy( $ct['rewrite'], array_unique($targets), array(
					'hierarchical'      => $ct['hierarchical'],
					'labels'            => array(
						'name' => _x($ct['labels']['plural'], 'taxonomy general name', 'textdomain' ),
						'singular_name'     => _x( $ct['labels']['singular'], 'taxonomy singular name', 'textdomain' ),
						'search_items'      => __( 'Search '.$ct['labels']['plural'], 'textdomain' ),
						'all_items'         => __( 'All '.$ct['labels']['plural'], 'textdomain' ),
						'parent_item'       => __( 'Parent '.$ct['labels']['singular'], 'textdomain' ),
						'parent_item_colon' => __( 'Parent '.$ct['labels']['singular'].':', 'textdomain' ),
						'edit_item'         => __( 'Edit '.$ct['labels']['singular'], 'textdomain' ),
						'update_item'       => __( 'Update '.$ct['labels']['singular'], 'textdomain' ),
						'add_new_item'      => __( 'Add New '.$ct['labels']['singular'], 'textdomain' ),
						'new_item_name'     => __( 'New '.$ct['labels']['singular'].' Name', 'textdomain' ),
						'menu_name'         => __( $ct['labels']['menu'] == '' ? $ct['labels']['plural'] : $ct['labels']['menu'], 'textdomain' ),
					),
					'show_ui'           => $ct['show_ui'],
					'show_admin_column' => $ct['show_admin_column'],
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $ct['rewrite'] )
				));
			}
		}

		register_post_type('forms', 
			array(	
			'label' => 'Forms',
			'description' => 'Past, Present, Future Communities',
			'public' => true,
			'menu_icon' => 'dashicons-feedback',
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'capabilities' => array(
					'edit_post'					=> 'moderate_comments',
					'read_post'					=> 'moderate_comments',
					'delete_post'				=> 'moderate_comments',
					'delete_posts'				=> 'moderate_comments',
					'edit_posts'				 => 'moderate_comments',
					'edit_others_posts'	=> 'moderate_comments',
					'publish_posts'			=> 'moderate_comments',
					'read_private_posts' => 'moderate_comments'
			),
			'hierarchical' => false,
			'rewrite' => array('slug' => 'forms'),
			'query_var' => true,
			'menu_position' => 20,
			'supports' => array('title'),
			'labels' => array(
				'name' => 'Forms', 
				'singular_name' => 'Form',
				'menu_name' => 'Forms',
				'add_new' => 'Create Form',
				'add_new_item' => 'Add New Form',
				'edit' => 'Edit',
				'edit_item' => 'Edit Form',
				'new_item' => 'New Form',
				'view' => 'View Forms',
				'view_item' => 'View Form',
				'search_items' => 'Search Forms',
				'not_found' => 'No Forms Found',
				'not_found_in_trash' => 'No Forms Found in Trash',
				'parent' => 'Parent Form',
			),) );
	}
}

function wpdocs_set_html_mail_content_type() {
    return 'text/html';
}
?>