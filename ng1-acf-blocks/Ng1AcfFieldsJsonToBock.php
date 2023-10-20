<?php

class Ng1AcfFieldsJsonToBock {

    public function __construct() {
        // Ajoute des actions WordPress
        //add_action('wp_enqueue_scripts',  array($this, 'load_js'), 100);
        //add_action('wp_enqueue_scripts',  array($this, 'load_styles'), 103);
       //add_action('wp_ajax_copy_acf_block_fields_json',  array($this, 'copy_acf_fields_json_to_block_folder'));
       //add_action('wp_ajax_nopriv_copy_acf_block_fields_json',  array($this, 'copy_acf_fields_json_to_block_folder'));
        new Ng1AjaxFrontMenu('acf_fields_to_blockfolder', array($this, 'copy_acf_fields_json_to_block_folder'), 'acf_fields_to_blockfolder', 10, 'Copier les groupes de champs ACF vers les dossiers de blocs');
        //add_action('ng1_front_menu',array($this, 'add_in_front_menu'),10);
    }

    /**
     * Recherche et lit les fichiers "block.json" dans les sous-dossiers de "acf-blocks".
     *
     * @return array Un tableau associatif contenant le nom du block et le chemin du dossier contenant un "block.json".
     */
    public function get_block_json_data() {
        $block_data = [];

        $acf_blocks_directory = get_stylesheet_directory() . '/acf-blocks/';

        if (is_dir($acf_blocks_directory)) {
            $subdirectories = glob($acf_blocks_directory . '*', GLOB_ONLYDIR);

            foreach ($subdirectories as $subdirectory) {
                $block_json_file = $subdirectory . '/block.json';

                if (file_exists($block_json_file)) {
                    $json_data = file_get_contents($block_json_file);
                    $block_info = json_decode($json_data, true);

                    if (isset($block_info['name'])) {
                        $block_data[$block_info['name']] = $subdirectory;
                    }
                }
            }
        }

        return $block_data;
    }

    /**
     * Lit les fichiers JSON ACF (Advanced Custom Fields) du répertoire acf-json.
     *
     * @return array Tableau associatif des correspondances entre les blocks et les fichiers JSON.
     * @throws Exception en cas d'erreur de lecture ou de décodage des fichiers JSON.
     */
    public function read_acf_json_files() {
        $acf_json_directory = get_template_directory() . '/acf-json/';
        $acf_blocks_group = [];
    
        // Vérifiez si le dossier existe, sinon, créez-le
        if (!is_dir($acf_json_directory)) {
            mkdir($acf_json_directory, 0755, true);
        }
    
        if (is_dir($acf_json_directory)) {
            $json_files = glob($acf_json_directory . '*.json');
    
            if ($json_files) {
                foreach ($json_files as $json_file) {
                    $json_data = file_get_contents($json_file);
                    $acf_group = json_decode($json_data, true);
    
                    if ($acf_group && isset($acf_group['location'])) {
                        $block_location = array_filter($acf_group['location'], function($loc) {
                            return isset($loc[0]['param']) && $loc[0]['param'] === 'block';
                        });
    
                        if (!empty($block_location)) {
                            $block_value = reset($block_location);
                            $acf_blocks_group[$block_value[0]['value']] = $this->findThemeFilesWithLocation($block_value[0]['value']);
                        }
                    }
                }
            }
            return $acf_blocks_group;
        }
    }
    

    public function findThemeFilesWithLocation($locationValue) {
        $results = [];
        $themeDirectory = get_template_directory() . '/acf-json';
        $files = scandir($themeDirectory);

        foreach ($files as $filename) {
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'json') {
                $filePath = $themeDirectory . '/' . $filename;
                $jsonData = file_get_contents($filePath);
                $data = json_decode($jsonData, true);

                if (isset($data['location'])) {
                    foreach ($data['location'] as $location) {
                        foreach ($location as $item) {
                            if ($item['param'] === 'block' && $item['operator'] === '==' && $item['value'] === stripslashes($locationValue)) {
                                $results[] = $filePath;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $results;
    }

    public function ng1_array_acf_field_block_correspondance() {
        $out = [];
        $tableau_des_block_avec_block_json = $this->get_block_json_data();
        $tableau_acf_json_pour_block = $this->read_acf_json_files();

        foreach ($tableau_des_block_avec_block_json as $key => $value) {
            if (array_key_exists($key, $tableau_acf_json_pour_block)) {
                $out[$key] = ['from' => $tableau_acf_json_pour_block[$key], 'to' => $value];
            }
        }
        return $out;
    }

    public function copy_acf_fields_json_to_block_folder() {
        $data = $this->ng1_array_acf_field_block_correspondance();
        if (!empty($data)) {
    
            foreach ($data as $key => $values) {
                $fromFiles = $values["from"][0];
                $toDirectory = $values["to"];
                $destinationFile = $toDirectory . "/acf/fields.json";
    
                // Vérifiez si le répertoire 'acf-fields' existe, sinon, créez-le
                if (!is_dir($toDirectory . "/acf")) {
                    mkdir($toDirectory . "/acf", 0755, true);
                }
    
                // Copie le fichier source vers le répertoire de destination avec le nouveau nom
                if (copy($fromFiles, $destinationFile)) {
                    echo "Copie réussie de\n ' $fromFiles '\n vers '$destinationFile'.\n";
                } else {
                    echo "Échec de la copie de\n ' $fromFiles '\n vers '$destinationFile'.\n";
                }
            }
        }
    }
    
}

new Ng1AcfFieldsJsonToBock();