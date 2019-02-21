<?php
include_once('../config.php');
include_once('keygen.php');
$output = array(
	'key' => '',
	'status' => 'ok'
);
$p = false;
if(isset($_GET['token'])){
	$compare = degenerate($_GET['token'], true);
	if($compare == APIKEY){
		$output['key'] = generate();
		$p = true;
	}
}

//fail it!
if(!$p) {
	include_once('404.php');
}
else{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
	header('Content-type: application/json');
	echo json_encode($output);
}
?>