<?php
/**
 * Plugin Name: Ng1 Composition
 * Plugin URI: http://www.ng1.fr
 * Description: Permet d'ajouter des compositions et de les catégoriser
 * Version: 1.0
 * Author: Nicolas GEHIN
 * Author URI: http://www.ng1.fr
 * License: GPL2
 */

//===============================================================================================================================
// Activation du plugin
// Lance le plugin quand tous les autres sont chargés
// Message d'erreur si ACF et NG1_TOOLBOX ne sont pas disponibles
//===============================================================================================================================

add_action('plugins_loaded', 'ng1_composition_init');
function ng1_composition_init() {
    if ( !class_exists('ACF')) {
        add_action('admin_notices', 'ng1_composition__error');
        return;
    } else {
        require_once 'Ng1Composition.php';
        global $ng1_composition;
        $ng1_composition = new ng1Composition();
    }
}
function ng1_composition__error() {
    $class = 'notice notice-error';
    $message = __('Le plugin Ng1 Acf Block nécessite le plugin ACF pour fonctionner', 'ng1');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}