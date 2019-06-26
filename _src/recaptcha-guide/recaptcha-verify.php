<?php
$gResponse = $_POST['g-response'];
// this is based on your variable naming on where you stored your captcha response.
// based on the JS file I sent you, it's stored in 'g-response'.

$ch = curl_init();

$url = 'https://www.google.com/recaptcha/api/siteverify';
$qstring = 'secret=<Your "Site Secret" from the recaptcha console>&response='.$gResponse;

curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);

$result = json_decode(curl_exec($ch), true);

if($result['success']) {
	echo 'Succeeded';
}
else {
	echo 'Failed';
}
?>