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
            WITH RECURSIVE dates AS (
                SELECT MIN(date_debut) as date_min, MAX(GREATEST(date_fin, CURRENT_DATE)) as date_max
                FROM absences a
                JOIN utilisateurs u ON a.agent_id = u.id
                WHERE u.pm_id = :pm_id
                AND date_debut >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                
                UNION ALL
                
                SELECT date_min + INTERVAL 1 DAY, date_max
                FROM dates
                WHERE date_min < date_max
            ),
            absence_periods AS (
                SELECT 
                    a.agent_id,
                    a.date_debut,
                    IFNULL(a.date_fin, CURRENT_DATE) as date_fin,
                    @period_num := CASE 
                        WHEN @last_agent_id != a.agent_id THEN 1
                        WHEN DATEDIFF(a.date_debut, @last_end_date) > 7 THEN @period_num + 1
                        ELSE @period_num 
                    END as period_num,
                    @last_agent_id := a.agent_id as dummy1,
                    @last_end_date := IFNULL(a.date_fin, CURRENT_DATE) as dummy2
                FROM (
                    SELECT @period_num := 0, @last_agent_id := NULL, @last_end_date := NULL
                ) as vars,
                (
                    SELECT a.*, u.pm_id 
                    FROM absences a
                    JOIN utilisateurs u ON a.agent_id = u.id
                    WHERE u.pm_id = :pm_id
                    AND a.date_debut >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                    ORDER BY a.agent_id, a.date_debut
                ) as a
            ),
            agent_periods AS (
                SELECT 
                    agent_id,
                    COUNT(DISTINCT period_num) as total_periods
                FROM absence_periods
                GROUP BY agent_id
            )
            SELECT 
                a.id,
                a.agent_id,
                u.nom as agent_nom,
                u.prenom as agent_prenom,
                a.date_debut,
                a.date_fin,
                a.commentaire,
                DATEDIFF(IFNULL(a.date_fin, CURRENT_DATE), a.date_debut) + 1 as nombre_jours,
                a.signale_par_id,
                CONCAT(u2.prenom, ' ', u2.nom) as signale_par_nom,
                COALESCE(ap.total_periods, 0) as periodes_12_mois
            FROM absences a
            JOIN utilisateurs u ON a.agent_id = u.id
            LEFT JOIN utilisateurs u2 ON a.signale_par_id = u2.id
            LEFT JOIN agent_periods ap ON ap.agent_id = a.agent_id
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
        error_log("\n\n=== NOUVELLE REQUÊTE D'AJOUT D'ABSENCE ===");
        
        // 1. Vérifier si la table est vide
        $count_query = "SELECT COUNT(*) FROM absences";
        $count = $pdo->query($count_query)->fetchColumn();
        error_log("Nombre total d'absences dans la table: " . $count);
        
        if ($count > 0) {
            // Si la table n'est pas vide, afficher toutes les absences
            $all = $pdo->query("SELECT * FROM absences")->fetchAll(PDO::FETCH_ASSOC);
            error_log("Contenu de la table absences:");
            error_log(json_encode($all, JSON_PRETTY_PRINT));
        }
        
        // 2. Récupérer et vérifier les données
        $raw_data = file_get_contents('php://input');
        error_log("Données reçues: " . $raw_data);
        
        $data = json_decode($raw_data, true);
        if (!isset($data['agent_id']) || !isset($data['date_debut'])) {
            throw new Exception('Données manquantes');
        }

        // 3. Vérifier l'agent
        $check_agent_query = "
            SELECT id, nom, prenom 
            FROM utilisateurs 
            WHERE id = :agent_id 
            AND pm_id = :pm_id
        ";
        $check_agent_stmt = $pdo->prepare($check_agent_query);
        $check_agent_stmt->execute([
            'agent_id' => $data['agent_id'],
            'pm_id' => $_SESSION['user_id']
        ]);
        $agent = $check_agent_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agent) {
            throw new Exception('Agent non trouvé ou non autorisé');
        }

        // 4. Insérer directement l'absence sans vérification
        $query = "
            INSERT INTO absences (agent_id, date_debut, date_fin, commentaire, signale_par_id)
            VALUES (:agent_id, :date_debut, :date_fin, :commentaire, :signale_par_id)
        ";
        
        $date_fin = isset($data['date_fin']) && !empty($data['date_fin']) ? $data['date_fin'] : null;
        
        $params = [
            'agent_id' => $data['agent_id'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $date_fin,
            'commentaire' => $data['commentaire'] ?? null,
            'signale_par_id' => $_SESSION['user_id']
        ];
        
        error_log("Tentative d'insertion avec les paramètres: " . json_encode($params));
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute($params);

        if (!$result) {
            throw new Exception('Erreur lors de l\'insertion de l\'absence');
        }
        
        error_log("=== Absence ajoutée avec succès ===");
        echo json_encode(['success' => true, 'message' => 'Absence ajoutée avec succès']);

    } catch (Exception $e) {
        error_log("ERREUR: " . $e->getMessage());
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

        // Vérifier que l'absence appartient à un agent géré par ce PM
        $check_query = "
            SELECT a.id 
            FROM absences a
            JOIN utilisateurs u ON a.agent_id = u.id
            WHERE a.id = :id 
            AND u.pm_id = :pm_id
        ";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([
            'id' => $data['id'],
            'pm_id' => $_SESSION['user_id']
        ]);

        if (!$check_stmt->fetch()) {
            throw new Exception('Absence non trouvée ou non autorisée');
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
