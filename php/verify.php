<?php
include_once('../config.php');
include_once('keygen.php');
include_once('../_data/content.php');
include_once('text-verification.php');
if(isset($_GET['key']) && degenerate($_GET['key'])) {
	if(isset($_GET['verify'])){
		$input = json_decode(file_get_contents("php://input"), true);
		$fields = get_fields(false);
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
						if($field['type'] == 'email') {
							if(!filter_var($v, FILTER_VALIDATE_EMAIL)) {
								//not an email
								array_push($fail, array(
									'id' => $k,
									'error' => 'Invalid email format.'
								));
							}
						}
						else if($field['type'] == 'text') {
							
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
}
?>