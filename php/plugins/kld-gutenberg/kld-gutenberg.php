<?php
/*
Plugin Name: KLD - Gutenberg
Description: Core engine for gutenberg customization for KLD WordPress websites
Author: KLD
Version: 1.0
Author URI: http://kevinlouisdesign.com
*/



function gb_this_plugin($http = true) {
	if($http) {
		return plugin_dir_url(__FILE__);
	}
	else{
		return plugin_dir_path(__FILE__);
	}
}

function gscript_queue($hook) {
	wp_register_style('guten-styles', gb_this_plugin().'css/main.css', false, '1.0.0' );
	wp_enqueue_style('guten-styles');
}

add_action( 'admin_enqueue_scripts', 'gscript_queue' );

function kld_rename_default( $translation, $text, $domain ) {
    if ( $text == 'Default Template' ) {
        return __('Default (Blocks) Template', 'kld' );
    }
    return $translation;
}
add_filter( 'gettext', 'kld_rename_default', 10, 3 );


function kld_gutenberg_templates($can_edit, $post) {
	
	if (empty($post->ID)) return $can_edit;
	
	if($post->post_type == 'page') {
		if(get_page_template_slug($post) == '') {
			return true;
		}
	}
	
	return $can_edit;
	
}

add_filter('use_block_editor_for_post', 'kld_gutenberg_templates', 10, 2);

if(class_exists('ACF')) {
	//set category
	add_filter( 'block_categories', function( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'sample-blocks',
					'title' => 'Sample Category',
				),
			)
		);
	}, 10, 2 );

	//register blocks
	function register_acf_block_types() {
		// register a testimonial block.
		acf_register_block_type(array(
			'name'			  => 'sample',
			'title'			  => __('Sample Block'),
			'description'	  => __('Sample block for editing purpsoes later on.'),
			'render_template' => gb_this_plugin(false).'views/sample.php',
			'category'		  => 'sample-blocks',
			'icon'			  => 'slides',
			'keywords'		  => array( 'banner', 'header', 'slideshow' ),
			'enqueue_style'	  => gb_this_plugin().'css/sample.css',
			'enqueue_script'  => gb_this_plugin().'js/sample.js',
			'align'		=> 'wide', //left, right, center, wide, full
			'mode' => 'preview', //preview vs auto
			'supports'	=> array(
				'align'		=> false,
			)
		));
	}

	// Check if function exists and hook into setup.
	if( function_exists('acf_register_block_type') ) {
		add_action('acf/init', 'register_acf_block_types');
	}
}

function kld_parse_block_data($datarray) {
	$for_processing = array();

	if(!(!is_array($datarray) || sizeof($datarray) < 1)) {
		foreach($datarray as $in => $d) {
			if(substr($in, 0, 1) != '_') {
				$acfobj = get_field_object($datarray['_'.$in]);
				$acfobj['value'] = $d;
				$for_processing[$in] = $acfobj;
			}
		}
	}

	return $for_processing;
}

function kld_gb_blocks($page) {
	$blx = parse_blocks($page->post_content);
	$ret = array();

	foreach($blx as $b) {
		if(!is_null($b['blockName'])) {

			array_push($ret, array(
				'directive' => str_replace('acf/', '' ,$b['blockName']),
				'data' => kld_parse_block_data($b['attrs']['data']),
				'debug' => $b['attrs']['data']
			));
		}
	}

	return $ret;
}

class blockHelper {
	protected function fetchblockdata($post, $block_id, $preview = false) {
		if(!is_object($post) && is_numeric($post)) {
			$post = get_post($post);
		}

		if (!$post) return array(
			'block' => 'missingpost',
			'fields' => 'missingpost'
		);

		if(!$preview) {
			$blocks = parse_blocks($post->post_content);

			foreach($blocks as $block){
				if ($block['attrs']['id'] == $block_id) {
					acf_setup_meta($block['attrs']['data'], $block['attrs']['id'], true);
					$fields = get_fields();

					acf_reset_meta($block['attrs']['id']);

					return array(
						'block' => $block,
						'fields' => $fields
					);
				}
			}
		}
		else {
			$block = $preview;
			
			$fields = get_fields();

			return array(
				'block' => $block,
				'fields' => $fields
			);
		}

		return array(
			'block' => 'none',
			'fields' => 'none'
		);
	}

	protected function render_id($inAdmin, $type, $post, $block) {
		$id = $type.'-'.$block['id'];

		if($inAdmin) {
			return $id.'-'.$post->ID;
		}

		if( !empty($block['anchor']) ) {
			$id = $block['anchor'];
		}

		return $id;
	}

	protected function render_cssclass($type, $block) {
		$className = $type.'-block';
		if( !empty($block['className']) ) {
			$className .= ' ' . $block['className'];
		}
		if( !empty($block['align']) ) {
			$className .= ' align' . $block['align'];
		}

		return $className;
	}
}

$acfBlockTemplates = array();

include_once('models/sample.php'); // gutenberg sample

function kld_gutenberg_block_templates() {
	global $acfBlockTemplates;

	return $acfBlockTemplates;
}
?>