<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("
        SELECT 
            dc.*,
            CONCAT(u.prenom, ' ', u.nom) as repondu_par_nom
        FROM demandes_conges dc
        LEFT JOIN utilisateurs u ON dc.repondu_par = u.id
        WHERE dc.utilisateur_id = ?
        ORDER BY dc.date_demande DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'demandes' => $demandes
    ]);

} catch (Exception $e) {
    error_log('Erreur liste congés: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des demandes']);
}
