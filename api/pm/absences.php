<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'PM') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

// GET : Récupérer les absences
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $query = "
            SELECT 
                a.id,
                a.agent_id,
                u.nom as agent_nom,
                u.prenom as agent_prenom,
                a.date_debut,
                a.date_fin,
                a.commentaire,
                DATEDIFF(IFNULL(a.date_fin, '2999-12-31'), a.date_debut) + 1 as nombre_jours,
                a.signale_par_id,
                CONCAT(u2.prenom, ' ', u2.nom) as signale_par_nom
            FROM absences a
            JOIN utilisateurs u ON a.agent_id = u.id
            LEFT JOIN utilisateurs u2 ON a.signale_par_id = u2.id
            WHERE u.pm_id = :pm_id
            ORDER BY a.date_debut DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['pm_id' => $_SESSION['user_id']]);
        $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'absences' => $absences]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// POST : Ajouter une absence
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['agent_id']) || !isset($data['date_debut'])) {
            throw new Exception('Données manquantes');
        }

        // Si pas de date de fin, utiliser 31/12/2999
        $date_fin = !empty($data['date_fin']) ? $data['date_fin'] : '2999-12-31';
        
        $query = "
            INSERT INTO absences (agent_id, date_debut, date_fin, commentaire, signale_par_id)
            VALUES (:agent_id, :date_debut, :date_fin, :commentaire, :signale_par_id)
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'agent_id' => $data['agent_id'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $date_fin,
            'commentaire' => $data['commentaire'] ?? null,
            'signale_par_id' => $_SESSION['user_id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Absence ajoutée avec succès']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// DELETE : Supprimer une absence
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            throw new Exception('ID manquant');
        }
        
        $query = "DELETE FROM absences WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $data['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Absence supprimée avec succès']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
