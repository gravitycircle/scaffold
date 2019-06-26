<?php
include_once('../../config.php');
include_once('../keygen.php');
include_once(DOCROOT.'/_data/collate.php');
include_once('../mail-template.php');
if(isset($_GET['key']) && degenerate($_GET['key'])){
	if(isset($_GET['send'])){
		$input = json_decode(file_get_contents("php://input"), true);

		$final = array();
		$internal = array();
		foreach($input as $id => $va) {
			$match = ng_get_match($_GET['send'], $id, $va, '', true);
			$final[$match['key']] = $match['value'];

			$match2 = ng_get_match($_GET['send'], $id, $va, '', false);

			$internal[intval(str_replace('field-'.$_GET['send'].'-', '', $id))] = array(
				'heading' => $match2['key'],
				'value' => $match2['value']
			);
		}

		ksort($internal);
		$internal = array_values($internal);
		$form = get_post($_GET['send']);

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

		update_option($opt, serialize($earlier_responses));
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
					echo json_encode(array(
						'success' => false,
						'debug' => 'Mail Function: Send failed.'
					));
				}
				else{
					echo json_encode(array(
						'success' => true,
						'debug' => 'Mail Function: ok.'
					));
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
					
					header('Cache-Control: no-cache, must-revalidate');
					header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
					header('Content-type: application/json');
					echo json_encode(array(
						'success' => true,
						'debug' => 'Mailer: ok.'
					));
				} catch (phpmailerException $e) {
					//echo $e->errorMessage(); //Pretty error messages from PHPMailer
					header('Cache-Control: no-cache, must-revalidate');
					header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
					header('Content-type: application/json');
					echo json_encode(array(
						'success' => false,
						'debug' => 'Mailer: '.$e->errorMessage()
					));
				} catch (Exception $e) {
					//echo $e->getMessage(); //Boring error messages from anything else!
					header('Cache-Control: no-cache, must-revalidate');
					header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
					header('Content-type: application/json');
					echo json_encode(array(
						'success' => false,
						'debug' => 'PHP: '.$e->getMessage()
					));
				}
			}
		}
		else{
			echo json_encode(array(
				'success' => true,
				'debug' => 'WordPress Recording: ok.'
			));
		}
	}
}
?>