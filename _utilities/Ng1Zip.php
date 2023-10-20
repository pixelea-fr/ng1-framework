<?php
/**
 * Classe ng1Zip pour la manipulation de fichiers ZIP.
 */
class ng1Zip {
    /**
     * Décompresse un fichier à partir d'une URL donnée vers le dossier de destination spécifié.
     *
     * @param string $zipFileURL L'URL du fichier ZIP à télécharger et à extraire.
     * @param string $destinationFolder Le dossier où les fichiers extraits seront stockés.
     * @return string Renvoie un message de succès si le fichier est extrait avec succès, ou un message d'erreur s'il y a un problème lors du processus d'extraction.
     */
    public static function unzipFile($zipFileURL, $destinationFolder) {
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        // Télécharge le fichier ZIP localement
        $zipFileName = $destinationFolder . '/temp.zip';
        if (file_put_contents($zipFileName, file_get_contents($zipFileURL)) !== false) {
            $zip = new ZipArchive;

            if ($zip->open($zipFileName) === true) {
                if ($zip->extractTo($destinationFolder)) {
                    $zip->close();
                    // Supprimer le fichier ZIP temporaire après décompression
                    unlink($zipFileName);
                    return 'Succès'; // Renvoyer une réponse de succès
                } else {
                    return '1 : Erreur lors de la décompression'; // Renvoyer une réponse d'erreur
                }
            } else {
                // return '2 : Erreur lors de l'ouverture du fichier ZIP : ' . $zipFileURL;
            }
        } else {
            return '3 : Erreur lors du téléchargement du fichier ZIP : ' . $zipFileURL;
        }
    }

    /**
     * Ajoute tous les fichiers d'un dossier spécifié à une archive zip.
     *
     * @param string $folder Le chemin vers le dossier contenant les fichiers.
     * @param ZipArchive $zip Une référence à l'objet ZipArchive représentant l'archive zip.
     * @throws Exception S'il y a une erreur lors de l'ajout du fichier à l'archive zip.
     * @return void
     */
    public static function addFilesToZip($folder, &$zip) {
        $dir = new RecursiveDirectoryIterator($folder);
        $iterator = new RecursiveIteratorIterator($dir);

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getPathname();
                $relativePath = substr($filePath, strlen($folder) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
