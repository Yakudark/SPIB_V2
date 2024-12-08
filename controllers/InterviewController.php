<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Interview.php';

class InterviewController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
        $this->model = new Interview($db);
    }

    public function index() {
        try {
            $interviews = $this->model->getAll();
            $this->successResponse($interviews->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function show($id) {
        try {
            $interview = $this->model->getById($id);
            if (!$interview) {
                $this->errorResponse('Entretien non trouvé', 404);
                return;
            }
            
            // Récupérer les notes associées
            $notes = $this->model->getNotes($id);
            $interview['notes'] = $notes;
            
            $this->successResponse($interview);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['utilisateur_id']) || !isset($data['type_action']) || !isset($data['date_action'])) {
                $this->errorResponse('Données manquantes');
                return;
            }

            if ($this->model->create($data)) {
                $this->successResponse(null, 'Entretien créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de l\'entretien');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($this->model->update($id, $data)) {
                $this->successResponse(null, 'Entretien mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour de l\'entretien');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function getByUser($userId) {
        try {
            $interviews = $this->model->getByUser($userId);
            $this->successResponse($interviews);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function addNote($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['note'])) {
                $this->errorResponse('La note est requise');
                return;
            }

            if ($this->model->addNote($id, $data['note'])) {
                $this->successResponse(null, 'Note ajoutée avec succès');
            } else {
                $this->errorResponse('Erreur lors de l\'ajout de la note');
            }
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }

    public function getNotes($id) {
        try {
            $notes = $this->model->getNotes($id);
            $this->successResponse($notes);
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage());
        }
    }
}
?>
