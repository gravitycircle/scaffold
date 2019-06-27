<?php
include_once('../../config.php');

if(!file_exists(DOCROOT.'/_src/wp-src.zip')) {
	echo json_encode(array(
		'status' => 'error',
		'debug' => 'The downloaded ZIP file does not exist.'
	));
}
else{

	if(file_exists(DOCROOT.'/_bin')) {
		echo json_encode(array(
			'status' => 'error',
			'debug' => '_bin directory is not empty. It is likely that there is an installation present in the folder.'
		));
	}
	else{
		$zip = new ZipArchive;

		$res = $zip->open(DOCROOT.'/_src/wp-src.zip');

		if($res === TRUE) {
			$zip->extractTo(DOCROOT);
			$zip->close();

			rename(DOCROOT.'/wordpress', DOCROOT.'/_bin');
			mkdir(DOCROOT.'/_bin/media', '0777', true);

			unlink(DOCROOT.'/_src/wp-src.zip');

			echo json_encode(array(
				'status' => 'ok',
				'debug' => false
			));
		}
		else{
			echo json_encode(array(
				'status' => 'error',
				'debug' => 'The file at _src/wp-src.zip is not a valid zip archive.'
			));
		}
	}
}
?>