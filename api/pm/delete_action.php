<?php
session_start();
header('Content-Type: application/json');

require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'PM') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $db = new DB();
    $pdo = $db->connect();
    
    // Récupérer l'ID de l'action
    $data = json_decode(file_get_contents('php://input'), true);
    $action_id = $data['id'] ?? null;

    if (!$action_id) {
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit;
    }

    // Supprimer uniquement si l'action appartient au PM et est en statut 'planifie'
    $stmt = $pdo->prepare("
        DELETE FROM pm_actions 
        WHERE id = ? AND pm_id = ? AND statut = 'planifie'
    ");
    $stmt->execute([$action_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Action non trouvée ou non supprimable']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
