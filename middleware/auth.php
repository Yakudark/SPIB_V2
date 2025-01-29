<?php
// Clé secrète pour signer les tokens JWT
define('JWT_SECRET', 'votre_cle_secrete_stib_2024');

function generateJWT($payload) {
    // Header
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

    // Payload
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    // Signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Token complet
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function decodeJWT($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        throw new Exception('Token invalide');
    }

    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

    // Vérifier la signature
    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    
    if (!hash_equals($signature, $expectedSignature)) {
        throw new Exception('Signature invalide');
    }

    // Décoder le payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)));
    if (!$payload) {
        throw new Exception('Payload invalide');
    }

    return $payload;
}

function checkAuth() {
    $headers = getallheaders();
    $authHeader = null;
    
    // Recherche case-insensitive de l'en-tête Authorization
    foreach ($headers as $name => $value) {
        if (strtolower($name) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }
    
    if (!$authHeader) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => 'Token non fourni']);
        exit;
    }

    try {
        $token = str_replace('Bearer ', '', $authHeader);
        $payload = decodeJWT($token);
        return $payload;
    } catch (Exception $e) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

function checkRole($allowedRoles) {
    $payload = checkAuth();
    if (!in_array($payload->role, $allowedRoles)) {
        header('HTTP/1.0 403 Forbidden');
        echo json_encode(['error' => 'Accès non autorisé']);
        exit;
    }
    return $payload;
}

function isAuthorized($roles) {
    try {
        $payload = checkAuth();
        return in_array($payload->role, $roles);
    } catch (Exception $e) {
        return false;
    }
}
?>
