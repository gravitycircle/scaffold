<?php
include_once('../config.php');
include_once('keygen.php');
include_once('../_data/collate.php');
include_once('mail-template.php');
if(isset($_GET['key']) && degenerate($_GET['key'])){
	if(isset($_GET['mail']) && isset($_POST))
	{ 
		$data = json_decode(file_get_contents("php://input"), true);
		$cnfg = get_configs();

		$configurator['username'] = $cnfg['user'];
		$configurator['password'] = $cnfg['pass'];
		$configurator['host'] = $cnfg['host'];
		$configurator['port'] = $cnfg['port'];


		require_once('phpmailer/class.phpmailer.php');
		include_once("phpmailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

		//compute for date:
		$tz = 'America/Vancouver';
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
		$dt->setTimestamp($timestamp); //adjust the object to correct timestamp
		$theDate = $dt->format('F d, Y - h:ia (T)');

		$output = array();

		foreach($data['data'] as $id => $va) {
			$match = get_match($id, $va, $data['default'], false);
			array_push($output, array(
				'heading' => $match['key'],
				'value' => $match['value']
			));
		}

		$template = new emailTemplate(urldecode($data['subject']), urldecode($data['disclaimer']), 'http://angular.richardbryanong.com/img/non-render/email-header.jpg');

		$template->addH1('Online Registration');

		$template->addText('A new registration entry has arrived. The registration entry was received <b>last '.$theDate.'</b> with the data as follows:');
		
		$template->addTable('Entry Details', $output);

		$template->addText('This is an automatically generated email, please do not reply.');

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP
		
		try {
			$mail->Host = $configurator['host']; // SMTP server
//			$mail->SMTPDebug	= 2;										 // enables SMTP debug information (for testing)
			$mail->SMTPAuth	= true;									// enable SMTP authentication
			$mail->SMTPSecure = "ssl";
			$mail->Host = $configurator['host']; // sets the SMTP server
			$mail->Port = $configurator['port'];	// set the SMTP port for the GMAIL server
			$mail->Username	 = $configurator['username']; // SMTP account username
			$mail->Password	 = $configurator['password']; // SMTP account password
			
			$mail->AddAddress(explode('|', urldecode($data['To']))[1], explode('|', urldecode($data['To']))[0]);
			$mail->SetFrom(explode('|', urldecode($data['from']))[1], explode('|', urldecode($data['from']))[0]);
			$mail->AddReplyTo(explode('|', urldecode($data['replyTo']))[1], explode('|', urldecode($data['replyTo']))[0]);
			$mail->Subject = urldecode($data['subject']);

			$mail->AltBody = $template->render(false);
			$mail->MsgHTML($template->render(true));
			$mail->Send();
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
			header('Content-type: application/json');
			echo json_encode(array(
				'success' => true,
				'debug' => 'ok'
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
	include_once('404.php');
}
?>