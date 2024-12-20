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

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Récupérer les demandes de congés
        $query = "
            SELECT 
                dc.id,
                dc.date_debut,
                dc.date_fin,
                dc.nb_jours,
                dc.statut,
                dc.commentaire,
                dc.reponse_commentaire,
                dc.date_demande,
                dc.date_reponse,
                CONCAT(u.prenom, ' ', u.nom) as repondu_par_nom
            FROM demandes_conges dc
            LEFT JOIN utilisateurs u ON dc.repondu_par = u.id
            WHERE dc.utilisateur_id = :utilisateur_id
            ORDER BY dc.date_demande DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer le solde des congés
        $querySolde = "
            SELECT conges_total, conges_pris, conges_restant
            FROM conges
            WHERE utilisateur_id = :utilisateur_id
            AND annee = YEAR(CURRENT_DATE)
        ";
        
        $stmt = $pdo->prepare($querySolde);
        $stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
        $solde = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'conges' => $conges,
            'solde' => $solde
        ]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ajouter une nouvelle demande de congés
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Calculer le nombre de jours
        $debut = new DateTime($data['date_debut']);
        $fin = new DateTime($data['date_fin']);
        $interval = $debut->diff($fin);
        $nb_jours = $interval->days + 1;
        
        $query = "
            INSERT INTO demandes_conges (
                utilisateur_id, 
                date_debut, 
                date_fin, 
                nb_jours,
                commentaire, 
                statut
            ) VALUES (
                :utilisateur_id,
                :date_debut,
                :date_fin,
                :nb_jours,
                :commentaire,
                'en_attente'
            )
        ";
        
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([
            'utilisateur_id' => $_SESSION['user_id'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'nb_jours' => $nb_jours,
            'commentaire' => $data['commentaire'] ?? null
        ]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Demande de congés créée avec succès'
            ]);
        } else {
            throw new Exception('Erreur lors de la création de la demande');
        }
    }
} catch (Exception $e) {
    error_log("Erreur dans conges.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors du traitement de la demande',
        'debug' => $e->getMessage()
    ]);
}