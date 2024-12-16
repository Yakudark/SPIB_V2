<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Vérifier la structure de la table utilisateurs
    $stmt = $pdo->query("DESCRIBE utilisateurs");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Structure de la table utilisateurs :\n";
    foreach ($columns as $column) {
        echo json_encode($column) . "\n";
    }
    
    // Vérifier le contenu de la table utilisateurs
    $stmt = $pdo->query("SELECT * FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nContenu de la table utilisateurs :\n";
    foreach ($users as $user) {
        echo json_encode($user) . "\n";
    }
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
