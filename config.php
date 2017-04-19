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
define('APIKEY', 'ea77d54c0fbd');

define('SMTPUSER', 'richard@kevinlouisdesign.com');
define('SMTPPW', 'K74ep47D');
define('SMTPHOST', 'mail.emailhome.com');
define('SMTPPORT', 465);
define('GOOGLEAPI', '@');
?>