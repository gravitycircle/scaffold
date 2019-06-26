<?php
function ng_get_fields($postid, $match) {
	//---BUILD FIELDS HERE
	if(get_page_template_slug($postid) != '') {
		return array();
	}

	$fields = array();

	$farray = get_field('fields', $postid);

	
	foreach($farray as $fin => $f) {
		$field_data = array(
			'pid' => 'page-'.$postid,
			'id' => 'field-'.$postid.'-'.$fin,
			'label' => $f['label'],
			'require' => $f['require'],
			'key' => 'field-'.$fin
		);

		switch($f['type']) {
			case 'text':
				$field_data['type'] = 'text';
				$field_data['verify'] = $f['filter'];
			break;

			case 'par':
				$field_data['type'] = 'paragraph';
				$field_data['verify'] = 'max/9999';
			break;

			case 'menu':
				$field_data['type'] = $f['radio'] ? 'radio' : 'dropdown';
				$field_data['values'] = array();
				foreach($f['values'] as $ind => $choice) {
					array_push($field_data['values'], array(
						'key' => 'value-set-'.$ind,
						'name' => $choice['label'],
						'value' => $choice['value']
					));
				}
			break;

			case 'checkbox':
				$field_data['label'] = $f['copy_text'];
				$field_data['type'] = 'checkbox';
				$field_data['set'] = array(
					'label' => $f['value'],
					'value' => 'yes',
					'key' => 1,
					'no' => 'No'
				);
			break;
		}

		array_push($fields, $field_data);
	}


	$rarray = get_field('responses', $postid);
	array_push($fields, array(
		'pid' => 'page-'.$postid,
		'label' => get_field('submit', $postid),
		'type' => 'submit',
		'id' => 'field-'.$postid.'-'.sizeof($fields),
		'receiver' => array('Administrator', get_field('receiver', $postid)),
		'defaults' => array(
			'disclaimer' =>  $rarray['disclaimer'],
			'empty' => 'Unspecified'
		),
		'prompts' => array(
			'success' => array(
				'title' => 'Registration Complete',
				'message' => $rarray['success']
			),
			'verify_error' => array(
				'title' => 'Submission Failed',
				'message' => $rarray['failed']
			),
			'submit_error' => array(
				'title' => 'Submission Failed',
				'message' => $rarray['technical']
			)
		)
	));
	//----

	$build = array();

	if(!$match) {
		foreach($fields as $field) {
			$built = array(
				'pid' => $field['pid'],
				'label' => $field['label'],
				'id' => $field['id'],
				'type' => $field['type'],
			);

			if($field['type'] != 'submit') {
				$built['require'] = $field['require'];
			}

			if($field['type'] == 'text') {
				$built['verify'] = $field['verify'];	
			}

			if($field['type'] == 'dropdown'){
				$vals = array();
				foreach($field['values'] as $v) {
					array_push($vals, array(
						'value' => $v['key'],
						'name' => $v['name']
					));
				}

				$built['values'] = $vals;
			}

			if($field['type'] == 'multiple'){
				$vals = array();
				foreach($field['values'] as $v) {
					array_push($vals, array(
						'value' => $v['key'],
						'name' => $v['name']
					));
				}

				$built['values'] = $vals;
				$built['particle'] = array(
					'singular' => $field['particle'][0],
					'plural' => $field['particle'][1]
				);
			}

			if($field['type'] == 'checkbox') {
				$built['value'] = $field['set']['key'];
			}

			if($field['type'] == 'submit') {
				$built['receiver'] = $field['receiver'];
				$built['prompts'] = $field['prompts'];
				$built['defaults'] = $field['defaults'];
				$built['recaptcha'] = array(
					'key' => get_field('recap-key', $postid),
					'fail-message' => get_field('recap-fail', $postid)
				);
			}

			array_push($build, $built);
		}
	}
	else{
		foreach($fields as $field) {
			if($field['type'] == 'dropdown') {
				$vars = array();

				foreach($field['values'] as $v) {
					$vars[$v['key']] = array(
						'label' => $v['name'],
						'val' => $v['value']
					);
				}

				$build[$field['id']] = array(
					'key' => $field['key'],
					'label' => $field['label'],
					'type' => 'dropdown',
					'matches' => $vars
				);
			}
			else if($field['type'] == 'multiple') {
				$vars = array();

				foreach($field['values'] as $v) {
					$vars[$v['key']] = array(
						'label' => $v['name'],
						'val' => $v['value']
					);
				}

				$build[$field['id']] = array(
					'key' => $field['key'],
					'label' => $field['label'],
					'type' => 'multiple',
					'matches' => $vars
				);
			}
			else if($field['type'] == 'checkbox') {
				$build[$field['id']] = array(
					'key' => $field['key'],
					'label' => $field['label'],
					'type' => 'checkbox',
					'no' => $field['set']['no'],
					'match' => array(
						$field['set']['key'] => array(
							'val' => $field['set']['value'],
							'label' => $field['set']['label']
						)
					)
				);
			}
			else{
				if($field['type'] != 'submit') {
					$build[$field['id']] = array(
						'key' => $field['key'],
						'label' => $field['label'],
						'type' => $field['type']
					);
				}
			}
		}
	}

	return $build;
}

function ng_get_match($postid, $inputName, $inputValue, $defaultValue, $crm) {
	$matches = ng_get_fields($postid, true);

	if($matches[$inputName]['type'] == 'dropdown') {
		if($crm) {
			return array(
				'key' => $matches[$inputName]['key'],
				'value' => (!isset($matches[$inputName]['matches'][$inputValue]['val']) ? $defaultValue : $matches[$inputName]['matches'][$inputValue]['val'])
			);
		}
		else{
			return array(
				'key' => $matches[$inputName]['label'],
				'value' => (!isset($matches[$inputName]['matches'][$inputValue]['label']) ? $defaultValue : $matches[$inputName]['matches'][$inputValue]['label'])
			);
		}
	}
	else if($matches[$inputName]['type'] == 'multiple') {
		if($crm) {

			$answergroup = json_decode(urldecode($inputValue), true);
			$answervalue = array();
			foreach($answergroup as $ans) {
				array_push($answervalue, (!isset($matches[$inputName]['matches'][$ans]['val']) ? $defaultValue : $matches[$inputName]['matches'][$ans]['val']));
			}

			return array(
				'key' => $matches[$inputName]['key'], // crm key equivalent
				'value' => $answervalue
			);
		}
		else{
			$answergroup = json_decode(urldecode($inputValue), true);
			$answervalue = array();
			foreach($answergroup as $ans) {
				array_push($answervalue, (!isset($matches[$inputName]['matches'][$ans]['label']) ? $defaultValue : $matches[$inputName]['matches'][$ans]['label']));
			}

			return array(
				'key' => $matches[$inputName]['label'],
				'value' => implode(', ', $answergroup)
			);
		}
	}
	else if ($matches[$inputName]['type'] == 'checkbox') {
		if($crm) {
			if($inputValue != '' && isset($matches[$inputName]['match'][$inputValue]['val'])){
				return array(
					'key' => $matches[$inputName]['key'],
					'value' => $matches[$inputName]['match'][$inputValue]['val']
				);
			}
			else{
				return array(
					'key' => $matches[$inputName]['key'],
					'value' => false
				);
			}
		}
		else{
			if($inputValue != '' && isset($matches[$inputName]['match'][$inputValue]['val'])){
				return array(
					'key' => $matches[$inputName]['label'],
					'value' => $matches[$inputName]['match'][$inputValue]['label']
				);
			}
			else{
				return array(
					'key' => $matches[$inputName]['label'],
					'value' => (!isset($matches[$inputName]['no']) ? $defaultValue : $matches[$inputName]['no'])
				);
			}
		}
	}
	else {
		if($matches[$inputName]['type'] != 'submit') {
			if(!$inputValue || $inputValue == '') {
				$inputValue = $defaultValue;
			}
			if($crm) {
				return array(
					'key' => $matches[$inputName]['key'],
					'value' => $inputValue
				);
			}
			else{
				return array(
					'key' => $matches[$inputName]['label'],
					'value' => $inputValue
				);
			}
		}
	}
}
?>