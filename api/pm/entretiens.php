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
    
    $type = $_GET['type'] ?? 'upcoming';
    $agent_id = $_GET['agent_id'] ?? null;
    
    // Requête de base
    $query = "
        SELECT 
            a.id,
            a.date_action,
            at.nom as type_action,
            CONCAT(u.prenom, ' ', u.nom) as agent_name,
            CONCAT(COALESCE(pm.prenom, em.prenom), ' ', COALESCE(pm.nom, em.nom)) as manager_name,
            a.commentaire,
            a.statut
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN utilisateurs u ON a.agent_id = u.id
        LEFT JOIN utilisateurs pm ON a.pm_id = pm.id
        LEFT JOIN utilisateurs em ON a.em_id = em.id
        WHERE (a.pm_id = :pm_id OR u.pm_id = :pm_id2)
        AND a.statut = 'planifie'
    ";
    
    // Filtrer par agent si spécifié
    if ($agent_id) {
        $query .= " AND a.agent_id = :agent_id";
    }
    
    // Ajouter la condition de date selon le type
    if ($type === 'upcoming') {
        $query .= " AND a.date_action >= CURRENT_DATE";
    } else {
        $query .= " AND a.date_action < CURRENT_DATE";
    }
    
    $query .= " ORDER BY a.date_action " . ($type === 'upcoming' ? 'ASC' : 'DESC');
    
    $stmt = $pdo->prepare($query);
    $params = [
        'pm_id' => $_SESSION['user_id'],
        'pm_id2' => $_SESSION['user_id']
    ];
    
    if ($agent_id) {
        $params['agent_id'] = $agent_id;
    }
    
    $stmt->execute($params);
    $entretiens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    $entretiens = array_map(function($entretien) {
        return [
            'id' => $entretien['id'],
            'date' => date('d/m/Y', strtotime($entretien['date_action'])),
            'type' => $entretien['type_action'],
            'agent' => $entretien['agent_name'],
            'avec' => $entretien['manager_name'] ?: 'Non assigné',
            'commentaire' => $entretien['commentaire'] ?? '-'
        ];
    }, $entretiens);
    
    echo json_encode([
        'success' => true,
        'entretiens' => $entretiens
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans pm/entretiens.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des entretiens'
    ]);
}
