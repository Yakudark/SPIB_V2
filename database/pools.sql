-- Table des pools
CREATE TABLE IF NOT EXISTS pools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pool VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ajout de la colonne pool_id dans la table utilisateurs si elle n'existe pas déjà
ALTER TABLE utilisateurs ADD COLUMN IF NOT EXISTS pool_id INT;
ALTER TABLE utilisateurs ADD FOREIGN KEY IF NOT EXISTS (pool_id) REFERENCES pools(id);

-- Insertion des pools de base
INSERT INTO pools (pool) VALUES 
('Pool A'),
('Pool B'),
('Pool C'),
('Pool D');
