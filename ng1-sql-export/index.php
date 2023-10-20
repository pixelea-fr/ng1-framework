<?php
    /*
    Plugin Name: Export Database to Theme SQL
    Description: Export the database to a SQL file in your theme directory.
    Version: 1.0
    Author: Votre nom
    */

class ng1ExportDatabase {


    // Constructeur pour initialiser les propriétés de classe
    public function __construct() {

       new Ng1AjaxFrontMenu('generate_sql_file', array($this, 'export_database_to_sql'), null,10, 'Exporter la base de données');
    }

    // Fonction pour exporter la base de données
    public static function export_database_to_sql() {
        global $wpdb;
        // Créez un nom de fichier pour le fichier SQL basé sur la date et l'heure actuelles
        $filename = 'backup.sql';

        // Chemin complet du fichier SQL dans le répertoire du thème
        $file_path = get_stylesheet_directory() . '/sql/' . $filename;

        // Récupérez toutes les tables de la base de données WordPress
        $tables = $wpdb->get_results("SHOW TABLES");

        // Ouvrez le fichier SQL en écriture
        $file = fopen($file_path, 'w');

        // Parcourez les tables et exportez-les dans le fichier SQL
        foreach ($tables as $table) {
            $table = (array)$table;
            $table_name = $table[key($table)];

            $sql = "SHOW CREATE TABLE $table_name";
            $table_create = $wpdb->get_var($sql);
            fwrite($file, "\n\n-- Création de la table $table_name\n");
            fwrite($file, "$table_create;\n\n");

            $rows = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_N);

            if ($rows) {
                foreach ($rows as $row) {
                    $row = array_map('addslashes', $row);
                    fwrite($file, "INSERT INTO $table_name VALUES ('" . implode("', '", $row) . "');\n");
                }
            }
        }

        fclose($file);

        // Vérifiez si le fichier SQL a été créé avec succès
        if (file_exists($file_path)) {
            // Vous pouvez ajouter un message de réussite ici
            echo "La base de données a été exportée avec succès vers {$file_path}";
        } else {
            // En cas d'erreur, affichez un message d'erreur
            echo "Une erreur s'est produite lors de l'exportation de la base de données.";
        }
    }

}

// Instanciez la classe pour initialiser les propriétés de classe et charger le JavaScript
new ng1ExportDatabase();