<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: /JS/STIB/public/views/login.php');
    exit;
}

// Vérifier si l'URL actuelle correspond au rôle de l'utilisateur
$current_url = $_SERVER['PHP_SELF'];
$role = strtoupper($_SESSION['role']);

// Définir la page par défaut pour chaque rôle
$default_pages = [
    'SALARIÉ' => '/JS/STIB/dashboard/employee.php',
    'SALARIE' => '/JS/STIB/dashboard/employee.php',
    'MANAGER' => '/JS/STIB/dashboard/manager.php',
    'ADMIN' => '/JS/STIB/dashboard/admin.php',
    'PM' => '/JS/STIB/dashboard/pm.php',
    'EM' => '/JS/STIB/dashboard/em.php'
];

// Si le rôle n'est pas reconnu ou si la page actuelle n'est pas la page par défaut du rôle
if (!isset($default_pages[$role]) || $current_url !== $default_pages[$role]) {
    if (isset($default_pages[$role])) {
        header('Location: ' . $default_pages[$role]);
        exit;
    } else {
        // Si le rôle n'est pas reconnu, déconnecter l'utilisateur
        session_destroy();
        header('Location: /JS/STIB/public/views/login.php');
        exit;
    }
}
