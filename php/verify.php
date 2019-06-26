<?php
include_once('../config.php');
include_once('keygen.php');
include_once(DOCROOT.'/_data/collate.php');
include_once('text-verification.php');
if(isset($_GET['key']) && degenerate($_GET['key'])) {
	if(isset($_GET['verify']) && isset($_GET['page'])){
		$ch = curl_init();

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$qstring = 'secret='.get_field('recap-secret', $_GET['page']).'&response='.$_GET['verify'];

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		$result = json_decode(curl_exec($ch), true);

		// $result['success'] = true;

		if(!$result['success']){
			$fail = array(
				array(
					'id' => 'captcha-error',
					'error' => 'Captcha challenge failed.'
				)
			);
		}
		else{
			//captcha verified!
			$fail = array();
			$input = json_decode(file_get_contents("php://input"), true);

			$fields = ng_get_fields($_GET['page'], false);

			$rdpage = get_field('redirect-to-page', $_GET['page']);

			if($rdpage) {
				$x = get_field('redirect-page', $_GET['page']);

				if(!is_object($x)) {
					$x = get_post($x); 
				}

				$redirect = BASE.$x->post_name;
			}
			else{
				$redirect = false;
			}

			$fail = array();
			foreach($input as $k => $v) {
				if($v === false){
					array_push($fail, array(
						'id' => $k,
						'error' => 'This field is required.'
					));
				}
				else{
					foreach($fields as $field){
						if($field['id'] == $k){
							if($field['type'] == 'email' || $field['type'] == 'text') {
								if($field['type'] == 'email') {
									$res = verify_text_entry($v, 'email');
								}
								else{
									$res = verify_text_entry($v, $field['verify']);
								}

								if($res != 'ok') {
									array_push($fail, array(
										'id' => $k,
										'error' => $res
									));
								}
							}
							break;
						}
					}
				}
			}
		}
		

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode(array(
			'action' => $redirect,
			'errors' => $fail
		));
	}
}
?>