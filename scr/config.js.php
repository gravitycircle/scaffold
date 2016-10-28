<?php
include_once('../config.php');
include_once('../php/keygen.php');
require_once(DOCROOT.'/php/minifier/src/Minify.php');
require_once(DOCROOT.'/php/minifier/src/CSS.php');
require_once(DOCROOT.'/php/minifier/src/JS.php');
require_once(DOCROOT.'/php/minifier/src/Exception.php');
require_once(DOCROOT.'/php/minifier/src/Converter.php');
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
include_once('config.js');
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

use MatthiasMullie\Minify;
$shrink = new Minify\JS($js);
header("Content-type: text/javascript");
echo $shrink->minify();
?>
