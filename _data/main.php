<?php
include_once('../config.php');
$output = array();
$filearr = array();

if(isset($_GET['svg']) && $_GET['svg'] == 'true'){
	$folder = 'img/svg';
}
else{
	$folder = 'img/png';
}

$arr = scandir (DOCROOT.'/'.$folder.'/');
foreach($arr as $file)
{
	if(substr($file, 0, 1) !== '.' && substr($file, 0, 6) !== 'favico')
	{
		array_push($filearr, BASE.$folder.'/'.$file);
	}
}

$folder = 'img/all';
$arr = scandir (DOCROOT.'/'.$folder.'/');
foreach($arr as $file)
{
	if(substr($file, 0, 1) !== '.' && substr($file, 0, 6) !== 'favico')
	{
		array_push($filearr, BASE.$folder.'/'.$file);
	}
}
$output['preload'] = $filearr;
header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
header('Content-type: application/json');
echo json_encode($output);
?>