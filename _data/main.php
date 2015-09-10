<?php
include_once('../config.php');
$arr = scandir ('../img/');
$filearr = array();
$iter = 0;
foreach($arr as $file)
{
	if(substr($file, 0, 1) !== '.' && substr($file, 0, 6) !== 'favico')
	{
		$filearr[sizeof($filearr)] = array(
			'name' => $file.'-'.$iter,
			'url' => 'img/'.$file
		);

		$iter++;
	}
}
$output['preload'] = $filearr;
header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
header('Content-type: application/json');
echo json_encode($output);
?>