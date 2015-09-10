<?php
if(isset($_GET['verify']) && $_GET['verify'] != '')
{
	$emails = explode('|', $_GET['verify']);

	if(sizeof($emails) > 1)
	{
		$verify = array();
		foreach($emails as $email)
		{
			if((!filter_var($email, FILTER_VALIDATE_EMAIL) === false))
			{
				$verify['verified'][sizeof($verify['verified'])] = $email;
			}
			else
			{
				$verify['rejected'][sizeof($verify['rejected'])] = $email;
			}
		}
	}
	else
	{
		$verify = array();
		if((!filter_var($_GET['verify'], FILTER_VALIDATE_EMAIL) === false))
		{
			$verify['verified'] = 1;
		}
		else
		{
			$verify['verified'] = 0;
		}
	}
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
	header('Content-type: application/json');
	echo json_encode($verify);
}

if(isset($_GET['mail']) && isset($_POST))
{
	$data = json_decode(file_get_contents("php://input"), true);
	//print_r($data);

	$url1 = $_SERVER['HTTP_HOST'];
	
	if (strpos($url1, 'localhost') !== false) {
	    header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
		header('Content-type: application/json');
		echo json_encode(array('sent'));
	}
	else
	{
		$configurator['username'] = 'no.reply@thestanton.ca';
		$configurator['password'] = 'vCWr{RB?@0EI';
		$configurator['host'] = 'mail.thestanton.ca';
		$configurator['port'] = 587;


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
		  $mail->Send();
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
			header('Content-type: application/json');
		  echo json_encode(array('sent'));
		} catch (phpmailerException $e) {
		  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
			header('Content-type: application/json');
		  echo json_encode(array('error'));
		} catch (Exception $e) {
		  //echo $e->getMessage(); //Boring error messages from anything else!
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.date('D, d M Y H:i:s T', (strtotime('now') + 3600)));
			header('Content-type: application/json');
		  echo json_encode(array('error'));
		}
	}
}
?>