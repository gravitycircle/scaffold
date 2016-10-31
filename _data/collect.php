<?php
function scan_imgs(){
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
			$inspect = explode('.', $file);

			if(array_pop($inspect) != 'html'){
				array_push($filearr, BASE.$folder.'/'.$file);
			}
		}
	}

	$folder = 'img/all';
	$arr = scandir (DOCROOT.'/'.$folder.'/');
	foreach($arr as $file)
	{
		if(substr($file, 0, 1) !== '.' && substr($file, 0, 6) !== 'favico')
		{
			if(array_pop($inspect) != 'html'){
				array_push($filearr, BASE.$folder.'/'.$file);
			}
		}
	}

	return $filearr;
}

function scan_templates(){
	
}
?>