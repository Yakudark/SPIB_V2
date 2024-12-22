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
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Compter les demandes par statut
        $demandesStats = [
            'demandes_approuvees' => 0,
            'demandes_rejetees' => 0,
            'demandes_en_attente' => 0
        ];

        foreach ($demandes as $demande) {
            switch ($demande['statut']) {
                case 'approuve':
                    $demandesStats['demandes_approuvees']++;
                    break;
                case 'refuse':
                    $demandesStats['demandes_rejetees']++;
                    break;
                case 'en_attente':
                    $demandesStats['demandes_en_attente']++;
                    break;
            }
        }
        
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

        if (!$solde) {
            $solde = [
                'conges_total' => 25,
                'conges_pris' => 0,
                'conges_restant' => 25
            ];
        }
        
        // Formater les dates pour l'affichage
        $demandes = array_map(function($demande) {
            return [
                'id' => $demande['id'],
                'date_demande' => date('d/m/Y', strtotime($demande['date_demande'])),
                'date_debut' => date('d/m/Y', strtotime($demande['date_debut'])),
                'date_fin' => date('d/m/Y', strtotime($demande['date_fin'])),
                'nb_jours' => $demande['nb_jours'],
                'statut' => $demande['statut'],
                'commentaire' => $demande['commentaire'],
                'reponse_commentaire' => $demande['reponse_commentaire'],
                'date_reponse' => $demande['date_reponse'] ? date('d/m/Y', strtotime($demande['date_reponse'])) : null,
                'repondu_par_nom' => $demande['repondu_par_nom']
            ];
        }, $demandes);
        
        echo json_encode([
            'success' => true,
            'conges' => [
                'jours_disponibles' => (int)$solde['conges_restant'],
                'demandes_en_attente' => $demandesStats['demandes_en_attente']
            ],
            'demandes' => [
                'demandes_approuvees' => $demandesStats['demandes_approuvees'],
                'demandes_rejetees' => $demandesStats['demandes_rejetees'],
                'liste' => $demandes
            ]
        ]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier si les données sont envoyées via FormData
        $date_debut = $_POST['start_date'] ?? null;
        $date_fin = $_POST['end_date'] ?? null;
        $commentaire = $_POST['comment'] ?? null;

        if (!$date_debut || !$date_fin) {
            throw new Exception('Les dates de début et de fin sont requises');
        }
        
        // Calculer le nombre de jours
        $debut = new DateTime($date_debut);
        $fin = new DateTime($date_fin);
        $interval = $debut->diff($fin);
        $nb_jours = $interval->days + 1;
        
        $query = "
            INSERT INTO demandes_conges (
                utilisateur_id, 
                date_debut, 
                date_fin, 
                nb_jours,
                commentaire, 
                statut,
                date_demande
            ) VALUES (
                :utilisateur_id,
                :date_debut,
                :date_fin,
                :nb_jours,
                :commentaire,
                'en_attente',
                NOW()
            )
        ";
        
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([
            'utilisateur_id' => $_SESSION['user_id'],
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'nb_jours' => $nb_jours,
            'commentaire' => $commentaire
        ]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Demande de congés créée avec succès'
            ]);
        } else {
            throw new Exception('Erreur lors de la création de la demande');
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Récupérer l'ID de la demande à supprimer
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new Exception('ID de la demande manquant');
        }

        // Vérifier que la demande appartient bien à l'utilisateur et est en attente
        $query = "
            SELECT id 
            FROM demandes_conges 
            WHERE id = :id 
            AND utilisateur_id = :utilisateur_id 
            AND statut = 'en_attente'
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $id,
            'utilisateur_id' => $_SESSION['user_id']
        ]);
        
        if (!$stmt->fetch()) {
            throw new Exception('Demande introuvable ou non supprimable');
        }

        // Supprimer la demande
        $query = "DELETE FROM demandes_conges WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute(['id' => $id]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Demande supprimée avec succès'
            ]);
        } else {
            throw new Exception('Erreur lors de la suppression de la demande');
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}