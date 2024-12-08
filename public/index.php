<?php
require_once '../config/config.php';
require_once '../middleware/auth.php';

// Vérifier l'authentification
$user = checkAuth();

// Rediriger vers la page appropriée selon le rôle
switch ($user['role']) {
    case 'super_admin':
        include 'views/admin/dashboard.php';
        break;
    case 'RH':
        include 'views/rh/dashboard.php';
        break;
    case 'DM':
        include 'views/manager/dm-dashboard.php';
        break;
    case 'EM':
        include 'views/manager/em-dashboard.php';
        break;
    case 'PM':
        include 'views/manager/pm-dashboard.php';
        break;
    case 'salarié':
        include 'views/employee/dashboard.php';
        break;
    default:
        header('Location: login.php');
        exit;
}
?>
