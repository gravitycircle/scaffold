<?php
function _isEngine($agent){
	$crawlers = array(
		'facebookexternalhit', 'facebot', 'googlebot', 'yahoo', 'rambler', 'abachobot', 'accoona', 'croccrawler', 'dumbot', 'fast-webcrawler', 'geonabot', 'gigabot', 'lycos', 'msrbot', 'scooter', 'altavista', 'idbot', 'estyle', 'scrubby'
	);

	foreach($crawlers as $c){
		if(strpos(strtolower($agent), $c) !== false){
			return true;
		}
	}

	if(isset($_GET['_escaped_fragment_'])){
		return true;
	}

	return false;
}

function scanTemplates($dir, $base){
    $ffs = scandir($dir);
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' && substr($ff, 0, 1) !== '.'){
            
            if(is_dir($dir.'/'.$ff)){
            	scanTemplates($dir.'/'.$ff, $base.'/'.$ff);
            }
            else{
            	echo $base.'/'.$ff.'||';
            }
        }
    }
}
use MatthiasMullie\Minify;


function _build($scripts, $styles){
$request = str_replace(BASE, "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");


if($request == 'config.js' || $request == 'config.debug.js'){
	$filearr = array();

	ob_start();
	scanTemplates(DOCROOT.'/shadow', BASE.'shadow');
	$templates = explode('||', substr(ob_get_clean(), 0, -2));

	foreach($templates as $t) {
		$filedata = explode('/', $t);
		$filename = str_replace('.html', '', $filedata[(sizeof($filedata) - 1)]);
		$folder = $filedata[(sizeof($filedata) - 2)];
		$filecontent = file_get_contents($t);

		if(!isset($filearr[$folder])){
			$filearr[$folder] = array();
		}

		$filearr[$folder][$filename] = $filecontent;

	}
	ob_start();
	?>
	(function() {
	var cfg = angular.module("configurator", []);
	<?php
	include_once(DOCROOT.'/scr/config.js');
	?>
		cfg.factory('constants', function(){
			return {
				canonical: '<?=CANONICAL?>',
				base: '<?=BASE?>',
				smtp: {
					'user' : '<?=SMTPUSER?>',
					'pw' : '<?=SMTPPW?>'
				},
				api: '<?=generate(APIKEY)?>',
				templates: <?=json_encode($filearr)?>
			};
		});
	})();
	<?php
	$js = ob_get_clean();

	if($request != 'config.debug.js'){
		require_once(DOCROOT.'/php/minifier/src/Minify.php');
		require_once(DOCROOT.'/php/minifier/src/CSS.php');
		require_once(DOCROOT.'/php/minifier/src/JS.php');
		require_once(DOCROOT.'/php/minifier/src/Exception.php');
		require_once(DOCROOT.'/php/minifier/src/Converter.php');
		require_once(DOCROOT.'/php/keygen.php');

		$shrink = new Minify\JS($js);
		$js = $shrink->minify();
	}
	
	header("Content-type: text/javascript");
	echo $js;
}
else if ($request == 'library.js') {
	$time_start = microtime(true); 
	$js = '';

	foreach($scripts['libraries'] as $library) {
		if(file_exists(DOCROOT.'/lib/'.$library)){
			$js .= file_get_contents(DOCROOT.'/lib/'.$library);
		}
	}

	require_once(DOCROOT.'/php/minifier/src/Minify.php');
	require_once(DOCROOT.'/php/minifier/src/CSS.php');
	require_once(DOCROOT.'/php/minifier/src/JS.php');
	require_once(DOCROOT.'/php/minifier/src/Exception.php');
	require_once(DOCROOT.'/php/minifier/src/Converter.php');
	require_once(DOCROOT.'/php/keygen.php');

	$shrink = new Minify\JS($js);
	$js = $shrink->minify();
	$time_end = microtime(true); 
	
	header("Content-type: text/javascript");
	echo '/* BUILD: '.($time_end - $time_start).' */'."\r\n\r\n";
	echo $js;
}
else if ($request == 'script.js'){
	$time_start = microtime(true); 
	$js = '';


	foreach($scripts['extensions'] as $library) {
		if(file_exists(DOCROOT.'/ext/'.$library)){
			$js .= file_get_contents(DOCROOT.'/ext/'.$library);
		}
	}

	foreach($scripts['scripts'] as $library) {
		if(file_exists(DOCROOT.'/scr/'.$library)){
			$js .= file_get_contents(DOCROOT.'/scr/'.$library);
		}
	}

	require_once(DOCROOT.'/php/minifier/src/Minify.php');
	require_once(DOCROOT.'/php/minifier/src/CSS.php');
	require_once(DOCROOT.'/php/minifier/src/JS.php');
	require_once(DOCROOT.'/php/minifier/src/Exception.php');
	require_once(DOCROOT.'/php/minifier/src/Converter.php');
	require_once(DOCROOT.'/php/keygen.php');

	$shrink = new Minify\JS($js);
	$js = $shrink->minify();
	$time_end = microtime(true); 
	
	header("Content-type: text/javascript");
	echo '/* BUILD: '.($time_end - $time_start).' */'."\r\n\r\n";
	echo $js;
}
else{
	//args for building:
/*
$scripts = array(
	$libraries: array, list of all scripts. refer to lib folder
	$exts: array, list of all scripts. refer to ext folder
	$scripts: array, list of all scripts. refer to scr folder
	$externals: array, list of all scripts. refer to external links
	$inlines: array, list of all scripts. refer to ext folder
);

$styles: array - style urls
*/

	//build js file if not exist.
	$uridata = explode('/', $request);


	$search_engine = _isEngine($_SERVER["HTTP_USER_AGENT"]);
	$gen_data = main(false);
	
	ob_start();
?>
<!DOCTYPE html>
<html lang="en" ng-app="main">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title><?=$gen_data['site_name']?></title>
	<link rel="shortcut icon" href="<?=BASE?>img/favico.ico" type="image/vnd.microsoft.icon" />
	<link rel="icon" type="image/png" href="<?=BASE?>img/favico.png" />
<?php
if(!$search_engine){
	if($uridata[0] == 'debug') {
		$topush = array();

		foreach($scripts['libraries'] as $library) {
			array_push($topush, BASE.'lib/'.$library);
		}
		array_push($topush, BASE.'config.debug.js');
		foreach($scripts['extensions'] as $library) {
			array_push($topush, BASE.'ext/'.$library);
		}
		foreach($scripts['scripts'] as $library) {
			array_push($topush, BASE.'scr/'.$library);
		}

		foreach($topush as $jslibrary) {
?>
	<script type="text/javascript" src="<?=$jslibrary?>"></script>
<?php
		}
	}
	else{
?>
	<script type="text/javascript" src="<?=BASE?>library.js"></script>
	<script type="text/javascript" src="<?=BASE?>config.js"></script>
	<script type="text/javascript" src="<?=BASE?>script.js"></script>
<?php
	}
?>
	<link rel="stylesheet" href="<?=BASE?>css/style.css" />
	<base href="<?=BASE?>"><?php
}
?></head>
<body></body>
</html>
<?php
echo ob_get_clean();
}

}
?>