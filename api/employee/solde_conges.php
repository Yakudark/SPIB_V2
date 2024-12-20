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
    
    $annee = date('Y');
    
    // Récupérer les congés en attente
    $queryEnAttente = "
        SELECT COALESCE(SUM(nb_jours), 0) as jours_en_attente
        FROM demandes_conges
        WHERE utilisateur_id = :user_id
        AND statut = 'en_attente'
        AND YEAR(date_debut) = :annee
    ";
    
    $stmt = $pdo->prepare($queryEnAttente);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'annee' => $annee
    ]);
    
    $enAttente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les congés approuvés
    $queryApprouves = "
        SELECT COALESCE(SUM(nb_jours), 0) as jours_pris
        FROM demandes_conges
        WHERE utilisateur_id = :user_id
        AND statut = 'approuvé'
        AND YEAR(date_debut) = :annee
    ";
    
    $stmt = $pdo->prepare($queryApprouves);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'annee' => $annee
    ]);
    
    $approuves = $stmt->fetch(PDO::FETCH_ASSOC);
    $joursPris = (int)$approuves['jours_pris'];
    
    // Récupérer ou créer le solde de congés
    $query = "
        SELECT 
            conges_total
        FROM conges
        WHERE utilisateur_id = :user_id
        AND annee = :annee
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'annee' => $annee
    ]);
    
    $conges = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conges) {
        $congesTotal = (int)$conges['conges_total'];
    } else {
        // Si aucun enregistrement n'existe pour cette année, on en crée un
        $congesTotal = 25; // Valeur par défaut
        $insertQuery = "
            INSERT INTO conges (utilisateur_id, annee, conges_total)
            VALUES (:user_id, :annee, :conges_total)
        ";
        
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'annee' => $annee,
            'conges_total' => $congesTotal
        ]);
    }

    // Mettre à jour les jours pris et restants
    $updateQuery = "
        UPDATE conges
        SET conges_pris = :jours_pris,
            conges_restant = conges_total - :jours_pris
        WHERE utilisateur_id = :user_id
        AND annee = :annee
    ";
    
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'annee' => $annee,
        'jours_pris' => $joursPris
    ]);

    echo json_encode([
        'success' => true,
        'conges' => [
            'conges_total' => $congesTotal,
            'conges_pris' => $joursPris,
            'conges_restant' => $congesTotal - $joursPris,
            'jours_en_attente' => (int)$enAttente['jours_en_attente']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Erreur employee/solde_conges.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération du solde de congés'
    ]);
}
