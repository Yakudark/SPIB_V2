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
            dc.*,
            u.nom,
            u.prenom
        FROM demandes_conges dc
        JOIN utilisateurs u ON dc.utilisateur_id = u.id
        WHERE u.pm_id = :pm_id
    ";
    
    $params = ['pm_id' => $_SESSION['user_id']];
    
    // Filtrer par agent si spécifié
    if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
        $query .= " AND dc.utilisateur_id = :agent_id";
        $params['agent_id'] = $_GET['agent_id'];
    }
    
    $query .= " ORDER BY dc.date_demande DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($conges);
    
} catch (PDOException $e) {
    error_log("Erreur conges.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des demandes de congés',
        'debug' => $e->getMessage()
    ]);
}
