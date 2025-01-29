<?php
$config = [
    'host' => 'localhost',
    'dbname' => 'stib_gestion',
    'username' => 'root',
    'password' => 'root'
];

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        global $config;
        $this->host = $config['host'];
        $this->db_name = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            throw $e;
        }
    }
}

// Fonction helper pour obtenir une connexion
function getDBConnection() {
    try {
        $database = new Database();
        return $database->getConnection();
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
