<?php
function ng_template($id) {
	$p = get_post($id);

	if($p->post_type == 'page') {
		$g = get_page_template_slug($id);

		if($g == '') {
			return 'v-page';
		}
		else {
			$g = explode('.', $g);

			return 'v-'.$g[0];
		}
	}
	else{
		return 'p-'.$p->post_type;
	}
}

?>