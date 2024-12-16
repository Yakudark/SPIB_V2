<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'PM') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer les données JSON
    $rawData = file_get_contents('php://input');
    error_log("Données reçues : " . $rawData);
    $data = json_decode($rawData, true);
    
    // Récupérer et valider les données
    $agent_id = $data['agent_id'] ?? null;
    $action_type_id = $data['action_type'] ?? null;
    $date_action = $data['date_action'] ?? null;
    $commentaire = $data['commentaire'] ?? '';

    error_log("Données extraites : " . json_encode([
        'agent_id' => $agent_id,
        'action_type_id' => $action_type_id,
        'date_action' => $date_action,
        'commentaire' => $commentaire
    ]));

    if (!$agent_id || !$action_type_id || !$date_action) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes', 'debug' => [
            'agent_id' => $agent_id,
            'action_type_id' => $action_type_id,
            'date_action' => $date_action
        ]]);
        exit;
    }

    // Vérifier que l'agent est bien sous la responsabilité du PM
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = :agent_id AND pm_id = :pm_id");
    $stmt->execute([
        'agent_id' => $agent_id,
        'pm_id' => $_SESSION['user_id']
    ]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Agent non autorisé']);
        exit;
    }

    // Créer l'action
    $query = "
        INSERT INTO actions (agent_id, pm_id, type_action_id, date_action, commentaire, statut)
        VALUES (:agent_id, :pm_id, :action_type_id, :date_action, :commentaire, 'planifie')
    ";
    error_log("Requête SQL : " . $query);
    
    $stmt = $pdo->prepare($query);
    
    $params = [
        'agent_id' => $agent_id,
        'pm_id' => $_SESSION['user_id'],
        'action_type_id' => $action_type_id,
        'date_action' => $date_action,
        'commentaire' => $commentaire
    ];
    error_log("Paramètres : " . json_encode($params));
    
    $success = $stmt->execute($params);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        $error = $stmt->errorInfo();
        echo json_encode([
            'success' => false, 
            'error' => 'Erreur lors de la création de l\'action',
            'debug' => $error
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erreur create_action.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur lors de la création de l\'action',
        'debug' => $e->getMessage()
    ]);
}
