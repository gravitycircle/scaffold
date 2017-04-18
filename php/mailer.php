<?php
include_once('../config.php');
include_once('keygen.php');
include_once('../_data/content.php');
if(isset($_GET['key']) && degenerate($_GET['key'])){
	if(isset($_GET['mail']) && isset($_POST))
	{
		$data = json_decode(file_get_contents("php://input"), true);
		//print_r($data);

		$url1 = $_SERVER['HTTP_HOST'];
		
		if (strpos($url1, 'localhost') !== false) {
		    header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
			header('Content-type: application/json');
			echo json_encode(array(
			  	'status' => 'Successful',
			  	'message' => 'We appreciate you contacting us. You are now added to our mailing list and will now be among the first ones to receive updates.'
			  ));
		}
		else
		{
			$configurator['username'] = SMTPUSER;
			$configurator['password'] = SMTPPW;
			$configurator['host'] = SMTPHOST;
			$configurator['port'] = SMTPPORT;


			require_once('phpmailer/class.phpmailer.php');
			include_once("phpmailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

			//compute for date:
			$tz = 'America/Vancouver';
			$timestamp = time();
			$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
			$dt->setTimestamp($timestamp); //adjust the object to correct timestamp
			$theDate = $dt->format('F d, Y h:i a T');

			$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

			$mail->IsSMTP(); // telling the class to use SMTP

			try {
			  $mail->Host       = $configurator['host']; // SMTP server
	//		  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
			  $mail->SMTPAuth   = true;                  // enable SMTP authentication
			  $mail->Host       = $configurator['host']; // sets the SMTP server
			  $mail->Port       = $configurator['port'];  // set the SMTP port for the GMAIL server
			  $mail->Username   = $configurator['username']; // SMTP account username
			  $mail->Password   = $configurator['password'];       // SMTP account password
			  $mail->AddAddress(explode('|', urldecode($data['To']))[1], explode('|', urldecode($data['To']))[0]);
			  $mail->SetFrom(explode('|', urldecode($data['from']))[1], explode('|', urldecode($data['from']))[0]);
			  $mail->AddReplyTo(explode('|', urldecode($data['replyTo']))[1], explode('|', urldecode($data['replyTo']))[0]);
			  $mail->Subject = urldecode($data['subject']);
	//		  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			  $mail->MsgHTML('<div style="width: 650px; margin: 20px auto 20px;">'.str_replace('[date]', $theDate, urldecode($data['body'])).'</div>');
			  $mail->SMTPOptions = array(
				'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				)
			  );
			  $mail->Send();
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
				header('Content-type: application/json');
			  echo json_encode(array(
			  	'status' => 'Successful',
			  	'message' => 'We appreciate you contacting us. You are now added to our mailing list and will now be among the first ones to receive updates.',
			  	'debug' => 'ok'
			  ));
			} catch (phpmailerException $e) {
			  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
				header('Content-type: application/json');
			  echo json_encode(array(
			  	'status' => 'Failed',
			  	'message' => 'There was a technical issue in the registration process and we cannot continue with your registration. We are at work right now on fixing this issue. Please check back at a later time. Thank you for your interest.',
			  	'debug' => 'Mailer: '.$e->errorMessage()
			  ));
			} catch (Exception $e) {
			  //echo $e->getMessage(); //Boring error messages from anything else!
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
				header('Content-type: application/json');
			  echo json_encode(array(
			  	'status' => 'Failed',
			  	'message' => 'There was a technical issue in the registration process and we cannot continue with your registration. We are at work right now on fixing this issue. Please check back at a later time. Thank you for your interest.',
			  	'debug' => 'PHP: '.$e->getMessage()
			  ));
			}
		}
	}
}
else{
	include_once('404.php');
}
?>