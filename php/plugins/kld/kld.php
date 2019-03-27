<?php
/*
Plugin Name: KLD Data Management – 2019
Description: Core Engine for all customization in this current WordPress installation.
Author: KLD
Version: 1.0
Author URI: http://kevinlouisdesign.com
*/

function fm_this_plugin() {
	return plugin_dir_url(__FILE__);
}
include_once('acf-modifications/custom-types/acf-code-field/acf-code-field.php');
include_once('acf-modifications/custom-types/post-type/acf-kld-post-type.php');
include_once('acf-modifications/custom-types/dashicon/acf-kld-dashicon.php');
include_once('acf-modifications/custom-types/vector-image/acf-vector-image.php');
include_once('acf-modifications/pre-build-fields/data-dash.php');
include_once('third-party/storage.php');
include_once('actions/cpt.php');
include_once('actions/overrides.php');
include_once('actions/general-settings.php');
include_once('actions/scripts.php');
include_once('actions/triggers.php');
include_once('analyze.php');
?>