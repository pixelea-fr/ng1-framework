<?php
   /**
     * Classe Ng1AjaxPlugin
     * 
     * - Déclaration d'une action ajax (SLUG)
     * - Création des actions  correspondantes :
     *      add_action('wp_ajax_SLUG et add_action('wp_ajax_nopriv_SLUG
     * - Load la fonction callBack qui ajoute le scriptJs correspondant
     */
class Ng1Ajax {
    
    protected $ajax_action;
    protected $callback_function; 
    protected $load_js_callback;

    /**
     * Constructeur de la classe Ng1AjaxPlugin.
     *
     * @param string $ajax_action Le nom de l'action Ajax.
     * @param callable $callback_function La fonction de rappel pour la requête Ajax.
     * @param callable $load_js_callback La fonction de rappel pour charger des ressources JavaScript.
     */
    public function __construct($ajax_action, $callback_function, $load_js_callback) {

        $this->ajax_action = $ajax_action;
        $this->callback_function = $callback_function;
        $this->load_js_callback = $load_js_callback;

        $this->add_actions();
    }

    /**
     * Fonction pour gérer la requête Ajax.
     *
     * Effectue des vérifications de sécurité si nécessaire, puis appelle la fonction de rappel.
     */
    public function handle_ajax_request() {
        // Effectuer des vérifications de sécurité si nécessaire

        // Appeler la fonction de rappel
        if (is_callable($this->callback_function)) {
            call_user_func($this->callback_function);
        } else {
            wp_send_json_error(array('message' => 'Erreur dans la fonction de rappel.'));
        }

        wp_die();
    }

    /**
     * Fonction pour ajouter les actions liées au plugin.
     *
     * Ajoute les actions pour charger des scripts, gérer les requêtes Ajax et d'autres actions spécifiques au plugin.
     */
    protected function add_actions() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 100);
        add_action('wp_enqueue_scripts', array($this, 'load_js'), 100);
        add_action('wp_ajax_' . $this->ajax_action, array($this, 'handle_ajax_request'));
        add_action('wp_ajax_nopriv_' . $this->ajax_action, array($this, 'handle_ajax_request'));
        add_action('ng1_front_menu', array($this, 'ng1_front_menu'), 10);
    }
    public function enqueue_scripts() {
        // Enregistrez et chargez jQuery
        wp_enqueue_script('jquery');
    }
    /**
     * Fonction pour charger des ressources JavaScript.
     *
     * Cette fonction appelle la fonction de rappel pour charger des ressources JavaScript si elle est définie.
     */
    public function load_js() {
        if (is_callable($this->load_js_callback)) {
            call_user_func($this->load_js_callback);
        }
    }


}
