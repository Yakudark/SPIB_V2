<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../includes/db.php';

// Log pour vérifier la session
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'non défini'));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Log pour vérifier les données reçues
$rawInput = file_get_contents('php://input');
error_log("Données reçues: " . $rawInput);

$data = json_decode($rawInput, true);
$demandeId = intval($data['id']);

error_log("ID demande: " . $demandeId);
error_log("User ID: " . $_SESSION['user_id']);

try {
    $db = new DB();
    $pdo = $db->connect();
    
    // Vérifier d'abord si la demande existe
    $checkStmt = $pdo->prepare("SELECT id, statut FROM demandes_conges WHERE id = ? AND user_id = ?");
    $checkStmt->execute([$demandeId, $_SESSION['user_id']]);
    $demande = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("Demande trouvée: " . json_encode($demande));
    
    if (!$demande) {
        echo json_encode(['success' => false, 'error' => 'Demande non trouvée']);
        exit;
    }
    
    if ($demande['statut'] !== 'en_attente') {
        echo json_encode(['success' => false, 'error' => 'La demande ne peut plus être supprimée']);
        exit;
    }
    
    // Supprimer la demande
    $stmt = $pdo->prepare("DELETE FROM demandes_conges WHERE id = ? AND user_id = ? AND statut = 'en_attente'");
    $stmt->execute([$demandeId, $_SESSION['user_id']]);
    
    $rowCount = $stmt->rowCount();
    error_log("Nombre de lignes supprimées: " . $rowCount);
    
    if ($rowCount > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Échec de la suppression']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
