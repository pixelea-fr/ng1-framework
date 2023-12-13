<?php  
class Ng1LoadThemeAcfBLocks {

private static $instance = null;

/**
 * Récupère l'instance unique de la classe en utilisant le modèle Singleton.
 *
 * @return Ng1LoadThemeAcfBLocks
 */
public static function get_instance() {
    if (null === self::$instance) {
        self::$instance = new self();
    }
    return self::$instance;
}

/**
 * Constructeur privé pour s'assurer qu'une seule instance est créée.
 */
private function __construct() {
    // Actions et filtres WordPress
    add_action( 'init', array( $this, 'load_blocks' ), 5 );
    add_filter( 'acf/settings/load_json', array( $this, 'load_acf_field_group' ) );
    add_filter( 'block_categories_all', array( $this, 'block_categories' ), 10, 2 );
    add_action('wp_enqueue_scripts', array($this, 'register_block_script_from_folder'));
}

/**
 * Charge les blocs.
 */
public function load_blocks() {
    // Récupère le thème en cours
    $theme  = wp_get_theme();
    // Récupère la liste des blocs
    $blocks = $this->get_blocks();

    foreach( $blocks as $block ) {
        // Vérifie si le fichier block.json existe
        if ( file_exists( get_stylesheet_directory() . '/acf-blocks/' . $block . '/block.json' ) ) {
            // Enregistre le type de bloc
            register_block_type( get_stylesheet_directory() . '/acf-blocks/' . $block . '/block.json' );
        }
    }
}

/**
 * Enregistre les scripts JavaScript des sous-répertoires des blocs.
 */
public function register_block_script_from_folder() {
    // Chemin vers le répertoire principal des blocs
    $block_directory = get_stylesheet_directory() . '/acf-blocks/';

    // Vérifie si le répertoire principal des blocs existe
    if (!file_exists($block_directory) || !is_dir($block_directory)) {
        wp_mkdir_p($block_directory);
        return;
    }
        // Vérifie si le répertoire principal des blocs existe


    // Récupère la liste de tous les sous-répertoires dans le répertoire principal des blocs
    $block_folders = scandir($block_directory);

    // Parcours chaque sous-répertoire
    foreach ($block_folders as $block) {
        // Ignore les répertoires '.' et '..'
        if ($block === '.' || $block === '..') {
            continue;
        }

        // Chemin vers le fichier 'function.js' dans le sous-répertoire actuel
        $js_file_path = $block_directory . $block . '/assets/js/function.js';

        // Vérifie si le fichier 'function.js' existe
        if (file_exists($js_file_path)) {
            // Génère un identifiant basé sur le nom du sous-répertoire
            $handle = sanitize_title($block);

            // Définit l'URL source du script
            $src = get_stylesheet_directory_uri() . '/acf-blocks/' . $block . '/assets/js/function.js';

            // Enregistre le script
            wp_enqueue_script($handle, $src, array('jquery'), null, true);
        }
    }
}

/**
 * Charge les groupes de champs ACF pour les blocs.
 */
public function load_acf_field_group( $paths ) {
    $blocks = $this->get_blocks();
    foreach( $blocks as $block ) {
        $paths[] = get_stylesheet_directory() . '/acf-blocks/' . $block.'/acf';
    }
    return $paths;
}

/**
 * Récupère la liste des blocs.
 */
public function get_blocks() {
    $theme   = wp_get_theme();
    $blocks  = get_option( 'cwp_blocks' );
    $version = get_option( 'cwp_blocks_version' );

    // Vérifie si la liste des blocs est vide ou si la version du thème a changé
    if ( empty( $blocks ) || version_compare( $theme->get( 'Version' ), $version ) || ( function_exists( 'wp_get_environment_type' ) && 'production' !== wp_get_environment_type() ) ) {
        // Récupère la liste des sous-répertoires dans le répertoire des blocs
        $blocks = scandir( get_stylesheet_directory() . '/acf-blocks/' );
        // Supprime les éléments indésirables
        $blocks = array_values( array_diff( $blocks, array( '..', '.', '.DS_Store', '_base-block' ) ) );

        // Met à jour les options
        update_option( 'cwp_blocks', $blocks );
        update_option( 'cwp_blocks_version', $theme->get( 'Version' ) );
    }
    return $blocks;
}

/**
 * Catégories de blocs.
 */
public function block_categories($block_categories, $editor_context) {
    if (!empty($editor_context->post)) {
        array_unshift(
            $block_categories,
            array(
                'slug'  => 'pixelea',
                'title' => __('pixelea', 'pixelea'),
                'icon'  => 'games',
            )
        );
    }
    return $block_categories;
}
}

// Instancie la classe en utilisant le modèle Singleton
Ng1LoadThemeAcfBLocks::get_instance();
