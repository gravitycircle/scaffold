<?php
include_once('../config.php');
header("Content-type: text/javascript");
?>
(function() {
var cfg = angular.module("configurator", []);

cfg.factory('constants', function(){
	return {
		canonical: '<?=CANONICAL?>',
		base: '<?=BASE?>',
		smtp: {
			'user' : '<?=SMTPUSER?>',
			'pw' : '<?=SMTPPW?>'
		}
	};
});

<?php
include_once('config.js');
?>
})();