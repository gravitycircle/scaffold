<?php
$docroot = str_replace('\\', '/', realpath((getenv('DOCUMENT_ROOT') && preg_match('^'.preg_quote(realpath(getenv('DOCUMENT_ROOT'))).'^', realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)))));
$servername = $_SERVER['SERVER_NAME'];

//uploads folder
define('UPLOADS', 'media');
define('WP_PLUGIN_DIR', dirname(__FILE__).'/php/plugins');
define('WP_PLUGIN_URL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://').str_replace($docroot, $servername, dirname(__FILE__)).'/php/plugins');

define('WP_HOME', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://').str_replace($docroot, $servername, dirname(__FILE__)).'/_bin');
define('WP_SITEURL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://').str_replace($docroot, $servername, dirname(__FILE__)).'/_bin');

define('KLD_DOCROOT', dirname(__FILE__));

define('ALLOW_UNFILTERED_UPLOADS', true);
?>