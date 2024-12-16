<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Lire le contenu du fichier SQL
    $sql = file_get_contents(__DIR__ . '/../sql/create_actions_tables.sql');

    // Exécuter les requêtes SQL
    $pdo->exec($sql);
    
    echo "Tables créées avec succès !";
} catch (PDOException $e) {
    echo "Erreur lors de la création des tables : " . $e->getMessage();
}
