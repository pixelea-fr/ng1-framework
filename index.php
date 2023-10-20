<?php
/*
Plugin Name: Ng1 Framework Plugin
Description: A WordPress framework plugin with reusable classes.
Version: 1.0
Author: Votre nom
*/
// Inclure la classe Ng1Ajax

require_once plugin_dir_path(__FILE__) . '_utilities/index.php';

require_once plugin_dir_path(__FILE__) . 'ng1-register-script/index.php';

include_once plugin_dir_path(__FILE__) . 'ng1-hybrid-theme/index.php';

require_once plugin_dir_path(__FILE__) . 'ng1-theme-json-folder/index.php';
require_once plugin_dir_path(__FILE__) . 'ng1-front-menu/index.php';
require_once plugin_dir_path(__FILE__) . 'ng1-acf-blocks/index.php';

require_once plugin_dir_path(__FILE__) . 'ng1-sql-export/index.php';
require_once plugin_dir_path(__FILE__) . 'ng1-theme-json-to-scss/index.php';
require_once plugin_dir_path(__FILE__) . 'ng1-generate-block-styles/index.php';
require_once plugin_dir_path(__FILE__) . 'ng1-zip-theme-folders/index.php';
//require_once plugin_dir_path(__FILE__) . 'ng1-zip.php';

// Activation du plugin
function ng1_framework_plugin_activate() {
    // Ajoutez des opérations d'activation si nécessaire
}
register_activation_hook(__FILE__, 'ng1_framework_plugin_activate');

// Désactivation du plugin
function ng1_framework_plugin_deactivate() {
    // Ajoutez des opérations de désactivation si nécessaire
}
register_deactivation_hook(__FILE__, 'ng1_framework_plugin_deactivate');

