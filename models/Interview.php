<?php
require_once 'BaseModel.php';

class Interview extends BaseModel {
    protected $table = 'entretiens';

    public function create($data) {
        $query = "INSERT INTO entretiens 
                (utilisateur_id, type_action, date_action, feedback) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['utilisateur_id'],
            $data['type_action'],
            $data['date_action'],
            $data['feedback'] ?? null
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE entretiens SET 
                utilisateur_id = ?, 
                type_action = ?, 
                date_action = ?, 
                feedback = ? 
                WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['utilisateur_id'],
            $data['type_action'],
            $data['date_action'],
            $data['feedback'] ?? null,
            $id
        ]);
    }

    public function getByUser($userId) {
        $query = "SELECT e.*, u.nom, u.prenom 
                 FROM entretiens e
                 JOIN utilisateurs u ON e.utilisateur_id = u.id
                 WHERE e.utilisateur_id = ?
                 ORDER BY e.date_action DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByType($type) {
        $query = "SELECT e.*, u.nom, u.prenom 
                 FROM entretiens e
                 JOIN utilisateurs u ON e.utilisateur_id = u.id
                 WHERE e.type_action = ?
                 ORDER BY e.date_action DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addNote($entretienId, $note) {
        $query = "INSERT INTO prise_de_notes (entretien_id, note) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$entretienId, $note]);
    }

    public function getNotes($entretienId) {
        $query = "SELECT * FROM prise_de_notes WHERE entretien_id = ? ORDER BY date_note DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$entretienId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
