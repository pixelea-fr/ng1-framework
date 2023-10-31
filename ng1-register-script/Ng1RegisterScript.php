<?php
class ng1RegisterScript {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_front_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
    }

    public function register_front_scripts() {

        $this->register_style('aos', 'https://unpkg.com/aos@next/dist/aos.css');
        $this->register_script('aos', 'https://unpkg.com/aos@next/dist/aos.js', array('jquery'), '4.0', true);
        //SLICK
        $this->register_style('slick','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        $this->register_script('slick','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
        //FANCYBOX
        $this->register_style('fancy','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');
        $this->register_script('fancy','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js', array('jquery'), '4.0', true);
         //LEAFLET
        $this->register_style('leaflet','https://unpkg.com/leaflet@1.9.3/dist/leaflet.css');
        $this->register_script('leaflet','https://unpkg.com/leaflet@1.9.3/dist/leaflet.js', array('jquery'), '4.0', true);
        //SCROLIFY
        $this->register_script('scrollify',plugins_url('', __FILE__) ."/assets/js/jquery.scrollify.js", array('jquery'), '4.0', true);
        //THEME SCRIPT
        $this->register_script('theme-js',get_stylesheet_directory_uri()."/assets/js/function.js", array('jquery'), '1.8.1', true);
    }
    public function register_admin_scripts() {
        $this->register_style('aos', 'https://unpkg.com/aos@next/dist/aos.css');
        $this->register_script('aos', 'https://unpkg.com/aos@next/dist/aos.js', array('jquery'), '4.0', true);
        //SLICK
        $this->register_style('slick','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        $this->register_script('slick','//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
        //FANCYBOX
        $this->register_style('fancy','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');
        $this->register_script('fancy','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js', array('jquery'), '4.0', true);
         //LEAFLET
        $this->register_style('leaflet','https://unpkg.com/leaflet@1.9.3/dist/leaflet.css');
        $this->register_script('leaflet','https://unpkg.com/leaflet@1.9.3/dist/leaflet.js', array('jquery'), '4.0', true);
        //SCROLIFY
        $this->register_script('scrollify',plugins_url('', __FILE__) ."/assets/js/jquery.scrollify.js", array('jquery'), '4.0', true);
    }


    public function register_script($handle, $src, $deps = array(), $ver = false, $in_footer = false) {
        // Vérifiez d'abord si le script est déjà enregistré
        if (!wp_script_is($handle, 'registered')) {
            wp_register_script($handle, $src, $deps, $ver, $in_footer);
        }
    }


    public function register_style($handle, $src, $deps = array(), $ver = false, $media = 'all') {
        // Vérifiez d'abord si le style est déjà enregistré
        if (!wp_style_is($handle, 'registered')) {
            wp_register_style($handle, $src, $deps, $ver, $media);
        }
    }

}
$ng1_script_manager = new ng1RegisterScript();