<?php
include_once('config.php');
if(file_exists (DOCROOT.'/_bin/wp-config.php')) {
	ob_start();
?>

				<div class="title">
					Installation Exists
				</div>
				<div class="copy">
					<p>
						There seems to be an installation already setup for this instance. It is recommended that this file be deleted in order to preserve website security.
					</p>
				</div>
			<?php
	$rendered = ob_get_clean();
}
else if(!file_exists (DOCROOT.'/_bin/wp-load.php')) {
	ob_start();
?>

				<div class="title">
					Pre-Setup Installation
				</div>
				<div class="copy">
					<p>
						Welcome to the getting started step of setting up this platform. This is an attempt to automate the setup process to be as streamlined as possible.
					</p>
					<p>
						Through this step we aim to create the necessary folders, download the WordPress&trade; installation, unzip the archive and move on the WordPress setup proper.
					</p>
					<p>
						If you wish to go through the setup process manually, please refer to the <a href="https://github.com/gravitycircle/scaffold" target="_blank">github page</a> for this framework for more information.
					</p>
					<p style="text-align: center; padding-top: 1em;">
						<span id="begin" class="cta" d-target="<?=BASE?>">Start the Process</span>
					</p>
				</div>
			<?php
	$rendered = ob_get_clean();
}
else{
	ob_start();
?>

				<div class="title">
					Pre-Setup Complete
				</div>
				<div class="copy">
					<p>The next step from this point forward is to setup WordPress&trade;. Do not forget to do the following after your Wordpress installation is complete:</p>
					<ul>
						<li>Download all the necessary plugins. You may use the plugins folder located at <span class="code">php/plugins</span> to manually install the plugins listed below:</strong>
							<ul>
								<li><a href="https://wordpress.org/plugins/advanced-custom-fields/">Advanced Custom Fields</a></li>
								<li><a href="https://wordpress.org/plugins/classic-editor/">Classic Editor</a></li>
								<li><a href="https://wordpress.org/plugins/wp-migrate-db/">WP Migrate DB</a></li>
							</ul>
						</li>
						<li>Add in <span class="code">include_once(str_replace('/_bin', '', dirname(__FILE__)).'/setup.php');</span> in <span class="code">wp-config.php</span> right after <span class="code">define('DB_COLLATE', '');</span>.</li>
						<li>If you haven't already, install the plugins listed above via the WordPress admin panel: <span class="code">Plugins > Add New</span></li>
						<li>Activate the theme and all newly installed plugins.</li>
						<li>Update everything from the WordPress admin.</li>
						<li>Delete all extra themes / plugins. We're departing from those.</li>
					</ul>
					<p style="text-align: center; padding-top: 1em;">
						<a href="<?=BASE?>_bin" target="_blank" class="cta">Proceed to WordPress&trade; Setup</a>
					</p>
				</div>
			<?php
	$rendered = ob_get_clean();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Site Scaffolding & Bootstrap - Setup</title>
	<link rel="shortcut icon" href="img/shortcut-icon.ico" type="image/vnd.microsoft.icon" />
	<link rel="icon" type="image/png" href="img/shortcut-icon.png" />
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,500,600&display=swap" rel="stylesheet">
<?php
	if(!file_exists ( DOCROOT.'/_bin/wp-config.php')) {
	?>
	<script type="text/javascript" src="<?=BASE?>lib/jquery.js"></script>
	<script type="text/javascript" src="<?=BASE?>_src/setup/run.js"></script>
	<?php
	}
?>
	<link rel="stylesheet" href="css/intro.css" />
	<base href="<?=BASE?>">
</head>
<body>
	<div id="setup">
		<div class="content-area">
			<div class="content-rendered"><?=$rendered?></div>
		</div>
	</div>
</body>
</html>