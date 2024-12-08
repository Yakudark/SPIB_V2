<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Department.php';

class DepartmentController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
        $this->model = new Department($db);
    }

    public function index() {
        try {
            $departments = $this->model->getWithResponsable();
            $this->successResponse($departments);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function show($id) {
        try {
            $department = $this->model->getById($id);
            if (!$department) {
                $this->errorResponse('Département non trouvé', 404);
                return;
            }
            $this->successResponse($department);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nom_departement'])) {
                $this->errorResponse('Le nom du département est requis');
                return;
            }

            if ($this->model->create($data)) {
                $this->successResponse(null, 'Département créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création du département');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($this->model->update($id, $data)) {
                $this->successResponse(null, 'Département mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour du département');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function getServices($id) {
        try {
            $services = $this->model->getServicesByDepartment($id);
            $this->successResponse($services);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }
}
?>
