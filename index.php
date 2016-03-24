<?php
include_once('config.php');
include_once(DOCROOT.'/_data/main.php');
include_once('php/server.php');
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
	<script type="text/javascript" src="lib/modernizr.js"></script> 
	<script type="text/javascript" src="lib/jquery.js"></script>
	<script type="text/javascript" src="lib/angular.js"></script>
	<script type="text/javascript" src="lib/transit.js"></script>
	<script type="text/javascript" src="scr/config.js.php"></script>
	<script type="text/javascript" src="scr/views.js"></script>
	<script type="text/javascript" src="scr/html.js"></script>
	<script type="text/javascript" src="scr/modes.js"></script>
	<script type="text/javascript" src="scr/main.js"></script>
	<link rel="stylesheet" href="css/style.css" />
	<base href="<?=BASE?>">
</head>
<body></body>
</html>
<?php
echo _serve(ob_get_clean());
?>