<?php
function verify_text_entry($text, $pattern) {
	if($text != '') {
		$validpatterns = ['phone', 'postal-code', 'email', 'url', 'name'];

		if(in_array($pattern, $validpatterns)) {
			switch ($pattern) {
				case "phone":
					if(preg_match('/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/', $text)) {
						return 'ok';
					}
					else{
						return 'Invalid phone number.';
					}
				break;
				case "postal-code":
					if(preg_match('/(\d{5}([\-]\d{4})?)/', $text) || preg_match('/[A-Za-z][0-9][A-Za-z]\s?[0-9][A-Za-z][0-9]/', $text) || preg_match('/(\d{3,6})/', $text)) {
						return 'ok';
					}
					else{
						return 'Invalid postal code.';
					}
				break;
				case "email":
					if(filter_var($text, FILTER_VALIDATE_EMAIL)) {
						return 'ok';
					}
					else{
						return 'Not a valid email address.';
					}
				break;
				case "url":
					if(filter_var($text, FILTER_VALIDATE_URL)) {
						return 'ok';
					}
					else{
						return 'Not a valid url.';
					}
				break;
				case "name":
					if(preg_match('/^[\D]+.+$/', $text)) {
						return 'ok';
					}
					else{
						return 'Invalid name.';
					}
				break;
			}
		}
		else if(strpos($pattern, 'min') === 0) {
			$checker = explode('/', $pattern);

			if($checker[0] == 'min' && is_numeric($checker[1])) {
				if(strlen($text) >= $checker[1]) {
					return 'ok';
				}
				else{
					return 'Must be at least '.$checker[1].' character'.($checker[1] === 1 ? '' : 's');
				}
			}
			else{
				return 'ok';
			}
		}
		else if(strpos($pattern, 'max') === 0) {
			$checker = explode('/', $pattern);

			if($checker[0] == 'max' && is_numeric($checker[1])) {
				if(strlen($text) <= $checker[1]) {
					return 'ok';
				}
				else{
					return 'Must be at most '.$checker[1].' character'.($checker[1] === 1 ? '' : 's');
				}
			}
			else{
				return 'ok';
			}
		}
		else{
			return 'ok';
		}
	}
	else{
		return 'ok';
	}
}
?>