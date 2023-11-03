<?php

class Ng1LoadThemeAcfBLocks {
    
    /**
     * Constructor for the Ng1AcfBlocks class.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'load_blocks' ), 5 );
        add_filter( 'acf/settings/load_json', array( $this, 'load_acf_field_group' ) );
        add_filter( 'block_categories_all', array( $this, 'block_categories' ),10,2 );
        add_action('wp_enqueue_scripts', array($this, 'register_block_script_from_folder'));
    }

    /**
     * Load Blocks
     */
    public function load_blocks() {
        $theme  = wp_get_theme();
        $blocks = $this->get_blocks();

        foreach( $blocks as $block ) {
            if ( file_exists( get_stylesheet_directory() . '/acf-blocks/' . $block . '/block.json' ) ) {
                register_block_type( get_stylesheet_directory() . '/acf-blocks/' . $block . '/block.json' );
            }
        }
    }
    /**
     * Enregistre les scripts JavaScript des sous-répertoires des blocs.
     *
     * Cette fonction recherche un fichier 'function.js' dans chaque sous-répertoire du répertoire 'acf-blocks'
     * et l'enregistre comme script avec un identifiant basé sur le nom du sous-répertoire.
     */
    function register_block_script_from_folder() {
        // Chemin vers le répertoire principal des blocs
        $block_directory = get_stylesheet_directory() . '/acf-blocks/';

        // Vérifie si le répertoire principal des blocs existe
        if (!is_dir($block_directory)) {
            return;
        }

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
     * Load ACF field groups for blocks
     */
    public function load_acf_field_group( $paths ) {
        $blocks = $this->get_blocks();
        foreach( $blocks as $block ) {
            $paths[] = get_stylesheet_directory() . '/acf-blocks/' . $block.'/acf';
        }
        return $paths;
    }

    /**
     * Get Blocks
     */
    public function get_blocks() {
        $theme   = wp_get_theme();
        $blocks  = get_option( 'cwp_blocks' );
        $version = get_option( 'cwp_blocks_version' );
        if ( empty( $blocks ) || version_compare( $theme->get( 'Version' ), $version ) || ( function_exists( 'wp_get_environment_type' ) && 'production' !== wp_get_environment_type() ) ) {
            $blocks = scandir( get_stylesheet_directory() . '/acf-blocks/' );
            $blocks = array_values( array_diff( $blocks, array( '..', '.', '.DS_Store', '_base-block' ) ) );

            update_option( 'cwp_blocks', $blocks );
            update_option( 'cwp_blocks_version', $theme->get( 'Version' ) );
        }
        return $blocks;
    }

    /**
     * Block categories
     *
     * @since 1.0.0
     */
 

	
	function block_categories($block_categories, $editor_context)
	{
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

new Ng1LoadThemeAcfBLocks();
