<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    $service = $_GET['service'] ?? '';
    
    if (empty($service)) {
        echo json_encode(['success' => false, 'error' => 'Service non spécifié']);
        exit;
    }

    // Récupérer les managers du service en utilisant la table services
    $query = "SELECT 
                s.responsable_pm_id as pm_id,
                CONCAT(pm.prenom, ' ', pm.nom) as pm_name,
                s.responsable_em_id as em_id,
                CONCAT(em.prenom, ' ', em.nom) as em_name,
                s.responsable_dm_id as dm_id,
                CONCAT(dm.prenom, ' ', dm.nom) as dm_name
              FROM services s
              LEFT JOIN utilisateurs pm ON s.responsable_pm_id = pm.id
              LEFT JOIN utilisateurs em ON s.responsable_em_id = em.id
              LEFT JOIN utilisateurs dm ON s.responsable_dm_id = dm.id
              WHERE s.nom_service = :service";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['service' => $service]);
    $managers = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'managers' => $managers
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des managers: ' . $e->getMessage()
    ]);
}
