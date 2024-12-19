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
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les pools uniques avec leurs PMs
    $query = "
        SELECT DISTINCT 
            u.pool,
            pm.id as pm_id,
            CONCAT(pm.prenom, ' ', pm.nom) as pm_name
        FROM utilisateurs u
        JOIN utilisateurs pm ON u.pm_id = pm.id
        WHERE u.em_id = :em_id 
        AND u.pool IS NOT NULL
        ORDER BY u.pool ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['em_id' => $_SESSION['user_id']]);
    
    $pools = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'pools' => $pools]);
    
} catch (PDOException $e) {
    error_log("Erreur em/pools.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des pools'
    ]);
}
