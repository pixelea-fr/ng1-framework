<?php class ng1FrontMenuTabManager {
    public function __construct() {
       // add_action('wp_enqueue_scripts',  array($this, 'load_styles'), 103);
        //add_action('wp_footer',  array($this, 'display_tabs'), 100);
    }

    public function load_styles() {
        // Code pour charger les styles CSS des onglets
    }

    public function add_tab($tab_id, $tab_name ) {
        add_action('ng1_front_menu', function() use ($tab_id, $tab_name) {
            echo '<div class="ng1-tab" data-tab="' . esc_attr($tab_id) . '">' . do_action('ng1_tab_content_' .$tab_id ).'</div>';
        });
    }

}