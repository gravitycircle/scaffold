<?php
$text = $_GET['text'];
$pattern = $_GET['pattern'];
switch ($pattern) {
	case "phone":
		if(preg_match('/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/', $text)) {
			echo 'ok';
		}
		else{
			echo 'Invalid phone number.';
		}
	break;
	case "postal-code":
		if(preg_match('/(\d{5}([\-]\d{4})?)/', $text) || preg_match('/[A-Za-z][0-9][A-Za-z]\s?[0-9][A-Za-z][0-9]/', $text) || preg_match('/(\d{3,6})/', $text)) {
			echo 'ok';
		}
		else{
			echo 'Invalid postal code.';
		}
	break;
	case "email":
		if(filter_var($text, FILTER_VALIDATE_EMAIL)) {
			echo 'ok';
		}
		else{
			echo 'Not a valid email address.';
		}
	break;
	case "url":
		if(filter_var($text, FILTER_VALIDATE_URL)) {
			echo 'ok';
		}
		else{
			echo 'Not a valid url.';
		}
	break;
	case "name":
		if(preg_match('/^[\D]+.+$/', $text)) {
			echo 'ok';
		}
		else{
			echo 'Invalid name.';
		}
	break;
}
?>