<?php
include_once('../../config.php');
include_once('../keygen.php');
include_once('../../_data/content.php');
if(isset($_GET['key']) && degenerate($_GET['key'])){
	if(isset($_GET['send'])){
		$input = json_decode(file_get_contents("php://input"), true);
		$final = array();
		foreach($input as $id => $va) {
			$match = get_match($id, $va, '', true);
			$final[$match['key']] = $match['value'];
		}


		$result = array();

		foreach ($final as $key => $val) {
			$keyParts = preg_split('/[\[\]]+/', $key, -1, PREG_SPLIT_NO_EMPTY);

			$ref = &$result;

			while ($keyParts) {
				$part = array_shift($keyParts);

				if (!isset($ref[$part])) {
					$ref[$part] = array();
				}

				$ref = &$ref[$part];
			}

			$ref = $val;
		}

		$output = http_build_query($result);

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode(array(
			'success' => false,
			'debug' => $result
		));
	}
}
?>