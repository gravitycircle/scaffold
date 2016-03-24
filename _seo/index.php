<?php
function _seo($page){
include_once(DOCROOT.'/_seo/partials/boilerplate.php');
ob_start();
$get_pages = array();

foreach($GLOBALS['data']['meta'] as $pg => $x){
	if($pg != 'lost'){
		array_push($get_pages, $pg);
	}
}

if(sizeof($get_pages) < 1){
	$page = 'lost';
}

include_once(DOCROOT.'/_seo/pages/'.$page.'.php');

return ob_get_clean();
}
?>