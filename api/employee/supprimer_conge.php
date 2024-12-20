<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'salarié') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de la demande manquant']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que la demande appartient bien à l'utilisateur et est en attente
    $query = "
        SELECT id, statut
        FROM demandes_conges
        WHERE id = :id 
        AND utilisateur_id = :user_id
        AND statut = 'en_attente'
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $data['id'],
        'user_id' => $_SESSION['user_id']
    ]);
    
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$demande) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Demande non trouvée ou non supprimable'
        ]);
        exit;
    }
    
    // Supprimer la demande
    $query = "
        DELETE FROM demandes_conges
        WHERE id = :id
    ";
    
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute(['id' => $data['id']]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Échec de la suppression");
    }
    
} catch (Exception $e) {
    error_log("Erreur employee/supprimer_conge.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la suppression de la demande'
    ]);
}
