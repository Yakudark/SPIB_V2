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
        SELECT 
            COUNT(*) as nb_absences,
            SUM(DATEDIFF(IFNULL(date_fin, date_debut), date_debut) + 1) as total_jours
        FROM absences
        WHERE agent_id = :user_id
        AND YEAR(date_debut) = YEAR(CURRENT_DATE)
    ";
    
    $stmt = $pdo->prepare($queryAbsences);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $absences = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les entretiens par type de manager
    $queryEntretiens = "
        SELECT 
            CASE 
                WHEN at.nom LIKE 'PM%' THEN 'PM'
                WHEN at.nom LIKE 'EM%' THEN 'EM'
                WHEN at.nom LIKE 'DM%' THEN 'DM'
                ELSE 'Autre'
            END as type_manager,
            COUNT(*) as nb_entretiens
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        WHERE a.agent_id = :user_id
        AND a.statut = 'effectue'
        AND YEAR(a.date_action) = YEAR(CURRENT_DATE)
        GROUP BY 
            CASE 
                WHEN at.nom LIKE 'PM%' THEN 'PM'
                WHEN at.nom LIKE 'EM%' THEN 'EM'
                WHEN at.nom LIKE 'DM%' THEN 'DM'
                ELSE 'Autre'
            END
    ";
    
    $stmt = $pdo->prepare($queryEntretiens);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $entretiens = [
        'PM' => 0,
        'EM' => 0,
        'DM' => 0
    ];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($entretiens[$row['type_manager']])) {
            $entretiens[$row['type_manager']] = (int)$row['nb_entretiens'];
        }
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'absences' => [
                'nombre' => (int)$absences['nb_absences'],
                'total_jours' => (int)($absences['total_jours'] ?? 0)
            ],
            'entretiens' => $entretiens
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur dans absences_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des statistiques',
        'debug' => $e->getMessage()
    ]);
}
