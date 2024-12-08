<?php
require_once 'BaseModel.php';

class Department extends BaseModel {
    protected $table = 'departements';

    public function create($data) {
        $query = "INSERT INTO departements (nom_departement, responsable_dm_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nom_departement'],
            $data['responsable_dm_id'] ?? null
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE departements SET nom_departement = ?, responsable_dm_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nom_departement'],
            $data['responsable_dm_id'] ?? null,
            $id
        ]);
    }

    public function getWithResponsable() {
        $query = "SELECT d.*, 
                        u.nom as responsable_nom, 
                        u.prenom as responsable_prenom
                 FROM departements d
                 LEFT JOIN utilisateurs u ON d.responsable_dm_id = u.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesByDepartment($departmentId) {
        $query = "SELECT s.* FROM services s
                 JOIN departements d ON s.departement_id = d.id
                 WHERE d.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
