<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $query = "SELECT 
                u.*,
                u.pool as user_pool,
                pm.nom as pm_nom,
                pm.prenom as pm_prenom,
                pm.id as pm_id
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

        // Stockage des informations dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['matricule'] = $user['matricule'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['pool'] = $user['user_pool'] ?? null;
        $_SESSION['pm_id'] = $user['pm_id'];
        $_SESSION['pm_nom'] = $user['pm_nom'];
        $_SESSION['pm_prenom'] = $user['pm_prenom'];

        // Nettoyage et normalisation du rôle
        $role = trim($user['role']);
        
        // Conversion des caractères accentués (avant la mise en majuscules)
        $role = strtr($role, [
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'à' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ï' => 'i',
            'ô' => 'o',
            'û' => 'u',
            'ù' => 'u',
            'ü' => 'u'
        ]);

        // Conversion en majuscules après avoir retiré les accents
        $role = strtoupper($role);

        // Définir l'URL de redirection en fonction du rôle
        $redirectUrl = '';
        
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
                $redirectUrl = '/JS/STIB/dashboard/employee.php';
                break;
            default:
                echo json_encode([
                    'success' => false, 
                    'message' => 'Rôle non reconnu: ' . htmlspecialchars($role) . ' (Original: ' . htmlspecialchars($user['role']) . ')'
                ]);
                exit;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'redirect' => $redirectUrl,
            'user' => [
                'id' => $user['id'],
                'matricule' => $user['matricule'],
                'role' => $role
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    }
} catch (PDOException $e) {
    error_log("Erreur de connexion: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la connexion: ' . $e->getMessage()
    ]);
}
