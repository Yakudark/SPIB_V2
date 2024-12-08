<?php
require_once 'BaseModel.php';

class Service extends BaseModel {
    protected $table = 'services';

    public function create($data) {
        $query = "INSERT INTO services (nom_service, responsable_em_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nom_service'],
            $data['responsable_em_id'] ?? null
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE services SET nom_service = ?, responsable_em_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nom_service'],
            $data['responsable_em_id'] ?? null,
            $id
        ]);
    }

    public function getWithResponsable() {
        $query = "SELECT s.*, 
                        u.nom as responsable_nom, 
                        u.prenom as responsable_prenom
                 FROM services s
                 LEFT JOIN utilisateurs u ON s.responsable_em_id = u.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByService($serviceId) {
        $query = "SELECT * FROM utilisateurs WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$serviceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
