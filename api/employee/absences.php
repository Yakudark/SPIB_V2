<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les absences
    $query = "
        SELECT 
            a.id,
            a.date_debut,
            a.date_fin,
            a.commentaire,
            DATEDIFF(IFNULL(a.date_fin, '2999-12-31'), a.date_debut) + 1 as nombre_jours,
            COUNT(*) OVER() as total_absences
        FROM absences a
        WHERE a.agent_id = :agent_id
        ORDER BY a.date_debut DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['agent_id' => $_SESSION['user_id']]);
    $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer le nombre total d'absences
    $total_absences = $absences[0]['total_absences'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'absences' => $absences,
        'total_absences' => $total_absences
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans absences.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des absences',
        'debug' => $e->getMessage()
    ]);
}
