<?php
include_once('../../config.php');
include_once('../keygen.php');
include_once(DOCROOT.'/_data/collate.php');
include_once('../mail-template.php');
if(isset($_GET['key']) && degenerate($_GET['key'])){
	if(isset($_GET['send'])){
		$echo = array(
			'wordpress' => array(
				'success' => '',
				'debug' => ''
			),
			'smtp' => array(
				'success' => '',
				'debug' => ''
			),
			'lasso' => array(
				'success' => '',
				'debug' => ''
			)
		);

//===== BUILD DATA ==============================================================================================
		$mkid = explode('-', $_GET['send']);

		$input = json_decode(file_get_contents("php://input"), true);

		$final = array(
			'ProjectID' => get_field('ProjectID', $mkid[0]),
			'ClientID' => get_field('ClientID', $mkid[0]),
			'LassoUID' => get_field('LassoUID', $mkid[0]),
			'domainAccountId' => get_field('domainAccountId', $mkid[0]),
			'guid' => $mkid[1]
		);


		$internal = array();
		foreach($input as $id => $va) {
			$match = ng_get_match($mkid[0], $id, $va, '', true);
			$final[$match['key']] = $match['value'];

			$match2 = ng_get_match($mkid[0], $id, $va, '', false);

			$internal[intval(str_replace('field-'.$mkid[0].'-', '', $id))] = array(
				'heading' => $match2['key'],
				'value' => $match2['value']
			);
		}

		ksort($internal);
		$internal = array_values($internal);
//===== END BUILD DATA ==========================================================================================

//===== WP RECORD ===============================================================================================
		$form = get_post($mkid[0]);

		$tz = get_option('timezone_string');
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
		$dt->setTimestamp($timestamp); //adjust the object to correct timestamp
		$opt = 'responses-'.$form->ID.'-'.$dt->format('Y-m');

		if(get_option('catalogue-'.$form->ID) == '') {
			update_option('catalogue-'.$form->ID, serialize(array()));
		}

		$catalogue = unserialize(get_option('catalogue-'.$form->ID));

		if(!in_array($opt, $catalogue)) {
			array_push($catalogue, $opt);

			update_option('catalogue-'.$form->ID, serialize($catalogue));
		}

		$earlier_responses = get_option($opt);

		if($earlier_responses == '') {
			$earlier_responses = array();
		}
		else{
			$earlier_responses = unserialize($earlier_responses);
		}

		array_push($earlier_responses, array(
			'received' => $dt->format('F d, Y - h:ia (T)'),
			'data' => $internal
		));

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

		$wprecorded = update_option($opt, serialize($earlier_responses));

		$echo['wordpress']['success'] = $wprecorded;
		$echo['wordpress']['debug'] = $wprecorded ? 'ok.' : 'Recording failed.';

//===== END WP RECORD ===========================================================================================

//===== SEND AS EMAIL ===========================================================================================
		$receive = get_field('receiver', $form->ID);
		if($receive != '') {
			//build template
			$r = get_field('responses', $form->ID);
			$template = new emailTemplate('Form Submission: '.$form->post_title, $r['disclaimer'], wp_get_attachment_url(get_option('site_icon_og')));
			$template->addH1($form->post_title);
			$template->addText('A new registration entry has arrived. The registration entry was received <b>last '.$dt->format('F d, Y - h:ia T').'</b> with the data as follows:');
			$template->addTable('Entry Details', $internal);
			$template->addText('This is an automatically generated email, please do not reply.');

			$c = get_configs();

			$cnfg = $c['smtp'];

			if(!$cnfg['user'] || !$cnfg['pass'] || !$cnfg['host'] || !$cnfg['port']) {
				//do not use mailer
				$headers = array();

				array_push($headers, 'Content-Type: text/html; charset=UTF-8');
				array_push($headers, 'From: '.$form->post_title.' <site@vendaliving.com>');

				if(!wp_mail( $receive, 'Form Submission: '.$form->post_title, $r['disclaimer'], $template->render(false), $headers)){
					$echo['smtp']['success'] = false;
					$echo['smtp']['debug'] = 'wp_mail function failed.';
				}
				else{
					$echo['smtp']['success'] = true;
					$echo['smtp']['debug'] = 'wp_mail ok.';
				}
			}
			else{
				//use mailer
				$configurator['username'] = $cnfg['user'];
				$configurator['password'] = $cnfg['pass'];
				$configurator['host'] = $cnfg['host'];
				$configurator['port'] = $cnfg['port'];

				require_once('../phpmailer/class.phpmailer.php');
				include_once("../phpmailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

				$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

				$mail->IsSMTP(); // telling the class to use SMTP
				
				try {
					$mail->Host = $configurator['host']; // SMTP server
					//$mail->SMTPDebug	= 2;										 // enables SMTP debug information (for testing)
					$mail->SMTPAuth	= true;									// enable SMTP authentication
					$mail->SMTPSecure = "ssl";
					$mail->Host = $configurator['host']; // sets the SMTP server
					$mail->Port = $configurator['port'];	// set the SMTP port for the GMAIL server
					$mail->Username	 = $configurator['username']; // SMTP account username
					$mail->Password	 = $configurator['password']; // SMTP account password
					
					$mail->AddAddress($receive, 'Administrator');

					$mail->SetFrom($cnfg['user'], 'Site Submission: '.str_replace(array('http://', 'https://', '/'), array('', '', ''), BASE));
					$mail->AddReplyTo($cnfg['user'], 'Site Submission: '.str_replace(array('http://', 'https://', '/'), array('', '', ''), BASE));
					$mail->Subject = 'Form Submission: '.$form->post_title;

					$mail->AltBody = $template->render(false);
					$mail->MsgHTML($template->render(true));
					$mail->Send();
					
					
					$echo['smtp']['success'] = true;
					$echo['smtp']['debug'] = 'Mailer: ok.';
				} catch (phpmailerException $e) {
					//echo $e->errorMessage(); //Pretty error messages from PHPMailer
					
					$echo['smtp']['success'] = false;
					$echo['smtp']['debug'] = 'Mailer: '.$e->errorMessage();
				} catch (Exception $e) {
					//echo $e->getMessage(); //Boring error messages from anything else!
					$echo['smtp']['success'] = false;
					$echo['smtp']['debug'] = 'PHP: '.$e->getMessage();
				}
			}
		}
		else {
			$echo['smtp']['success'] = false;
			$echo['smtp']['debug'] = 'No email address provided.';
		}
//===== END SEND AS EMAIL =======================================================================================

//===== SEND TO LASSO ===========================================================================================

		// $ch = curl_init();

		// $url = 'https://app.lassocrm.com/registrant_signup/';
				
		// curl_setopt($ch,CURLOPT_URL, $url);
		// curl_setopt($ch,CURLOPT_POST, 1);
		// curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// $result = curl_exec($ch);

		// if (curl_errno($ch)) {
		// 	// this would be your first hint that something went wrong

		// 	$echo['lasso']['success'] = false;
		// 	$echo['lasso']['debug'] = 'Warning: '.curl_error($ch);
		// } else {	
		// 	// check the HTTP status code of the request
		// 	$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// 	if (intval($resultStatus) >= 200 && intval($resultStatus) < 300) {
		// 		// everything went better than expected
		// 		//file_put_contents('results.html', $result);
		// 		if(intval($resultStatus) == 200) {
		// 			$echo['lasso']['success'] = false;
		// 			$echo['lasso']['debug'] = 'Result 200, contact LassoCRM.';
		// 		}
		// 		else {
		// 			$echo['lasso']['success'] = true;
		// 			$echo['lasso']['debug'] = 'ok.';
		// 		}
 	// 		} else {
		// 		// the request did not complete as expected. common errors are 4xx
		// 		// (not found, bad request, etc.) and 5xx (usually concerning
		// 		// errors/exceptions in the remote script execution)
		// 		$echo['lasso']['success'] = false;
		// 		$echo['lasso']['debug'] = 'Fatal: '.$url.' -'.$resultStatusl;
		// 	}
		// }

		// curl_close($ch);
		
//===== END SEND TO LASSO ========================================================================================
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		
		echo json_encode($echo);
	}
}
?>