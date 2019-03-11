<?php
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

  global $wp_version;
  if ( $wp_version !== '4.7.1' ) {
     return $data;
  }

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function fix_svg() {
 
}

function set_mimes($mime_types){
	//Creating a new array will reset the allowed filetypes
	$mime_types['svg'] = 'image/svg+xml';
	return $mime_types;
}



// Remove default image sizes here. 
function disable_wp_image_resizer( $sizes ) {
	foreach($sizes as $s => $v) {
		unset( $sizes[$s]); // 150px
	}
	return $sizes;
}

function kld_admin_sandbox(){
	echo '<pre>';
	$x = new REST_output(187, true);
	print_r($x->toObject());
	echo '</pre>';
}
 
function customize_menus(){
	$u = get_current_user_id();

	if(get_field('hide_menu', 'option') != false) {
		foreach(get_field('hide_menu', 'option') as $cap) {
			// menu_slug
			// hidden_from

			if($cap['hidden_from'] == 'everyone') {
				remove_menu_page($cap['menu_slug']);
			}
			else {
				if(!current_user_can($cap['hidden_from'])) {
					remove_menu_page($cap['menu_slug']);
				}
			}
		}
	}
	//hide_submenu
	if(get_field('hide_submenu', 'option') != false) {
		foreach(get_field('hide_submenu', 'option') as $cap) {
			// menu_slug
			// hidden_from

			if($cap['hidden_from'] == 'everyone') {
				remove_submenu_page($cap['menu_slug'], $cap['submenu_slug']);
			}
			else {
				if(!current_user_can($cap['hidden_from'])) {
					remove_submenu_page($cap['menu_slug'], $cap['submenu_slug']);
				}
			}
		}
	}

	if($u != 1) {
		remove_menu_page( 'tools.php' );                  //Tools
		remove_menu_page( 'themes.php' );                 //Appearance
		remove_menu_page( 'edit.php' );						//posts
	}
	else {
		add_menu_page( 'Sandbox Area', 'Sandbox', 'manage_options', 'sandbox', 'kld_admin_sandbox', 'dashicons-edit', 3 );
	}
	
	// print_r(get_field('remove-post-features', 'option'));

	//submenus
	global $submenu;
	// Appearance Menu
}

function title_changer( $title ){
	$screen = get_current_screen();
  
	// if  ( 'product' == $screen->post_type ) {
	// 	$title = 'Enter item name';
	// }
  
	return $title;
}

function remove_dashboard_widgets() {
	global $wp_meta_boxes;

	$checkfor = array('side-core-dashboard_quick_press', 'normal-core-dashboard_incoming_links', 'normal-core-dashboard_right_now', 'normal-core-dashboard_plugins', 'normal-core-dashboard_recent_drafts', 'normal-core-dashboard_recent_comments', 'side-core-dashboard_primary', 'side-core-dashboard_secondary', 'normal-core-dashboard_activity');

	foreach($checkfor as $gf) {
		if(!get_field($gf, 'option')) {

			$gfarray = explode('-', $gf);
			remove_meta_box( $gfarray[2], 'dashboard', $gfarray[0]);
			unset($wp_meta_boxes['dashboard'][$gfarray[0]][$gfarray[1]][$gfarray[2]]);
		}
	}
}

function remove_post_taxonomies() {
	$rpf = get_field('remove-post-features', 'option');
	if($rpf != false) {
		if(sizeof($rpf) >= 1) {
			foreach($rpf as $postfeat) {
				$post_do = explode('-', $postfeat);

				if($post_do[0] == 'sp') {
					remove_post_type_support('post', $post_do[1]);
				}
				else if($post_do[0] == 'mb'){
					remove_meta_box($post_do[1],'post','side');
				}
			}
		}
	}
	//remove-page-features
	$rpf = get_field('remove-page-features', 'option');
	if($rpf != false) {
		if(sizeof($rpf) >= 1) {
			foreach($rpf as $postfeat) {
				$post_do = explode('-', $postfeat);

				if($post_do[0] == 'sp') {
					remove_post_type_support('post', $post_do[1]);
				}
				else if($post_do[0] == 'mb'){
					remove_meta_box($post_do[1],'post','side');
				}
			}
		}
	}
	$x = get_current_screen();
	if($x->action != 'add') {
		add_meta_box('form-responses', 'Form Responses', 'ng_form_responses', 'forms', 'normal', 'low');
	}
}

function rd_duplicate_post_as_draft(){
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die('No post to duplicate has been supplied!');
	}
 
	/*
	 * Nonce verification
	 */
	if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
		return;
 
	/*
	 * get the original post id
	 */
	$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
	/*
	 * and all the original post data then
	 */
	$post = get_post( $post_id );
 
	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;
 
	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset( $post ) && $post != null) {
 
		/*
		 * new post data array
		 */
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);
 
		/*
		 * insert the post by wp_insert_post() function
		 */
		$new_post_id = wp_insert_post( $args );
 
		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}
 
		/*
		 * duplicate all post meta just in two SQL queries
		 */
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				if( $meta_key == '_wp_old_slug' ) continue;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
 
 
		/*
		 * finally, redirect to the edit post screen for the new draft
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
}

function rd_duplicate_post_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=rd_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
	}
	return $actions;
}

function acf_menu_visibility( $show ) {
    $u = get_current_user_id();

    if($u != 1) {
    	return false;
    }
    else{
    	return current_user_can('manage_options');
    }
    
}

function my_acf_init() {
	$x = fe_fetch_array('api-keys', 'api', 'Google Cloud Platform');

	acf_update_setting('google_api_key', $x['key']);
}

function fm_create_menus() {
	register_nav_menu( 'navigation', 'Navigation Menu' );
}

function ng_form_responses() {
	global $post;
	if(get_option('catalogue-'.$post->ID) == '') {
		update_option('catalogue-'.$post->ID, serialize(array()));
	}

	
	$catalogue = unserialize(get_option('catalogue-'.$post->ID));

	$in_view = null;
	$for_dl = array();
	foreach($catalogue as $i => $x) {
		if($i == 0) {
			$in_view = array(
				'name' => $x,
				'data' => get_option($x) == '' ? array() : array_reverse(unserialize(get_option($x)))
			);
		}
		else{
			array_push($for_dl, $x);
		}
	}
	//responses-199-2019-02
	$general = explode('-', $in_view['name']);
	?>
	<p>
		<strong>Responses for the month of <?=date('F, Y', strtotime($general[2].'-'.$general[3].'-01'));?></strong>
	</p>
	<hr>
	<?php
	if(!(!$in_view['data'] || sizeof($in_view['data']) < 1)) {
		foreach($in_view['data'] as $indx => $response) {
			?>
			<div class="response-handle" style="padding: 1em 0;">
				<div class="response-title" style="line-height: 40px; border-bottom: 1px solid #ccc; padding-left: 5px; padding-right: 5px; font-weight: bold;">Received: <?=$response['received']?></div>
				<table style="width: 100%;" cellpadding="0" cellspacing="0">
					<?php
					foreach($response['data'] as $sindx => $kvpair) {
						?>
						<tr style="background: #<?=$sindx % 2 == 0 ? 'efefef' : 'ffffff'?>;">
							<td style="font-weight: bold; padding: 10px;"><?=$kvpair['heading']?>:</td>
							<td style="padding: 10px;"><?=$kvpair['value']?></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}
	}
}
?>