<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$demandeId = $data['id'] ?? null;

if (!$demandeId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de demande manquant']);
    exit;
}

try {
    $pdo = getConnection();
    
    // Vérifier que la demande appartient bien à l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM demandes_conges WHERE id = ? AND user_id = ?");
    $stmt->execute([$demandeId, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Demande non trouvée ou non autorisée']);
        exit;
    }
    
    // Supprimer la demande
    $stmt = $pdo->prepare("DELETE FROM demandes_conges WHERE id = ? AND user_id = ?");
    $stmt->execute([$demandeId, $_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Demande supprimée avec succès']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
}
