<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté et est un SuperAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Liste des rôles autorisés
const ROLES_AUTORISES = ['salarié', 'RH', 'EM', 'PM', 'DM', 'RH'];

// Fonction pour valider le rôle
function validerRole($role) {
    return in_array($role, ROLES_AUTORISES);
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Récupérer la méthode HTTP
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Récupérer les données JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Log pour le débogage
    error_log("Received data: " . print_r($data, true));
    
    switch ($method) {
        case 'GET':
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

        case 'POST':
            if (!isset($data['role']) || !isset($data['nom']) || !isset($data['prenom']) || !isset($data['matricule'])) {
                echo json_encode(['success' => false, 'error' => 'Données manquantes']);
                exit;
            }

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

            // Démarrer une transaction
            $pdo->beginTransaction();

            try {
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
                    'pm_id' => $data['pm_id'] ?? null,
                    'em_id' => $data['em_id'] ?? null,
                    'dm_id' => $data['dm_id'] ?? null
                ]);

                if ($result) {
                    $userId = $pdo->lastInsertId();
                    
                    // Ajouter l'entrée dans la table connexions avec le mot de passe par défaut
                    $connexionQuery = "INSERT INTO connexions (utilisateur_id, matricule, password) VALUES (:user_id, :matricule, :password)";
                    $connexionStmt = $pdo->prepare($connexionQuery);
                    $connexionResult = $connexionStmt->execute([
                        'user_id' => $userId,
                        'matricule' => $data['matricule'],
                        'password' => password_hash('123456', PASSWORD_DEFAULT)
                    ]);

                    if (!$connexionResult) {
                        throw new Exception('Erreur lors de la création des identifiants de connexion');
                    }

                    // Mettre à jour la table services pour PM, EM, DM
                    if (in_array($data['role'], ['PM', 'EM', 'DM'])) {
                        error_log("Role: " . $data['role'] . ", Service: " . $data['pool'] . ", ID: " . $userId);
                        
                        // Vérifier si le service existe déjà
                        $checkService = "SELECT id FROM services WHERE pool = ?";
                        $checkStmt = $pdo->prepare($checkService);
                        $checkStmt->execute([$data['pool']]);
                        $existingService = $checkStmt->fetch();

                        error_log("Service existe: " . ($existingService ? "Oui" : "Non"));

                        if (!$existingService) {
                            // Créer le service s'il n'existe pas
                            $serviceQuery = "INSERT INTO services (pool, em_id, pm_id, dm_id) VALUES (:pool, :em_id, :pm_id, :dm_id)";
                            $params = [
                                'pool' => $data['pool'],
                                'em_id' => $data['role'] === 'EM' ? $userId : null,
                                'pm_id' => $data['role'] === 'PM' ? $userId : null,
                                'dm_id' => $data['role'] === 'DM' ? $userId : 0
                            ];
                            
                            $serviceStmt = $pdo->prepare($serviceQuery);
                            $serviceResult = $serviceStmt->execute($params);

                            if (!$serviceResult) {
                                error_log("Erreur insertion: " . print_r($serviceStmt->errorInfo(), true));
                                throw new Exception('Erreur lors de la création du service');
                            }
                        } else {
                            // Mettre à jour le responsable si le service existe
                            $updateResult = false;
                            if ($data['role'] === 'EM') {
                                $updateService = "UPDATE services SET em_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$userId, $data['pool']]);
                                error_log("Mise à jour EM - ID: " . $userId);
                            } elseif ($data['role'] === 'PM') {
                                $updateService = "UPDATE services SET pm_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$userId, $data['pool']]);
                                error_log("Mise à jour PM - ID: " . $userId);
                            } elseif ($data['role'] === 'DM') {
                                $updateService = "UPDATE services SET dm_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$userId, $data['pool']]);
                                error_log("Mise à jour DM - ID: " . $userId);
                            }

                            if (!$updateResult) {
                                error_log("Erreur mise à jour: " . print_r($updateStmt->errorInfo(), true));
                                throw new Exception('Erreur lors de la mise à jour du service');
                            }
                        }
                    }

                    // Valider la transaction
                    $pdo->commit();
                    echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès', 'id' => $userId]);
                } else {
                    throw new Exception('Erreur lors de la création de l\'utilisateur');
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'PUT':
            if (!isset($data['id'])) {
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
            $checkStmt->execute([$data['matricule'], $data['id']]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Ce matricule existe déjà']);
                exit;
            }

            // Démarrer une transaction
            $pdo->beginTransaction();

            try {
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
                    'id' => $data['id'],
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'matricule' => $data['matricule'],
                    'role' => $data['role'],
                    'pool' => $data['pool'],
                    'pm_id' => $data['pm_id'] ?? null,
                    'em_id' => $data['em_id'] ?? null,
                    'dm_id' => $data['dm_id'] ?? null
                ]);

                if ($result) {
                    // Mettre à jour le matricule dans la table connexions
                    $updateConnexion = "UPDATE connexions SET matricule = :matricule WHERE utilisateur_id = :id";
                    $connexionStmt = $pdo->prepare($updateConnexion);
                    $connexionResult = $connexionStmt->execute([
                        'matricule' => $data['matricule'],
                        'id' => $data['id']
                    ]);

                    if (!$connexionResult) {
                        throw new Exception('Erreur lors de la mise à jour des identifiants de connexion');
                    }

                    // Mettre à jour la table services pour PM, EM, DM
                    if (in_array($data['role'], ['PM', 'EM', 'DM'])) {
                        error_log("Modification - Role: " . $data['role'] . ", Service: " . $data['pool'] . ", ID: " . $data['id']);
                        
                        // Vérifier si le service existe déjà
                        $checkService = "SELECT id FROM services WHERE pool = ?";
                        $checkStmt = $pdo->prepare($checkService);
                        $checkStmt->execute([$data['pool']]);
                        $existingService = $checkStmt->fetch();

                        error_log("Service existe (modification): " . ($existingService ? "Oui" : "Non"));

                        if (!$existingService) {
                            // Créer le service s'il n'existe pas
                            $serviceQuery = "INSERT INTO services (pool, em_id, pm_id, dm_id) VALUES (:pool, :em_id, :pm_id, :dm_id)";
                            $params = [
                                'pool' => $data['pool'],
                                'em_id' => $data['role'] === 'EM' ? $data['id'] : null,
                                'pm_id' => $data['role'] === 'PM' ? $data['id'] : null,
                                'dm_id' => $data['role'] === 'DM' ? $data['id'] : 0
                            ];
                            
                            $serviceStmt = $pdo->prepare($serviceQuery);
                            $serviceResult = $serviceStmt->execute($params);

                            if (!$serviceResult) {
                                error_log("Erreur insertion modification: " . print_r($serviceStmt->errorInfo(), true));
                                throw new Exception('Erreur lors de la création du service');
                            }
                        } else {
                            // Mettre à jour le responsable si le service existe
                            $updateResult = false;
                            if ($data['role'] === 'EM') {
                                $updateService = "UPDATE services SET em_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$data['id'], $data['pool']]);
                                error_log("Mise à jour EM modification - ID: " . $data['id']);
                            } elseif ($data['role'] === 'PM') {
                                $updateService = "UPDATE services SET pm_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$data['id'], $data['pool']]);
                                error_log("Mise à jour PM modification - ID: " . $data['id']);
                            } elseif ($data['role'] === 'DM') {
                                $updateService = "UPDATE services SET dm_id = ? WHERE pool = ?";
                                $updateStmt = $pdo->prepare($updateService);
                                $updateResult = $updateStmt->execute([$data['id'], $data['pool']]);
                                error_log("Mise à jour DM modification - ID: " . $data['id']);
                            }

                            if (!$updateResult) {
                                error_log("Erreur mise à jour modification: " . print_r($updateStmt->errorInfo(), true));
                                throw new Exception('Erreur lors de la mise à jour du service');
                            }
                        }
                    }

                    // Valider la transaction
                    $pdo->commit();
                    echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
                } else {
                    throw new Exception('Erreur lors de la mise à jour de l\'utilisateur');
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant']);
                exit;
            }

            // Démarrer une transaction
            $pdo->beginTransaction();

            try {
                // Supprimer d'abord les références dans les autres tables
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

                // Mettre à null le responsable_id dans la table services
                $updateServices = "UPDATE services SET responsable_em_id = NULL WHERE responsable_em_id = ?";
                $servicesStmt = $pdo->prepare($updateServices);
                $servicesStmt->execute([$id]);

                $updateServices = "UPDATE services SET pm_id = NULL WHERE pm_id = ?";
                $servicesStmt = $pdo->prepare($updateServices);
                $servicesStmt->execute([$id]);

                // Supprimer l'utilisateur
                $deleteUser = "DELETE FROM utilisateurs WHERE id = ?";
                $userStmt = $pdo->prepare($deleteUser);
                $userStmt->execute([$id]);

                // Valider la transaction
                $pdo->commit();

                echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression de l\'utilisateur']);
            }
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Une erreur est survenue']);
}
