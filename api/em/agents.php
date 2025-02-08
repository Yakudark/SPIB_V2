<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérification du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'EM') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les salariés qui sont sous la responsabilité de l'EM
    $query = "
        (SELECT 
            id,
            nom,
            prenom,
            matricule,
            'employee' as source
        FROM employee 
        WHERE em_id = :em_id)
        
        UNION
        
        (SELECT 
            id,
            nom,
            prenom,
            matricule,
            'utilisateur' as source
        FROM utilisateurs 
        WHERE em_id = :em_id)
        
        ORDER BY nom ASC, prenom ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['em_id' => $_SESSION['user_id']]);
    
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'agents' => $agents]);
    
} catch (PDOException $e) {
    error_log("Erreur em/agents.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des agents'
    ]);
}
