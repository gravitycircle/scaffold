<?php
//actions/overrides.php
add_filter('upload_mimes', 'set_mimes', 1, 1);
add_action( 'admin_head', 'fix_svg' );
add_filter( 'intermediate_image_sizes_advanced', 'disable_wp_image_resizer' );
add_filter( 'enter_title_here', 'title_changer');
add_filter( 'page_row_actions', 'rd_duplicate_post_link', 10, 2 );
add_filter( 'post_row_actions', 'rd_duplicate_post_link', 10, 2 );
add_filter('acf/settings/show_admin', 'acf_menu_visibility');
add_action( 'admin_action_rd_duplicate_post_as_draft', 'rd_duplicate_post_as_draft' );
add_action( 'admin_menu', 'customize_menus', 100 );
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
add_action('add_meta_boxes', 'remove_post_taxonomies');
add_action('acf/init', 'my_acf_init');
add_action( 'after_setup_theme', 'fm_create_menus' );


//actions/scripts.php
add_action( 'admin_enqueue_scripts', 'script_queue' );

//actions/general-settings.php
add_filter('admin_init', 'register_general_settings_fields');

//actions/cpt.php
add_action( 'init', 'create_post_type' );
add_action('acf/input/admin_enqueue_scripts', 'acf_js_includes');
acf_add_options_page(array(
	'page_title' => 'Data Management',
	'menu_title' => '',
	'menu_slug' => 'cpt',
	'capability' => 'setup_network',
	'position' => 4,
	'parent_slug' => '',
	'icon_url' => 'dashicons-welcome-widgets-menus'
));
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

//standalone
add_filter('use_block_editor_for_post', '__return_false', 5);
?>