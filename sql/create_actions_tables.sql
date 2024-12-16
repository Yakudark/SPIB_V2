-- Table des types d'actions
CREATE TABLE IF NOT EXISTS action_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des actions
CREATE TABLE IF NOT EXISTS actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    pm_id INT NOT NULL,
    type_action_id INT NOT NULL,
    date_action DATE NOT NULL,
    commentaire TEXT,
    statut ENUM('planifie', 'effectue', 'annule') DEFAULT 'planifie',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (pm_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (type_action_id) REFERENCES action_types(id)
);

-- Insertion des types d'actions de base
INSERT INTO action_types (nom, description) VALUES
('Entretien mensuel', 'Entretien mensuel de suivi avec l''agent'),
('Point technique', 'Discussion sur les aspects techniques du travail'),
('Evaluation', 'Evaluation des performances'),
('Formation', 'Session de formation'),
('Briefing', 'Briefing d''équipe ou individuel'),
('Debriefing', 'Debriefing après une action ou un projet'),
('Accompagnement', 'Session d''accompagnement sur le terrain'),
('Recadrage', 'Entretien de recadrage'),
('Autre', 'Autre type d''action');
