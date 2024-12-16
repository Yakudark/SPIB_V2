<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'PM') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que la demande appartient bien à un agent du PM
    $stmt = $pdo->prepare("
        SELECT v.id 
        FROM vacations v
        JOIN utilisateurs u ON v.user_id = u.id
        WHERE v.id = :vacation_id 
        AND u.pm_id = :pm_id
    ");
    $stmt->execute([
        'vacation_id' => $data['id'],
        'pm_id' => $_SESSION['user_id']
    ]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Demande non trouvée ou non autorisée']);
        exit;
    }
    
    // Mettre à jour le statut
    $stmt = $pdo->prepare("
        UPDATE vacations 
        SET statut = :status,
            date_modification = NOW()
        WHERE id = :id
    ");
    
    $success = $stmt->execute([
        'id' => $data['id'],
        'status' => $data['status']
    ]);
    
    echo json_encode(['success' => $success]);
    
} catch (PDOException $e) {
    error_log("Erreur update_vacation.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la mise à jour de la demande',
        'debug' => $e->getMessage()
    ]);
}
