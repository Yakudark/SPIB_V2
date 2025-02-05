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

    // Requête pour vérifier les identifiants
    $query = "SELECT * FROM utilisateurs WHERE matricule = :matricule LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['matricule' => $matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        // Mise à jour de la dernière connexion
        $updateQuery = "UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['id' => $user['id']]);

        // Stockage des informations dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['matricule'] = $user['matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        // Debug du rôle
        error_log("=== Debug du rôle ===");
        error_log("Rôle original: '" . $user['role'] . "'");
        error_log("Rôle en majuscules: '" . strtoupper($user['role']) . "'");
        error_log("Longueur du rôle: " . strlen($user['role']));
        error_log("Rôle en hexadécimal: " . bin2hex($user['role']));

        // Définir l'URL de redirection en fonction du rôle
        $redirectUrl = '';
        $role = trim(strtoupper($user['role'])); // Ajout de trim()
        error_log("Rôle après trim et majuscules: '" . $role . "'");

        switch($role) {
            case 'EM':
                $redirectUrl = '/JS/STIB/dashboard/em.php';
                break;
            case 'PM':
                $redirectUrl = '/JS/STIB/dashboard/pm.php';
                break;
            case 'RH':
                $redirectUrl = '/JS/STIB/dashboard/rh.php';
                break;
            case 'DM':
                $redirectUrl = '/JS/STIB/dashboard/manager.php';
                break;
            case 'SUPERADMIN':
                $redirectUrl = '/JS/STIB/dashboard/admin.php';
                break;
            case 'SALARIE':
            case 'SALARIÉ':
                $redirectUrl = '/JS/STIB/dashboard/employee.php';
                break;
            default:
                $redirectUrl = '/JS/STIB/public/login.php';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'redirect' => $redirectUrl,
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
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion']);
}
