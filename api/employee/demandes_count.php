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
    
    $query = "
        SELECT 
            COUNT(*) as total_demandes,
            SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as demandes_en_attente,
            SUM(CASE WHEN statut = 'approuve' THEN 1 ELSE 0 END) as demandes_approuvees,
            SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as demandes_rejetees
        FROM demandes_conges
        WHERE utilisateur_id = :user_id
        AND YEAR(date_demande) = YEAR(CURRENT_DATE)
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $_SESSION['user_id']
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Convertir les valeurs NULL en 0
    $result['total_demandes'] = (int)$result['total_demandes'];
    $result['demandes_en_attente'] = (int)$result['demandes_en_attente'];
    $result['demandes_approuvees'] = (int)$result['demandes_approuvees'];
    $result['demandes_rejetees'] = (int)$result['demandes_rejetees'];
    
    echo json_encode([
        'success' => true,
        'demandes' => $result
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/demandes_count.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération du nombre de demandes'
    ]);
}
