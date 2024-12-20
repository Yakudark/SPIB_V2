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
    
    $conditions = ['u.em_id = :em_id'];
    $params = ['em_id' => $_SESSION['user_id']];
    
    if (isset($_GET['pool']) && !empty($_GET['pool'])) {
        $conditions[] = 'u.pool = :pool';
        $params['pool'] = $_GET['pool'];
    }
    
    if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
        $conditions[] = 'dc.utilisateur_id = :agent_id';
        $params['agent_id'] = $_GET['agent_id'];
    }
    
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $conditions[] = 'dc.statut = :status';
        $params['status'] = $_GET['status'];
    }
    
    $whereClause = implode(' AND ', $conditions);
    
    $query = "
        SELECT 
            dc.*,
            u.prenom,
            u.nom,
            CONCAT(u.prenom, ' ', u.nom) as agent_name
        FROM demandes_conges dc
        JOIN utilisateurs u ON dc.utilisateur_id = u.id
        WHERE $whereClause
        ORDER BY dc.date_demande DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'conges' => $conges]);
    
} catch (PDOException $e) {
    error_log("Erreur em/conges.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des congés'
    ]);
}
