<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Accès non autorisé</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4 text-center">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-danger mb-4">Accès non autorisé</h1>
            <p class="text-gray-600 mb-6">Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
            <a href="index.php" class="btn btn-primary inline-block">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
