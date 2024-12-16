<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Debug log
error_log("Login attempt started");

function generateJWT($user_id, $role) {
    $payload = [
        'user_id' => $user_id,
        'role' => $role,
        'exp' => time() + JWT_EXPIRATION
    ];
    
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $header = base64_encode($header);
    
    $payload = json_encode($payload);
    $payload = base64_encode($payload);
    
    $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET, true);
    $signature = base64_encode($signature);
    
    return "$header.$payload.$signature";
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Debug log
    error_log("Received data: " . print_r($data, true));
    
    if (!isset($data['matricule']) || !isset($data['password'])) {
        http_response_code(400);
        error_log("Missing matricule or password");
        echo json_encode(['success' => false, 'message' => 'Matricule et mot de passe requis']);
        exit;
    }

    $matricule = $data['matricule'];
    $password = $data['password'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT u.*, c.* FROM connexions c 
              INNER JOIN utilisateurs u ON c.utilisateur_id = u.id 
              WHERE c.matricule = ? AND c.password = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$matricule, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug log
    error_log("Query result: " . print_r($user, true));

    if ($user) {
        $updateQuery = "UPDATE connexions SET derniere_connexion = NOW() WHERE utilisateur_id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$user['utilisateur_id']]);

        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['matricule'] = $user['matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        // Debug log
        error_log("Session set: " . print_r($_SESSION, true));

        // Générer le token JWT
        $token = generateJWT($user['utilisateur_id'], $user['role']);

        $response = [
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user['utilisateur_id'],
                'matricule' => $user['matricule'],
                'role' => $user['role'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom']
            ]
        ];
        
        // Debug log
        error_log("Sending response: " . print_r($response, true));
        
        echo json_encode($response);
    } else {
        http_response_code(401);
        error_log("Authentication failed for matricule: $matricule");
        echo json_encode(['success' => false, 'message' => 'Matricule ou mot de passe incorrect']);
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
}
