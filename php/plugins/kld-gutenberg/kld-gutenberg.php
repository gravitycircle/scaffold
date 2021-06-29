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
	wp_register_script('block-manifest', gb_this_plugin().'js/block-manifest.js', array('jquery'), '1.0.0');
	wp_register_style('guten-styles', gb_this_plugin().'css/main.css', false, '1.0.0' );
	wp_enqueue_style('guten-styles');
	wp_enqueue_script('block-manifest');
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

function kld_slugify($text) {
	return str_replace(' ', '-', strtolower($text));
}

add_filter('use_block_editor_for_post', 'kld_gutenberg_templates', 10, 2);


if(class_exists('ACF')) {
	//set category

	add_filter( 'block_categories', function( $categories, $post ) {
		$kldblocks = get_field('custom-blocks', 'option');

		if(!$kldblocks || sizeof($kldblocks) < 1) {
			return $categories;
		}
		else{
			$newcats = array();
			foreach($kldblocks as $b) {
				$cats = explode(',', str_replace(', ', ',', $b['category']));

				foreach($cats as $c) {
					array_push($newcats, $c);
				}
			}
			
			$addlc = array();
			foreach(array_unique($newcats) as $nc) {
				array_push($addlc, array(
					'slug'  => kld_slugify($nc),
					'title' => $nc,
				));
			}
			return array_merge(
				$categories,
				array_unique($addlc)
			);
		}
	}, 10, 2 );

	//register blocks
	function register_acf_block_types() {
		// register a testimonial block.
		$blocks = get_field('custom-blocks', 'option');
		//check options for blocks
		
		//print_r($blocks);
		foreach($blocks as $block) {

			//build files

			//-view
			if(!file_exists(gb_this_plugin(false).'views/'.kld_slugify($block['directive']).'.php')) {
				$wr = fopen(gb_this_plugin(false).'views/'.kld_slugify($block['directive']).'.php', 'w');
				ob_start();
?>if($is_preview) {
	$<?=kld_slugify($block['directive'])?> = new <?=kld_slugify($block['directive'])?>Block($post_id, $block['id'], $block);
}
else{
	$<?=kld_slugify($block['directive'])?> = new <?=kld_slugify($block['directive'])?>Block($post_id, $block['id']);
}

echo $<?=kld_slugify($block['directive'])?>;<?php
				$txt = "<?php\r\n".ob_get_clean()."\r\n?>";

				fwrite($wr, $txt);
				fclose($wr);
			}

			if(!file_exists(gb_this_plugin(false).'models/'.kld_slugify($block['directive']).'.php')){
				$mainDirective = '<?php
class '.kld_slugify($block['directive']).'Block extends blockHelper {
	private $post;
	private $fieldData;
	private $blockData;
	private $preview = false; //denotes ajax loading in admin

	function __construct($id, $blockid, $preview = false) {
		
		//verify
		if(!is_object($id)) {
			$post = get_post($id);
		}
		else{
			$post = $id;
		}

		if(!$post) {
			throw new Exception(\'Post Object with ID \'.$id.\' does not exist. Cannot finish construction.\');
			wp_die(\'FATAL: Post Object with ID \'.$id.\' does not exist. Cannot finish construction.\');
		}
		else {
			$this->post = $post;
		}

		if($blockid == \'\' || $blockid == false) {
			throw new Exception(\'Block ID not specified.\');
			wp_die(\'FATAL: Block ID not specified.\');
		}

		$fetch = $this->fetchblockdata($this->post->ID, $blockid, $preview);

		$this->fieldData = $fetch[\'fields\'];
		$this->blockData = $fetch[\'block\'];
	}

	public function __toString() {
		ob_start();
		$divid = $this->render_id(true, \''.kld_slugify($block['directive']).'\', $this->post, $this->blockData);
		?>
		<div id="<?=$divid?>" class="<?=$this->render_cssclass(\''.kld_slugify($block['directive']).'\', $this->blockData)?>">
			<div class="kld-block-content">
				This is a newly created '.$block['name'].' block -- as shown in admin.
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

ob_start();
?>
<div class="'.kld_slugify($block['directive']).'-block"></div>
<?php
$acfBlockTemplates[\''.kld_slugify($block['directive']).'\'] = ob_get_clean();';

				$wr = fopen(gb_this_plugin(false).'models/'.kld_slugify($block['directive']).'.php', 'w');
				

				fwrite($wr, $mainDirective);
				fclose($wr);
			}

			//categories
			acf_register_block_type(array(
				'name'			  => $block['slug'],
				'title'			  => __($block['name']),
				'description'	  => __($block['description']),
				'render_template' => gb_this_plugin(false).'views/'.kld_slugify($block['directive']).'.php',
				'category'		  => kld_slugify($block['category']),
				'icon'			  => $block['icon'],
				'keywords'		  => explode(PHP_EOL, $block['keywords']),
				// 'enqueue_style'	  => gb_this_plugin().'css/sample.css',
				// 'enqueue_script'  => gb_this_plugin().'js/sample.js',
				'align'		=> 'wide', //left, right, center, wide, full
				'mode' => 'preview', //preview vs auto
				'supports'	=> array(
					'align'		=> false,
				)
			));
		}		
	}

	// Check if function exists and hook into setup.
	if( function_exists('acf_register_block_type') ) {
		add_action('acf/init', 'register_acf_block_types');
	}
}

add_filter( 'allowed_block_types', 'control_block_types', 10, 2 );
 
function control_block_types( $allowed_blocks, $post ) {
 	$allowed_blocks = [];
 	$blocks = get_field('custom-blocks', 'option');

 	foreach($blocks as $block) {
 		array_push($allowed_blocks, 'acf/'.$block['slug']);
 	}

	return $allowed_blocks;
 
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

add_filter( 'block_editor_settings' , 'remove_guten_wrapper_styles' );
function remove_guten_wrapper_styles( $settings ) {
	
    $settings['styles'][0]['css'] = '';
    $settings['styles'][1]['css'] = '';
    $settings['styles'][2]['css'] = '';
    // print_r($settings);
    return $settings;
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

if( function_exists('acf_add_local_field_group') ):
acf_add_local_field_group(array(
	'key' => 'group_5cb8f88c9cbfd',
	'title' => 'Custom Blocks',
	'fields' => array(
		array(
			'key' => 'field_60d3dd8a40012',
			'label' => 'Block Types',
			'name' => 'custom-blocks',
			'type' => 'repeater',
			'instructions' => 'Only applies on default page templates.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => 'field_60d3e18a40014',
			'min' => 0,
			'max' => 0,
			'layout' => 'block',
			'button_label' => 'Add Block Type',
			'sub_fields' => array(
				array(
					'key' => 'field_60d3e18a40014',
					'label' => 'Name',
					'name' => 'name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_60d3e13240013',
					'label' => 'Slug',
					'name' => 'slug',
					'type' => 'text',
					'instructions' => 'Must be unique among block types.',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_61d3e13240083',
					'label' => 'Description',
					'name' => 'description',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '3',
					'new_lines' => '',
				),
				array(
					'key' => 'field_60d3e1ab40015',
					'label' => 'Category',
					'name' => 'category',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_60d3e90040016',
					'label' => 'Directive Name',
					'name' => 'directive',
					'type' => 'text',
					'instructions' => 'Names the block directive and the php template file for this block.',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_60d3e94940017',
					'label' => 'Icon',
					'name' => 'icon',
					'type' => 'kld_dashicon',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'font_size' => 14,
				),
				array(
					'key' => 'field_60d3e96240018',
					'label' => 'Keywords',
					'name' => 'keywords',
					'type' => 'textarea',
					'instructions' => 'Keywords for this block, separate by line.',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'new_lines' => '',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'cpt',
			),
		),
	),
	'menu_order' => 7,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));
endif;

$acfBlockTemplates = array();

$acfScanTP = scandir(gb_this_plugin(false).'models');

foreach($acfScanTP as $tpfile) {
	if(!(substr($tpfile, 0, 1) == '.' || substr($tpfile, 0, 1) == '_')) {
		include_once(gb_this_plugin(false).'models/'.$tpfile);
	}
}

function kld_gutenberg_block_templates() {
	global $acfBlockTemplates;

	return $acfBlockTemplates;
}
?>