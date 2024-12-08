<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    private $table = 'utilisateurs';

    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['matricule']) || !isset($data['password'])) {
                $this->errorResponse('Matricule et mot de passe requis', 400);
                return;
            }

            $query = "SELECT id, nom, prenom, matricule, role, service_id FROM {$this->table} 
                     WHERE matricule = ? AND password = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$data['matricule'], hash('sha256', $data['password'])]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user'] = $user;
                $this->successResponse([
                    'user' => [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'role' => $user['role']
                    ],
                    'redirect' => $this->getRedirectUrl($user['role'])
                ]);
            } else {
                $this->errorResponse('Identifiants invalides', 401);
            }
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la connexion: ' . $e->getMessage(), 500);
        }
    }

    public function logout() {
        session_destroy();
        $this->successResponse(['redirect' => '/login.php']);
    }

    private function getRedirectUrl($role) {
        switch ($role) {
            case 'super_admin':
                return '/admin/dashboard.php';
            case 'RH':
                return '/rh/dashboard.php';
            case 'DM':
                return '/manager/dm-dashboard.php';
            case 'EM':
                return '/manager/em-dashboard.php';
            case 'PM':
                return '/manager/pm-dashboard.php';
            case 'salarié':
                return '/employee/dashboard.php';
            default:
                return '/login.php';
        }
    }

    public function checkAuth() {
        if (!isset($_SESSION['user'])) {
            $this->errorResponse('Non authentifié', 401);
            return false;
        }
        return true;
    }

    public function checkRole($allowedRoles) {
        if (!$this->checkAuth()) return false;
        
        if (!in_array($_SESSION['user']['role'], $allowedRoles)) {
            $this->errorResponse('Accès non autorisé', 403);
            return false;
        }
        return true;
    }
}
?>
