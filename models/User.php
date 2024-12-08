<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'utilisateurs';

    public function create($data) {
        $query = "INSERT INTO utilisateurs 
                (nom, prenom, matricule, service_id, responsable_direct_id, 
                dm_id, em_id, pm_id, role, pool) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['matricule'],
            $data['service_id'],
            $data['responsable_direct_id'],
            $data['dm_id'],
            $data['em_id'],
            $data['pm_id'],
            $data['role'],
            $data['pool']
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE utilisateurs SET 
                nom = ?, 
                prenom = ?, 
                matricule = ?,
                service_id = ?,
                responsable_direct_id = ?,
                dm_id = ?,
                em_id = ?,
                pm_id = ?,
                role = ?,
                pool = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['matricule'],
            $data['service_id'],
            $data['responsable_direct_id'],
            $data['dm_id'],
            $data['em_id'],
            $data['pm_id'],
            $data['role'],
            $data['pool'],
            $id
        ]);
    }

    public function getByRole($role) {
        $query = "SELECT * FROM utilisateurs WHERE role = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPool($pool) {
        $query = "SELECT * FROM utilisateurs WHERE pool = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$pool]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
