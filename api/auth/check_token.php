<?php
require_once '../../config/cors.php';
require_once '../../middleware/auth.php';

header('Content-Type: application/json');

try {
    // Vérifier le token
    $payload = checkAuth();
    
    // Si on arrive ici, le token est valide
    echo json_encode([
        'success' => true,
        'user_id' => $payload->user_id,
        'role' => $payload->role
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
