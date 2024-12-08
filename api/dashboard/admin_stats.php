<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Récupérer le nombre total d'utilisateurs
    $queryUsers = "SELECT COUNT(*) as total FROM utilisateurs";
    $stmtUsers = $db->prepare($queryUsers);
    $stmtUsers->execute();
    $totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer le nombre de départements
    $queryDepts = "SELECT COUNT(*) as total FROM departements";
    $stmtDepts = $db->prepare($queryDepts);
    $stmtDepts->execute();
    $totalDepts = $stmtDepts->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer le nombre de services
    $queryServices = "SELECT COUNT(*) as total FROM services";
    $stmtServices = $db->prepare($queryServices);
    $stmtServices->execute();
    $totalServices = $stmtServices->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer le nombre total d'entretiens
    $queryInterviews = "SELECT COUNT(*) as total FROM entretiens";
    $stmtInterviews = $db->prepare($queryInterviews);
    $stmtInterviews->execute();
    $totalInterviews = $stmtInterviews->fetch(PDO::FETCH_ASSOC)['total'];

    // Récupérer la liste des utilisateurs récents
    $queryUsersList = "SELECT u.id, u.matricule, u.nom, u.prenom, u.role 
                      FROM utilisateurs u 
                      ORDER BY u.created_at DESC 
                      LIMIT 10";
    $stmtUsersList = $db->prepare($queryUsersList);
    $stmtUsersList->execute();
    $users = $stmtUsersList->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer la liste des départements et services
    $queryDeptsList = "SELECT 
                        CASE 
                            WHEN d.id IS NOT NULL THEN d.nom_departement
                            ELSE s.nom_service
                        END as nom,
                        CASE 
                            WHEN d.id IS NOT NULL THEN 'Département'
                            ELSE 'Service'
                        END as type,
                        CASE 
                            WHEN d.id IS NOT NULL THEN (
                                SELECT CONCAT(u.prenom, ' ', u.nom) 
                                FROM utilisateurs u 
                                WHERE u.id = d.responsable_dm_id
                            )
                            ELSE (
                                SELECT CONCAT(u.prenom, ' ', u.nom) 
                                FROM utilisateurs u 
                                WHERE u.id = s.responsable_em_id
                            )
                        END as responsable,
                        CASE 
                            WHEN d.id IS NOT NULL THEN d.id
                            ELSE s.id
                        END as id
                      FROM departements d
                      FULL OUTER JOIN services s ON 1=0
                      ORDER BY type, nom
                      LIMIT 10";
    $stmtDeptsList = $db->prepare($queryDeptsList);
    $stmtDeptsList->execute();
    $departments = $stmtDeptsList->fetchAll(PDO::FETCH_ASSOC);

    // Simuler un journal d'activité (à implémenter avec une vraie table plus tard)
    $activities = [
        [
            'id' => 1,
            'date' => date('Y-m-d H:i:s'),
            'action' => 'Connexion',
            'user' => 'John Doe'
        ],
        [
            'id' => 2,
            'date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'action' => 'Création utilisateur',
            'user' => 'Jane Smith'
        ]
    ];

    // Retourner les données
    echo json_encode([
        'success' => true,
        'totalUsers' => $totalUsers,
        'totalDepartments' => $totalDepts,
        'totalServices' => $totalServices,
        'totalInterviews' => $totalInterviews,
        'users' => $users,
        'departments' => $departments,
        'activities' => $activities
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
