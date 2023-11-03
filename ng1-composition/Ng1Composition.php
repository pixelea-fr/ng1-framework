<?php

class ng1Composition {
    public function __construct() {
        add_action('admin_menu', array($this,'add_option_page_ng1'));
        add_action('init', array($this, 'remove_theme_support'));
        //CPT
        add_action('init', array($this, 'cpt_pattern'));
        //taxonomie
        add_action('init', array($this, 'register_pattern_categorie'));
        //Default Terms
          add_action('init', array($this, 'register_block_pattern_category'));
          add_action('init', array($this, 'ng1_block_patterns_categorie'));
          //GENERATION DU PATTERN JSON dans le dossier du template
          add_action('save_post', array($this,'save_composition_as_block_pattern_json'));
          //Enregistrement des pattern a partir des json du dossier
          add_action('init',  array($this,'register_block_patterns_from_files'));
          //Mise en page par defaut d'un type de contenu
          add_filter('default_content', array($this, 'ng1_default_post_type_template'), 10, 2);


          if (get_option('ng1_pattern_categories_registered') !== 'registered') {
              // Si les termes n'ont pas encore été enregistrés, on les enregistre
              add_action('init', array($this, 'create_pattern_categories'));
              // Marquez les termes comme enregistrés pour éviter de les réenregistrer
              update_option('ng1_pattern_categories_registered', 'registered');
          }
    }

    public function remove_theme_support() {
        remove_theme_support('core-block-patterns');
    }

    public function register_block_pattern_category() {
        register_block_pattern_category('template', array('label' => 'Template de page'));
        register_block_pattern_category('sans-cat', array('label' => 'Non catégorisé'));
    }

    /* ------------------------------------------
       Ajout de la page d'option
    ------------------------------------------ */
    public function add_option_page_ng1() {
        add_submenu_page(
            'edit.php?post_type=pattern',  // Slug du menu parent
            'Assignation',  // Titre de la sous-page
            'Assignation',  // Titre de la sous-page dans le backend
            'edit_posts',  // Capacité nécessaire pour accéder à la sous-page
            'ng1-assignation',  // Slug de la sous-page
            array($this,'ng1_assignation_page')   // Callback vide
        );
    }
    public function get_custom_post_types() {
        $args = array(
            "show_ui"=>true
        );
    
        $custom_post_types = get_post_types($args, 'names');
        $excluded_post_types = array(
            'attachment',
            'wp_block',
            'wp_navigation',
            'acf-taxonomy',
            'acf-post-type',
            'acf-field-group',
            'pattern'
        );
        $filtered_post_types = array_diff($custom_post_types, $excluded_post_types);
 
        return  $filtered_post_types;
    }
    
    public function ng1_assignation_page() {
        $custom_post_types = $this->get_custom_post_types();
    
        ob_start(); // Démarre la mise en tampon de la sortie
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Vérifiez si le formulaire a été soumis
            foreach ($custom_post_types as $post_type) {
                // Récupérez la valeur sélectionnée pour cet CPT
                $selected_pattern = isset($_POST['assigned_pattern_' . $post_type]) ? $_POST['assigned_pattern_' . $post_type] : '';
                // Enregistrez la valeur sélectionnée en tant que métadonnée
                update_option('assigned_pattern_' . $post_type, $selected_pattern);
            }
        }
        ?>
        <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>">
            <?php foreach ($custom_post_types as $post_type) { ?>
                <div class="ng1-assignation-metabox">
                    <label for="assigned_pattern_<?php echo $post_type; ?>">Template pour <?php echo $post_type; ?>:</label>
                    <div>
                        <select name="assigned_pattern_<?php echo $post_type; ?>" id="assigned_pattern_<?php echo $post_type; ?>">
                            <option value="">Aucun</option>
                            <?php
                            $assigned_pattern = get_option('assigned_pattern_' . $post_type, true);
                            $patterns = get_posts(array(
                                'post_type' => 'pattern',
                                'numberposts' => -1,
                            ));
                            foreach ($patterns as $pattern) {
                                $pattern_id = $pattern->ID;
                                $pattern_title = get_the_title($pattern_id);
                                $selected = selected($assigned_pattern, $pattern_id, false);
                                echo '<option value="' . $pattern_id . '" ' . $selected . '>' . $pattern_title . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php submit_button(); ?>
        </form>
        <?php
        $output = ob_get_clean(); // Obtient le contenu tamponné et le nettoie
        echo $output; // Affiche le contenu tamponné
    }
    


    public function cpt_pattern() {
        $icon = '<svg id="Bold" fill="rgb(0,0,0)" enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path d="M23.25 7.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M23.25 10.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M23.25 13.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M23.25 16.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M23.25 19.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M1.75 15h7.5c.965 0 1.75-.785 1.75-1.75v-4c0-.965-.785-1.75-1.75-1.75h-7.5c-.965 0-1.75.785-1.75-1.75v4c0 .965.785 1.75 1.75 1.75z"/><path d="M1.75 24h7.5c.965 0 1.75-.785 1.75-1.75v-4c0-.965-.785-1.75-1.75-1.75h-7.5c-.965 0-1.75.785-1.75-1.75v4c0 .965.785 1.75 1.75 1.75z"/><path d="M23.25 22.5h-9.5c-.414 0-.75.336-.75.75s.336.75.75.75h9.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75z"/><path d="M22.25 0h-12.5c-.965 0-1.75.785-1.75 1.75v2.5c0 .965.785 1.75 1.75 1.75h12.5c.965 0 1.75-.785 1.75-1.75v-2.5c0-.965-.785-1.75-1.75-1.75z"/><circle cx="3" cy="3" r="3"/></svg>';
        $labels = array(
            'name' => __('Compositions', 'ng1'),
            'singular_name' => __('Composition', 'ng1'),
            'add_new' => __('Ajouter', 'ng1', 'ng1'),
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
            'name' => __('Types', 'ng1'),
            'singular_name' => __('Type', 'ng1'),
        ];

        $args = [
            'label' => __('Type', 'ng1'),
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'query_var' => true,
            'show_admin_column' => false,
            'show_in_rest' => true,
            'rest_base' => 'pattern_categorie',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
            'show_in_quick_edit' => false,
        ];
        register_taxonomy('pattern_categorie', ['pattern'], $args);
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
            'taxonomy' => 'pattern_categorie',
        ]);
        foreach ($terms as $term) {
            register_block_pattern_category($term->slug, array('label' => $term->name));
        }
    }

    // -----------------------------------------------------------------------------
    // NG1 default template
    // -----------------------------------------------------------------------------
        public function ng1_default_post_type_template($content, $post) {
        $custom_post_types = $this->get_custom_post_types();

            // Vérifiez si le formulaire a été soumis
        foreach ($custom_post_types as $post_type) {
            if ($post->post_type == $post_type) {
                $template_id = get_option('assigned_pattern_' . $post_type);
                $html = get_post_field('post_content', $template_id );
                if(!empty($html)) {
                    return $html;
                }
            }
        }
        return $content;
    }


   
    function save_composition_as_block_pattern_json($post_id) {

  
        // Vérifiez si le post en cours est un CPT "composition"
        if (get_post_type($post_id) == 'pattern') {
            // Récupérez les données nécessaires depuis le post
            $title = get_the_title($post_id);
            $description = get_the_excerpt($post_id);
            $content = get_post_field('post_content', $post_id);
     
            $categories = ['sans-cat'];
            $cats = get_the_terms($post_id, 'pattern_categorie');
            if (!empty($cats)) {
                foreach ($cats as $cat) {
                    array_push($categories, $cat->slug);
                }
            }
    
            $pattern_data = array(
                'title' => $title,
                'description' => $description,
                'content' => $content,
                'categories' => $categories,
            );
    
            // Créez le nom de fichier à partir du slug du post
            $post_slug = sanitize_title($title);
            $file_name = get_stylesheet_directory() . '/pattern/' . $post_slug . '.json';
    
            // Assurez-vous que le dossier pattern existe, sinon, créez-le
            $pattern_directory = dirname($file_name);
            if (!file_exists($pattern_directory)) {
                wp_mkdir_p($pattern_directory);
            }
    
            // Enregistrez les données dans un fichier JSON
            file_put_contents($file_name, json_encode($pattern_data));
        }
    }
    function register_block_patterns_from_files() {
        $pattern_directory = get_stylesheet_directory() . '/pattern/';
    
        // Vérifiez si le dossier pattern existe
        if (!is_dir($pattern_directory)) {
            return; // Si le dossier n'existe pas, quittez la fonction
        }
    
        // Obtenez la liste de fichiers JSON dans le dossier pattern
        $json_files = glob($pattern_directory . '*.json');
    
        // Parcourez chaque fichier JSON et enregistrez-le comme modèle de bloc
        foreach ($json_files as $json_file) {
            $pattern_data = json_decode(file_get_contents($json_file), true);
    
            if ($pattern_data) {
                $pattern_slug = basename($json_file, '.json');
                register_block_pattern($pattern_slug, $pattern_data);
            }
        }
    }

}