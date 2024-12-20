ALTER TABLE entretiens
DROP COLUMN type_manager,
ADD COLUMN type_action_id INT NOT NULL,
ADD COLUMN manager_role ENUM('PM', 'EM', 'DM') NOT NULL,
ADD FOREIGN KEY (type_action_id) REFERENCES action_types(id);
