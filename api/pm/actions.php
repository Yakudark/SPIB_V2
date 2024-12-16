<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'PM') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les actions avec les informations des agents et les types d'actions
    $stmt = $pdo->prepare("
        SELECT 
            pa.*, 
            u.nom as agent_nom, 
            u.prenom as agent_prenom,
            at.nom as type_action
        FROM pm_actions pa
        JOIN utilisateurs u ON pa.agent_id = u.id
        JOIN action_types at ON pa.action_type_id = at.id
        WHERE pa.pm_id = ?
        ORDER BY pa.date_action DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($actions);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
