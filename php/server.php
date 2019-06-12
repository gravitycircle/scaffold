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

function descriptionGenerator($descstring) {
	if(strlen($descstring) < 157 ) {
		return $descstring;
	}

	$pos=strpos($descstring, ' ', 157);

	$newstr = substr($descstring, 0, $pos);

	if($newstr != $descstring) {
		return $newstr.'&hellip;';
	}

	return $pos;
}
use MatthiasMullie\Minify;


function _build($scripts, $styles){
//isolate query string
$qstring = explode('?', SSL."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$query = array();
$request = str_replace(BASE, "", array_shift($qstring));
if(sizeof($qstring) >= 1) {
	foreach	(explode('&',$qstring[0]) as $q) {
		$qstr = explode('=', $q);
		$query[$qstr[0]] = $qstr[1];
	}
}

$query = json_encode($query);

if($request == 'config.js' || $request == 'config.debug.js'){
	$filearr = array();

	ob_start();
	scanTemplates(DOCROOT.'/shadow', DOCROOT.'/shadow');
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
			var dynamic = {};
			return {
				canonical: '<?=CANONICAL?>',
				base: '<?=BASE?>',
				api: '<?=generate(APIKEY)?>',
				templates: <?=json_encode($filearr)?>,
				protocol: '<?=SSL?>',
				debug_mode: <?=DEBUG_MODE ? 'true' : 'false'?>,
				append : function(name,obj) {
					if(typeof dynamic[name] == 'undefined') {
						dynamic[name] = obj;
					}
				},
				retrieve: function(name) {
					return dynamic[name];
				}
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
	$seconds_to_cache = 2592000;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";

	header("Content-type: text/javascript");
	header("Expires: $ts");
	header("Pragma: cache");
	header("Cache-Control: max-age=$seconds_to_cache");
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
		if(file_exists(DOCROOT.'/scr/'.$library['src'])){
			$js .= file_get_contents(DOCROOT.'/scr/'.$library['src']);
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
	$seconds_to_cache = 2592000;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
	
	header("Content-type: text/javascript");
	header("Expires: $ts");
	header("Pragma: cache");
	header("Cache-Control: max-age=$seconds_to_cache");
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
	
// build metadata
	if($uridata[0] == '') {
		$metadata_full = $gen_data['contents']['home']['metadata'];
	}
	else{
		$alltypes = get_post_types(array(
			'public' => true,
			'_builtin' => false
		), 'objects');

		$cpts = array(
			'post' => 'post'
		);

		foreach($alltypes as $at) {
			if($at->name != 'forms' ){
				$cpts[$at->name] = $at->rewrite['slug'];
			}
		}

		if($uridata[0] == 'debug') {
			if(!isset($uridata[1])){
				$target = 'home';
			}
			else{
				if($uridata[1] == '') {
					$target = 'home';
				}
				else{
					$target = $uridata[1];
				}
			}
		}
		else{
			$target = $uridata[0];
		}



		if(!isset($gen_data['contents'][$target])){
			//if page dont exist, resolve cpt

			$urlparts = explode('/', rtrim($request, '/'));
			$b = false;
			foreach($cpts as $k=>$rw) {
				if($urlparts[0] == $rw) {
					// post_name__in
					if(sizeof($urlparts) > 1) {
						$allposts = get_posts(array(
							'post_type' => $k,
							'post_name__in' => $urlparts[2]
						));

						if(!(!$allposts || sizeof($allposts) < 1)) {
							foreach($allposts as $p) {
								$thep = new REST_output($p->ID, 'meta');

								$metadata_full = $thep->toObject();
								$b = true;
								break;
							}
						}
					}
					else {
						$pt_obj = get_post_type_object($k);
						$thumbnail = wp_get_attachment_image_src(get_option('site_icon_og'), 'full');
						$metadata_full = array(
							'title' => $pt_obj->label.' &lsaquo; '.get_bloginfo('name'),
							'description' => $pt_obj->description,
							'og' => array(
								'type' => 'article',
								'title' => $pt_obj->label.' &lsaquo; '.get_bloginfo('name'),
								'description' => $pt_obj->description,
								'site_name' => get_bloginfo('name'),
								'url' => BASE,
								'image' => $thumbnail[0],
								'image:width' => $thumbnail[1],
								'image:height' =>$thumbnail[2]
							),
							'tw' => array(
								'card' => 'summary_large_image',
								'title' => $pt_obj->label.' &lsaquo; '.get_bloginfo('name'),
								'image' => $thumbnail[0],
								'width' => $thumbnail[1],
								'height' => $thumbnail[2],
								'description' => $pt_obj->description
							)
						);
						$b = true;
					}
					break;
				}
			}



			if(!$b) {
				$metadata_full = $gen_data['contents']['lost']['metadata'];

				// print_r($gen_data);
			}
			else{

			}
		}
		else{
			$metadata_full = $gen_data['contents'][$target]['metadata'];

		}
	}

	ob_start();
?>
<!DOCTYPE html>
<html lang="en" ng-app="main">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="theme-color" content="#ffffff">
	<link rel="icon" sizes="192x192" type="image/png" href="<?=wp_get_attachment_url(get_option('site_icon_png'))?>" />
	<link rel="shortcut icon" href="<?=wp_get_attachment_url(get_option('site_icon_ico'))?>" type="image/vnd.microsoft.icon" />
	<title><?=$metadata_full['title']?></title>
	<meta name="description" content="<?=descriptionGenerator($metadata_full['description'])?>"/>
<?php
	foreach($metadata_full['tw'] as $twtag => $twval) {
?>
	<meta name="twitter:<?=$twtag?>" content="<?=$twval?>"/>
<?php		
	}
	foreach($metadata_full['og'] as $ogtag => $ogval) {
?>
	<meta property="og:<?=$ogtag?>" content="<?=$ogval?>"/>
<?php
	}
?>
<?php
if(!$search_engine){
	if(DEBUG_MODE) {
		$topush = array();

		foreach($scripts['libraries'] as $library) {
			array_push($topush, BASE.'lib/'.$library);
		}
		array_push($topush, BASE.'config.debug.js');
		foreach($scripts['extensions'] as $library) {
			array_push($topush, BASE.'ext/'.$library);
		}
		foreach($scripts['scripts'] as $library) {
			if($library['predeployed']){
				array_push($topush, BASE.'scr/'.$library['src']);
			}
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
	foreach($scripts['externals'] as $library) {
		if(sizeof($library['attributes']) > 0) {
			$attrrender = array('');
			foreach($library['attributes'] as $ind => $rattr) {
				if($rattr != null) {
					array_push($attrrender, $ind.'="'.$rattr.'"');
				}
				else{
					array_push($attrrender, $ind);
				}
			}
		}
		else{
			$attrrender = array();
		}


		if($library['predeployed']){
?>
	<script type="text/javascript" src="<?=$library['src']?>"<?=implode(' ', $attrrender)?>></script>
<?php
		}
		else{
			if(!DEBUG_MODE){
?>
	<script type="text/javascript" src="<?=$library['src']?>"<?=implode(' ', $attrrender)?>></script>
<?php
			}
		}
	}

?>
	<link rel="stylesheet" href="<?=BASE?>css/style.css" />
<?php
	if(sizeof($styles) > 0 ){
		foreach($styles as $im) {
?>
	<link rel="stylesheet" href="<?=$im?>" />
<?php
		}
	}

	if(sizeof($scripts['inlines']) > 0) {
		$readScr = array();

		foreach($scripts['inlines'] as $scrtxt) {
			if($scrtxt['predeployed']) {
				array_push($readScr, $scrtxt['src']);
			}
			else{
				if(!DEBUG_MODE) {
					array_push($readScr, $scrtxt['src']);
				}
			}
		}
?>
	<script type="text/javascript"><?=implode("\r\n", $readScr)?></script>
<?php
	}
?>
	<base href="<?=BASE?>"><?php
}
?></head>
<body data-preload="<?=$target == '' ? 'home' : $target?>"></body>
</html>
<?php
echo ob_get_clean();
}

}
?>