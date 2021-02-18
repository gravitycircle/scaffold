<?php
include_once('../../config.php');
include_once('../keygen.php');
include_once(DOCROOT.'/_data/collate.php');
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
			'crm' => array(
				'success' => '',
				'debug' => ''
			),
			'tolerance' => 0
		);

//===== BUILD DATA ==============================================================================================
		$mkid = explode('|', $_GET['send']);
		$input = json_decode(file_get_contents("php://input"), true);

		$hidden = get_field('hidden', $mkid[0]);
		$guid = get_field('include-guid', $mkid[0]);

		$final = array();

		foreach($hidden as $h) {
			$final[$h['name']] = $h['value'];
		}

		if(!$guid) {
			$final['guid'] = $mkid[1];
		}

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

		$rt = 0;
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

		if($wprecorded) {
			$rt++;
		}
//===== END WP RECORD ===========================================================================================

//===== SEND AS EMAIL ===========================================================================================
		$receive = get_field('receiver', $form->ID);
		$c = get_field('smtp-details', $form->ID);



		if($c['smtp-email'] != '') {
	
			//build template
			if($receive != '') {
				$r = get_field('responses', $form->ID);
				$form_responses = new emailTemplate('Form Submission: '.$form->post_title, $r['disclaimer'], wp_get_attachment_url(get_option('site_icon_og')));
				$form_responses->addH1($form->post_title);
				$form_responses->addText('A new registration entry has arrived. The registration entry was received <b>last '.$dt->format('F d, Y - h:ia T').'</b> with the data as follows:');
				$form_responses->addTable('Entry Details', $internal);
				$form_responses->addText('If you wish to view the form that generated this response, please click <a href="'.get_bloginfo('wpurl').'/post.php?post='.$form->ID.'&action=edit" target="_blank" style="color: #d03029; text-decoration: none;">here</a>. This is an automatically generated email, please do not reply.');
			}
			else{
				$form_responses = false;
			}


			//autoreplies
			$autoreplies = kld_write_autoreply($form->ID, $final);

			

			if(!$c['is-smtp']) {
				//do not use mailer
				$headers = array();
				$armetric = array(
					'success' => 0,
					'failed' => 0
				);
				foreach($autoreplies as $envelope) {
					$headers = array();
					array_push($headers, 'Content-Type: text/html; charset=UTF-8');
					array_push($headers, 'From: '.get_bloginfo('name').' Registration <'.$c['smtp-email'].'>');

					if(!wp_mail($envelope['addressee']['email'], $envelope['subject'], $envelope['message'], $headers)){
						$armetric['failed']++;
					}
					else{
						$armetric['success']++;
					}
				}

				//to admin
				if($receive != '') {
					$headers = array();
					array_push($headers, 'Content-Type: text/html; charset=UTF-8');
					array_push($headers, 'From: '.get_bloginfo('name').' Registration <'.$c['smtp-email'].'>');

					if(!wp_mail($receive, 'Viewer Submission: '.get_bloginfo('name'), $form_responses->render(true), $headers)){
						$echo['smtp']['success'] = false;
						$echo['smtp']['debug'] = array(
							'Admin' => 'Failed',
							'Autoreplies' => $armetric
						);
					}
					else{
						if(!$armetric['success'] && !(!$armetric['failed'])) {
							$echo['smtp']['success'] = false;
							$echo['smtp']['debug'] = array(
								'Admin' => 'OK',
								'Autoreplies' => $armetric
							);
						}
						else{
							$echo['smtp']['success'] = true;
							$echo['smtp']['debug'] = array(
								'Admin' => 'OK',
								'Autoreplies' => $armetric
							);
							$rt++;
						}
					}
				}
				else{
					if(!$armetric['success'] && !(!$armetric['failed'])) {
						$echo['smtp']['success'] = false;
						$echo['smtp']['debug'] = array(
							'Admin' => 'Disabled',
							'Autoreplies' => $armetric
						);
					}
					else{
						$echo['smtp']['success'] = true;
						$echo['smtp']['debug'] = array(
							'Admin' => 'Disabled',
							'Autoreplies' => $armetric
						);
						$rt++;
					}
				}
			}
			else{
				// use mailer
				$configurator['username'] = $c['smtp-login'] == '' ? $c['smtp-email'] : $c['smtp-login'];
				$configurator['password'] = $c['smtp-password'];
				$configurator['host'] = $c['smtp-server'];
				$configurator['port'] = $c['smtp-port'];

				require_once('../phpmailer/class.phpmailer.php');
				include_once("../phpmailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded


				$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

				//$mail->IsSMTP(); // telling the class to use SMTP
				$mail->IsHTML(true);
				$mail->SMTPDebug	= 1;										 // enables SMTP debug information (for testing)
				$mail->SMTPAuth	= true;									// enable SMTP authentication
				if($c['is-ssl']) {
					$mail->SMTPSecure = "ssl";
				}

				$mail->Host = $configurator['host']; // sets the SMTP server
				$mail->Port = $configurator['port'];	// set the SMTP port for the GMAIL server
				$mail->Username	 = $configurator['username']; // SMTP account username
				$mail->Password	 = $configurator['password']; // SMTP account password
				$mail->SetFrom($c['smtp-email'], get_bloginfo('name').' Registration ');
				$mail->AddReplyTo($c['smtp-email'], get_bloginfo('name').' Registration ');

				$armetric = array(
					'success' => 0,
					'failed' => 0,
					'debug' => []
				);

				foreach($autoreplies as $envelope) {
					$err = false;
					try{
						$mail->AddAddress($envelope['addressee']['email'], $envelope['addressee']['name']);
						$mail->Subject = $envelope['subject'];
						$mail->AltBody = $envelope['plaintext'];
						$mail->MsgHTML($envelope['message']);
						$mail->Send();
						$mail->ClearAddresses();
					}
					catch (phpmailerException $e) {
						$err = true;
						$armetric['failed']++;
						array_push($armetric['debug'], array(
							'to' => $envelope['addressee']['email'],
							'debug' => 'Mailer: '.$e->errorMessage()
						));
					}
					catch (Exception $e) {
						$err = true;
						$armetric['failed']++;
						array_push($armetric['debug'], array(
							'to' => $envelope['addressee']['email'],
							'debug' => 'PHP: '.$e->getMessage()
						));
					}

					if(!$err) {
						$armetric['success']++;
						array_push($armetric['debug'], array(
							'to' => $envelope['addressee']['email'],
							'debug' => 'OK'
						));
					}
				}

				if($receive != '') {
					//send admin email
					$rerr = 'OK';
					try {
						$mail->AddAddress($receive, 'Administrator');//*
						$mail->Subject = 'Viewer Submission: '.get_bloginfo('name'); //*
						$mail->AltBody = $form_responses->render(false); //*
						$mail->MsgHTML($form_responses->render(true));//*
						$mail->Send();
					}
					catch (phpmailerException $e) {
						$rerr = 'Mailer: '.$e->errorMessage();
					}
					catch (Exception $e) {
						$rerr = 'PHP: '.$e->errorMessage();	
					}

					if($rerr != 'OK') {
						$echo['smtp']['success'] = false;
						$echo['smtp']['debug'] = array(
							'Admin' => $rerr,
							'Autoreplies' => $armetric
						);
					}
					else{
						if(!$armetric['success'] && !(!$armetric['failed'])) {
							$echo['smtp']['success'] = false;
							$echo['smtp']['debug'] = array(
								'Admin' => 'OK',
								'Autoreplies' => $armetric
							);
						}
						else{
							$echo['smtp']['success'] = true;
							$echo['smtp']['debug'] = array(
								'Admin' => 'OK',
								'Autoreplies' => $armetric
							);
							$rt++;
						}
					}
				}
				else{
					if(!$armetric['success'] && !(!$armetric['failed'])) {
						$echo['smtp']['success'] = false;
						$echo['smtp']['debug'] = array(
							'Admin' => 'Disabled',
							'Autoreplies' => $armetric
						);
					}
					else{
						$echo['smtp']['success'] = true;
						$echo['smtp']['debug'] = array(
							'Admin' => 'Disabled',
							'Autoreplies' => $armetric
						);
						$rt++;
					}
				}
			}
		}
		else {
			$echo['smtp']['success'] = true;
			$echo['smtp']['debug'] = 'Emailer / Autoresponders Disabled. No email address provided. - '.$form->ID;
		}
//===== END SEND AS EMAIL =======================================================================================

//===== SEND TO LASSO ===========================================================================================
		

		$url = get_field('crm-url', $form->ID);
		if($url != '' && $url != false) {
			//check if lasso
			$lasso = (strpos($url, 'lassocrm.com') !== false);

			//begin
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $qstring);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);

			$result = curl_exec($ch);

			if (curl_errno($ch)) {
				// this would be your first hint that something went wrong

				$echo['crm']['success'] = false;
				$echo['crm']['debug'] = 'Warning: '.curl_error($ch);
			} else {	
				// check the HTTP status code of the request
				$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if (intval($resultStatus) >= 200 && intval($resultStatus) < 300) {
					// everything went better than expected
					//file_put_contents('results.html', $result);

					if($lasso) {
						if(intval($resultStatus) == 200) {
							$echo['crm']['success'] = false;
							$echo['crm']['debug'] = 'Result 200, contact LassoCRM.';
						}
						else {
							$echo['crm']['success'] = true;
							$echo['crm']['debug'] = 'ok.';
						}
					}
					else{
						$echo['crm']['success'] = true;
						$echo['crm']['debug'] = 'ok.';
					}
	 			} else {
					// the request did not complete as expected. common errors are 4xx
					// (not found, bad request, etc.) and 5xx (usually concerning
					// errors/exceptions in the remote script execution)
					$echo['crm']['success'] = false;
					$echo['crm']['debug'] = 'Fatal: '.$url.' -'.$resultStatusl;
				}
			}

			curl_close($ch);
		}
		else{
			$rt++;
			$echo['crm']['success'] = true;
			$echo['crm']['debug'] = 'CRM Disabled';
		}
		
//===== END SEND TO LASSO ========================================================================================
		
		$echo['tolerance'] = 3 - $rt;
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		
		echo json_encode($echo);
	}
}
?>