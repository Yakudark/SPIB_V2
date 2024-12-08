<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Récupérer l'ID du manager depuis le token (à implémenter)
    $managerId = isset($_GET['id']) ? $_GET['id'] : null;

    // Récupérer le nombre total d'employés
    $queryEmployees = "SELECT COUNT(*) as total FROM utilisateurs WHERE dm_id = ?";
    $stmtEmployees = $db->prepare($queryEmployees);
    $stmtEmployees->execute([$managerId]);
    $totalEmployees = $stmtEmployees->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer le nombre total d'entretiens
    $queryInterviews = "SELECT COUNT(*) as total FROM entretiens e 
                       INNER JOIN utilisateurs u ON e.utilisateur_id = u.id 
                       WHERE u.dm_id = ?";
    $stmtInterviews = $db->prepare($queryInterviews);
    $stmtInterviews->execute([$managerId]);
    $totalInterviews = $stmtInterviews->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer les entretiens à venir
    $queryUpcoming = "SELECT e.*, u.nom, u.prenom 
                     FROM entretiens e 
                     INNER JOIN utilisateurs u ON e.utilisateur_id = u.id 
                     WHERE u.dm_id = ? AND e.date_action > NOW() 
                     ORDER BY e.date_action ASC 
                     LIMIT 5";
    $stmtUpcoming = $db->prepare($queryUpcoming);
    $stmtUpcoming->execute([$managerId]);
    $upcomingInterviews = [];
    while ($row = $stmtUpcoming->fetch(PDO::FETCH_ASSOC)) {
        $upcomingInterviews[] = [
            'id' => $row['id'],
            'date' => $row['date_action'],
            'employee' => $row['prenom'] . ' ' . $row['nom'],
            'type' => $row['type_action']
        ];
    }

    // Récupérer les équipes
    $queryTeams = "SELECT s.id, s.nom_service as pool, 
                   CONCAT(u.prenom, ' ', u.nom) as manager,
                   (SELECT COUNT(*) FROM utilisateurs WHERE service_id = s.id) as employees
                   FROM services s 
                   LEFT JOIN utilisateurs u ON s.responsable_em_id = u.id 
                   WHERE EXISTS (SELECT 1 FROM utilisateurs WHERE dm_id = ? AND service_id = s.id)";
    $stmtTeams = $db->prepare($queryTeams);
    $stmtTeams->execute([$managerId]);
    $teams = [];
    while ($row = $stmtTeams->fetch(PDO::FETCH_ASSOC)) {
        $teams[] = [
            'id' => $row['id'],
            'pool' => $row['pool'],
            'manager' => $row['manager'],
            'employees' => $row['employees']
        ];
    }

    // Retourner les données
    echo json_encode([
        'success' => true,
        'totalEmployees' => $totalEmployees,
        'totalInterviews' => $totalInterviews,
        'pendingInterviews' => count($upcomingInterviews),
        'upcomingInterviews' => $upcomingInterviews,
        'teams' => $teams
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
