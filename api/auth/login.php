<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

try {
    // Récupérer les données POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    $matricule = $data['matricule'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($matricule) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Matricule et mot de passe requis']);
        exit;
    }

    // Connexion à la base de données
    $database = new Database();
    $pdo = $database->getConnection();

    // Vérifier les identifiants
    $query = "SELECT u.*, c.password, c.matricule as login_matricule
              FROM utilisateurs u 
              INNER JOIN connexions c ON u.id = c.utilisateur_id 
              WHERE c.matricule = :matricule";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['matricule' => $matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {  // Comparaison directe car les mots de passe sont en texte brut
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['matricule'] = $user['login_matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        // Mettre à jour la dernière connexion
        $updateQuery = "UPDATE connexions SET derniere_connexion = NOW() WHERE utilisateur_id = :user_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['user_id' => $user['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['id'],
                'matricule' => $user['login_matricule'],
                'role' => $user['role'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Matricule ou mot de passe incorrect']);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la connexion: ' . $e->getMessage()
    ]);
}
