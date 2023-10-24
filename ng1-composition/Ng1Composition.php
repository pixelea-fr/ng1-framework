<?php
class ng1Composition {
    public function __construct() {
        // Ajout des actions et des filtres ici
        add_action('init',  array($this, 'remove_theme_support'));
        add_action('init',  array($this, 'cpt_pattern'));
        add_action('init',  array($this, 'register_pattern_categorie'));
        add_action('init',  array($this, 'register_block_pattern_category'));
        add_action('init',  array($this, 'ng1_block_patterns_categorie'));
        add_action('init',  array($this, 'ng1_block_patterns'));
        add_filter('default_content',  array($this, 'ng1_default_post_type_template'), 10, 2);
        
        if (get_option('ng1_pattern_categories_registered') !== 'registered') {
            // Si les termes n'ont pas encore été enregistrés, on les enregistre
            add_action('init',array($this,'create_pattern_categories'));
            // Marquez les termes comme enregistrés pour éviter de les réenregistrer
            update_option('ng1_pattern_categories_registered', 'registered');
        }

    }

    public function remove_theme_support() {
        remove_theme_support('core-block-patterns');
    }

    public function register_block_pattern_category() {
        register_block_pattern_category("template", array('label' => "Template de page"));
        register_block_pattern_category("sans-cat", array('label' => "Non catégorisé"));
    }

    /* ------------------------------------------
       Ajout de la page d'option
    ------------------------------------------ */
    public function add_option_page_ng1() {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_sub_page(array(
                'page_title' => "Compositions",
                'menu_title' => 'Compositions',
                'menu_slug' => 'ng1-composition',
                'capability' => 'edit_posts',
                'icon_url' => 'dashicons-block-default',
                'redirect' => false,
                'parent_slug' => 'ng1'
            ));
            acf_add_options_sub_page(array(
                'page_title' => "Assignation",
                'menu_title' => 'Assignation',
                'menu_slug' => 'ng1-assignation',
                'capability' => 'edit_posts',
                'icon_url' => 'dashicons-block-default',
                'redirect' => false,
                'parent_slug' => 'ng1'
            ));
        }
    }

    public function autofill_post_type_block($field) {
        $all = get_post_types();
        $choices = array_diff($all, array("acf-field", "acf-field-group", "nav_menu_item", "attachment", "revision", "custom_css", 'customize_changeset', 'oembed_cache', 'user_request', "wp_navigation", "wp_global_styles", 'wp_template', 'wp_template_part', "wp_block"));
        $field["choices"] = $choices;
        return $field;
    }

    public function cpt_pattern() {
        $icon = '<svg id="Bold" fill="rgb(0,0,0)" enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m23.25 7.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m23.25 10.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m23.25 13.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m23.25 16.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m23.25 19.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m1.75 15h7.5c.965 0 1.75-.785 1.75-1.75v-4c0-.965-.785-1.75-1.75-1.75h-7.5c-.965 0-1.75.785-1.75-1.75v4c0 .965.785 1.75 1.75 1.75z"/><path d="m1.75 24h7.5c.965 0 1.75-.785 1.75-1.75v-4c0-.965-.785-1.75-1.75-1.75h-7.5c-.965 0-1.75.785-1.75 1.75v4c0 .965.785 1.75 1.75 1.75z"/><path d="m23.25 22.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="m22.25 0h-12.5c-.965 0-1.75.785-1.75 1.75v2.5c0 .965.785 1.75 1.75 1.75h12.5c.965 0 1.75-.785 1.75-1.75v-2.5c0-.965-.785-1.75-1.75-1.75z"/><circle cx="3" cy="3" r="3"/></svg>';
        $labels = array(
            'name' => __('Compositions', 'ng1'),
            'singular_name' => __('Composition', 'ng1'),
            'add_new' => _x('Ajouter', 'ng1', 'ng1'),
            'add_new_item' => __('Ajouter un élément', 'ng1'),
            'edit_item' => __('Editer l\'élémént', 'ng1'),
            'new_item' => __('Nouveau', 'ng1'),
            'view_item' => __('Voir', 'ng1'),
            'search_items' => __('Rechercher une composition', 'ng1'),
            'not_found' => __('Aucune composition', 'ng1'),
            'not_found_in_trash' => __('Aucun pattern dans la corbeille', 'ng1'),
            'parent_item_colon' => __('Elément parent', 'ng1'),
            'menu_name' => __('Compositions', 'ng1'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'description',
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => false,
            'menu_position' => null,
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode($icon),
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
            ),
        );

        register_post_type('pattern', $args);
    }

    public function register_pattern_categorie() {
        $labels = [
            "name" => __("Types", "ng1"),
            "singular_name" => __("Type", "ng1"),
        ];

        $args = [
            "label" => __("Type", "ng1"),
            "labels" => $labels,
            "public" => true,
            "publicly_queryable" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => false,
            "query_var" => true,
            "show_admin_column" => false,
            "show_in_rest" => true,
            "rest_base" => "pattern_categorie",
            "rest_controller_class" => "WP_REST_Terms_Controller",
            "show_in_quick_edit" => false,
        ];
        register_taxonomy("pattern_categorie", ["pattern"], $args);
    }
    public function create_pattern_categories() {
        $categories = array(
            'Header' => 'Modèle de page pour l\'en-tête',
            'Footer' => 'Modèle de page pour le pied de page',
            'Modèle de page' => 'Modèle de page générique',
            'Media Texte' => 'Modèle de page pour du contenu média et texte',
            'Texte' => 'Modèle de page pour du contenu texte',
            'Image' => 'Modèle de page pour des images',
            'Vidéo' => 'Modèle de page pour des vidéos',
        );
    
        foreach ($categories as $category => $description) {
            wp_insert_term($category, 'pattern_categorie', array(
                'description' => $description,
            ));
        }
    }

    public function ng1_block_patterns_categorie() {
        $terms = get_terms([
            'taxonomy' => "pattern_categorie",
        ]);
        foreach ($terms as $term) {
            register_block_pattern_category($term->slug, array('label' => $term->name));
        }
    }

    // -----------------------------------------------------------------------------
    // NG1 default template
    // -----------------------------------------------------------------------------
    public function ng1_default_post_type_template($content, $post) {
        $template_postype = get_field('custom_post_ui_default', 'option');
        $content = "";
        if (!empty($template_postype) && is_array($template_postype)) {
            foreach ($template_postype as $item) {
                if ($post->post_type == $item['post_type']) {
                    $post_actu = get_post($item['template']);
                    $content = $post_actu->post_content;
                }
            }
        }
        return $content;
    }

    public function ng1_block_patterns() {
        $args = array(
            'post_type' => 'pattern',
            'posts_per_page' => -1,
        );

        // 2. on exécute la query
        $query = new WP_Query($args);

        // 3. on lance la boucle !
        if ($query->have_posts()):
            while ($query->have_posts()):
                $query->the_post();
                $categories = ["sans-cat"];
                $cats = get_the_terms(get_the_ID(), "pattern_categorie");
                if (!empty($cats)) {
                    foreach ($cats as $cat) {
                        array_push($categories, $cat->slug);
                    }
                }
                register_block_pattern(
                    'ng1-pattern/' . get_post_field('post_name'),
                    array(
                        'title' => __(get_the_title(), 'page-intro-block'),
                        'description' => get_the_excerpt(),
                        'content' => get_the_content(),
                        'categories' => $categories,
                    )
                );
            endwhile;
        endif;

        // 4. On réinitialise à la requête principale (important)
        wp_reset_postdata();
    }
}
