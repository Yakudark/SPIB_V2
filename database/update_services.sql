-- Ajouter la colonne pm_id à la table services
ALTER TABLE services ADD COLUMN pm_id INT;

-- Ajouter la contrainte de clé étrangère
ALTER TABLE services ADD FOREIGN KEY (pm_id) REFERENCES utilisateurs(id);

-- Mettre à jour les services avec les PM correspondants (à adapter selon vos besoins)
UPDATE services SET pm_id = 6 WHERE id = 1;  -- Pool Delta 1-2
UPDATE services SET pm_id = 7 WHERE id = 2;  -- Pool Delta 1-3
UPDATE services SET pm_id = 8 WHERE id = 3;  -- Pool Delta 1-4
UPDATE services SET pm_id = 9 WHERE id = 4;  -- Pool Delta 1-5
UPDATE services SET pm_id = 10 WHERE id = 5; -- Pool Delta 1-6
UPDATE services SET pm_id = 11 WHERE id = 6; -- Pool Delta 1-7
UPDATE services SET pm_id = 12 WHERE id = 7; -- Pool Delta 1-8
UPDATE services SET pm_id = 13 WHERE id = 8; -- Pool Delta 2-1
UPDATE services SET pm_id = 14 WHERE id = 9; -- Pool Delta 2-2
UPDATE services SET pm_id = 15 WHERE id = 10; -- Pool Delta 2-3
UPDATE services SET pm_id = 16 WHERE id = 11; -- Pool Delta 2-4
UPDATE services SET pm_id = 17 WHERE id = 12; -- Pool Delta 2-5
UPDATE services SET pm_id = 18 WHERE id = 13; -- Pool Delta 2-6
