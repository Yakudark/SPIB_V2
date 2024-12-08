<?php
class BaseController {
    protected $db;
    protected $model;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    protected function errorResponse($message, $status = 400) {
        $this->jsonResponse(['error' => $message], $status);
    }

    protected function successResponse($data = null, $message = 'Success') {
        $response = ['message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->jsonResponse($response);
    }
}
?>
