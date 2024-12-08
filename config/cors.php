<?php
// Autoriser l'accès depuis n'importe quelle origine
header('Access-Control-Allow-Origin: *');

// Autoriser les méthodes HTTP spécifiques
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Autoriser les en-têtes spécifiques
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Durée de validité du pre-flight
header('Access-Control-Max-Age: 1728000');
header('Content-Length: 0');
header('Content-Type: text/plain');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}
