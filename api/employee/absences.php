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
    
    // Paramètres de pagination
    $items_per_page = 2;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    
    // Compter le nombre total d'absences
    $count_query = "SELECT COUNT(*) as total FROM absences WHERE agent_id = :agent_id";
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $total_items = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_items / $items_per_page);
    
    // Récupérer les absences avec pagination
    $query = "
        SELECT 
            a.id,
            a.date_debut,
            a.date_fin,
            a.commentaire as motif,
            DATEDIFF(IFNULL(a.date_fin, CURRENT_DATE), a.date_debut) + 1 as jours_passes,
            CASE 
                WHEN a.signale_par_id IS NULL THEN 'Système'
                ELSE CONCAT(u.prenom, ' ', u.nom)
            END as signale_par
        FROM absences a
        LEFT JOIN utilisateurs u ON a.signale_par_id = u.id
        WHERE a.agent_id = :agent_id
        ORDER BY a.date_debut DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':agent_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les dates et les données
    $absences = array_map(function($absence) {
        return [
            'id' => $absence['id'],
            'date_debut' => date('d/m/Y', strtotime($absence['date_debut'])),
            'date_fin' => $absence['date_fin'] === '2999-12-31' ? 'Non définie' : date('d/m/Y', strtotime($absence['date_fin'])),
            'jours_passes' => $absence['jours_passes'],
            'signale_par' => $absence['signale_par'],
            'motif' => $absence['motif'] ?? '-'
        ];
    }, $absences);
    
    echo json_encode([
        'success' => true,
        'absences' => $absences,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_items' => $total_items,
            'items_per_page' => $items_per_page
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
