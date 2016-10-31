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

function get_fields($lasso) {
	//---BUILD FIELDS HERE
	$fields = array(
		array(
			'key' => 'lasso-key',
			'label' => 'Lasso Field',
			'id' => 'field-id',
			'type' => 'email',
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
					'value' => 'value-set-1',
					'name' => 'Value Set 1',
					'lasso' => 'lasso-value-1'
				),
				array(
					'value' => 'value-set-2',
					'name' => 'Value Set 2',
					'lasso' => 'lasso-value-2'
				)
			)
		),
		array(
			'key' => 'lasso-key-3',
			'label' => 'Checkbox',
			'id' => 'cb-sample',
			'type' => 'checkbox',
			'require' => false,
			'set' => array(
				'lasso' => 'lasso-value',
				'value' => 'post-value'
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

	if($lasso) {
		foreach($fields as $field) {
			if($field['type'] != 'submit') {
				$built = array(
					'id' => $field['id'],
					'key' => $field['key']
				);

				if($field['type'] == 'dropdown'){
					$vals = array();
					foreach($field['values'] as $v) {
						array_push($vals, array(
							'value' => $v['lasso'],
							'id' => $v['value']
						));
					}

					$built['matches'] = $vals;
				}
				else if($field['type'] == 'checkbox') {
					$build['match'] = $field['set']['lasso'];
				}

				array_push($build, $built);
			}
		}
	}
	else{
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
						'value' => $v['value'],
						'name' => $v['name']
					));
				}

				$built['values'] = $vals;
			}
			else if($field['type'] == 'checkbox') {
				$built['value'] = $field['set']['value'];
			}

			array_push($build, $built);
		}
	}

	return $build;
}
?>