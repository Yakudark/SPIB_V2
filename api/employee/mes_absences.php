<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $query = "
        SELECT 
            a.id,
            a.date_absence,
            a.motif,
            CONCAT(u.prenom, ' ', u.nom) as signale_par,
            u.role as role_signaleur,
            DATEDIFF(CURDATE(), a.date_absence) as jours_passes
        FROM absences a
        JOIN utilisateurs u ON a.signale_par_id = u.id
        WHERE a.utilisateur_id = :user_id
        ORDER BY a.date_absence DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    
    $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'absences' => $absences
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/mes_absences.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des absences'
    ]);
}
