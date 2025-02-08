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
    
    // Récupérer les agents associés à ce PM
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, matricule 
        FROM employee 
        WHERE pm_id = ? 
        ORDER BY nom, prenom
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($agents);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
