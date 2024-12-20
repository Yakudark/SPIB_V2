<?php
require_once 'database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Lire le contenu du fichier SQL
    $sql = file_get_contents(__DIR__ . '/tables/absence.sql');
    
    // Exécuter les requêtes
    $result = $pdo->exec($sql);
    
    echo "Tables créées avec succès!\n";
    
} catch (Exception $e) {
    echo "Erreur lors de la création des tables: " . $e->getMessage() . "\n";
}
