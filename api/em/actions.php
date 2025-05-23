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

    // Sous-requête pour obtenir les informations des agents des deux tables
    $subquery = "
        (SELECT 
            id,
            nom,
            prenom,
            matricule
        FROM employee 
        WHERE em_id = :em_id
        
        UNION
        
        SELECT 
            id,
            nom,
            prenom,
            matricule
        FROM utilisateurs 
        WHERE em_id = :em_id)
    ";
    
    // Requête principale pour obtenir les actions
    $query = "
        SELECT 
            a.id,
            a.date_action,
            at.nom as type_action,
            a.commentaire,
            a.statut,
            CONCAT(ag.prenom, ' ', ag.nom, ' (', ag.matricule, ')') as agent_name
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN ($subquery) ag ON a.agent_id = ag.id
        WHERE a.em_id = :em_id
        ORDER BY a.date_action DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['em_id' => $_SESSION['user_id']]);
    
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'actions' => $actions]);
    
} catch (PDOException $e) {
    error_log("Erreur em/actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des actions'
    ]);
}
