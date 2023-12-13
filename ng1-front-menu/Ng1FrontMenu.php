<?php class ng1FrontMenu {

private static $instance = null;

/**
 * Récupère l'instance unique de la classe en utilisant le modèle Singleton.
 *
 * @return ng1FrontMenu
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
    // Actions WordPress
    add_action('init', array($this, 'add_action_for_administrator'));
}

/**
 * Ajoute des actions pour l'administrateur.
 */
public function add_action_for_administrator() {
    // Vérifiez si l'utilisateur est connecté
    if (is_user_logged_in()) {
        // Récupérez l'ID de l'utilisateur
        $user_id = get_current_user_id();

        // Vérifiez si l'utilisateur a le rôle d'administrateur
        if (user_can($user_id, 'administrator')) {
            // L'utilisateur est administrateur, exécutez vos actions ici
            add_action('wp_enqueue_scripts', array($this, 'load_js'), 100);
            add_action('wp_enqueue_scripts', array($this, 'load_styles'), 103);
            add_action('wp_footer', array($this, 'create_menu_footer'), 100);
        }
    }
}

/**
 * Crée le menu dans le footer.
 */
public function create_menu_footer() {
    // Ajoutez ici le code HTML que vous souhaitez afficher dans le footer
    ob_start();?>
      <div class='ng1-front-menu__toggle frontMenuToggleJs' >
            <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="30px" height="30px" viewBox="0 0 32 32" id="icon">
                <defs>
                    <style>
                    .cls-1 {
                        fill-rule: evenodd;
                    }

                    .cls-2 {
                        fill: none;
                    }
                    </style>
                </defs>
                <path d="M28,13V8a2.0023,2.0023,0,0,0-2-2H23V8h3v5a3.9756,3.9756,0,0,0,1.3823,3A3.9756,3.9756,0,0,0,26,19v5H23v2h3a2.0023,2.0023,0,0,0,2-2V19a2.0023,2.0023,0,0,1,2-2V15A2.0023,2.0023,0,0,1,28,13Z"/>
                <path id="Combined-Shape" class="cls-1" d="M17,9l-.857,3h2L19,9h2l-.857,3H22v2H19.572l-1.143,4H21v2H17.857L17,23H15l.857-3h-2L13,23H11l.857-3H10V18h2.429l1.143-4H11V12h3.143L15,9Zm.572,5h-2l-1.143,4h2Z"/>
                <path d="M6,13V8H9V6H6A2.0023,2.0023,0,0,0,4,8v5a2.0023,2.0023,0,0,1-2,2v2a2.0023,2.0023,0,0,1,2,2v5a2.0023,2.0023,0,0,0,2,2H9V24H6V19a3.9756,3.9756,0,0,0-1.3823-3A3.9756,3.9756,0,0,0,6,13Z"/>
                <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-2" width="32" height="32"/>
            </svg>
      </div>
    <div class='ng1-front-menu'>
        <div class='ng1-front-menu__inner'>
            <?php do_action('ng1_front_menu');?>
            <div class="ng1-front-menu-label-tabs__items">
        <?php for($i=1;$i<20;$i++):   ?>
            <?php if (has_action('ng1_front_menu_tabs_'.$i)): ?>
            <div class="ng1-front-menu-label-tabs__item ng1-front-menu-label-tabs__item--<?php echo $i; ?> ng1TabToggleJs" data-tabs="<?php echo $i; ?>">
                <?php echo apply_filters('ng1_front_menu_tabs_label_'.$i ,''); ?>
            </div>
            <?php endif; ?>
        <?php endfor; ?>
        </div>
        <div class='ng1-front-menu-tabs__item'>
        <?php for($i=1;$i<20;$i++):   ?>
            <?php if (has_action('ng1_front_menu_tabs_'.$i)): ?>
            <div class="ng1-front-menu-tabs__item ng1-front-menu-tabs__item--<?php echo $i; ?> ng1TabJs" data-tabs="<?php echo $i; ?>">
                <?php do_action('ng1_front_menu_tabs_'.$i ); ?>
            </div>
            <?php endif; ?>
        <?php endfor; ?>
        </div>
        </div>
   </div>
   <?php
    echo ob_get_clean();
    return;
}

/**
 * Charge les styles CSS dans l'en-tête.
 */
public function load_styles() {
    $plugin_url = plugins_url('', __FILE__);
    wp_enqueue_style('ng1-front-menu', $plugin_url . '/assets/css/style.css', array(), null, 'all');
}

/**
 * Charge les scripts JavaScript.
 */
public function load_js() {
    // Code JavaScript en ligne correctement formaté
    $custom_js = "
    (function($){
        jQuery(document).ready(function($) {
            $('body').on('click', '.frontMenuToggleJs', function() {
               $('.ng1-front-menu').toggleClass('active');
            });
            $('.ng1TabJs').hide();

            $('body').on('click','.ng1TabToggleJs', function() {
              var tabactive =$(this).attr('data-tabs');
              $('.ng1TabJs').hide();
              $('.ng1TabJs[data-tabs='+tabactive+']').show();
            });
        });

    })(jQuery);
    ";

    wp_add_inline_script('jquery', $custom_js);
}
}

// Instancie la classe en utilisant le modèle Singleton
ng1FrontMenu::get_instance();
