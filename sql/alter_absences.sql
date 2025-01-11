-- Ajouter une colonne pour l'ID de la personne qui a enregistré l'absence
ALTER TABLE absences
ADD COLUMN signale_par_id INT,
ADD FOREIGN KEY (signale_par_id) REFERENCES users(id);

-- Mettre à jour les enregistrements existants
UPDATE absences SET signale_par_id = NULL WHERE signale_par_id IS NULL;
