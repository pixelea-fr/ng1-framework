<?php 
class Ng1GenerateHeadingBlockStyles {
    private $themeJSONFile;
    private $outputCssFolder;
    private $outputSCSSFile;
    private $display_echo_message;

    protected $ng1ThemeJsonUtilities;

    public function __construct() {
        $this->display_echo_message = false;
        $this->themeJSONFile = get_stylesheet_directory() . "/theme.json";
        $this->outputCssFolder = plugin_dir_path(__FILE__) . "assets/css";
        $this->outputSCSSFile = plugin_dir_path(__FILE__) . "assets/scss/_styles.scss";

        $this->ng1ThemeJsonUtilities = new Ng1ThemeJsonUtilities();
        // usage :  $this->ng1ThemeJsonUtilities->convertJsonPropertiesToCssProperties($value);
        new Ng1AjaxFrontMenu('generate_typographie_styles_css', array($this, 'generateTypographySCSS'), 'generate_typographie_styles_css', 1, 'Créé style des typographies (is-style-h1,...)', 'Thème');
        //new Ng1AjaxFrontMenu('generate_typographie_block_syles', array($this, 'addHeadingBlockStyles'), 'generate_typographie_block_syles', 10, 'Générer les style de block des typographies');
       // add_action('init', array($this, 'generateTypographySCSS'));
       add_action('init', array($this,'addHeadingBlockStyles'));
    }

    public function generateTypographySCSS() {
        //$this->generateTypographySCSSFromThemeJSON($this->themeJSONFile, $this->outputSCSSFile);
        if (!file_exists($this->outputCssFolder)) {
            mkdir($this->outputCssFolder, 0755, true);
        }
        $this->generateTypographySCSSFilesFromThemeJSON($this->themeJSONFile);
    }

    public function generateTypographySCSSFilesFromThemeJSON($themeJSONFile) {
      
        // Lire le fichier theme.json
        $themeJSON = file_get_contents($themeJSONFile);

        if ($themeJSON === false) {
            die("Impossible de lire le fichier theme.json.");
        }
   
        $themeData = json_decode($themeJSON, true);
   
        if ($themeData === null) {
            die("Erreur lors de la lecture du fichier JSON.");
        }
 
            // Crée le contenu SCSS
            $tags = array("p","h1","h2","h3","h4","h5","h6");
       
        if (isset($themeData['styles']['elements'])) {
  
            foreach ($themeData['styles']['elements'] as $elementName => $elementStyles) {
                if(!in_array($elementName, $tags)){
                    continue;
                }
                $scssContent ="";
                $selector =array();
                foreach($tags as $tag){
                    $selector[] = "$tag.is-style-$elementName";
                    $selector[] = ".editor-styles-wrapper $tag.is-style-$elementName";
                }
                $cssSelector = implode(", ",  $selector);
                // Créez une classe .is-style-[elementName] avec les styles
                $scssContent .= "$cssSelector {\n";

    
                    foreach ($elementStyles as $property => $value) {
                        // Conversion de la propriété
                        $property = $this->ng1ThemeJsonUtilities->convertJsonPropertiesToCssProperties($property);
            
                        // Si la valeur est un tableau, nous devons la traiter correctement
                        if (is_array($value)) {
                            $formattedValue = '';
                            foreach ($value as $subProperty => $subValue) {
                                $subProperty = $this->ng1ThemeJsonUtilities->convertJsonPropertiesToCssProperties($subProperty);
                                $subValue = $this->ng1ThemeJsonUtilities->convertJsonVarToCssVar($subValue);
                                $formattedValue .= "  $subProperty: $subValue;\n";
                            }
                            $scssContent .= $formattedValue;
                        } else {
                            // Conversion de la valeur
                            $value = $this->ng1ThemeJsonUtilities->convertJsonVarToCssVar($value);
                            $scssContent .= "  $property: $value;\n";
                        }
                    }
            
                    $scssContent .= "}\n";
       
         
            // Nom du fichier de sortie pour cet élément
             $outputSCSSFile = "$this->outputCssFolder/$elementName.css";

    
                // Écrire le contenu SCSS dans le fichier de sortie
                if (file_put_contents($outputSCSSFile, $scssContent) !== false) {
                    if (!empty($this->display_echo_message )){
                    echo "Fichier SCSS pour $elementName généré avec succès !\n";
                    }
                } else {
                    if (!empty($this->display_echo_message )){
                    echo "Erreur lors de la création du fichier SCSS pour $elementName.\n";
                    }
                }
            }
        }
    
    }

    public function generateTypographySCSSFromThemeJSON($themeJSONFile, $outputSCSSFile) {
        // Lire le fichier theme.json
        $themeJSON = file_get_contents($themeJSONFile);

        if ($themeJSON === false) {
            die("Impossible de lire le fichier theme.json.");
        }

        $themeData = json_decode($themeJSON, true);

        if ($themeData === null) {
            die("Erreur lors de la lecture du fichier JSON.");
        }

        // Crée le contenu SCSS
        $scssContent = "p,h1,h2,h3,h4,h5,h6{\n";

        foreach ($themeData['styles']['elements'] as $elementName => $elementStyles) {
            // Créez une classe .is-style-[elementName] avec les styles
            $scssContent .= ".is-style-$elementName {\n";

            foreach ($elementStyles as $property => $value) {
                // Conversion de la propriété
                $property = $this->ng1ThemeJsonUtilities->convertJsonPropertiesToCssProperties($property);

                // Si la valeur est un tableau, nous devons la traiter correctement
                if (is_array($value)) {
                    $formattedValue = '';
                    foreach ($value as $subProperty => $subValue) {
                        $subProperty = $this->ng1ThemeJsonUtilities->convertJsonPropertiesToCssProperties($subProperty);
                        $subValue = $this->ng1ThemeJsonUtilities->convertJsonVarToCssVar($subValue);
                        $formattedValue .= "  $subProperty: $subValue;\n";
                    }
                    $scssContent .= $formattedValue;
                } else {
                    // Conversion de la valeur
                    $value = $this->ng1ThemeJsonUtilities->convertJsonVarToCssVar($value);
                    $scssContent .= "  $property: $value;\n";
                }
            }

            $scssContent .= "}\n\n";
        }
        $scssContent .= "}\n\n";

        // Supprime le fichier existant s'il existe
        if (file_exists($outputSCSSFile)) {
            unlink($outputSCSSFile);
        }

        // Écrire le contenu SCSS dans le fichier de sortie
        if (file_put_contents($outputSCSSFile, $scssContent) !== false) {
            echo "Fichier SCSS généré avec succès !";
        } else {
            echo "Erreur lors de la création du fichier CSS.";
        }
    }
    public function addHeadingBlockStyles() {
        // Obtenez la liste des fichiers dans le répertoire de sortie SCSS
        $scssFiles = scandir($this->outputCssFolder);
    
        // Définissez un tableau pour stocker les styles d'en-tête
        $heading_styles = array();
    
        // Parcourez les fichiers SCSS et ajoutez-les comme styles d'en-tête
        foreach ($scssFiles as $file) {
          
            if (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                // Obtenez le nom du fichier sans l'extension
                $style_name = pathinfo($file, PATHINFO_FILENAME);
                
                // Ajoutez le style d'en-tête correspondant au tableau
                $heading_styles[] = array(
                    'name'  => $style_name,
                    'label' => $style_name,
                    'inline_style' =>file_get_contents($this->outputCssFolder . '/' . $style_name.'.css')
                );
            }
        }
    
        // Ajoutez les styles d'en-tête aux blocs de titre
        foreach ($heading_styles as $style) {
            register_block_style('core/heading', $style);
        }
        foreach ($heading_styles as $style) {
            register_block_style('core/paragraph', $style);
        }
    }
    

}
new Ng1GenerateHeadingBlockStyles();