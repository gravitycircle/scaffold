<?php
include_once('config.php');
include_once(DOCROOT.'/_data/collate.php');
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
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=GOOGLEAPI?>"></script>
	<script type="text/javascript" src="<?=BASE?>lib/modernizr.js"></script> 
	<script type="text/javascript" src="<?=BASE?>lib/jquery.js"></script>
	<script type="text/javascript" src="<?=BASE?>lib/angular.js"></script>
	<script type="text/javascript" src="<?=BASE?>lib/angular-maps.js"></script>
	<script type="text/javascript" src="<?=BASE?>lib/transit.js"></script>
	<script type="text/javascript" src="<?=BASE?>scr/config.js.php"></script>
	<script type="text/javascript" src="<?=BASE?>scr/views.js"></script>
	<script type="text/javascript" src="<?=BASE?>ext/html.js"></script>
	<script type="text/javascript" src="<?=BASE?>ext/comms.js"></script>
	<script type="text/javascript" src="<?=BASE?>scr/modes.js"></script>
	<script type="text/javascript" src="<?=BASE?>scr/main.js"></script>
	<link rel="stylesheet" href="<?=BASE?>css/style.css" />
	<base href="<?=BASE?>">
</head>
<body></body>
</html>
<?php
echo _serve(ob_get_clean());
?>