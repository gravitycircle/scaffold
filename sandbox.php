<?php
include_once('config.php');
include_once('_data/collate.php');

print_r(get_post_types(array(
	'public' => true,
	'_builtin' => false
), 'objects'));
?>