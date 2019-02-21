<?php
$docroot = str_replace('\\', '/', realpath((getenv('DOCUMENT_ROOT') && preg_match('^'.preg_quote(realpath(getenv('DOCUMENT_ROOT'))).'^', realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)))));
$servername = $_SERVER['SERVER_NAME'];

$agent = $_SERVER['HTTP_USER_AGENT'];
$browser = 'evergreen';
if(strpos(strtolower($agent),'edge') !== false){
	$browser = 'edge';
}
else if(strpos(strtolower($agent),'trident') !== false){
	$browser = 'internet explorer';
}



define('DOCROOT', dirname(__FILE__));
define('BROWSER', $browser);
define('SSL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://'));
define('CANONICAL', SSL.str_replace($docroot, $servername, dirname(__FILE__)).'/');
define('BASE', SSL.str_replace($docroot, $servername, dirname(__FILE__)).'/');
define('DOMAIN', str_replace(SSL, '', BASE));
//up to 12 digit hex only. ty.
define('APIKEY', 'ee9616833955');


define('DEBUG_MODE', true);

if(SSL != 'https://') {
	$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
?>