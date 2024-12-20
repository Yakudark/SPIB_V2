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
    
    // Récupérer les actions planifiées
    $queryActions = "
        SELECT 
            'action' as type,
            a.id,
            a.date_action as date,
            at.nom as type_action,
            a.commentaire,
            a.statut,
            CONCAT(u.prenom, ' ', u.nom) as manager_name,
            'PM' as manager_role
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        JOIN utilisateurs u ON a.pm_id = u.id
        WHERE a.agent_id = :agent_id
        AND a.date_action >= CURDATE()
        AND a.statut = 'planifie'
    ";
    
    // Récupérer les absences
    $queryAbsences = "
        SELECT 
            'absence' as type,
            a.id,
            a.date_absence as date,
            'Absence' as type_action,
            a.motif as commentaire,
            NULL as statut,
            CONCAT(u.prenom, ' ', u.nom) as manager_name,
            u.role as manager_role
        FROM absences a
        JOIN utilisateurs u ON a.signale_par_id = u.id
        WHERE a.utilisateur_id = :agent_id
        AND a.date_absence >= CURDATE()
    ";
    
    // Exécuter les requêtes
    $stmt = $pdo->prepare($queryActions);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare($queryAbsences);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner et trier les résultats
    $evenements = array_merge($actions, $absences);
    usort($evenements, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
    
    echo json_encode(['success' => true, 'actions' => $evenements]);
    
} catch (PDOException $e) {
    error_log("Erreur employee/actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des actions'
    ]);
}
