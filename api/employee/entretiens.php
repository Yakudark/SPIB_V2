<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les entretiens planifiés
    $query = "
        SELECT 
            a.id,
            a.date_action,
            at.nom as type_action,
            CONCAT(u.prenom, ' ', u.nom) as manager_name,
            a.commentaire,
            a.statut
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN utilisateurs u ON a.pm_id = u.id
        WHERE a.agent_id = :agent_id
        AND a.statut = 'planifie'
        AND a.date_action >= CURRENT_DATE
        ORDER BY a.date_action ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $entretiens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    $entretiens = array_map(function($entretien) {
        return [
            'id' => $entretien['id'],
            'date' => date('d/m/Y', strtotime($entretien['date_action'])),
            'type' => $entretien['type_action'],
            'avec' => $entretien['manager_name'],
            'commentaire' => $entretien['commentaire'] ?? '-'
        ];
    }, $entretiens);
    
    echo json_encode([
        'success' => true,
        'entretiens' => $entretiens
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans entretiens.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des entretiens',
        'debug' => $e->getMessage()
    ]);
}
