<?php
include_once('directives.php');

function get_lasso() {
	return array(
		'ProjectID' => '',
		'ClientID' => '',
		'LassoUID' => ''
	);
}

function build_content(){
	$output = array(
		'test' => array(
			'fields' => get_fields(false)
		)
	);

	return $output;
}

function get_fields($match) {
	//---BUILD FIELDS HERE
	$fields = array(
		array(
			'key' => 'Questions[85248]',
			'label' => 'Lasso Field',
			'id' => 'field-id',
			'type' => 'text',
			'require' => false
		),
		array(
			'key' => 'Questions[69592]',
			'label' => 'Lasso Paragraph',
			'id' => 'field-id-2',
			'type' => 'paragraph',
			'require' => true
		),
		array(
			'key' => 'Questions[15434]',
			'label' => 'Dropdown',
			'id' => 'dd-sample',
			'type' => 'dropdown',
			'require' => true,
			'values' => array(
				array(
					'key' => 'value-set-1',
					'name' => 'Value Set 1',
					'value' => '00001'
				),
				array(
					'key' => 'value-set-2',
					'name' => 'Value Set 2',
					'value' => '00002'
				)
			)
		),
		array(
			'key' => 'Questions[41415]',
			'label' => 'Checkbox',
			'id' => 'cb-sample',
			'type' => 'checkbox',
			'require' => true,
			'set' => array(
				'label' => 'Nice Value',
				'value' => '00003',
				'key' => 'post-value',
				'no' => 'Negative Answer'
			)
		),
		array(
			'label' => 'Test',
			'type' => 'submit',
			'id' => 'test-submitter',
			'receiver' => array('Administrator', 'richard.y.ong@outlook.com'),
			'defaults' => array(
				'disclaimer' => 'This email is intended only for the person(s) named in the message header. Unless otherwise indicated, it contains information that is confidential, privileged and/or exempt from disclosure under applicable law. If you have received this message in error, please notify the sender of the error and delete the message. Thank you.',
				'empty' => 'Unspecified'
			),
			'prompts' => array(
				'success' => array(
					'title' => 'Registration Complete',
					'message' => 'We appreciate you contacting us. You are now added to our mailing list and will now be among the first ones to receive updates. Thank you for your interest.'
				),
				'verify_error' => array(
					'title' => 'Submission Failed',
					'message' => 'Unfortunately, your submission was not completed due to some missing or incorrect information. Please fill in all fields that are marked with an asterisk (*) correctly. The fields that need editing are highlighted in red.'
				),
				'submit_error' => array(
					'title' => 'Submission Failed',
					'message' => 'There was a connection issue and we cannot connect to the submission system. You may come back and try again on a later date if you wish to try again. We apologize for the inconvenience.'
				)
			)
		)
	);
	//----

	$build = array();

	if(!$match) {
		foreach($fields as $field) {
			$built = array(
				'label' => $field['label'],
				'id' => $field['id'],
				'type' => $field['type'],
			);

			if($field['type'] != 'submit') {
				$built['require'] = $field['require'];
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
			else if($field['type'] == 'checkbox') {
				$built['value'] = $field['set']['key'];
			}

			if($field['type'] == 'submit') {
				$built['receiver'] = $field['receiver'];
				$built['prompts'] = $field['prompts'];
				$built['defaults'] = $field['defaults'];
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

function get_match($inputName, $inputValue, $defaultValue, $crm) {
	$matches = get_fields(true);

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