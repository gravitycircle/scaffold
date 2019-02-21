<?php
function fe_fetch_array($option, $index, $key) {
	$option = json_decode(html_entity_decode(get_option($option), ENT_QUOTES), true);

	if(sizeof($option) == 0 || !$option || $option == '') {
		return '';
	}

	foreach($option as $op) {
		if($op[$index] == $key) {
			return $op;
		}
	}
	
	return '';
}

function print_image_field($v)
{	
	$allargs = $v;

	foreach($allargs as $args) {
		$value = get_option( $args['option'], 0);

		$r = ($value == 0 ? 'Add' : 'Change');
		$txt = str_replace('[act]', $r, $args['button']);

		$img = wp_get_attachment_url($value);
		ob_start();
		$size = array();
		if(isset($args['width']) && is_numeric($args['width'])){
			array_push($size, 'width: '.$args['width'].'px;');
		}

		if(isset($args['height']) && is_numeric($args['height'])){
			array_push($size, 'min-height: '.$args['height'].'px;');
		}

		if(sizeof($size) > 0) {
			$size = 'style="'.implode(' ', $size).'"';
		}
		else{
			$size = '';
		}
	?>
	<span id="<?php echo $args['id']?>" class="img-field" style="margin-bottom: 20px;">
		<input type="hidden" name="<?php echo $args['option']?>" value="<?php echo $value?>" />
		<span class="image-container"<?php echo $size?>><?php echo ($img == '' ? '' : '<img src="'.$img.'" alt="'.$args['alt'].'" />')?></span>
		<a class="edit-image button" data-target="<?php echo $args['id']?>" data-file-types='<?php echo json_encode($args['file_types'])?>'><?php echo $txt?></a>
		<?php echo (isset($args['tooltip']) && $args['tooltip'] != '' ? '<p class="instruction">'.$args['tooltip'].'</p>' : '')?>
	</span>
	<?php
		echo ob_get_clean();
	}
}

function print_text_field($v) {
	foreach($v as $args) {
		$value = get_option( $args['option'], '');
		$sts = '';
		if(isset($args['width']) && is_numeric($args['width'])){
			$sts = ' style="width: '.$args['width'].'em"';
		}

		$type = 'text';

		if(isset($args['type'])) {
			if(in_array($args['type'], array('email', 'number', 'url'))) {
				$type = $args['type'];
			}
		}

		$ph = '';

		if(isset($args['placeholder'])) {
			$ph = ' placeholder="'.$args['placeholder'].'"';
		}
		echo '<div style="margin-bottom: 20px">';
		if(isset($args['label']) && $args['label'] != '') {
			echo '<div class="label"><strong style="opacity: 0.7;">'.$args['label'].'</strong></div>';
		}
		echo '<input id="'.$args['id'].'" type="'.$type.'" name="'.$args['option'].'" value="'.$value.'"'.$sts.$ph.' />';
		echo (isset($args['tooltip']) && $args['tooltip'] != '' ? '<p class="instruction">'.$args['tooltip'].'</p>' : '');
		echo '</div>';
	}
}

function print_text_repeater($v) {
	if(get_option($v['option']) != '') {
		$option = json_decode(html_entity_decode(get_option($v['option']), ENT_QUOTES), true);
		$optionjson = get_option($v['option']);
	}
	else {
		$option = array();
		$optionjson = '[]';
	}
	$sts = '';
	if(isset($v['width']) && is_numeric($v['width'])){
		$sts = ' style="width: '.$v['width'].'em"';
	}

	ob_start();
	?>
	<div style="margin-bottom: 20px;">
		<?php
		if(isset($v['label']) && $v['label'] != '') {
			?>
			<div class="label"><strong style="opacity: 0.7;"><?=$v['label']?></strong></div>
			<?php
		}

		?>
		<div id="<?=$v['id']?>" class="repeater_option">
			<input type="hidden" class="repeater-final" name="<?=$v['option']?>" value="<?=$optionjson?>"/>
			<table class="custom-repeater-options" cellspacing="0" cellpadding="0"<?=$sts?>>
				<tbody>
					<tr>
						<td class="left hdg"><strong>Platform</strong></td>
						<td class="right hdg"><strong>API Key</strong></td>
						<td><span class="removal-button button not-active"><span class="dashicons dashicons-no-alt"></span><span>Remove</span></span></td>
					</tr>
					<?php
					if(sizeof($option) >= 1) {
						foreach($option as $op) {
						?>
							<tr>
								<td class="left">
									<input type="text" class="repeater-head" value="<?=$op['api']?>" style="width: 100%;">
								</td>
								<td class="right">
									<input type="text" class="repeater-value" value="<?=$op['key']?>" style="width: 100%;">
								</td>
								<td style="vertical-align: middle;">
									<span class="removal-button button"><span class="dashicons dashicons-no-alt"></span><span>Remove</span></span>
								</td>
							</tr>
						<?php
						}
					}
					?>
				</tbody>
			</table>
			<div style="padding-left: 5px;"><span class="button add-option"><?=$v['button']?></span></div>
		</div>
	</div>
	<?php

	echo ob_get_clean();
}

function print_par_field($args) {
	$value = get_option( $args['option'], '');
	$sts = array();
	if(isset($args['width']) && is_numeric($args['width'])){
		array_push($sts, 'width: '.$args['width'].'em;');
	}
	
	if(isset($args['resize']) && !$args['resize']){
		array_push($sts, 'resize: none;');
	}

	if(sizeof($sts) > 0) {
		$sts = 'style="'.implode(' ', $sts).'"';
	}

	$height = 4;
	if(isset($args['height']) && is_numeric($args['height']) && $args['height'] > 0) {
		$height = $args['height'];
	}

	$ph = '';

	if(isset($args['placeholder'])) {
		$ph = ' placeholder="'.$args['placeholder'].'"';
	}

	echo '<textarea name="'.$args['option'].'" id="'.$args['id'].'" rows="'.$height.'"'.$sts.$ph.'>'.$value.'</textarea>';
	echo (isset($args['tooltip']) && $args['tooltip'] != '' ? '<p class="instruction">'.$args['tooltip'].'</p>' : '');
}


function print_options_field($args) {
	$value = get_option( $args['option'], '');
	$sts = '';
	if(isset($args['width']) && is_numeric($args['width'])){
		$sts = ' style="width: '.$args['width'].'em"';
	}
	echo '<select id="'.$args['id'].'" name="'.$args['option'].'"'.$sts.'>';

	foreach($args['choices'] as $option) {
		if($value == $option['value']) {
			echo '<option value="'.$option['value'].'" selected>'.$option['label'].'</option>';
		}
		else{
			echo '<option value="'.$option['value'].'">'.$option['label'].'</option>';
		}
	}

	echo '</select>';

	echo (isset($args['tooltip']) && $args['tooltip'] != '' ? '<p class="instruction">'.$args['tooltip'].'</p>' : '');
}

function register_general_settings_fields(){
	add_settings_section(
		'logo_identity_section',
		'Images and Identity',
		'settings_empty',
		'general'
	);

	add_settings_section(
		'seo_identity_section',
		'Search Engine Optimization',
		'settings_empty',
		'general'
	);

	add_settings_section(
		'api_keys',
		'API Keys & Others',
		'settings_empty',
		'general'
	);

	//----------- LOGO AND IDENTITY IMAGES
	register_setting('general', 'site_icon_ico', 'esc_attr');
	register_setting('general', 'site_icon_png', 'esc_attr');
	add_settings_field('site_icon', '<label for="favInput">'.__('Shortcut Icon' , 'site_icon' ).'</label>' , 'print_image_field', 'general', 'logo_identity_section', array(
		array(
			'option' => 'site_icon_ico',
			'id' => 'favInput',
			'alt' => 'ICO Icon',
			'button' => '[act] ICO Icon',
			'width' => 80,
			'height' => 80,
			'tooltip' => 'This attribute sets the icon on the left of the Website Name on the viewer\'s title bar. (Internet Explorer)',
			'file_types' => array('image/x-icon')
		),
		array(
			'option' => 'site_icon_png',
			'id' => 'favInput-png',
			'alt' => 'PNG Icon',
			'button' => '[act] PNG Icon',
			'width' => 80,
			'height' => 80,
			'tooltip' => 'This attribute sets the icon on the left of the Website Name on the viewer\'s title bar. (All other browsers)',
			'file_types' => array('image/png')
		)
	));

	register_setting('general', 'logo_svg', 'esc_attr');
	register_setting('general', 'logo_png', 'esc_attr');
	add_settings_field('site_logo', '<label for="favLogo">'.__('Website Logo' , 'site_logo' ).'</label>' , 'print_image_field', 'general', 'logo_identity_section', array(
		array(
			'option' => 'logo_svg',
			'id' => 'favLogo',
			'alt' => 'Vector Icon',
			'button' => '[act] Vector Icon',
			'width' => 400,
			'height' => 113,
			'tooltip' => 'This attribute sets the logo found on the navigation bar. (Modern Browsers).',
			'file_types' => array('image/svg+xml', 'application/svg+xml')
		),
		array(
			'option' => 'logo_png',
			'id' => 'favLogo-png',
			'alt' => 'PNG Icon',
			'button' => '[act] PNG Icon',
			'width' => 400,
			'height' => 113,
			'tooltip' => 'This attribute sets the logo found on the navigation bar. (Older Browsers).',
			'file_types' => array('image/png')
		)
	));

	register_setting('general', 'api-keys', 'esc_attr');
	add_settings_field('site-keys', '<label for="sitename-mapi">'.__('API Key List' , 'API Key List' ).'</label>' , 'print_text_repeater', 'general', 'api_keys',array(
		'option' => 'api-keys',
		'id' => 'sitename-mapi',
		'width' => 60,
		'tooltip' => 'API Keys for integration',
		'button' => 'Add API Key'
	));

	$pagelist = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'page'
	));

	$allp = array();


	foreach($pagelist as $p) {
		if($p->ID != get_option('page_on_front') && $p->ID != get_option('page_for_posts')) {
			array_push($allp, array(
				'value' => $p->ID,
				'label' => $p->post_title
			));
		}
	}

	register_setting('general', 'page_for_lost', 'esc_attr');
	add_settings_field('page_for_lost', '<label for="lostpage">'.__('Page Displayed when Lost' , 'Page Displayed when Lost' ).'</label>' , 'print_options_field', 'general', 'api_keys',array(
		'option' => 'page_for_lost',
		'id' => 'lostpage',
		'width' => 60,
		'tooltip' => 'The page that is seen when a user enters a URL that does not exist.',
		'choices' => $allp
	));
	//------SEO DEFAULTS

	register_setting('general', 'seo_name', 'esc_attr');
	add_settings_field('site_name', '<label for="sitename-head">'.__('Site Name' , 'site_name' ).'</label>' , 'print_text_field', 'general', 'seo_identity_section', array(
		array(
			'option' => 'seo_name',
			'id' => 'sitename-head',
			'width' => 22,
			'tooltip' => 'The text that appears as the site\'s name in the title bar of every page.',
			'placeholder' => get_bloginfo('name')
		)
	));

	register_setting('general', 'seo_description', 'esc_attr');
	add_settings_field('site_desc', '<label for="site_descr">'.__('Site Description' , 'site_descr' ).'</label>' , 'print_par_field', 'general', 'seo_identity_section', array(
		'option' => 'seo_description',
		'id' => 'site_descr',
		'width' => 22,
		'height' => 2,
		'resize' => false,
		'placeholder' => get_bloginfo('description'),
		'tooltip' => 'The text that appears under the description of this page when being searched by Google&trade; or other search engines.'
	));

	register_setting('general', 'site_icon_og', 'esc_attr');
	add_settings_field('og_icon', '<label for="ogInput">'.__('Website Thumbnail' , 'og_icon' ).'</label>' , 'print_image_field', 'general', 'seo_identity_section', array(
		array(
			'option' => 'site_icon_og',
			'id' => 'ogInput',
			'alt' => 'Image File',
			'button' => '[act] Website Thumbnail',
			'width' => 400,
			'height' => 210,
			'tooltip' => 'This attribute sets the icon or thumbnail of this website when shared across various social media platforms and/or websites. For best results, upload aa JPG / PNG image with 1200px width and 630px height.',
			'file_types' => array('image/png', 'image/jpg', 'image/jpeg')
		),
	));


	//========== READING
	

	$psts = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'page',
		'order' => 'ASC',
		'orderby' => 'title'
	));

	$link_choices = array(
		array(
			'value' => '---',
			'label' => 'Disable Link'
		)
	);

	foreach($psts as $plst) {
		array_push($link_choices, array(
			'value' => $plst->ID,
			'label' => $plst->post_title
		));
	}

	register_setting('reading', 'footer_link_target', 'esc_attr');
	add_settings_field('footer_link_url', '<label for="f-lurl">'.__('Footer Link Target' , 'footer_link_url' ).'</label>' , 'print_options_field', 'reading', 'footer_top', array(
		'option' => 'footer_link_target',
		'id' => 'f-lurl',
		'width' => 22,
		'tooltip' => 'The page this link points to.',
		'choices' => $link_choices
	));

	//----
	register_setting('general', 'footer_entity_name', 'esc_attr');
	add_settings_field('entity_name', '<label for="sitename-head">'.__('Entity Name' , 'entity_name' ).'</label>' , 'print_text_field', 'reading', 'footer_details', array(
		array(
			'option' => 'footer_entity_name',
			'id' => 'entity_name',
			'width' => 22,
			'tooltip' => 'The text that appears as the entity name in the footer of every page.',
			'placeholder' => get_bloginfo('name')
		)
	));

	register_setting('reading', 'footer_text_block_l', 'esc_attr');
	add_settings_field('c_details_l', '<label for="textblock_l">'.__('Text Block (Left)' , 'textblock_l' ).'</label>' , 'print_par_field', 'reading', 'footer_details', array(
		'option' => 'footer_text_block_l',
		'id' => 'textblock_l',
		'width' => 22,
		'height' => 4,
		'resize' => false,
		'tooltip' => 'The text that appears under the entity name in the footer (Left).'
	));

	register_setting('reading', 'footer_text_block_r', 'esc_attr');
	add_settings_field('c_details_r', '<label for="textblock_r">'.__('Text Block (Right)' , 'textblock_r' ).'</label>' , 'print_par_field', 'reading', 'footer_details', array(
		'option' => 'footer_text_block_r',
		'id' => 'textblock_r',
		'width' => 22,
		'height' => 4,
		'resize' => false,
		'tooltip' => 'The text that appears under the entity name in the footer (Right).'
	));

	register_setting('reading', 'footer_copyline', 'esc_attr');
	add_settings_field('copyline', '<label for="sitename-head">'.__('Entity Name' , 'copyline' ).'</label>' , 'print_text_field', 'reading', 'footer_details', array(
		array(
			'option' => 'footer_copyline',
			'id' => 'copyline',
			'width' => 28,
			'tooltip' => 'The text that appears as the copy line in the footer of every page. Enclose the starting<br>year with a \'y\' and brackets to automatically update the year via the framework.<br>Example: &copy;[y2016] Company Name.',
			'placeholder' => '&copy;'.date('Y').' '.get_bloginfo('name').'. All Rights Reserved.'
		)
	));
}

function settings_empty($arg) {};

function settings_nav($arg) {
	$navleft = (get_option('nav-left') == false || is_null(get_option('nav-left')) || get_option('nav-left') == '' ? array() : json_decode(html_entity_decode(get_option('nav-left')), true));
	$navright = (get_option('nav-right') == false || is_null(get_option('nav-right')) || get_option('nav-right') == '' ? array() : json_decode(html_entity_decode(get_option('nav-right')), true));
	$navregister = (get_option('nav-register') == false || is_null(get_option('nav-register')) || get_option('nav-register') == '' ? array() : json_decode(html_entity_decode(get_option('nav-register')), true));

	$get_pages = get_posts(array(
		'post_type' => 'page',
		'posts_per_page' => -1,
		'order_by' => 'title',
		'order' => 'ASC',
		'post__not_in' => array(get_option('page_for_posts'), get_option('page_on_front'))
	));
	$page_options = array();
	$pages_on_left = array();
	$pages_on_right = array();
	$page_on_register = array();

	foreach($get_pages as $g){
		$taken = false;
		if(sizeof($navleft) > 0) {
			foreach($navleft as $n) {
				if($g->ID == $n) {
					$taken = true;
					array_push($pages_on_left, '<span class="page-handle" data-value="'.$g->ID.'"><span class="dashicons dashicons-menu"></span>'.$g->post_title.'</span>');
				}
			}
		}

		if(sizeof($navright) > 0) {
			foreach($navright as $n) {
				if($g->ID == $n) {
					$taken = true;
					array_push($pages_on_right, '<span class="page-handle" data-value="'.$g->ID.'"><span class="dashicons dashicons-menu"></span>'.$g->post_title.'</span>');
				}
			}
		}

		if(sizeof($navregister) > 0) {
			foreach($navregister as $n) {
				if($g->ID == $n) {
					$taken = true;
					array_push($page_on_register, '<span class="page-handle" data-value="'.$g->ID.'"><span class="dashicons dashicons-menu"></span>'.$g->post_title.'</span>');
				}
			}
		}

		if(!$taken) {
			array_push($page_options, '<span class="page-handle" data-value="'.$g->ID.'"><span class="dashicons dashicons-menu"></span>'.$g->post_title.'</span>');
		}
	}
	?>
	<p class="instruction">Pages that can be sorted into navigation areas are listed below. Pages under 'Left Navigation' will be listed on the left side of the logo on the front end layout. Pages list under 'Right Navigation' will be listed on the right side of the logo on the front end layout. The page listed under 'Bookmarked Page' will be the page the user arrives to when clicking the link on the right of the header. To re-sort pages, use the 3-line handle on the left of a 'Page' to your desired location.</p>
	<input type="hidden" id="nav-left" value="<?php echo get_option('nav-left')?>" name="nav-left"/>
	<input type="hidden" id="nav-right" value="<?php echo get_option('nav-right')?>" name="nav-right"/>
	<input type="hidden" id="nav-register" value="<?php echo get_option('nav-register')?>" name="nav-register"/>
	<div id="nav-sorter">
		<div class="l-t title">Unmapped Pages</div>
		<div class="options-avail for-sorter">
			<?php echo implode('', $page_options)?>
		</div>
		<div class="m-t title">Left Navigation</div>
		<div class="sort-left for-sorter">
			<?php echo implode('', $pages_on_left)?>
		</div>
		<div class="r-t title">Right Navigation</div>
		<div class="sort-right for-sorter">
			<?php echo implode('', $pages_on_right)?>
		</div>
		<div class="rr-t title">Bookmarked Page</div>
		<div class="sort-register for-sorter">
			<?php echo implode('', $page_on_register)?>
		</div>
	</div>
	<?php
}

function settings_ext_link_l() {
	?>
	<p class="instruction">Configures the external link (left) found at the footer.</p>
	<?php
}
function settings_ext_link_r() {
	?>
	<p class="instruction">Configures the external link (right) found at the footer.</p>
	<?php
}
?>