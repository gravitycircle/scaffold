<?php
class REST_output {
	private $postDetails;
	private $output = array();
	private $nav = array();
	private $baseurl = '';
	private $seo = array('page', 'post');
	

	public function __construct($postID) {
		$post = get_post($postID);

		if(defined('BASE')) {
			$this->baseurl = BASE;
		}
		else{
			$this->baseurl = get_bloginfo('url');
		}

		if(!$post) {
			$this->postDetails = false;
		}
		else{
			$this->postDetails = $post;
		}
	}

	//=== T Y P E   A N A L Y S I S
	private function analyzeType($object) {
		switch($object['type']) {
			case 'gallery':
				$ret = array();
				foreach($object['value'] as $v) {
					array_push($ret, wp_get_attachment_url($v['id']));
				}
				return $ret;
			break;
			case 'image':
				if(is_array($object['value'])) {
					return $object['value']['url'];
				}
				else {
					return wp_get_attachment_url($object['value']);
				}
			break;
			case 'true_false': 
				if(!$object['value']) {
					return false;
				}
				else{
					return true;
				}
			break;

			//=================MULTI-LAYOUT FIELDS
			case 'repeater' :
			// - model: sub_fields
			// - values: value
				
				// build data model
				$model = array();
				foreach($object['sub_fields'] as $sf) {
					if($sf['name'] != '') {
						$subf = false;

						if(isset($sf['sub_fields'])){
							$subf = $sf['sub_fields'];
						}

						$ly = false;

						if(isset($sf['layouts'])) {
							$ly = $sf['layouts'];
						}

						$model[$sf['name']] = array(
							'type' => $sf['type'],
							'subfields' => $subf,
							'layout' => $ly
						);
					}
				}

				$ret = array();
				//build actual data
				foreach($object['value'] as $thevalueset) {
					$valuebuild = array();
					foreach($thevalueset as $valueindex => $valueproper) {
						$valuebuild[$valueindex] = $this->analyzeType(array(
							'type' => $model[$valueindex]['type'],
							'value' => $valueproper,
							'sub_fields' => $model[$valueindex]['subfields'],
							'layouts' => $model[$valueindex]['layout']
						));
					}

					array_push($ret, $valuebuild);
				}


				return $ret;
			break;
			case 'flexible_content':

				//build data model from layouts
				$model = array();
				foreach($object['layouts'] as $layout) {
					$model[$layout['name']] = array();

					foreach($layout['sub_fields'] as $sf) {
						if($sf['name'] != '') {
							$subf = false;

							if(isset($sf['sub_fields'])){
								$subf = $sf['sub_fields'];
							}

							$ly = false;

							if(isset($sf['layouts'])) {
								$ly = $sf['layouts'];
							}


							$model[$layout['name']][$sf['name']] = array(
								'type' => $sf['type'],
								'subfields' => $subf,
								'layout' => $ly
							);
						}
					}
				}

				$ret = array();
				//build actual data
				foreach($object['value'] as $thevalueset) {
					$valuebuild = array();
					$submodel = $model[$thevalueset['acf_fc_layout']];
					foreach($thevalueset as $valueindex => $valueproper) {
						if($valueindex != 'acf_fc_layout') {
							$valuebuild[$valueindex] = $this->analyzeType(array(
								'type' => $submodel[$valueindex]['type'],
								'value' => $valueproper,
								'sub_fields' => $submodel[$valueindex]['subfields'],
								'layouts' => $submodel[$valueindex]['layout']
							));
						}
					}

					array_push($ret, $valuebuild);
				}

				return $ret;
			break;
			case 'group':
				//build model (same as repeater)
				$model = array();
				foreach($object['sub_fields'] as $sf) {
					if($sf['name'] != '') {
						$subf = false;

						if(isset($sf['sub_fields'])){
							$subf = $sf['sub_fields'];
						}

						$ly = false;

						if(isset($sf['layouts'])) {
							$ly = $sf['layouts'];
						}

						$model[$sf['name']] = array(
							'type' => $sf['type'],
							'subfields' => $subf,
							'layout' => $ly
						);
					}
				}


				//build actual data
				$valuebuild = array();
				foreach($object['value'] as $valueindex => $valueproper) {
					$valuebuild[$valueindex] = $this->analyzeType(array(
						'type' => $model[$valueindex]['type'],
						'value' => $valueproper,
						'sub_fields' => $model[$valueindex]['subfields'],
						'layouts' => $model[$valueindex]['layout']
					));
				}

				return $valuebuild;
			break;
			//==================================
			// case 'text':
			// case 'textarea':
			// case 'wysiwyg':
			// case 'select':
			// 	return $object['value'];
			// break;
			default:
				return array(
					'v' => $object['value'],
					't' => $object['type']
				);
			break;
		}
	}
	//=============================


	//== T Y P E S E T U P
	private function buildACFData($objs = false, $model = false) {
		$for_out = array();
		if($objs == false && $model == false) {
			//top-level
			foreach(get_field_objects($this->postDetails->ID) as $o) {
				if(!(strpos($o['name'], 'seo-') !== false)){
					$for_out[$o['name']] = $this->analyzetype($o);
				}
			}
		}
		else{

		}

		return $for_out;
	}
	//====================
	
	private function build() {
		//build seo
		$site = ' &lsaquo; '.get_bloginfo('name');
		$sitename = get_bloginfo('name');
		if(get_option('seo_name') != '') {
			$site = ' &lsaquo; '.get_option('seo_name');
			$sitename = get_option('seo_name');
		}

		$globaldesc = get_bloginfo('description');

		if(get_option('seo_description') != '') {
			$globaldesc = get_option('seo_description');
		}

		//build title
		if($this->postDetails->ID == get_option('page_on_front')) {
			$title = 'Home'.$site;
			if(get_field('seo-title', $this->postDetails->ID)) {
				$title = get_field('seo-title', $this->postDetails->ID).$site;
			}
		}
		else{
			$title = $this->postDetails->post_title.$site;

			if(get_field('seo-title', $this->postDetails->ID) != '') {
				$title = get_field('seo-title', $this->postDetails->ID).$site;
			}
		}

		//build desc
		$desc = $globaldesc;
		if(get_field('seo-description', $this->postDetails->ID) != '') {
			$desc = get_field('seo-description', $this->postDetails->ID);
		}

		//build url
		if(get_option('page_on_front') == $this->postDetails->ID) {
			$url = $this->baseurl;
		}
		else{
			$url = $this->baseurl.$this->postDetails->post_name;
		}

		//build thumbnail
		if(!get_field('seo-thumbnail', $this->postDetails->ID)) {
			$thumbnail = wp_get_attachment_image_src(get_option('site_icon_og'), 'full');
		}
		else {
			$thumbnail = wp_get_attachment_image_src(get_field('seo-thumbnail', $this->postDetails->ID), 'full');
		}

		$this->output = array(
			'metadata' => array(
				'title' => $title,
				'description' => $desc,
				'og' => array(
					'type' => 'article',
					'title' => $title,
					'description' => $desc,
					'site_name' => $sitename,
					'url' => $url,
					'image' => $thumbnail[0],
					'image:width' => $thumbnail[1],
					'image:height' =>$thumbnail[2]
				),
				'tw' => array(
					'card' => 'summary_large_image',
					'title' => $title,
					'image' => $thumbnail[0],
					'width' => $thumbnail[1],
					'height' => $thumbnail[2],
					'description' => $desc
				)
			),
			'content' => array(
			)
		);



		if($this->postDetails->post_type == 'page') {
			$template = array_pop(explode('/', get_page_template_slug($this->postDetails->postID)));
			$menu = array();

			foreach(get_field('site_navigation', 'option') as $itm) {
				array_push($menu, $itm['target']->ID);
			}

			if($this->postDetails->ID == get_option('page_on_front')) {
				$this->nav = array(
					'name' => 'Home',
					'path' => '',
					'directive' => str_replace('.php', '', $template),
					'visible' => in_array($this->postDetails->ID, $menu)
				);
			}
			else{
				$this->nav = array(
					'name' => $this->postDetails->post_title,
					'path' => $this->postDetails->post_name,
					'directive' => str_replace('.php', '', $template),
					'visible' => in_array($this->postDetails->ID, $menu)
				);
			}

			$this->output['content']['id'] = $this->postDetails->ID;
			$this->output['content']['acf'] = $this->buildACFData();
			print_r($this->output['content']['acf']);

			
		}
		else{
			$cats = get_post_taxonomies($this->postDetails->ID);
			$taxes = array();
			foreach($cats as $tax) {
				$taxes[$tax] = array();
				foreach(get_the_terms($this->postDetails->ID, $tax) as $term) {
					array_push($taxes[$tax], array(
						'name' => $term->name,
						'slug' => $term->slug,
						'id' => $term->term_id
					));
				}
			}

			$this->output['content']['id'] = $this->postDetails->ID;
			$this->output['content']['slug'] = $this->postDetails->post_name;
			$this->output['content']['categories'] = $taxes;
			$this->output['content']['acf'] = $this->buildACFData();
		}

		//build acf data
		// $this->output['content']['data'] = $this->buildACFData();
	}

	public function __toString() {
		if(!$this->postDetails) {
			return 'invalid';
		}
		else{
			$this->build();
			return 'valid';
		}
	}
};
?>