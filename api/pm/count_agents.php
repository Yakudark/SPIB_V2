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
    
    // Compter le nombre d'agents sous la responsabilité du PM
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_agents 
        FROM utilisateurs 
        WHERE pm_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'count' => (int)$result['total_agents']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
