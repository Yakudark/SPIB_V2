<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les actions planifiées
    $query = "
        SELECT 
            a.id,
            a.date_action as date,
            at.nom as type_action,
            a.commentaire,
            a.statut,
            CONCAT(u.prenom, ' ', u.nom) as manager_name
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN utilisateurs u ON a.pm_id = u.id
        WHERE a.agent_id = :agent_id
        AND a.date_action >= CURDATE()
        AND a.statut = 'planifie'
        ORDER BY a.date_action ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'actions' => $actions
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des actions',
        'debug' => $e->getMessage()
    ]);
}
