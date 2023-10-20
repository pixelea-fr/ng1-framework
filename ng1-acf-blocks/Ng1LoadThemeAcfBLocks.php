<?php

class Ng1LoadThemeAcfBLocks {
    
    /**
     * Constructor for the Ng1AcfBlocks class.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'load_blocks' ), 5 );
        add_filter( 'acf/settings/load_json', array( $this, 'load_acf_field_group' ) );
        add_filter( 'block_categories_all', array( $this, 'block_categories' ),10,2 );
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
