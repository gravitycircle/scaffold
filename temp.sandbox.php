<?php
$styles = array(
	'paragraph' => implode(' ', array(
		'font-family: \'Arial\', sans-serif;',
		'color: #555;',
		'line-height: 20px;',
		'font-size: 14px;',
		'margin-top: 15px;',
		'margin-bottom: 15px;'
	)),
	'tiny' => implode(' ', array(
		'font-family: \'Arial\', sans-serif;',
		'color: #555;',
		'line-height: 14px;',
		'font-size: 10px;',
		'margin-top: 15px;',
		'margin-bottom: 15px;'
	)),
	'h2' => implode(' ', array(
		'font-family: \'Arial\', sans-serif;',
		'color: #555;',
		'margin-top: 30px;',
		'margin-bottom: 15px;',
		'line-height: 18px;',
		'font-size: 18px;',
	)),
	'h1' => implode(' ', array(
		'font-family: \'Arial\', sans-serif;',
		'color: #555;',
		'text-align: center;',
		'margin-top: 30px;',
		'margin-bottom: 15px;',
		'line-height: 32px;',
		'font-size: 32px;',
	)),
	'main' => implode(' ', array(
		'width: 650px;',
		'margin-left: auto;',
		'margin-right: auto;',
		'margin-top: 40px;',
		'margin-bottom: 40px;',
		'background: #ffffff;'
	)),
	'img' => implode(' ', array(
		'display: block;'
	)),
	'td' => implode(' ', array(
		'padding: 0;'
	)),
	'body' => implode(' ', array(
		'padding-top: 0;',
		'padding-left: 0;',
		'padding-bottom: 30px;',
		'padding-right: 0;'
	)),
	'footer' => implode(' ', array(
		'padding-top: 15px;',
		'padding-left: 0;',
		'padding-bottom: 15px;',
		'padding-right: 0;'
	)),
	'exit' => implode(' ', array(
		'background: #dadada;'
	)),
	'data-table' => implode(' ', array(
		'width: 100%;',
		'border-collapse: collapse;',
		'border-top: 1px solid #999999;',
		'margin-bottom: 15px;'
	)),
	'data-headings' => implode(' ', array(
		'border-bottom: 1px solid #999999;',
		'padding: 10px;',
		'width: 30%;',
		'font-family: \'Arial\', sans-serif;',
		'font-weight: bold;',
		'font-size: 16px;',
		'line-height: 16px;',
		'color: #555;'
	)),
	'data-values' => implode(' ', array(
		'border-bottom: 1px solid #999999;',
		'padding: 10px;',
		'width: 70%;',
		'font-family: \'Arial\', sans-serif;',
		'font-size: 16px;',
		'line-height: 16px;',
		'color: #555;'
	)),
);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="UTF-8">
		<title>Email Template</title>
	</head>
	<body style="background: #999999;">
		<table border="0" cellpadding="0" cellspacing="0" width="650" style="<?=$styles['main']?>">
			<tbody>
				<tr>
					<td colspan="3" style="<?=$styles['td']?>">
						<img src="http://angular.richardbryanong.com/img/non-render/email-header.jpg" alt="LOGO" style="<?=$style['img']?>" />
					</td>
				</tr>
				<tr>
					<td style="width: 80px;"> </td>
					<td style="<?=$styles['body']?>">
						<h1 style="<?=$styles['h1']?>">Tabulated Data</h1>
						<p style="<?=$styles['paragraph']?>">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quaerat commodi excepturi laudantium possimus doloremque, laboriosam eos. Cum nulla dignissimos voluptate minima, sapiente eius veniam dolores consequatur, labore vel numquam eveniet!</p>
						<p style="<?=$styles['paragraph']?>">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quaerat commodi excepturi laudantium possimus doloremque, laboriosam eos. Cum nulla dignissimos voluptate minima, sapiente eius veniam dolores consequatur, labore vel numquam eveniet!</p>
						<h2 style="<?=$styles['h2']?>">Subheading</h2>
						<table cellpadding="0" cellspacing="0" style="<?=$styles['data-table']?>">
							<tbody>
								<tr>
									<td style="<?=$styles['data-headings']?>">
										Name
									</td>
									<td style="<?=$styles['data-values']?>">
										John Doe
									</td>
								</tr>
								<tr>
									<td style="<?=$styles['data-headings']?>">
										Another Header
									</td>
									<td style="<?=$styles['data-values']?>">
										More Data
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="width: 80px;"> </td>
				</tr>
				<tr style="<?=$styles['exit']?>">
					<td style="width: 80px;"> </td>
					<td style="<?=$styles['footer']?>">
						<p style="<?=$styles['tiny']?>">
							This email is intended only for the person(s) named in the message header. Unless otherwise indicated, it contains information that is confidential, privileged and/or exempt from disclosure under applicable law. If you have received this message in error, please notify the sender of the error and delete the message. Thank you.
						</p>
					</td>
					<td style="width: 80px;"> </td>
				</tr>
			</tbody>
		</table>
	</body>
</html>