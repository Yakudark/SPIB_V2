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
    $data = json_decode(file_get_contents('php://input'), true);
    error_log("Données reçues : " . print_r($data, true));
    
    if (!isset($data['agent_id'], $data['type_action_id'], $data['date_action'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que l'agent est bien sous la responsabilité de l'EM (dans une des deux tables)
    $checkQuery = "
        SELECT id FROM (
            SELECT id FROM employee WHERE em_id = :em_id
            UNION
            SELECT id FROM utilisateurs WHERE em_id = :em_id
        ) as agents 
        WHERE id = :agent_id
    ";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([
        'em_id' => $_SESSION['user_id'],
        'agent_id' => $data['agent_id']
    ]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Agent non autorisé']);
        exit;
    }
    
    $query = "
        INSERT INTO actions (
            agent_id, 
            em_id,
            pm_id,
            type_action_id, 
            date_action, 
            commentaire,
            statut
        ) VALUES (
            :agent_id,
            :em_id,
            NULL,
            :type_action_id,
            :date_action,
            :commentaire,
            'planifie'
        )
    ";
    
    $stmt = $pdo->prepare($query);
    $params = [
        'agent_id' => $data['agent_id'],
        'em_id' => $_SESSION['user_id'],
        'type_action_id' => $data['type_action_id'],
        'date_action' => $data['date_action'],
        'commentaire' => $data['commentaire'] ?? null
    ];
    error_log("Paramètres de la requête : " . print_r($params, true));
    
    $success = $stmt->execute($params);
    
    if (!$success) {
        error_log("Erreur PDO : " . print_r($stmt->errorInfo(), true));
    }
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Action créée avec succès']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la création de l\'action: ' . implode(', ', $stmt->errorInfo())]);
    }
    
} catch (PDOException $e) {
    error_log("Erreur em/create_action.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la création de l\'action: ' . $e->getMessage()
    ]);
}
