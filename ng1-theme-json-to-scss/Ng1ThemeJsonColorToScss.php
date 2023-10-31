<?php

/**
 * Cette classe permet de convertir les couleurs d'un fichier theme.json en fichiers SCSS.
 */
class Ng1ThemeJsonColorToScss {
    /**
     * @var string Le chemin du fichier theme.json.
     */
    private $themeJSONFile;

    /**
     * @var string Le chemin du fichier SCSS de sortie.
     */
    private $outputSCSSFile;



    /**
     * Constructeur de la classe.
     *
     * @param string $themeJSONFile     Le chemin du fichier theme.json.
     * @param string $outputSCSSFile    Le chemin du fichier SCSS de sortie.
     */
    public function __construct() {
        $this->themeJSONFile = get_stylesheet_directory_uri() . "/theme.json";
        $this->outputSCSSFile = get_stylesheet_directory()."/src/scss/theme-json/_colors.scss";

        new Ng1AjaxFrontMenu('generate_color_scss', array($this, 'generate_scss'), 'generate_color_scss', 2, 'Générer les fichiers SCSS, à partir du theme.json','Thème JSON');
    }

    /**
     * Cette méthode génère le fichier SCSS à partir du fichier theme.json.
     */
    public function generate_scss() {
        // Lire le fichier theme.json
        $themeJSON = file_get_contents($this->themeJSONFile);

        if ($themeJSON === false) {
            die("Impossible de lire le fichier theme.json.");
        }

        $themeData = json_decode($themeJSON, true);

        if ($themeData === null) {
            die("Erreur lors de la lecture du fichier JSON.");
        }

        // Récupérer les couleurs de la palette
        $colors = $themeData['settings']['color']['palette'];

        // Créer le contenu du fichier SCSS
        $scssContent = '';

        foreach ($colors as $color) {
            $colorName = $color['name'];
            $colorValue = $color['color'];

            // Assurez-vous que le nom de la variable est en minuscules et sans espaces
            $variableName = strtolower(str_replace(' ', '-', $colorName));

            // Ajouter la variable SCSS
            $scssContent .= "\$" . $variableName . ": " . $colorValue . ";\n";
        }
            // Création du répertoire de sortie s'il n'existe pas
            $outputDirectory = dirname($this->outputSCSSFile);
            if (!file_exists($outputDirectory)) {
                if (!mkdir($outputDirectory, 0777, true)) {
                    die("Impossible de créer le répertoire de sortie.");
                }
            }
    
            // Vérifier si le fichier SCSS de sortie existe
            if (!file_exists($this->outputSCSSFile)) {
                // S'il n'existe pas, le créer
                if (file_put_contents($this->outputSCSSFile, '') === false) {
                    die("Impossible de créer le fichier SCSS de sortie.");
                }
            }

            // Écrire le contenu SCSS dans le fichier de sortie
            if (file_put_contents($this->outputSCSSFile, $scssContent) !== false) {
                echo "Fichier SCSS généré avec succès !";
            } else {
                echo "Erreur lors de la création du fichier SCSS.";
            }
        
    }
}
new Ng1ThemeJsonColorToScss();