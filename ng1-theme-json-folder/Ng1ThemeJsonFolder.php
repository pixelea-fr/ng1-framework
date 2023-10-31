<?php

/**
 * Classe ng1ThemeJsonFolder pour la gestion des fichiers JSON du thème.
 */
class ng1ThemeJsonFolder {
    protected $ng1ThemeJsonUtilities;
    /**
     * Constructeur de la classe.
     */
    public function __construct() {
        $this->ng1ThemeJsonUtilities = new Ng1ThemeJsonUtilities();
      new Ng1AjaxFrontMenu('merge_theme_json', array($this, 'merge_theme_json_from_folder'), 'theme_json_from_folder', 2, 'Générer le theme.json à partir du dossier theme-json du thème');
    }
    /**
     * Fusionne les fichiers JSON du thème en un seul fichier.
     *
     * @param string $theme_json Chemin du fichier de sortie fusionné.
     */
    public function merge_theme_json_from_folder() {
        $merge_file_folder = "theme-json";
        $merge_file_name = "theme.json";

        // Récupère le thème actuel
        $theme = wp_get_theme();
        // Récupère le chemin absolu du répertoire du thème actuel
        $theme_dir = $theme->get_stylesheet_directory();
        // Récupère le chemin absolu du répertoire contenant les fichiers JSON
        $json_dir = $theme_dir . '/' . trim($merge_file_folder);
        $output_file_path = $theme_dir . "/" . $merge_file_name;
        // Récupère les fichiers
        $theme_json_files = $this->ng1ThemeJsonUtilities->listJsonFiles();

        // Fusionne les fichiers JSON en un seul fichier
        $this->ng1ThemeJsonUtilities->mergeJson($json_dir, $output_file_path);

        return;
    }

}

// Instanciation de la classe
$theme_json_folder = new ng1ThemeJsonFolder();
