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
    
    // Récupérer et valider les données
    $agent_id = $_POST['agent_id'] ?? null;
    $action_type_id = $_POST['action_type'] ?? null;
    $date_action = $_POST['date_action'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';

    if (!$agent_id || !$action_type_id || !$date_action) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    // Vérifier que l'agent est bien sous la responsabilité du PM
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ? AND manager_id = ?");
    $stmt->execute([$agent_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Agent non autorisé']);
        exit;
    }

    // Créer l'action
    $stmt = $pdo->prepare("
        INSERT INTO pm_actions (agent_id, pm_id, action_type_id, date_action, commentaire, statut)
        VALUES (?, ?, ?, ?, ?, 'planifie')
    ");
    $stmt->execute([
        $agent_id,
        $_SESSION['user_id'],
        $action_type_id,
        $date_action,
        $commentaire
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
