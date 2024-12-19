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
    
    if (!isset($data['agent_id'], $data['type_action_id'], $data['date_action'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que l'agent est bien sous la responsabilité de l'EM
    $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ? AND em_id = ?");
    $checkStmt->execute([$data['agent_id'], $_SESSION['user_id']]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Agent non autorisé']);
        exit;
    }
    
    $query = "
        INSERT INTO actions (
            agent_id, 
            em_id,
            type_action_id, 
            date_action, 
            commentaire,
            statut
        ) VALUES (
            :agent_id,
            :em_id,
            :type_action_id,
            :date_action,
            :commentaire,
            'planifie'
        )
    ";
    
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([
        'agent_id' => $data['agent_id'],
        'em_id' => $_SESSION['user_id'],
        'type_action_id' => $data['type_action_id'],
        'date_action' => $data['date_action'],
        'commentaire' => $data['commentaire'] ?? null
    ]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Action créée avec succès']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la création de l\'action']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur em/create_action.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la création de l\'action'
    ]);
}
