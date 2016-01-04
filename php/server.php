<?php
function _isEngine($agent){
	$crawlers = array(
		'facebookexternalhit', 'facebot', 'googlebot', 'yahoo', 'rambler', 'abachobot', 'accoona', 'croccrawler', 'dumbot', 'fast-webcrawler', 'geonabot', 'gigabot', 'lycos', 'msrbot', 'scooter', 'altavista', 'idbot', 'estyle', 'scrubby'
	);

	foreach($crawlers as $c){
		if(strpos(strtolower($agent), $c) !== false){
			return true;
		}
	}

	if(isset($_GET['_escaped_fragment_'])){
		return true;
	}

	return false;
}

function _serve($html){
	if(_isEngine($_SERVER["HTTP_USER_AGENT"]))
	{
		$page = (isset($_GET['page']) ? $_GET['page'] : 'home');
		include_once(DOCROOT.'/_seo/index.php');
		return _seo($page);
	}
	else
	{
		return $html;
	}
}
?>