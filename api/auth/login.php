<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $matricule = $data['matricule'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($matricule) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Matricule et mot de passe requis']);
        exit;
    }

    $database = new Database();
    $pdo = $database->getConnection();

    // Requête pour vérifier les identifiants et récupérer les infos du PM
    $query = "SELECT u.*, 
              pm.nom as pm_nom, 
              pm.prenom as pm_prenom 
              FROM utilisateurs u 
              LEFT JOIN utilisateurs pm ON u.pm_id = pm.id 
              WHERE u.matricule = :matricule 
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['matricule' => $matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        // Mise à jour de la dernière connexion
        $updateQuery = "UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['id' => $user['id']]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['matricule'] = $user['matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['pool'] = $user['pool'];
        $_SESSION['pm_id'] = $user['pm_id'];
        $_SESSION['pm_nom'] = $user['pm_nom'];
        $_SESSION['pm_prenom'] = $user['pm_prenom'];

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['id'],
                'matricule' => $user['matricule'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion']);
}
