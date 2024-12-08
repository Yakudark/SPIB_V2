<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['matricule']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Matricule et mot de passe requis']);
        exit;
    }

    $matricule = $data['matricule'];
    $password = $data['password'];

    $database = new Database();
    $db = $database->getConnection();

    // Vérification simple dans la table connexions
    $query = "SELECT u.*, c.* FROM connexions c 
              INNER JOIN utilisateurs u ON c.utilisateur_id = u.id 
              WHERE c.matricule = ? AND c.password = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$matricule, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Mise à jour de la dernière connexion
        $updateQuery = "UPDATE connexions SET derniere_connexion = NOW() WHERE utilisateur_id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$user['utilisateur_id']]);

        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['matricule'] = $user['matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['utilisateur_id'],
                'matricule' => $user['matricule'],
                'role' => $user['role'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Identifiants incorrects'
        ]);
    }
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données'
    ]);
}
