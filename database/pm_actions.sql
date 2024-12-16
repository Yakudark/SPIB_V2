-- Table pour stocker les types d'actions possibles
CREATE TABLE IF NOT EXISTS action_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour stocker les actions effectuées par les PM
CREATE TABLE IF NOT EXISTS pm_actions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    pm_id INT NOT NULL,
    action_type_id INT NOT NULL,
    date_action DATE NOT NULL,
    commentaire TEXT,
    statut ENUM('planifie', 'effectue', 'annule') DEFAULT 'planifie',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (pm_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (action_type_id) REFERENCES action_types(id)
);

-- Insertion des types d'actions
INSERT INTO action_types (nom, description) VALUES
('Point attention nouvelle période', 'Si nouvelle période prévoir un entretien'),
('Appel bienveillant', 'Appel avec l''agent pour prendre de ses nouvelles, aider sur les démarches administratives, être présent pour l''agent'),
('Entretien Welcome Back', 'Sert à accueillir l''agent et l''informer de ce qui s''est passé dans l''entreprise durant son absence, voir ce qui doit être fait pour aider à une reprise en douceur'),
('Entretien absentéisme informel', 'S''entretenir avec l''agent pour voir s''il n''y a pas un problème sous-jacent et mettre en place des actions pour éviter l''aggravation de la situation'),
('Entretien absentéisme formel', 'Entretien formel concernant les absences nombreuses et répétées qui perturbent l''organisation du service'),
('Pas d''action', 'Aucune action nécessaire');
