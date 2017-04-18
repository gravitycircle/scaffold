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
			'key' => 'lasso-key',
			'label' => 'Lasso Field',
			'id' => 'field-id',
			'type' => 'text',
			'require' => true
		),
		array(
			'key' => 'lasso-key3',
			'label' => 'Lasso Paragraph',
			'id' => 'field-id-2',
			'type' => 'paragraph',
			'require' => true
		),
		array(
			'key' => 'lasso-key2',
			'label' => 'Dropdown',
			'id' => 'dd-sample',
			'type' => 'dropdown',
			'require' => true,
			'values' => array(
				array(
					'key' => 'value-set-1',
					'name' => 'Value Set 1',
					'value' => 'lasso-value-1'
				),
				array(
					'key' => 'value-set-2',
					'name' => 'Value Set 2',
					'value' => 'lasso-value-2'
				)
			)
		),
		array(
			'key' => 'lasso-key-3',
			'label' => 'Checkbox',
			'id' => 'cb-sample',
			'type' => 'checkbox',
			'require' => true,
			'set' => array(
				'label' => 'Nice Value',
				'value' => 'lasso-value',
				'key' => 'post-value',
				'no' => 'Negative Answer'
			)
		),
		array(
			'label' => 'Test',
			'type' => 'submit',
			'id' => 'test-submitter'
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
					'label' => $field['key'],
					'type' => 'dropdown',
					'matches' => $vars
				);
			}
			else if($field['type'] == 'checkbox') {
				$build[$field['id']] = array(
					'key' => $field['key'],
					'label' => $field['key'],
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
				$build[$field['id']] = array(
					'key' => $field['key'],
					'label' => $field['label'],
					'type' => $field['type']
				);
			}
		}
	}

	return $build;
}

function get_match($inputName, $inputValue, $crm) {
	$matches = get_fields(true);

	if($matches[$inputName]['type'] == 'dropdown') {
		if($crm) {
			return array(
				'key' => $matches[$inputName]['key'],
				'value' => $matches[$inputName]['matches'][$inputValue]['val']
			);
		}
		else{
			return array(
				'key' => $matches[$inputName]['label'],
				'value' => $matches[$inputName]['matches'][$inputValue]['label']
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
					'value' => $matches[$inputName]['no']
				);
			}
		}
	}
	else {
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

?>