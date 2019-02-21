<?php
class REST_output {
	private $postDetails;
	private $output = array();
	private $baseurl = '';
	private $seo = array('page', 'post');
	private $full = false;
	private $preload = array();

	public function __construct($postID, $full = false) {
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
			if(!(!$full)) {
				$this->full = $full;
			}

			$this->build();
		}
	}

	//=== T Y P E   A N A L Y S I S
	private function analyzeType($object) {
		switch($object['type']) {
			case 'gallery':
				$ret = array();
				foreach($object['value'] as $v) {
					array_push($ret, wp_get_attachment_url($v['id']));
					array_push($this->preload, wp_get_attachment_url($v['id']));
				}
				return $ret;
			break;
			case 'image':
				if(is_array($object['value'])) {
					$img = wp_get_attachment_image_src($object['value']['id']);
				}
				else {
					$img = wp_get_attachment_image_src($object['value']);
				}

				array_push($this->preload, $img[0]);

				return array(
					'url' => $img[0],
					'dimensions' => array(
						'width' => $img[1],
						'height' => $img[2]
					)
				);
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
			case 'file':
				$url = wp_get_attachment_url($object['value']['id']);

				$ext = array_pop(explode('.', $url));

				if(in_array($ext, array('jpg', 'jpeg', 'gif', 'bmp', 'png', 'svg'))) {
					array_push($this->preload, $url);
				}

				return $url;
			break;
			case 'google_map':
				return array(
					'address' => $object['value']['address'],
					'coordinates' => array($object['value']['lat'], $object['value']['lng'])
				);
			break;
			case 'wysiwyg':
				return wpautop($object['value']);
			break;
			case 'oembed':
				$rvalue = '';
				preg_match_all('/(src)=("[^"]*")/i',$object['value'], $rvalue);
				$url = str_replace('"', '', $rvalue[2][0]);
				$types = array('youtube', 'vimeo');
				$type = '';
				
				foreach($types as $t) {
					if (strpos($url, $t) !== false) {
						$type = $t;
					}
				}

				return array(
					'url' => $url,
					'source' => ucfirst($type)
				);
			break;
			case 'post_object':
				if(is_array($object['value']) || is_object($object['value'])) {
					if(is_object($object['value'])) {
						$id = $object['value'];
					}
					else{
						$id = get_post($object['value']);
					}

					if($id->post_type == 'forms') {
						return array(
							'fields' => ng_get_fields($id->ID, false),
							'title' => $id->post_title
						);
					}
					else{
						$o = new REST_output($id->ID, false);
						$o = $o->toObject();	

						return $o['content'];
					}
					
				}
			break;
			case 'relationship':
				if($object['value'] !== false && is_array($object['value']) && sizeof($object['value']) > 0) {
					$r = array();
					foreach($object['value'] as $v) {
						if(is_object($v)) {
							$id = $v;
						}
						else{
							$id = get_post($v);
						}

						if($id->post_type == 'forms') {
							array_push($r, array(
								'fields' => ng_get_fields($id->ID, false),
								'title' => $id->post_title
							));
						}
						else{
							$o = new REST_output($id->ID, false);
							$o = $o->toObject();
							array_push($r, $o['content']);
						}
					}

					return $r;
				}
				return false;
			break;
			case 'taxonomy':
				if(is_array($object['value'])) {
					$r = array();

					foreach($object['value'] as $v) {
						if(is_object($v)) {
							array_push($r, array(
								'ID' => $v->term_id,
								'label' => $v->name,
								'slug' => $v->slug,
								'taxonomy' => $v->taxonomy
							));
						}
						else {
							$g = get_term($v);
							array_push($r, array(
								'ID' => $g->term_id,
								'label' => $g->name,
								'slug' => $g->slug,
								'taxonomy' => $g->taxonomy
							));
						}
					}
					return $r;
				}
				else{
					$v = $object['value'];
					if(is_object($v)) {
						return array(
							'ID' => $v->term_id,
							'label' => $v->name,
							'slug' => $v->slug,
							'taxonomy' => $v->taxonomy
						);
					}
					else {
						$g = get_term($v);
						return array(
							'ID' => $g->term_id,
							'label' => $g->name,
							'slug' => $g->slug,
							'taxonomy' => $g->taxonomy
						);
					}
				}
			break;
			case 'vector_image':
				$vset = explode('-', $object['value']);

				$raster_base = wp_get_attachment_image_src($vset[0]);
				if(isset($_GET['svg']) && $_GET['svg'] == 'true') {
					array_push(wp_get_attachment_url($vset[1]));

					return array(
						'url' => wp_get_attachment_url($vset[1]),
						'dimensions' => array(
							'width' => $raster_base[1],
							'height' => $raster_base[2]
						)
					);
				}
				else{
					array_push(wp_get_attachment_url($raster_base[0]));
					return array(
						'url' => $raster_base[0],
						'dimensions' => array(
							'width' => $raster_base[1],
							'height' => $raster_base[2]
						)
					);
				}
			break;
			//==================================
			case 'text':
			case 'select':
			case 'link';
			case 'button_group':
			case 'checkbox':
				return $object['value'];
			break;
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
		$g = get_field_objects($this->postDetails->ID);
		if(!$g || sizeof($g) < 1) {
			$for_out = false;
		}
		else {
			foreach($g as $o) {
				if(strpos($o['name'], 'seo-') === false && strpos($o['name'], 'css-') === false){
					$for_out[$o['name']] = $this->analyzetype($o);
				}
			}
		}
		
		if(!$for_out) {
			return false;
		}
		else{
			if(sizeof($for_out) < 1) {
				return false;
			}
			else{
				return $for_out;
			}
		}
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
		else if($this->postDetails->ID == get_option('page_for_lost')) {
			$title = 'Page Not Found'.$site;
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
		else if($this->postDetails->ID == get_option('page_for_lost')) {
			$url = $this->baseurl.'lost';
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

		if(!$this->full) {
			$this->output = array(
				'content' => array(
					'page-title' => $title,
					'title' => $this->postDetails->post_title
				)
			);
		}
		else{
			if($this->full === true || $this->full == 'all') {
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
			}
			else if($this->full == 'meta') {
				$this->output = array(
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
				);
			}
		}



		if($full != 'meta') {
			if($this->postDetails->post_type == 'page') {
				$this->output['content']['id'] = $this->postDetails->ID;
				$this->output['content']['slug'] = $this->postDetails->post_name;
				$this->output['content']['acf'] = $this->buildACFData();			
			}
			else{
				$cats = get_post_taxonomies($this->postDetails->ID);
				$taxes = array();

				foreach($cats as $tax) {
					$taxes[$tax] = array();
					$gtt = get_the_terms($this->postDetails->ID, $tax);
					if(is_array($gtt) && sizeof($gtt) > 0) {
						foreach($gtt as $term) {
							array_push($taxes[$tax], array(
								'name' => $term->name,
								'slug' => $term->slug,
								'id' => $term->term_id
							));
						}
					}
				}
				$this->output['content']['id'] = $this->postDetails->ID;
				$this->output['content']['slug'] = $this->postDetails->post_name;
				$this->output['content']['categories'] = $taxes;
				$this->output['content']['acf'] = $this->buildACFData();
			}
		}
	}

	public function __toString() {
		if(!$this->postDetails) {
			return false;
		}
		else{			
			if($this->postDetails->ID == get_option('page_on_front')) {
				return 'home';
			}
			else if($this->postDetails->ID == get_option('page_for_lost')) {
				return 'lost';
			}
			else{
				return $this->postDetails->post_name;
			}
		}
	}

	public function toObject() {
		return $this->output;
	}

	public function toPreload() {
		return $this->preload;
	}
};
?>