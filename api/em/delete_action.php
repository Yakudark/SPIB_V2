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
    
    if (!isset($data['action_id'])) {
        echo json_encode(['success' => false, 'error' => 'ID de l\'action manquant']);
        exit;
    }
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que l'action appartient bien à l'EM
    $checkStmt = $pdo->prepare("SELECT id FROM actions WHERE id = ? AND em_id = ?");
    $checkStmt->execute([$data['action_id'], $_SESSION['user_id']]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Action non trouvée ou non autorisée']);
        exit;
    }
    
    // Supprimer l'action
    $deleteStmt = $pdo->prepare("DELETE FROM actions WHERE id = ? AND em_id = ?");
    $success = $deleteStmt->execute([$data['action_id'], $_SESSION['user_id']]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur em/delete_action.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la suppression de l\'action'
    ]);
}
