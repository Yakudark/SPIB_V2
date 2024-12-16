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
    
    // Récupérer les données
    $data = json_decode(file_get_contents('php://input'), true);
    $action_id = $data['id'] ?? null;
    $statut = $data['statut'] ?? null;

    if (!$action_id || !$statut) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    // Mettre à jour uniquement si l'action appartient au PM
    $stmt = $pdo->prepare("
        UPDATE pm_actions 
        SET statut = ? 
        WHERE id = ? AND pm_id = ?
    ");
    $stmt->execute([$statut, $action_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Action non trouvée ou non autorisée']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
