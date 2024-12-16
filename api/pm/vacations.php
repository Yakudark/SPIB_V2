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
            v.id,
            v.user_id,
            u.nom,
            u.prenom,
            v.date_debut,
            v.date_fin,
            v.type_conge,
            v.statut,
            v.commentaire
        FROM vacations v
        JOIN utilisateurs u ON v.user_id = u.id
        WHERE u.pm_id = :pm_id
    ";
    
    $params = ['pm_id' => $_SESSION['user_id']];
    
    // Filtrer par agent si spécifié
    if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
        $query .= " AND v.user_id = :agent_id";
        $params['agent_id'] = $_GET['agent_id'];
    }
    
    $query .= " ORDER BY v.date_debut DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $vacations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($vacations);
    
} catch (PDOException $e) {
    error_log("Erreur vacations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des demandes de vacances',
        'debug' => $e->getMessage()
    ]);
}
