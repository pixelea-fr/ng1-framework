<?php 
class Ng1FormatText
{
    public static function textToHtmlId($text) {
        // Supprimez les accents
        $text = remove_accents($text);

        // Remplacez les espaces par des tirets
        $text = str_replace(' ', '-', $text);

        // Convertissez le texte en minuscules
        $text = strtolower($text);

        return $text;
    }
}