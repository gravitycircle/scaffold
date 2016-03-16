<?php
$docroot = realpath((getenv('DOCUMENT_ROOT') && ereg('^'.preg_quote(realpath(getenv('DOCUMENT_ROOT'))), realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__))));
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
?>