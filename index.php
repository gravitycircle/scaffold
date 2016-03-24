<?php
include_once('config.php');
?>
<!DOCTYPE html>
<html lang="en" ng-app="main">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Angular: Site Scaffolding & Bootstrap</title>
	<link rel="shortcut icon" href="img/favico.ico" type="image/vnd.microsoft.icon" />
	<link rel="icon" type="image/png" href="img/favico.png" />
	<link rel="stylesheet" href="css/style.css" />
	<base href="<?=BASE?>">
</head>
<body>
	<div id="wrapper">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1 style="border-bottom: 1px solid #ccc; padding-bottom: 20px"><img src="img/favico.png" alt="---"> Angular: Site Scaffolding & Bootstrap v0.4</h1>
					<p>This is the starting point of every new site you'll create using a combination of the following frameworks:</p>
					<ul>
						<li>
							<strong>Javascript</strong>
							<ul>
								<li>AngularJS v1.3.15</li>
								<li>jQuery v2.1.3</li>
							</ul>
						</li>
						<li>
							<strong>Preprocessors</strong>
							<ul>
								<li>Compass (requires Ruby and gem installation)</li>
								<li>SASS (requires Ruby and gem installation)</li>
								<li>PHP v5.6.11 (requires running on a server. Preferrably Apache 2.0)</li>
							</ul>
						</li>
						<li>
							<strong>Markup and Styling</strong>
							<ul>
								<li>HTML 5</li>
								<li>CSS</li>
							</ul>
						</li>
					</ul>
					<p>Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to:</p>
					<ul>
						<li><strong>BASE: </strong><?=BASE?></li>
						<li><strong>CANONICAL: </strong><?=CANONICAL?></li>
					</ul>
					<p>Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application.</p>
					<p>SEO has been set up. Under the <em>_seo</em> folder, the <i>index.php</i> file has an _seo function. Utilize that for your SEO needs. <i>$page</i> would be the last part of the url to track. There is no deep linking available yet.</p>
					<p>This is only the introduction page. The installation file for this scaffolding setup has not yet been configured since the framework stack is still in active development. To start working with the stack anyway, please rename this file into something different and rename the _index.php file to index.php. </p>
					
					<p><strong>This version is a fork of the base framework with Angular Maps included. The plugin is based on ng-maps and the Google Maps API.</strong></p>

					<h4 style="border-top: 1px solid #ccc; padding-top: 20px;">~ Happy Coding!</h4>
				</div>
			</div>
		</div>
	</div>
</body>
</html>