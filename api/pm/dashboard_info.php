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
    
    // Récupérer les informations du PM (service/pool) et le nombre d'agents
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(s.nom_service, 'Non assigné') as pool,
            (
                SELECT COUNT(*) 
                FROM utilisateurs 
                WHERE pm_id = :user_id
            ) as agents_count
        FROM utilisateurs u
        LEFT JOIN services s ON s.pm_id = u.id
        WHERE u.id = :user_id
    ");
    
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'pool' => $result['pool'],
            'agents_count' => (int)$result['agents_count']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Utilisateur non trouvé',
            'pool' => 'Non assigné',
            'agents_count' => 0
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erreur dashboard_info: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la récupération des informations',
        'debug' => $e->getMessage()
    ]);
}
