<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Récupérer les statistiques de congés
    $query = "SELECT 
                COUNT(*) as total_conges,
                SUM(CASE WHEN statut = 'accepté' THEN 1 ELSE 0 END) as conges_pris
              FROM demandes_conges 
              WHERE utilisateur_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculer les congés restants (exemple: 30 jours par an)
    $total_annuel = 30;
    $pris = $stats['conges_pris'] ?? 0;
    $restant = $total_annuel - $pris;

    echo json_encode([
        'success' => true,
        'conges' => [
            'total' => $total_annuel,
            'pris' => $pris,
            'restant' => $restant
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques'
    ]);
}
