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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['date_debut']) || !isset($data['date_fin'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Dates manquantes']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $date_debut = $data['date_debut'];
    $date_fin = $data['date_fin'];
    $commentaire = $data['commentaire'] ?? null;

    // Calculer le nombre de jours ouvrés entre les deux dates
    $debut = new DateTime($date_debut);
    $fin = new DateTime($date_fin);
    $interval = new DateInterval('P1D');
    $periode = new DatePeriod($debut, $interval, $fin->modify('+1 day'));

    $nb_jours = 0;
    foreach ($periode as $date) {
        if ($date->format('N') < 6) { // Du lundi (1) au vendredi (5)
            $nb_jours++;
        }
    }

    $pdo = getDBConnection();

    // Vérifier si l'utilisateur a assez de jours de congés
    $stmt = $pdo->prepare("
        SELECT conges_restant 
        FROM conges 
        WHERE utilisateur_id = ? AND annee = YEAR(CURRENT_DATE)
    ");
    $stmt->execute([$user_id]);
    $conges = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conges || $conges['conges_restant'] < $nb_jours) {
        http_response_code(400);
        echo json_encode(['error' => 'Jours de congés insuffisants']);
        exit;
    }

    // Insérer la demande de congés
    $stmt = $pdo->prepare("
        INSERT INTO demandes_conges 
        (utilisateur_id, date_debut, date_fin, nb_jours, commentaire, statut, date_demande) 
        VALUES (?, ?, ?, ?, ?, 'en_attente', NOW())
    ");
    
    $stmt->execute([
        $user_id,
        $date_debut,
        $date_fin,
        $nb_jours,
        $commentaire
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Demande de congés enregistrée',
        'nb_jours' => $nb_jours
    ]);

} catch (Exception $e) {
    error_log('Erreur demande congés: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la demande de congés']);
}
