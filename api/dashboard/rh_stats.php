<?php
require_once '../../config/database.php';
require_once '../../middleware/auth.php';

header('Content-Type: application/json');

try {
    $pdo = getConnection();
    
    // Récupérer les statistiques des entretiens
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN DATE(date_entretien) = CURDATE() THEN 1 ELSE 0 END) as today
        FROM entretiens
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer le nombre total de mesures RH actives
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM mesures_rh
        WHERE status = 'active'
    ");
    $stmt->execute();
    $mesuresStats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les prochains entretiens
    $stmt = $pdo->prepare("
        SELECT e.id, e.date_entretien as date, 
               CONCAT(u.prenom, ' ', u.nom) as employee,
               e.type
        FROM entretiens e
        JOIN utilisateurs u ON e.utilisateur_id = u.id
        WHERE e.date_entretien >= CURDATE()
        ORDER BY e.date_entretien ASC
        LIMIT 5
    ");
    $stmt->execute();
    $upcomingInterviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les mesures RH en cours
    $stmt = $pdo->prepare("
        SELECT m.id, m.date_creation as date,
               CONCAT(u.prenom, ' ', u.nom) as employee,
               m.type
        FROM mesures_rh m
        JOIN utilisateurs u ON m.utilisateur_id = u.id
        WHERE m.status = 'active'
        ORDER BY m.date_creation DESC
        LIMIT 5
    ");
    $stmt->execute();
    $currentMeasures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les appels bienveillants à effectuer
    $stmt = $pdo->prepare("
        SELECT a.id, a.date_prevue as date,
               CONCAT(u.prenom, ' ', u.nom) as employee,
               a.status
        FROM appels_bienveillants a
        JOIN utilisateurs u ON a.utilisateur_id = u.id
        WHERE a.status != 'completed'
        ORDER BY a.date_prevue ASC
        LIMIT 5
    ");
    $stmt->execute();
    $welcomeCalls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Préparer la réponse
    $response = [
        'totalEntretiens' => $stats['total'],
        'entretiensEnAttente' => $stats['pending'],
        'entretiensDuJour' => $stats['today'],
        'totalMesures' => $mesuresStats['total'],
        'upcomingInterviews' => $upcomingInterviews,
        'currentMeasures' => $currentMeasures,
        'welcomeCalls' => $welcomeCalls
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
