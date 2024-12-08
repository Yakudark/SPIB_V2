<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
require_once __DIR__ . '/../controllers/InterviewController.php';

// Initialisation de la base de données
$database = new Database();
$db = $database->getConnection();

// Récupération de la méthode HTTP et de l'URL
$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// Parsing de l'URL pour extraire le endpoint et l'ID
$parts = explode('/', trim($request, '/'));
$endpoint = $parts[1] ?? ''; // Le premier segment après /api/
$id = $parts[2] ?? null;     // L'ID si présent

// Router simple
try {
    switch ($endpoint) {
        case 'users':
            $controller = new UserController($db);
            
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $controller->show($id);
                    } else {
                        $controller->index();
                    }
                    break;
                    
                case 'POST':
                    $controller->create();
                    break;
                    
                case 'PUT':
                    if ($id) {
                        $controller->update($id);
                    } else {
                        throw new Exception('ID manquant pour la mise à jour');
                    }
                    break;
                    
                case 'DELETE':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        throw new Exception('ID manquant pour la suppression');
                    }
                    break;
                    
                default:
                    throw new Exception('Méthode non supportée');
            }
            break;
            
        case 'services':
            $controller = new ServiceController($db);
            switch ($method) {
                case 'GET':
                    if ($id) {
                        if (isset($parts[3]) && $parts[3] === 'users') {
                            $controller->getUsers($id);
                        } else {
                            $controller->show($id);
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'POST':
                    $controller->create();
                    break;
                case 'PUT':
                    if ($id) {
                        $controller->update($id);
                    } else {
                        throw new Exception('ID manquant pour la mise à jour');
                    }
                    break;
                case 'DELETE':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        throw new Exception('ID manquant pour la suppression');
                    }
                    break;
                default:
                    throw new Exception('Méthode non supportée');
            }
            break;

        case 'departments':
            $controller = new DepartmentController($db);
            switch ($method) {
                case 'GET':
                    if ($id) {
                        if (isset($parts[3]) && $parts[3] === 'services') {
                            $controller->getServices($id);
                        } else {
                            $controller->show($id);
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'POST':
                    $controller->create();
                    break;
                case 'PUT':
                    if ($id) {
                        $controller->update($id);
                    } else {
                        throw new Exception('ID manquant pour la mise à jour');
                    }
                    break;
                case 'DELETE':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        throw new Exception('ID manquant pour la suppression');
                    }
                    break;
                default:
                    throw new Exception('Méthode non supportée');
            }
            break;

        case 'interviews':
            $controller = new InterviewController($db);
            switch ($method) {
                case 'GET':
                    if ($id) {
                        if (isset($parts[3])) {
                            switch ($parts[3]) {
                                case 'notes':
                                    $controller->getNotes($id);
                                    break;
                                default:
                                    throw new Exception('Action non supportée');
                            }
                        } else {
                            $controller->show($id);
                        }
                    } else {
                        if (isset($_GET['user_id'])) {
                            $controller->getByUser($_GET['user_id']);
                        } else {
                            $controller->index();
                        }
                    }
                    break;
                case 'POST':
                    if ($id && isset($parts[3]) && $parts[3] === 'notes') {
                        $controller->addNote($id);
                    } else {
                        $controller->create();
                    }
                    break;
                case 'PUT':
                    if ($id) {
                        $controller->update($id);
                    } else {
                        throw new Exception('ID manquant pour la mise à jour');
                    }
                    break;
                case 'DELETE':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        throw new Exception('ID manquant pour la suppression');
                    }
                    break;
                default:
                    throw new Exception('Méthode non supportée');
            }
            break;
            
        default:
            throw new Exception('Endpoint non trouvé');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
