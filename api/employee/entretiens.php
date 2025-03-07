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
    
    $type = $_GET['type'] ?? 'upcoming';
    
    // Requête de base
    $query = "
        SELECT 
            a.id,
            a.date_action,
            at.nom as type_action,
            CONCAT(COALESCE(pm.prenom, em.prenom), ' ', COALESCE(pm.nom, em.nom)) as manager_name,
            a.commentaire,
            a.statut
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        LEFT JOIN utilisateurs pm ON a.pm_id = pm.id
        LEFT JOIN utilisateurs em ON a.em_id = em.id
        WHERE a.agent_id = :agent_id
        AND a.statut = 'planifie'
    ";
    
    // Ajouter la condition de date selon le type
    if ($type === 'upcoming') {
        $query .= " AND a.date_action >= CURRENT_DATE";
    } else {
        $query .= " AND a.date_action < CURRENT_DATE";
    }
    
    $query .= " ORDER BY a.date_action " . ($type === 'upcoming' ? 'ASC' : 'DESC');
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $entretiens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    $entretiens = array_map(function($entretien) {
        return [
            'id' => $entretien['id'],
            'date' => date('d/m/Y', strtotime($entretien['date_action'])),
            'type' => $entretien['type_action'],
            'avec' => $entretien['manager_name'] ?: 'Non assigné',
            'commentaire' => $entretien['commentaire'] ?? '-'
        ];
    }, $entretiens);
    
    echo json_encode([
        'success' => true,
        'entretiens' => $entretiens
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans employee/entretiens.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des entretiens'
    ]);
}
