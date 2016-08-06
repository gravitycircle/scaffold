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

define('SMTPUSER', '@');
define('SMTPPW', '@');
define('SMTPHOST', 'mail.'.str_replace('/', '', DOMAIN));
define('SMTPPORT', 587);
define('GOOGLEAPI', 'AIzaSyAw6KL4jOFvIkIV3f5Oz9mRkZ0gG2iHOyw');
?>