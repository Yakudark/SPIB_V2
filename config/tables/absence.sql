CREATE TABLE IF NOT EXISTS absences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    signale_par_id INT NOT NULL,
    date_absence DATE NOT NULL,
    motif TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (signale_par_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS entretiens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    manager_id INT NOT NULL,
    type_manager ENUM('PM', 'EM', 'DM') NOT NULL,
    date_entretien DATE NOT NULL,
    motif TEXT,
    compte_rendu TEXT,
    statut ENUM('planifié', 'réalisé', 'annulé') DEFAULT 'planifié',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id)
);
