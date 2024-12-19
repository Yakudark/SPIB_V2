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
    
    $query = "
        SELECT 
            a.id,
            a.date_action,
            at.nom as type_action,
            a.commentaire,
            a.statut,
            CONCAT(u.prenom, ' ', u.nom) as agent_name
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN utilisateurs u ON a.agent_id = u.id
        WHERE u.em_id = :em_id
    ";
    
    $params = ['em_id' => $_SESSION['user_id']];
    
    if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
        $query .= " AND a.agent_id = :agent_id";
        $params['agent_id'] = $_GET['agent_id'];
    }
    
    $query .= " ORDER BY a.date_action DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'actions' => $actions]);
    
} catch (PDOException $e) {
    error_log("Erreur em/actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des actions'
    ]);
}
