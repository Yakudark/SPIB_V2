<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}
if ($_SESSION['role'] !== 'salarié') {
    header('Location: /JS/SPIB/public/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPIB - Espace Salarié</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- En-tête -->
        <header class="bg-blue-900 text-white shadow">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Espace Salarié</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-lg"><?php echo $_SESSION['nom'] ?? 'Utilisateur'; ?></span>
                        <a href="/JS/SPIB/api/auth/logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg">
                            Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu principal -->
        <main class="container mx-auto px-4 py-8">
            <h1>Mon Profil</h1>
            <!-- Première ligne - Informations personnelles -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Nom Complet</h3>
                    <p class="text-xl text-gray-900"><?php echo htmlspecialchars($_SESSION['nom'] . ' ' . $_SESSION['prenom']); ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Matricule</h3>
                    <p class="text-xl text-gray-900"><?php echo htmlspecialchars($_SESSION['matricule']); ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Service</h3>
                    <p class="text-xl text-gray-900">Pool Delta 1-2</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Manager</h3>
                    <p class="text-xl text-gray-900">Appana Devadas</p>
                </div>
            </div>

            <!-- Deuxième ligne - Statistiques -->
            <div class="grid grid-cols-5 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Entretiens</h3>
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-blue-600" id="entretiens-count">0</span>
                        <i class="fas fa-users ml-4 text-blue-400"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Jours de congé</h3>
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-green-600" id="conges-count">0</span>
                        <i class="fas fa-calendar ml-4 text-green-400"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Formations</h3>
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-purple-600" id="formations-count">0</span>
                        <i class="fas fa-graduation-cap ml-4 text-purple-400"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Documents</h3>
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-yellow-600" id="documents-count">0</span>
                        <i class="fas fa-file-alt ml-4 text-yellow-400"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Demandes</h3>
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-indigo-600" id="demandes-count">0</span>
                        <i class="fas fa-clock ml-4 text-indigo-400"></i>
                    </div>
                </div>
            </div>

            <!-- Troisième ligne - Tableaux -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Mes Prochains Entretiens -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Prochains Entretiens</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avec</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Données dynamiques -->
                        </tbody>
                    </table>
                </div>

                <!-- Mes Formations -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Formations</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Formation</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Données dynamiques -->
                        </tbody>
                    </table>
                </div>

             

                <!-- Mes Demandes -->
                <div class="bg-white rounded-lg shadow p-3">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Demandes</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Données dynamiques -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="/JS/SPIB/public/js/dashboard.js"></script>
</body>
</html>
