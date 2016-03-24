<?php
$raw = $GLOBALS['data'];
$meta = $raw['meta']['lost'];
ob_start();
?>
		
<?php
$html = ob_get_clean();
$title = $meta['title'];
$description = $meta['description'];
$og = $meta['og'];
$tw = $meta['tw'];
echo do_html($html, $title, $description, $og, $tw);
?>