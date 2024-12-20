<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérification du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'EM') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

// Log des données reçues
error_log("Données reçues dans update_conge.php: " . print_r($data, true));

if (!isset($data['conge_id']) || !isset($data['status']) || 
    !in_array($data['status'], ['approuve', 'refuse'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier que la demande de congé existe et appartient à un employé géré par cet EM
    $checkQuery = "
        SELECT dc.id, dc.statut, u.em_id
        FROM demandes_conges dc
        JOIN utilisateurs u ON dc.utilisateur_id = u.id
        WHERE dc.id = :conge_id 
        AND u.em_id = :em_id
    ";
    
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([
        'conge_id' => $data['conge_id'],
        'em_id' => $_SESSION['user_id']
    ]);
    
    $conge = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("Résultat de la vérification: " . print_r($conge, true));
    
    if (!$conge) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Demande de congé non trouvée']);
        exit;
    }
    
    if ($conge['statut'] !== 'en_attente') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'La demande n\'est plus en attente']);
        exit;
    }
    
    // Mettre à jour le statut
    $updateQuery = "
        UPDATE demandes_conges 
        SET statut = :status,
            date_reponse = NOW(),
            repondu_par = :repondu_par
        WHERE id = :conge_id
    ";
    
    $stmt = $pdo->prepare($updateQuery);
    $success = $stmt->execute([
        'status' => $data['status'],
        'conge_id' => $data['conge_id'],
        'repondu_par' => $_SESSION['user_id']
    ]);
    
    if (!$success) {
        error_log("Erreur lors de l'exécution de la requête UPDATE: " . print_r($stmt->errorInfo(), true));
        throw new Exception("Échec de la mise à jour");
    }
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    error_log("Erreur em/update_conge.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage()
    ]);
}
