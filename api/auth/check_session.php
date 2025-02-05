<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté et si la session contient les informations nécessaires
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    echo json_encode([
        'success' => true,
        'session' => [
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Session non établie'
    ]);
}
