<?php
class emailTemplate {
	private $subject;
	private $banner;
	private $disclaimer;
	private $body = array();
	private $styles = array();

	public function __construct($s = false, $d = false, $b = false) {
		$this->subject = $s;
		$this->disclaimer = $d;
		$this->banner = $b;

		$this->styles = array(
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
				'color: #beb58d;',
				'text-align: left;',
				'margin-top: 30px;',
				'margin-bottom: 15px;',
				'line-height: 32px;',
				'font-size: 32px;',
			)),
			'main' => implode(' ', array(
				'width: 650px;',
				'margin-left: auto;',
				'margin-right: auto;',
				'background: #ffffff;'
			)),
			'img' => implode(' ', array(
				'display: block;',
				'width: 100%;',
				'height: auto;',
				'margin: 0;',
				'padding: 0;'
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
			'html' => implode(' ', array(
				'font-family: \'Arial\', sans-serif;',
				'color: #555;',
				'line-height: 20px;',
				'font-size: 14px;',
				'padding-bottom: 30px;'
			))
		);
	}

	public function addText($text, $exclude = false) {
		array_push($this->body, array(
			'type' => 'text',
			'content' => $text,
			'exclude' => $exclude
		));
	}

	public function addH1($text, $exclude = false) {
		array_push($this->body, array(
			'type' => 'h1',
			'content' => $text,
			'exclude' => $exclude
		));
	}

	public function addH2($text, $exclude = false) {
		array_push($this->body, array(
			'type' => 'h2',
			'content' => $text,
			'exclude' => $exclude
		));
	}

	public function addTable($title, $values, $exclude = false) {
		array_push($this->body, array(
			'type' => 'table',
			'title' => $title,
			'content' => $values,
			'exclude' => $exclude
		));
	}

	public function addHTML($text, $exclude = false) {
		array_push($this->body, array(
			'type' => 'html',
			'content' => $text,
			'exclude' => $exclude
		));
	}

	public function render($html) {
		if(!$this->subject || !$this->banner || !$this->body || !$this->disclaimer || sizeof($this->body) < 1){
			return false;
		}
		else{
			if($html) {
				ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="UTF-8">
		<title><?=$this->subject?></title>
	</head>
	<body style="background: #999999; padding-top: 60px; padding-bottom: 60px;">
		<table border="0" cellpadding="0" cellspacing="0" width="650" style="<?=$this->styles['main']?>">
			<tbody>
				<tr>
					<td colspan="3" style="<?=$this->styles['td']?>">
						<img src="<?=$this->banner?>" alt="<?=$this->subject?>" style="<?=$this->styles['img']?>" />
					</td>
				</tr>
				<tr>
					<td style="width: 80px;"> </td>
					<td style="<?=$this->styles['body']?>">
<?php
				foreach($this->body as $b) {
					if($b['type'] == 'h1') {
?>
						<h1 style="<?=$this->styles['h1']?>"><?=$b['content']?></h1>
<?php
					}
					else if($b['type'] == 'h2') {
?>
						<h2 style="<?=$this->styles['h2']?>"><?=$b['content']?></h2>
<?php					
					}
					else if($b['type'] == 'text') {
?>
						<p style="<?=$this->styles['paragraph']?>"><?=$b['content']?></p>
<?php
					}
					else if($b['type'] == 'html') {
?>
						<div class="html-content" style="<?=$this->styles['html']?>">
							<?=$b['content']?>
						</div>
<?php
					}
					else {
?>
						<h2 style="<?=$this->styles['h2']?>"><?=$b['title']?></h2>
						<table cellpadding="0" cellspacing="0" style="<?=$this->styles['data-table']?>">
							<tbody>
<?php
					foreach($b['content'] as $v) {
?>
								<tr>
									<td style="<?=$this->styles['data-headings']?>">
										<?=$v['heading']?>
									</td>
									<td style="<?=$this->styles['data-values']?>">
										<?=$v['value']?>
									</td>
								</tr>
<?php
					}
?>
							</tbody>
						</table>
<?php
					}
				}
?>
					</td>
					<td style="width: 80px;"> </td>
				</tr>
				<tr style="<?=$this->styles['exit']?>">
					<td style="width: 80px;"> </td>
					<td style="<?=$this->styles['footer']?>">
						<p style="<?=$this->styles['tiny']?>"><?=$this->disclaimer?></p>
					</td>
					<td style="width: 80px;"> </td>
				</tr>
			</tbody>
		</table>
	</body>
</html>
<?php
				return ob_get_clean();
			}
			else{
				$return  = $this->subject."\r\n";
				$return .= '=================='."\r\n\r\n";

				foreach($this->body as $b) {
					if(!$b['exclude']){
						if($b['type'] == 'h1'  || $b['type'] == 'h2' || $b['type'] == 'text' || $b['type'] == 'html'){
							$return .= strip_tags($b['content'])."\r\n\r\n";
						}
						else{
							$return .= strip_tags($b['title'])."\r\n";
							$return .= '---'."\r\n";
							foreach($b['content'] as $v) {
								$return .= strip_tags($v['heading']).': '.strip_tags($v['value'])."\r\n";
							}
							$return .= '---'."\r\n\r\n";
						}
					}
				}

				$return .= '=================='."\r\n";
				$return .= $this->disclaimer;

				return $return;
			}
		}
	}
}
?>