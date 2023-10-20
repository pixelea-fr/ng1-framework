<?php
/**
 * La classe Ng1ThemeJsonUtilities est utilisée pour effectuer des transformations sur les données JSON
 * afin de les convertir en propriétés CSS appropriées.
 */
class Ng1ThemeJsonUtilities
{
    /**
     * @var bool $display_echo_message Un indicateur pour afficher ou non les messages d'écho.
     */
    private $display_echo_message;

    /**
     * Constructeur de la classe.
     *
     * @param bool $display_echo_message Indique si les messages d'écho doivent être affichés.
     */
    public function __construct($display_echo_message = false)
    {
        $this->display_echo_message = $display_echo_message;
    }

    /**
     * Convertit les noms de propriétés JSON en noms de propriétés CSS.
     *
     * @param string $input Le nom de la propriété JSON.
     *
     * @return string Le nom de la propriété CSS correspondant.
     */
    public function convertJsonPropertiesToCssProperties($input)
    {
        // Utilise une expression régulière pour diviser les mots
        $output = preg_replace_callback('/(?<=[a-z])([A-Z])/', function($match) {
            return '-' . strtolower($match[1]);
        }, $input);

        // Convertit la première lettre en minuscules
        $output = lcfirst($output);
        // Supprime les espaces en trop
        $output = trim($output);

        // Ajoute une logique de switch pour des transformations spécifiques
        switch ($output) {
            case 'text':
                return 'color';
            case 'background-color':
                return 'background';
            // Ajoutez d'autres cas au besoin pour des propriétés spécifiques
            default:
                return $output;
        }
    }

    /**
     * Convertit les variables JSON en variables CSS.
     *
     * @param string $value La valeur de la variable JSON.
     *
     * @return string La valeur de la variable CSS correspondante.
     */
    public function convertJsonVarToCssVar($value)
    {
        if (preg_match_all('/var:([^|]+)\|([^|]+)\|([^|]+)/', $value, $matches, PREG_SET_ORDER)) {
            $replacements = '';
            foreach ($matches as $match) {
                $replacements .= "var(--wp--{$match[1]}--{$match[2]}--{$match[3]})";
            }
            return $replacements;
        } else {
            return $value; // Aucune correspondance trouvée, retourne la valeur d'origine
        }
    }

    /**
     * Fusionne les fichiers JSON d'un répertoire en un seul fichier.
     *
     * @param string $directory_path Chemin du répertoire contenant les fichiers JSON.
     * @param string $output_file_path Chemin du fichier de sortie fusionné.
     * @throws InvalidArgumentException Si le répertoire de sortie ne peut pas être créé.
     */

    public function mergeJson($directory_path, $output_file_path) {
        // Vérifier si le chemin spécifié est un répertoire
        if (!is_dir($directory_path)) {
            if (!mkdir($directory_path, 0777, true)) {
                throw new InvalidArgumentException("Impossible de créer le répertoire $directory_path");
            }
        }

        // Liste de tous les fichiers JSON dans le répertoire
        $json_files = array_filter(scandir($directory_path), function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'json';
        });

        // Fusionner les fichiers JSON en un seul tableau associatif
        $merged_array = [];
        foreach ($json_files as $json_file) {
            $file_contents = file_get_contents($directory_path . DIRECTORY_SEPARATOR . $json_file);
            $file_array = json_decode($file_contents, true);
            $merged_array = array_merge_recursive($merged_array, $file_array);
        }

        // Écrire le tableau associatif fusionné dans un fichier JSON
        $json_contents = json_encode($merged_array, JSON_PRETTY_PRINT);
        $json_contents = str_replace('\/', '/', $json_contents);
      
        $create = file_put_contents($output_file_path, $json_contents);
       echo "Les fichiers du $directory_path theme-json ont été fusionnés";
    }
     /**
     * Liste les fichiers JSON du thème.
     *
     * @param bool $debug Indique si le mode de débogage est activé.
     * @return array|null Tableau des noms de fichiers JSON valides ou null si aucun fichier valide n'est trouvé.
     */
    public function listJsonFiles($debug = false) {
        $merge_file_folder = "theme-json";
        // Récupère le thème actuel
        $theme = wp_get_theme();
        // Récupère le chemin absolu du répertoire du thème actuel
        $theme_dir = $theme->get_stylesheet_directory();
        // Récupère le chemin absolu du répertoire contenant les fichiers JSON
        $json_dir = $theme_dir . '/' . trim($merge_file_folder);
        // Tableau pour stocker les noms de fichiers JSON valides
        $files = array();

        // Vérifie si le répertoire existe
        if (is_dir($json_dir)) {
            // Parcourt le répertoire avec une boucle foreach
            $dir = new DirectoryIterator($json_dir);
            foreach ($dir as $file) {
                // Vérifie si l'élément parcouru est un fichier et a une extension .json
                if ($file->isFile() && strtolower($file->getExtension()) === 'json') {
                    // Lit le contenu du fichier JSON
                    $json = file_get_contents($file->getPathname());
                    // Décode la chaîne JSON en un tableau ou un objet PHP
                    $data = json_decode($json);
                    // Vérifie si le décodage a réussi
                    if ($data !== null) {
                        // Si le fichier JSON est valide, ajoute son nom au tableau $files
                        $files[] = $file->getFilename();
                    }
                }
            }
        }
        if ($debug) {
            // Si des fichiers valides ont été trouvés, les affiche dans une liste HTML
        }
        if (!empty($files)) {
            return $files;
        } else {
            return null;
        }
    }
}
