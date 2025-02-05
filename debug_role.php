<?php
require_once 'config/database.php';

$database = new Database();
$pdo = $database->getConnection();

// Récupérer tous les utilisateurs avec leurs rôles
$query = "SELECT matricule, role, BINARY role as role_binary, LENGTH(role) as role_length, 
          HEX(role) as role_hex FROM utilisateurs WHERE role = 'EM' OR role = 'PM'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
echo "=== Détails des rôles EM et PM ===\n";
foreach ($users as $user) {
    echo "\nUtilisateur matricule: " . $user['matricule'] . "\n";
    echo "Rôle: '" . $user['role'] . "'\n";
    echo "Rôle (binaire): " . $user['role_binary'] . "\n";
    echo "Longueur du rôle: " . $user['role_length'] . "\n";
    echo "Rôle (hex): " . $user['role_hex'] . "\n";
    echo "-------------------\n";
}
echo "</pre>";
