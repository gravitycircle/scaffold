<?php
include_once('../config.php');
header("Content-type: text/javascript");
?>
(function() {
var cfg = angular.module("configurator", []);

cfg.factory('constants', function(){
	return {
		canonical: '<?=CANONICAL?>'
	};
});

<?php
include_once('config.js');
?>
})();