<?php
/**
 * Classe Ng1FormatText.
 *
 * Cette classe propose des méthodes pour formater du texte en HTML.
 */
class Ng1FormatText
{
    /**
     * Convertit le texte en un identifiant HTML.
     *
     * @param string $text Le texte à convertir.
     * @return string L'identifiant HTML généré.
     */
    public static function textToHtmlId($text) {
        // Supprimez les accents
        $text = remove_accents($text);

        // Remplacez les espaces par des tirets
        $text = str_replace(' ', '-', $text);

        // Convertissez le texte en minuscules
        $text = strtolower($text);

        return $text;
    }

    /**
     * Convertit le texte d'un textarea en une liste HTML.
     *
     * @param string $textareaText Le texte provenant d'un textarea.
     * @param bool $includeUl Indique si la balise <ul> doit être incluse autour de la liste.
     * @return string La liste HTML générée.
     */
    public static function convertToHtmlList($textareaText, $includeUl = true) {
        // Explode la chaîne de texte en lignes
        $lines = explode("\n", $textareaText);

        // Commence la liste HTML si l'option est activée
        $htmlList = $includeUl ? '<ul>' : '';

        // Ajoute chaque ligne comme un élément de liste
        foreach ($lines as $line) {
            // Supprime les espaces inutiles au début et à la fin de la ligne
            $trimmedLine = trim($line);

            // Si la ligne n'est pas vide, l'ajoute comme un élément de liste
            if (!empty($trimmedLine)) {
                $htmlList .= '<li>' . htmlspecialchars($trimmedLine) . '</li>';
            }
        }

        // Termine la liste HTML si l'option est activée
        $htmlList .= $includeUl ? '</ul>' : '';

        // Retourne la liste HTML
        return $htmlList;
    }
}