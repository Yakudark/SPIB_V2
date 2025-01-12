<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté et est un SuperAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Récupérer les paramètres de pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Compter le nombre total d'utilisateurs
    $countQuery = "SELECT COUNT(*) as total FROM utilisateurs";
    $countStmt = $pdo->query($countQuery);
    $totalUsers = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalUsers / $limit);

    // Requête pour récupérer les utilisateurs avec pagination
    $query = "
        SELECT 
            u.id,
            u.nom,
            u.prenom,
            u.matricule,
            u.role,
            u.pool,
            CONCAT(pm.prenom, ' ', pm.nom) as pm_name,
            CONCAT(em.prenom, ' ', em.nom) as em_name,
            CONCAT(dm.prenom, ' ', dm.nom) as dm_name
        FROM utilisateurs u
        LEFT JOIN utilisateurs pm ON u.pm_id = pm.id
        LEFT JOIN utilisateurs em ON u.em_id = em.id
        LEFT JOIN utilisateurs dm ON u.dm_id = dm.id
        ORDER BY u.nom, u.prenom
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalUsers,
            'items_per_page' => $limit
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()
    ]);
}
