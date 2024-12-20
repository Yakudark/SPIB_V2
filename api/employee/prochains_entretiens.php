<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'salarié') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les absences
    $queryAbsences = "
        SELECT 
            a.date_absence as date,
            'Absence' as type,
            u.nom as manager_nom,
            u.prenom as manager_prenom,
            NULL as type_action,
            NULL as commentaire,
            'absence' as categorie
        FROM absences a
        JOIN utilisateurs u ON a.signale_par_id = u.id
        WHERE a.utilisateur_id = :user_id
        AND a.date_absence >= CURRENT_DATE
    ";
    
    // Récupérer les entretiens planifiés
    $queryEntretiens = "
        SELECT 
            e.date_entretien as date,
            'Entretien' as type,
            u.nom as manager_nom,
            u.prenom as manager_prenom,
            at.nom as type_action,
            e.motif as commentaire,
            e.manager_role as categorie
        FROM entretiens e
        JOIN utilisateurs u ON e.manager_id = u.id
        JOIN action_types at ON e.type_action_id = at.id
        WHERE e.utilisateur_id = :user_id
        AND e.date_entretien >= CURRENT_DATE
        AND e.statut = 'planifié'
    ";
    
    // Récupérer les actions planifiées
    $queryActions = "
        SELECT 
            a.date_action as date,
            'Action' as type,
            u.nom as manager_nom,
            u.prenom as manager_prenom,
            at.nom as type_action,
            a.commentaire,
            'PM' as categorie
        FROM actions a
        JOIN utilisateurs u ON a.pm_id = u.id
        JOIN action_types at ON a.type_action_id = at.id
        WHERE a.agent_id = :user_id
        AND a.date_action >= CURRENT_DATE
        AND a.statut = 'planifie'
    ";
    
    // Exécuter les requêtes
    $stmt = $pdo->prepare($queryAbsences);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare($queryEntretiens);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $entretiens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare($queryActions);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner tous les résultats
    $evenements = array_merge($absences, $entretiens, $actions);
    
    // Trier par date
    usort($evenements, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
    
    echo json_encode([
        'success' => true,
        'evenements' => $evenements
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/prochains_entretiens.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des prochains entretiens'
    ]);
}
