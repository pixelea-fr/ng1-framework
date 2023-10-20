<?php
class Ng1LoadThemeBlockStyles {
    public function __construct() {
        // Ajoutez un hook pour charger les styles lors de l'initialisation du thème
        add_action('after_setup_theme', array($this, 'load_ng1_block_styles'),5);
    }

    public function trouverFichiersIndex($dossier) {
        $fichiersIndex = array();
        // Ouvrir le dossier
        $handle = opendir($dossier);
        // Parcourir les fichiers et dossiers du dossier
        while (false !== ($fichier = readdir($handle))) {
            if ($fichier != "." && $fichier != "..") {
                // Construire le chemin complet du fichier
                $cheminFichier = $dossier . '/' . $fichier;
                // Si c'est un dossier, appeler la fonction de manière récursive
                if (is_dir($cheminFichier)) {
                    $fichiersIndex = array_merge($fichiersIndex, $this->trouverFichiersIndex($cheminFichier));
                } else {
                    // Si c'est un fichier index.php, ajouter le chemin au tableau
                    if (basename($cheminFichier) == 'register.php') {
                        $fichiersIndex[] = $cheminFichier;
                    }
                }
            }
        }

        // Fermer le dossier
        closedir($handle);

        return $fichiersIndex;
    }

    public function load_ng1_block_styles() {

        // Obtenez le chemin absolu de la racine de WordPress
        $cheminRacine = get_stylesheet_directory() .'/block-styles';
        $fichiersIndex = $this->trouverFichiersIndex($cheminRacine);
        
        // Utilisez un tableau pour suivre les fichiers inclus
        $fichiersInclus = array();

        foreach ($fichiersIndex as $fichier) {
            // Vérifiez si le fichier existe et s'il n'a pas déjà été inclus
            if (file_exists($fichier) ) {
              include_once $fichier;
                // Ajoutez le fichier à la liste des fichiers inclus
              

            }
        }

    }
}

new Ng1LoadThemeBlockStyles();
