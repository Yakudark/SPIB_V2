<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté et est un SuperAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Liste des rôles autorisés
const ROLES_AUTORISES = ['salarié', 'RH', 'EM', 'PM', 'DM'];

// Fonction pour valider le rôle
function validerRole($role) {
    return in_array($role, ROLES_AUTORISES);
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Récupérer la méthode HTTP
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET': // Récupérer un utilisateur spécifique
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant']);
                exit;
            }

            $query = "
                SELECT u.*, c.matricule as login_matricule
                FROM utilisateurs u
                LEFT JOIN connexions c ON u.id = c.utilisateur_id
                WHERE u.id = :id
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Utilisateur non trouvé']);
            }
            break;

        case 'POST': // Créer un nouvel utilisateur
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Valider le rôle
            if (!validerRole($data['role'])) {
                echo json_encode(['success' => false, 'error' => 'Rôle invalide']);
                exit;
            }

            // Vérifier si le matricule existe déjà
            $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE matricule = ?");
            $checkStmt->execute([$data['matricule']]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Ce matricule existe déjà']);
                exit;
            }

            // Insérer l'utilisateur
            $query = "INSERT INTO utilisateurs (nom, prenom, matricule, role, pool, pm_id, em_id, dm_id) 
                     VALUES (:nom, :prenom, :matricule, :role, :pool, :pm_id, :em_id, :dm_id)";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'matricule' => $data['matricule'],
                'role' => $data['role'],
                'pool' => $data['pool'],
                'pm_id' => $data['pm_id'] ?: null,
                'em_id' => $data['em_id'] ?: null,
                'dm_id' => $data['dm_id'] ?: null
            ]);

            if ($result) {
                $userId = $pdo->lastInsertId();
                
                // Créer l'entrée dans la table connexions avec le mot de passe par défaut
                $defaultPassword = password_hash($data['matricule'], PASSWORD_DEFAULT);
                $connexionQuery = "INSERT INTO connexions (utilisateur_id, matricule, password) VALUES (:user_id, :matricule, :password)";
                $connexionStmt = $pdo->prepare($connexionQuery);
                $connexionStmt->execute([
                    'user_id' => $userId,
                    'matricule' => $data['matricule'],
                    'password' => $defaultPassword
                ]);

                echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la création de l\'utilisateur']);
            }
            break;

        case 'PUT': // Mettre à jour un utilisateur
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;

            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant']);
                exit;
            }

            // Valider le rôle
            if (!validerRole($data['role'])) {
                echo json_encode(['success' => false, 'error' => 'Rôle invalide']);
                exit;
            }

            // Vérifier si le matricule existe déjà pour un autre utilisateur
            $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE matricule = ? AND id != ?");
            $checkStmt->execute([$data['matricule'], $id]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Ce matricule existe déjà']);
                exit;
            }

            $query = "UPDATE utilisateurs SET 
                     nom = :nom,
                     prenom = :prenom,
                     matricule = :matricule,
                     role = :role,
                     pool = :pool,
                     pm_id = :pm_id,
                     em_id = :em_id,
                     dm_id = :dm_id
                     WHERE id = :id";
            
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                'id' => $id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'matricule' => $data['matricule'],
                'role' => $data['role'],
                'pool' => $data['pool'],
                'pm_id' => $data['pm_id'] ?: null,
                'em_id' => $data['em_id'] ?: null,
                'dm_id' => $data['dm_id'] ?: null
            ]);

            // Mettre à jour le matricule dans la table connexions si nécessaire
            $updateConnexion = "UPDATE connexions SET matricule = :matricule WHERE utilisateur_id = :id";
            $connexionStmt = $pdo->prepare($updateConnexion);
            $connexionStmt->execute(['matricule' => $data['matricule'], 'id' => $id]);

            echo json_encode(['success' => $result, 'message' => $result ? 'Utilisateur mis à jour avec succès' : 'Erreur lors de la mise à jour']);
            break;

        case 'DELETE': // Supprimer un utilisateur
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant']);
                exit;
            }

            // Supprimer d'abord les références dans les autres tables
            $pdo->beginTransaction();
            try {
                // Supprimer de la table connexions
                $deleteConnexion = "DELETE FROM connexions WHERE utilisateur_id = ?";
                $connexionStmt = $pdo->prepare($deleteConnexion);
                $connexionStmt->execute([$id]);

                // Mettre à null les références dans les autres utilisateurs
                $updateRefs = "UPDATE utilisateurs SET 
                              pm_id = CASE WHEN pm_id = ? THEN NULL ELSE pm_id END,
                              em_id = CASE WHEN em_id = ? THEN NULL ELSE em_id END,
                              dm_id = CASE WHEN dm_id = ? THEN NULL ELSE dm_id END";
                $refsStmt = $pdo->prepare($updateRefs);
                $refsStmt->execute([$id, $id, $id]);

                // Supprimer l'utilisateur
                $deleteUser = "DELETE FROM utilisateurs WHERE id = ?";
                $userStmt = $pdo->prepare($deleteUser);
                $userStmt->execute([$id]);

                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Méthode non supportée']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur: ' . $e->getMessage()
    ]);
}
