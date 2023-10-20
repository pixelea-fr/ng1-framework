<?php
/**
 * Classe ng1ZipThemeFolders pour la gestion de dossiers de thème et la création de fichiers ZIP.
 */
class ng1ZipThemeFolders {
    public function __construct() {

    new Ng1AjaxFrontMenu('generate_theme_assets_zip', array($this, 'zip_theme_acf_blocks'), 'generate_theme_assets_zip', 10, 'Générer le zip de tous les dossiers de thème');
    new Ng1AjaxFrontMenu('generate_zip_customfolder', array($this, 'customfolder_zip_action'), 'generate_zip_customfolder', 10, 'Générer le zip du theme sélectionné','customfolder');
    add_filter('ng1_ajax_front_menu_customfolder_data', array($this,'customfolder_filter_js_ajax_data'),10);
    add_filter('ng1_ajax_front_menu_customfolder_form', array($this,'customfolder_generate_zip_form_ajax_front_menu'),10);

    }
    /**
     * Crée un fichier ZIP à partir du dossier de thème spécifié.
     *
     * @param string $folder_name Le nom du dossier à compresser.
     */
    public function zip_theme_folder($folder_name = '') {
        if (empty($folder_name)) {
            // Obtenez le nom du dossier à partir de la requête GET
            $folder_name = isset($_GET['folder']) ? sanitize_text_field($_GET['folder']) : '';
        }

        // Récupérez le chemin du dossier du thème actif
        $theme_folder = get_stylesheet_directory() . '/' . $folder_name;

        // Récupérez la liste des dossiers dans le répertoire du thème
        $theme_folders = scandir($theme_folder);
           // Vérifiez s'il y a des sous-dossiers dans le dossier du thème
        $has_subdirectories = false;
        $theme_folders = scandir($theme_folder);
        foreach ($theme_folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }
            if (is_dir($theme_folder . '/' . $folder)) {
                $has_subdirectories = true;
                break;
            }
        }

        foreach ($theme_folders as $folder) {
            if ($folder === '.' || $folder === '..' || !is_dir($theme_folder . '/' . $folder)) {
                continue;
            }

            // Chemin du répertoire d'export dans le dossier "uploads"
            $uploads_folder = wp_upload_dir();
            $exports_subfolder_path = trailingslashit($uploads_folder['basedir']) . 'ng1_export';
            $exports_folder_path = $exports_subfolder_path . '/' . $folder_name;
            if (!file_exists($exports_subfolder_path)) {
                mkdir($exports_subfolder_path);
            }

            // Créez le dossier d'export dans le dossier "uploads" s'il n'existe pas
            if (!file_exists($exports_folder_path)) {
                mkdir($exports_folder_path);
            }

            // Nom du fichier zip de sortie (dans le dossier "ng1_export" des "uploads")
            $zip_file_name = trailingslashit($exports_folder_path) . "$folder.zip";

            // Créez une nouvelle instance de la classe ZipArchive
            $zip = new ZipArchive();

            if ($zip->open($zip_file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Ajoutez tous les fichiers et dossiers du thème au fichier zip
                ng1Zip::addFilesToZip($theme_folder . '/' . $folder, $zip);

                // Fermez le fichier zip
                $zip->close();

                // Affichez un lien pour télécharger le fichier ZIP
                echo "<a href='$zip_file_name' download='$folder.zip'>Télécharger $folder.zip</a>";
            } else {
                echo 'La création du fichier zip pour ' . $folder . ' a échoué.';
            }
        }
        
    }


    public function zip_theme_acf_blocks() {
        $this->zip_theme_folder('acf-blocks');
    }
    public function customfolder_zip_action() {
        if (isset($_POST['zipfolder'])) {
            // Assurez-vous que la fonction zip_theme_folder existe et peut être appelée
                $this->zip_theme_folder($_POST['zipfolder']);
        }
    }
    public function customfolder_filter_js_ajax_data($data) {
        return $data.', zipfolder : $("#zipfolder").val()'; 
    }
    public function customfolder_generate_zip_form_ajax_front_menu(){
        $formFields = [
            ['name' => 'zipfolder','id'=>'zipfolder', 'type' => 'text', 'label' => 'Dossier du thème à zipper' ,"value"=> "assets"],
            ['name' => 'generate_zip_customfolder','id'=>'generate_zip_customfolder', 'type' => 'submit', 'label' => 'Zipper le dossier', 'value'=> "Zipper le dossier"],
        ];
        
        $formGenerator = new ng1Form($formFields);
        $html = $formGenerator->generateFormFields();
        return $html;
    }
}
new ng1ZipThemeFolders();