<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
        $this->model = new User($db);
    }

    public function index() {
        try {
            $users = $this->model->getAll();
            $this->successResponse($users->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function show($id) {
        try {
            $user = $this->model->getById($id);
            if (!$user) {
                $this->errorResponse('Utilisateur non trouvé', 404);
                return;
            }
            $this->successResponse($user);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation basique
            if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['matricule'])) {
                $this->errorResponse('Données manquantes');
                return;
            }

            if ($this->model->create($data)) {
                $this->successResponse(null, 'Utilisateur créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de l\'utilisateur');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($this->model->update($id, $data)) {
                $this->successResponse(null, 'Utilisateur mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour de l\'utilisateur');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            if ($this->model->delete($id)) {
                $this->successResponse(null, 'Utilisateur supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression de l\'utilisateur');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }
}
?>
