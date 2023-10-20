<?php
class ng1ThemeHybrid {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'add_guttenberg_styles'), 100);
        add_action('wp_enqueue_scripts', array($this, 'ng1_extend_guttenberg_styles'), 102);
    }
    public function add_guttenberg_styles() {
        wp_enqueue_style('wp-block-library', false, array(), null, 'all'); // Charge les styles CSS du bloc Gutenberg dans l'en-tête
        wp_enqueue_style('wp-block-library-theme', false, array(), null, 'all'); // Charge les styles du thème pour les blocs Gutenberg dans l'en-tête
        wp_enqueue_style('theme-css', get_stylesheet_uri(), array(), null, 'all'); // Charge les styles CSS du bloc Gutenberg dans l'en-tête
    }
    public function ng1_extend_guttenberg_styles() {
        $plugin_url = plugins_url('', __FILE__);
        wp_enqueue_style('ng1-hybrid',$plugin_url . '/assets/css/style.css', array(), null, 'all');

    }

}
$ng1_theme_hybrid = new ng1ThemeHybrid();