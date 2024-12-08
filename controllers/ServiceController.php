<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Service.php';

class ServiceController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
        $this->model = new Service($db);
    }

    public function index() {
        try {
            $services = $this->model->getWithResponsable();
            $this->successResponse($services);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function show($id) {
        try {
            $service = $this->model->getById($id);
            if (!$service) {
                $this->errorResponse('Service non trouvé', 404);
                return;
            }
            $this->successResponse($service);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nom_service'])) {
                $this->errorResponse('Le nom du service est requis');
                return;
            }

            if ($this->model->create($data)) {
                $this->successResponse(null, 'Service créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création du service');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($this->model->update($id, $data)) {
                $this->successResponse(null, 'Service mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour du service');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function getUsers($id) {
        try {
            $users = $this->model->getUsersByService($id);
            $this->successResponse($users);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }
}
?>
