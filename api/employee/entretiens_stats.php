<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'salarié') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer tous les types d'actions depuis la table action_types
    $queryTypes = "SELECT id, nom, description FROM action_types ORDER BY id ASC";
    $stmtTypes = $pdo->query($queryTypes);
    $allTypes = [];
    while ($row = $stmtTypes->fetch(PDO::FETCH_ASSOC)) {
        $allTypes[$row['id']] = [
            'nom' => $row['nom'],
            'description' => $row['description'],
            'count' => 0
        ];
    }
    
    // Récupérer le nombre d'actions pour chaque type
    $query = "
        SELECT 
            at.id as type_id,
            COUNT(*) as count
        FROM actions a
        JOIN action_types at ON a.type_action_id = at.id
        WHERE a.agent_id = :user_id
        AND YEAR(a.date_action) = :year
        AND a.statut IN ('planifie', 'effectue')
        GROUP BY at.id
        ORDER BY COUNT(*) DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'year' => $year
    ]);
    
    // Mettre à jour les compteurs
    $stats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($allTypes[$row['type_id']])) {
            $type = $allTypes[$row['type_id']];
            $stats[] = [
                'type' => $type['nom'],
                'description' => $type['description'],
                'count' => (int)$row['count']
            ];
            // Retirer le type pour qu'il ne soit pas ajouté à la fin avec count = 0
            unset($allTypes[$row['type_id']]);
        }
    }
    
    // Ajouter les types sans actions à la fin
    foreach ($allTypes as $type) {
        $stats[] = [
            'type' => $type['nom'],
            'description' => $type['description'],
            'count' => 0
        ];
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/entretiens_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des statistiques'
    ]);
}
