<?php
function do_html($html, $title = '', $description = '', $og = false, $tw = false){
ob_start();
$gendata = $GLOBALS['data'];
$ogtext = '';
$twtext = '';
  if(!(!$og) && is_array($og)){
    foreach($og as $name=>$value){
      $ogtext .= '
	<meta property="og:'.$name.'" content="'.$value.'"/>';
    }
  }
  
  if(!(!$tw) && is_array($tw)){
    foreach($tw as $name=>$value){
      $twtext .= '
	<meta name="twitter:'.$name.'" content="'.$value.'" />';
    }
  }

	$nav = $gendata['nav'];
	$navtext = '';
	foreach($nav as $n){
		$navtext .= '
			<a href="'.$n['path'].'">'.$n['name'].'</a>';
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=($title == '' ? $gendata['site_name'] : $gendata['site_name'].' - '.$title)?></title>
	<link rel="shortcut icon" href="<?=BASE?>img/favico.ico" type="image/vnd.microsoft.icon" />
	<link rel="icon" type="image/png" href="<?=BASE?>img/favico.png" />
	<meta name="description" content="<?=$description?>">			<?=$ogtext?><?=$twtext?>

</head>
<body>
	<header>
		<h1><?=($title == '' ? $gendata['site_name'] : $gendata['site_name'].' - '.$title)?></h1>
		<nav>			<?=$navtext?>
	
		</nav>
	</header>
	<main>
<?=$html?>
	</main>
</body>
</html>
<?php
    return ob_get_clean();
}
?>