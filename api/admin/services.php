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

    // Récupérer tous les services
    $query = "SELECT id, nom_service FROM services ORDER BY nom_service";
    $stmt = $pdo->query($query);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'services' => $services
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des services: ' . $e->getMessage()
    ]);
}
