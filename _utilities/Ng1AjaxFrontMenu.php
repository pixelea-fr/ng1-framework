<?php 
   /**
     * Classe Ng1AjaxFrontMenu
     * 
     * - Déclaration d'une action ajax (SLUG)
     * - Création des actions  correspondantes :
     *      add_action('wp_ajax_SLUG et add_action('wp_ajax_nopriv_SLUG
     * - Load la fonction callBack qui ajoute le scriptJs correspondant
     * - Créé le BOuton sur le Front Menu
     * 
     */
class Ng1AjaxFrontMenu extends Ng1Ajax {

    // Ajoutez la variable $menu_position
    private $menu_position;
    private $menu_label;
    private $filter_identifier;
    private $filter_base_tag;
    /**
     * Constructeur de la classe Ng1AjaxMenu.
     *
     * @param string $ajax_action Le nom de l'action Ajax.
     * @param callable $callback_function La fonction de rappel pour la requête Ajax.
     * @param callable $load_js_callback La fonction de rappel utilisée pour charger des ressources JavaScript. Il peut être soit un appel à une fonction de la classe actuelle en utilisant array($this, 'nom_de_la_fonction'), ce qui vous permet d'utiliser une fonction personnalisée de la classe, soit une chaîne de caractères unique pour charger le script par défaut en utilisant les paramètres spécifiques à l'instance.
     * @param int $menu_position La priorité pour l'action ng1_front_menu.
     * @param string $menu_label Le libellé du menu.
     * @param string $filter_identifier L'hook identifier perrmet d'ajouter des filtres:
     * - ng1_ajax_front_menu_ $filter_identifier _before
     * - ng1_ajax_front_menu_ $filter_identifier _after
     * - ng1_ajax_front_menu_ $filter_identifier _data
     * - ng1_ajax_front_menu_ $filter_identifier _success_message
     * - ng1_ajax_front_menu_ $filter_identifier _error_message
     * - ng1_ajax_front_menu_ $filter_identifier _output
     */
    public function __construct($ajax_action, $callback_function, $load_js_callback = null, $menu_position = 10, $menu_label = 'Label du bouton',$filter_identifier='default') {
        parent::__construct($ajax_action, $callback_function, $load_js_callback);
      
        $this->filter_identifier = $filter_identifier;
        $this->filter_base_tag = 'ng1_ajax_front_menu_' . trim($this->filter_identifier);
        // Supprime l'action de la classe de base
        remove_action('ng1_front_menu', array($this, 'add_in_front_menu'));
        // Utilisez la priorité passée en argument ou la valeur par défaut (10)
        $this->menu_position = $menu_position;
        $this->menu_label = $menu_label;


        // Ajoute la nouvelle action avec la priorité définie
        add_action('ng1_front_menu', array($this, 'ng1_front_menu'), $this->menu_position);

        // Si $load_js_callback n'est pas défini, utilisez load_js par défaut
        if ($load_js_callback === null) {
            $this->load_js_callback = array($this, 'load_generique_js');
        }elseif (is_array($load_js_callback)){
            $this->load_js_callback = $load_js_callback;
        }else{
            $this->load_js_callback = array($this, 'load_generique_js');
        }

    }

    /**
     * Fonction pour ajouter un élément au menu frontal avec la priorité définie.
     *
     * Insérez votre code pour ajouter un élément au menu frontal ici.
     */
    public function ng1_front_menu() {
        $html= '<div id="' . esc_attr($this->ajax_action) . '">' . esc_html($this->menu_label) .'</div>';
        echo apply_filters($this->filter_base_tag . '_form',  $html);
    }
        // Fonction pour charger le JavaScript
        public function load_generique_js() {
            // Code JavaScript correctement formaté
       
            $custom_js = "
        
       
            (function($){
                jQuery(document).ready(function($) {
                   //console.log('".$this->filter_base_tag."');
                   //console.log('Fichier AJax GENERIQUE pour " . esc_attr($this->ajax_action) . "');
                    // Associez un gestionnaire d'événements au clic sur le bouton d'exportation
                    $('body').on('click', '#" . esc_attr($this->ajax_action) . "', function() {".
                        apply_filters($this->filter_base_tag . '_before', "")
                        ."$.ajax({
                            url: '" . admin_url('admin-ajax.php') . "',
                            type: 'POST',
                            data: {" . apply_filters($this->filter_base_tag . '_data', " action: '" . esc_attr($this->ajax_action) . "' ") . "},
                            success: function(response) {
                                alert('" . apply_filters($this->filter_base_tag. '_success_message', esc_attr($this->menu_label)) . ": Réalisé avec succès');
                            },
                            error: function(error) {
                                alert('" .apply_filters($this->filter_base_tag  . '_error_message', 'ERREUR') . "');
                                console.error(error);
                            }
                        });
                        ".
                        apply_filters($this->filter_base_tag . '_after', "")
                        ."
                    });
                });
            })(jQuery);
            ";
           
            // Ajouter le code JavaScript en ligne
            wp_add_inline_script('jquery', apply_filters("ng1_ajax_front_menu_" . trim($this->filter_identifier) . '_output', $custom_js));
        }
        
}

