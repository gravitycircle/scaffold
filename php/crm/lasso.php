<?php
include_once('../../config.php');
include_once('../keygen.php');
include_once('../../_data/content.php');
if(isset($_GET['key']) && degenerate($_GET['key'])) {
	if(isset($_GET['send'])) {
		$lasso = get_lasso();
		$input = json_decode(file_get_contents("php://input"), true);

		$final = array();

		foreach($lasso as $i => $l){
			$final[$i] = $l;
		}

		$final['guid'] = $_GET['send'];

		foreach($input as $id => $va) {
			$match = get_match($id, $va, '', true);
			$final[$match['key']] = $match['value'];
		}

		$resultarray = array();

		foreach ($final as $key => $val) {
			$keyParts = preg_split('/[\[\]]+/', $key, -1, PREG_SPLIT_NO_EMPTY);

			$ref = &$resultarray;

			while ($keyParts) {
				$part = array_shift($keyParts);

				if (!isset($ref[$part])) {
					$ref[$part] = array();
				}

				$ref = &$ref[$part];
			}

			$ref = $val;
		}

		$qstring = http_build_query($resultarray);

		$ch = curl_init();

		$url = 'https://app.lassocrm.com/registrant_signup/';
				
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			// this would be your first hint that something went wrong
			$result = array(
				'success' => false,
				'debug' => 'Warning: '.curl_error($ch)
			);
		} else {	
			// check the HTTP status code of the request
			$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (intval($resultStatus) >= 200 && intval($resultStatus) < 300) {
				// everything went better than expected
				//file_put_contents('results.html', $result);
				$result = array(
					'success' => true,
					'debug' => 'ok'
				);
			} else {
				// the request did not complete as expected. common errors are 4xx
				// (not found, bad request, etc.) and 5xx (usually concerning
				// errors/exceptions in the remote script execution)
				$result = array(
					'success' => false,
					'debug' => 'Fatal: '.$url.' -'.$resultStatus
				);
			}
		}

		curl_close($ch);

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode($result);
	}
}
else{
	include_once('404.php');
}
?>