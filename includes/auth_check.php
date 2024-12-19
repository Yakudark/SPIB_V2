<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}

// Vérifier si l'URL actuelle correspond au rôle de l'utilisateur
$current_url = $_SERVER['PHP_SELF'];
$role = strtoupper($_SESSION['role']);

// Définir la page par défaut pour chaque rôle
$default_pages = [
    'SALARIÉ' => '/JS/SPIB/dashboard/employee.php',
    'SALARIE' => '/JS/SPIB/dashboard/employee.php',
    'MANAGER' => '/JS/SPIB/dashboard/manager.php',
    'ADMIN' => '/JS/SPIB/dashboard/admin.php',
    'PM' => '/JS/SPIB/dashboard/pm.php',
    'EM' => '/JS/SPIB/dashboard/em.php'
];

// Si le rôle n'est pas reconnu ou si la page actuelle n'est pas la page par défaut du rôle
if (!isset($default_pages[$role]) || $current_url !== $default_pages[$role]) {
    if (isset($default_pages[$role])) {
        header('Location: ' . $default_pages[$role]);
        exit;
    } else {
        // Si le rôle n'est pas reconnu, déconnecter l'utilisateur
        session_destroy();
        header('Location: /JS/SPIB/public/views/login.php');
        exit;
    }
}
