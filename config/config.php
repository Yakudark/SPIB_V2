<?php
define('BASE_URL', 'http://localhost/JS/SPIB');
define('API_URL', BASE_URL . '/api');

// Configuration de l'environnement
define('ENV', 'local');
define('DEBUG', true);

// Configuration des chemins
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Configuration de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Configuration des erreurs en mode debug
if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Fonction pour gérer les erreurs
function handleError($errno, $errstr, $errfile, $errline) {
    if (DEBUG) {
        echo json_encode([
            'error' => true,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]);
    } else {
        echo json_encode([
            'error' => true,
            'message' => 'Une erreur est survenue'
        ]);
    }
    exit;
}

set_error_handler('handleError');
?>
