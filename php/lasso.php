<?php
include_once('../config.php');
include_once('keygen.php');
include_once('../_data/content.php');
if(isset($_GET['key']) && degenerate($_GET['key'])) {
	if(isset($_GET['verify'])){
		$input = json_decode(file_get_contents("php://input"), true);
		$fields = get_fields(false);
		$fail = array();

		foreach($input as $k => $v) {
			if($v === false){
				array_push($fail, $k);
			}
			else{
				foreach($fields as $field){
					if($field['id'] == $k){
						if($field['type'] == 'email') {
							if(!filter_var($v, FILTER_VALIDATE_EMAIL)) {
								//not an email
								array_push($fail, $k);
							}
						}
						break;
					}
				}
			}
		}

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode($fail);
	}
	else if(isset($_GET['send'])) {
		$fields = get_fields(true);
		$lasso = get_lasso();
		$input = json_decode(file_get_contents("php://input"), true);

		$final = array();

		foreach($lasso as $i => $l){
			$final[$i] = $l;
		}

		$final['guid'] = $_GET['send'];

		foreach($fields as $field) {
			foreach($input as $index => $value) {
				if($index == $field['id']){
					if($index['type'] == 'text' || $index['type'] == 'paragraph'){
						$final[$field['key']] = $value;
					}
					else{

					}
				}
			}
		}

		$qstring = http_build_query($final);

		$ch = curl_init();

		$url = 'https://app.lassocrm.com/registrant_signup/';
		// $url = BASE.'php/test.php';

		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
		    // this would be your first hint that something went wrong
		    $result = 'Warning: '.curl_error($ch);
		} else {	
		    // check the HTTP status code of the request
		    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    if (intval($resultStatus) >= 200 && intval($resultStatus) < 300) {
		        // everything went better than expected
		        //file_put_contents('results.html', $result);
		        $result = 'ok';
		    } else {
		        // the request did not complete as expected. common errors are 4xx
		        // (not found, bad request, etc.) and 5xx (usually concerning
		        // errors/exceptions in the remote script execution)

		        $result = 'Fatal: '.$url.' -'.$resultStatus;
		    }
		}

		$output = array(
			'status' => 'passed',
			'data' => $result
		);

		curl_close($ch);

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode($output);
	}
}
else{
	include_once('404.php');
}
?>