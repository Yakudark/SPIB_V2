<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté et est un SuperAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SuperAdmin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Récupérer tous les utilisateurs
    $query = "SELECT id, nom, prenom, matricule, role FROM utilisateurs ORDER BY nom, prenom";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la récupération des utilisateurs']);
}
