<?php
function kld_pre_initialize() {
	$initurls = array();
	$imgs = get_field('image-preload', 'option');
	
	if(is_array($imgs)) {
		foreach($imgs as $i) {
			if($i['type'] == 'image') {
				array_push($initurls, wp_get_attachment_url($i['image']['id']));
			}
			else if($i['type'] == 'vector') {
				$x = explode('-', $i['composite']);
				array_push($initurls, wp_get_attachment_url($x[0]));
				array_push($initurls, wp_get_attachment_url($x[1]));
			}
		}
	}

	return $initurls;
}

class REST_optionset {
	private $tempfetch = false;
	private $details = array();
	private $output = array();
	private $preload = array();
	private $initload = array();
	private $initcheck = array();

	private function analyzeType($object) {
		switch($object['type']) {
			case 'gallery':
				$ret = array();
				foreach($object['value'] as $v) {
					array_push($ret, wp_get_attachment_url($v));

					if(in_array(wp_get_attachment_url($v), $this->initcheck)) {
						array_push($this->initload, wp_get_attachment_url($v));
					}
					else {
						array_push($this->preload, wp_get_attachment_url($v));
					}
				}
				return $ret;
			break;
			case 'image':
				if(!(!$object['value'])) {

					if(is_array($object['value'])) {
						$img = wp_get_attachment_image_src($object['value']['id'], 'full');
					}
					else {
						$img = wp_get_attachment_image_src($object['value'], 'full');
					}

					if(in_array($img[0], $this->initcheck)) {
						array_push($this->initload, $img[0]);
					}
					else {
						array_push($this->preload, $img[0]);
					}

					

					return array(
						'url' => $img[0],
						'dimensions' => array(
							'width' => $img[1],
							'height' => $img[2]
						)
					);
				}
				else{
					return false;
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
				if(!(!$object['value']) && sizeof($object['value']) > 0) {
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

						array_push($ret, array(
							'component' => $thevalueset['acf_fc_layout'],
							'values' => $valuebuild
						));
					}
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
					if(in_array($url, $this->initcheck)) {
						array_push($this->initload, $url);
					}
					else {
						array_push($this->preload, $url);
					}
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
						$recap = array(
							'key' => get_field('recap-key', $id->ID),
							'fail-message' => get_field('recap-fail', $id->ID)
						);
						return array(
							'recaptcha' =>$recap,
							'fields' => ng_get_fields($id->ID, false),
							'title' => $id->post_title,
							'id' => $id->ID
						);
					}
					else{
						//prevents memory leaks
						$this_slug = $id->post_name;

						if($id->ID == get_option('page_on_front')) {
							$this_slug = '/';
						}
						return array(
							'id' => $id->ID,
							'slug' => $this_slug,
							'title' => $id->post_title
						);
					}
				}
				else{
					$id = get_post($object['value']);
					if($id->post_type == 'forms') {
						$recap = array(
							'key' => get_field('recap-key', $id->ID),
							'fail-message' => get_field('recap-fail', $id->ID)
						);
						return array(
							'recaptcha' =>$recap,
							'fields' => ng_get_fields($id->ID, false),
							'title' => $id->post_title,
							'id' => $id->ID
						);
					}
					else{
						//prevents memory leaks
						$this_slug = $id->post_name;

						if($id->ID == get_option('page_on_front')) {
							$this_slug = '/';
						}
						return array(
							'id' => $id->ID,
							'slug' => $this_slug,
							'title' => $id->post_title
						);
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
				if($object['value'] !== false && $object['value'] != '') {
					$vset = explode('-', $object['value']);

					$raster_base = wp_get_attachment_image_src($vset[0], 'full');
					if(isset($_GET['svg']) && $_GET['svg'] == 'true') {

						if(in_array(wp_get_attachment_url($vset[1]), $this->initcheck)) {
							array_push($this->initload, wp_get_attachment_url($vset[1]));
						}
						else {
							array_push($this->preload, wp_get_attachment_url($vset[1]));
						}

						return array(
							'url' => wp_get_attachment_url($vset[1]),
							'dimensions' => array(
								'width' => $raster_base[1],
								'height' => $raster_base[2]
							)
						);

					}
					else{
						if(in_array($raster_base[0], $this->initcheck)) {
							array_push($this->initload, $raster_base[0]);
						}
						else {
							array_push($this->preload, $raster_base[0]);
						}

						return array(
							'url' => $raster_base[0],
							'dimensions' => array(
								'width' => $raster_base[1],
								'height' => $raster_base[2]
							)
						);
					}
				}
				else {
					return false;
				}
			break;
			case 'number':
				return floatval($object['value']);
			break;
			case 'textarea':
				$text = $object['value'];
				preg_match_all("/\[([^\]]*)\]/", $text, $matches);
				
				$s = $matches[0];
				$r = array();

				foreach($matches[1] as $in => $chk) {
					if(filter_var($chk, FILTER_VALIDATE_EMAIL)) {
						$r[$in] = '<a href="mailto: '.strtolower($chk).'">'.strtolower($chk).'</a>';
					}
					else if(filter_var($chk, FILTER_VALIDATE_URL)) {
						$r[$in] = '<a href="'.strtolower($chk).'" target="_blank">'.strtolower($chk).'</a>';	
					}
					else{
						$r[$in] = $chk;
					}
				}

				return str_replace($s, $r, $text);
			break;
			//==================================
			case 'text':
			case 'select':
			case 'link';
			case 'button_group':
			case 'checkbox':
			case 'color_picker':
				return $object['value'];
			break;
			default:
				return $object['value'];
			break;
		}
	}

	private function build() {
		if($this->tempfetch) {
			//build SEO
			$site = ' &lsaquo; '.get_bloginfo('name').' – '.get_bloginfo('description');
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
			$title = get_field('seo-title', 'option');

			if(!$title) {
				$title = 'Home';
			}

			$title = $title.$site;

			//build desc
			$desc = $globaldesc;
			if(get_field('seo-description', 'option') != '') {
				$desc = get_field('seo-description', 'option');
			}

			//build url
			$url = $this->baseurl;

			//build thumbnail
			if(!get_field('seo-thumbnail', 'option')) {
				$thumbnail = wp_get_attachment_image_src(get_option('site_icon_og'), 'full');
			}
			else {
				$thumbnail = wp_get_attachment_image_src(get_field('seo-thumbnail', 'option'), 'full');
			}

			$this->output['metadata']= array(
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

		//build content
		if(sizeof($this->details) > 0) {
			$for_out = array();
			foreach($this->details as $oname) {
				
				$o = get_field_object($oname, 'option');

				$for_out[$o['name']] = $this->analyzetype($o);
			}

			$this->output['content']['acf'] = $for_out;
		}
		else{
			return false;
		}
	}

	public function __construct($nameList, $setAsTemp = false) {
		if(is_array($nameList) && sizeof($nameList) > 0){
			foreach($nameList as $n) {
				array_push($this->details, $n);
			}

			$this->tempfetch = $setAsTemp;

			$this->build();
		}
	}

	public function __toString() {
		return 'temp';
	}

	public function toObject() {
		return $this->output;
	}

	public function toPreload() {
		return array_values(array_unique($this->preload));
	}

	public function toInit() {
		return array_values(array_unique($this->initload));
	}

	public function checkInit(){
		return $this->initcheck;
	}
}

class REST_output {
	private $postDetails;
	private $output = array();
	private $baseurl = '';
	private $seo = array('page', 'post');
	private $full = false;
	private $preload = array();
	private $initload = array();
	private $initcheck = false;
	
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
			$this->initcheck = kld_pre_initialize();
			$this->build();
		}
	}

	//=== T Y P E   A N A L Y S I S
	private function analyzeType($object) {
		switch($object['type']) {
			case 'gallery':
				$ret = array();
				foreach($object['value'] as $v) {
					array_push($ret, wp_get_attachment_url($v));

					if(in_array(wp_get_attachment_url($v), $this->initcheck)) {
						array_push($this->initload, wp_get_attachment_url($v));
					}
					else {
						array_push($this->preload, wp_get_attachment_url($v));
					}
				}
				return $ret;
			break;
			case 'image':
				if(!(!$object['value'])) {

					if(is_array($object['value'])) {
						$img = wp_get_attachment_image_src($object['value']['id'], 'full');
					}
					else {
						$img = wp_get_attachment_image_src($object['value'], 'full');
					}

					if(in_array($img[0], $this->initcheck)) {
						array_push($this->initload, $img[0]);
					}
					else {
						array_push($this->preload, $img[0]);
					}

					

					return array(
						'url' => $img[0],
						'dimensions' => array(
							'width' => $img[1],
							'height' => $img[2]
						)
					);
				}
				else{
					return false;
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
				if(!(!$object['value']) && sizeof($object['value']) > 0) {
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

						array_push($ret, array(
							'component' => $thevalueset['acf_fc_layout'],
							'values' => $valuebuild
						));
					}
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
					if(in_array($url, $this->initcheck)) {
						array_push($this->initload, $url);
					}
					else {
						array_push($this->preload, $url);
					}
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
						$recap = array(
							'key' => get_field('recap-key', $id->ID),
							'fail-message' => get_field('recap-fail', $id->ID)
						);
						return array(
							'recaptcha' =>$recap,
							'fields' => ng_get_fields($id->ID, false),
							'title' => $id->post_title,
							'id' => $id->ID
						);
					}
					else{
						//prevents memory leaks
						$this_slug = $id->post_name;

						if($id->ID == get_option('page_on_front')) {
							$this_slug = '/';
						}
						return array(
							'id' => $id->ID,
							'slug' => $this_slug,
							'title' => $id->post_title
						);
					}
				}
				else{
					$id = get_post($object['value']);
					if($id->post_type == 'forms') {
						$recap = array(
							'key' => get_field('recap-key', $id->ID),
							'fail-message' => get_field('recap-fail', $id->ID)
						);
						return array(
							'recaptcha' =>$recap,
							'fields' => ng_get_fields($id->ID, false),
							'title' => $id->post_title,
							'id' => $id->ID
						);
					}
					else{
						//prevents memory leaks
						$this_slug = $id->post_name;

						if($id->ID == get_option('page_on_front')) {
							$this_slug = '/';
						}
						return array(
							'id' => $id->ID,
							'slug' => $this_slug,
							'title' => $id->post_title
						);
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
				if($object['value'] !== false && $object['value'] != '') {
					$vset = explode('-', $object['value']);

					$raster_base = wp_get_attachment_image_src($vset[0], 'full');
					if(isset($_GET['svg']) && $_GET['svg'] == 'true') {

						if(in_array(wp_get_attachment_url($vset[1]), $this->initcheck)) {
							array_push($this->initload, wp_get_attachment_url($vset[1]));
						}
						else {
							array_push($this->preload, wp_get_attachment_url($vset[1]));
						}

						return array(
							'url' => wp_get_attachment_url($vset[1]),
							'dimensions' => array(
								'width' => $raster_base[1],
								'height' => $raster_base[2]
							)
						);

					}
					else{
						if(in_array($raster_base[0], $this->initcheck)) {
							array_push($this->initload, $raster_base[0]);
						}
						else {
							array_push($this->preload, $raster_base[0]);
						}

						return array(
							'url' => $raster_base[0],
							'dimensions' => array(
								'width' => $raster_base[1],
								'height' => $raster_base[2]
							)
						);
					}
				}
				else {
					return false;
				}
			break;
			case 'number':
				return floatval($object['value']);
			break;
			case 'textarea':
				$text = $object['value'];
				preg_match_all("/\[([^\]]*)\]/", $text, $matches);
				
				$s = $matches[0];
				$r = array();

				foreach($matches[1] as $in => $chk) {
					if(filter_var($chk, FILTER_VALIDATE_EMAIL)) {
						$r[$in] = '<a href="mailto: '.strtolower($chk).'">'.strtolower($chk).'</a>';
					}
					else if(filter_var($chk, FILTER_VALIDATE_URL)) {
						$r[$in] = '<a href="'.strtolower($chk).'" target="_blank">'.strtolower($chk).'</a>';	
					}
					else{
						$r[$in] = $chk;
					}
				}

				return str_replace($s, $r, $text);
			break;
			//==================================
			case 'text':
			case 'select':
			case 'link';
			case 'button_group':
			case 'checkbox':
			case 'color_picker':
				return $object['value'];
			break;
			default:
				return $object['value'];
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
		$site = ' &lsaquo; '.get_bloginfo('name').' – '.get_bloginfo('description');
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
				$this_slug = $this->postDetails->post_name;

				if($this->postDetails->ID == get_option('page_on_front')) {
					$this_slug = '/';
				}
				$this->output['content']['id'] = $this->postDetails->ID;
				$this->output['content']['slug'] = $this_slug;
				$this->output['content']['acf'] = $this->buildACFData();

				//build blocks
				if(function_exists('kld_gb_blocks')) {
					$blockarray = array();

					$parsed_blocks = kld_gb_blocks($this->postDetails);
					foreach($parsed_blocks as $pblockdata) {
						if($pblockdata['directive'] == 'form') {
							$pblockdata['directive'] = 'blockform';
						}

						$blockpr = array(
							'directive' => $pblockdata['directive'],
							'data' => array(),
							// 'debug' => $pblockdata['debug']
						);



						foreach($pblockdata['data'] as $bindex => $field) {
							if($field['type'] == 'group') {
								// echo $bindex.' - group <br>';
							}
							else if($field['type'] == 'repeater') {
								// print_r($pblockdata['debug']['_'.$bindex]);
							}
							else{
								if(strpos($bindex, '_') !== false){
									$test['data'][$bindex] = $this->analyzeType($field);

									foreach ($test as $testpath => $testvalue) {
										$ref = &$blockpr;
										foreach (explode('_', $testpath) as $key) {
											$ref = &$ref[$key];
										}
										$ref = $testvalue;
									}
								}
								else{
									$blockpr['data'][$bindex] = $this->analyzeType($field);
								}
							}
						}

						array_push($blockarray, $blockpr);
					}

					$this->output['content']['acf']['blocks'] = $blockarray;
				}
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

				//build blocks
				if(function_exists('kld_gb_blocks')) {
					$blockarray = array();

					$parsed_blocks = kld_gb_blocks($this->postDetails);
					foreach($parsed_blocks as $pblockdata) {
						$blockpr = array(
							'directive' => $pblockdata['directive'],
							'data' => array(),
							// 'debug' => $pblockdata['debug']
						);



						foreach($pblockdata['data'] as $bindex => $field) {
							if($field['type'] == 'group') {
								// echo $bindex.' - group <br>';
							}
							else if($field['type'] == 'repeater') {
								// print_r($pblockdata['debug']['_'.$bindex]);
							}
							else{
								if(strpos($bindex, '_') !== false){
									$test['data'][$bindex] = $this->analyzeType($field);

									foreach ($test as $testpath => $testvalue) {
										$ref = &$blockpr;
										foreach (explode('_', $testpath) as $key) {
											$ref = &$ref[$key];
										}
										$ref = $testvalue;
									}
								}
								else{
									$blockpr['data'][$bindex] = $this->analyzeType($field);
								}
							}
						}

						array_push($blockarray, $blockpr);
					}

					$this->output['content']['acf']['blocks'] = $blockarray;
				}
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
		return array_values(array_unique($this->preload));
	}

	public function toInit() {
		return array_values(array_unique($this->initload));
	}

	public function checkInit(){
		return $this->initcheck;
	}
};
?>