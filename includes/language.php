<?php
/**
 * Gestion de la langue du site
 */

// Définir la fonction de traduction
if (!function_exists('__')) {
    function __($key, $default = null) {
        global $lang;
        
        // Si la clé existe dans le tableau de traduction
        if (isset($lang[$key])) {
            return $lang[$key];
        }
        
        // Sinon retourner le texte par défaut ou la clé elle-même
        return $default !== null ? $default : $key;
    }
}

// Définir la langue par défaut
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';

// Charger le fichier de langue
$lang_file = 'lang/' . $current_lang . '.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    // Charger le français par défaut si le fichier n'existe pas
    require_once 'lang/fr.php';
}

// Fonction pour changer de langue
function change_language($language) {
    if (in_array($language, ['fr', 'en'])) {
        $_SESSION['lang'] = $language;
    }
}
?> 