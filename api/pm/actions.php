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
    
    $query = "
        SELECT 
            a.id,
            a.agent_id,
            u.nom as agent_nom,
            u.prenom as agent_prenom,
            at.nom as type_action,
            a.date_action,
            a.statut,
            a.commentaire
        FROM actions a
        JOIN utilisateurs u ON a.agent_id = u.id
        JOIN action_types at ON a.type_action_id = at.id
        WHERE a.pm_id = :pm_id
    ";
    
    $params = ['pm_id' => $_SESSION['user_id']];
    
    // Ajouter le filtre par agent si spécifié
    if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
        $query .= " AND a.agent_id = :agent_id";
        $params['agent_id'] = $_GET['agent_id'];
    }
    
    $query .= " ORDER BY a.date_action DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($actions);
    
} catch (PDOException $e) {
    error_log("Erreur actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des actions',
        'debug' => $e->getMessage()
    ]);
}
