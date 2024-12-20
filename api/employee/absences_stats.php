<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'salarié') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les absences
    $queryAbsences = "
        SELECT COUNT(*) as nb_absences
        FROM absences
        WHERE utilisateur_id = :user_id
        AND YEAR(date_absence) = YEAR(CURRENT_DATE)
    ";
    
    $stmt = $pdo->prepare($queryAbsences);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $absences = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les entretiens par type de manager
    $queryEntretiens = "
        SELECT 
            manager_role as type_manager,
            COUNT(*) as nb_entretiens
        FROM entretiens
        WHERE utilisateur_id = :user_id
        AND YEAR(date_entretien) = YEAR(CURRENT_DATE)
        AND statut = 'réalisé'
        GROUP BY manager_role
    ";
    
    $stmt = $pdo->prepare($queryEntretiens);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $entretiens = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $entretiens[$row['type_manager']] = $row['nb_entretiens'];
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'absences' => (int)$absences['nb_absences'],
            'entretiens' => [
                'PM' => isset($entretiens['PM']) ? (int)$entretiens['PM'] : 0,
                'EM' => isset($entretiens['EM']) ? (int)$entretiens['EM'] : 0,
                'DM' => isset($entretiens['DM']) ? (int)$entretiens['DM'] : 0
            ]
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/absences_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des statistiques'
    ]);
}
