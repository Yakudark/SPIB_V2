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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon Profil</h1>
            
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
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-green-600" id="conges-count">0</span>
                            <i class="fas fa-calendar ml-4 text-green-400"></i>
                        </div>
                        <button onclick="openVacationModal()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm">
                            <i class="fas fa-plus mr-1"></i> Demande
                        </button>
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="entretiens-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avec</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Les données seront insérées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mes Formations -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Formations</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="formations-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Formation</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Les données seront insérées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mes Demandes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Demandes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="demandes-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Les données seront insérées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de demande de vacances -->
    <div id="vacationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Demande de vacances</h3>
                <form id="vacationForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                        <textarea name="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVacationModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg text-sm">Annuler</button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/JS/SPIB/public/js/dashboard.js"></script>
    <script>
        function openVacationModal() {
            document.getElementById('vacationModal').classList.remove('hidden');
        }

        function closeVacationModal() {
            document.getElementById('vacationModal').classList.add('hidden');
        }

        document.getElementById('vacationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Créer l'objet de données
            const data = {
                date_debut: formData.get('start_date'),
                date_fin: formData.get('end_date'),
                commentaire: formData.get('comment')
            };

            try {
                const response = await fetch('/JS/SPIB/api/conges/demander.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (response.ok) {
                    alert('Demande envoyée avec succès');
                    closeVacationModal();
                    loadDashboardStats(); // Recharger les statistiques
                } else {
                    alert('Erreur : ' + (result.error || 'Erreur lors de l\'envoi de la demande'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi de la demande');
            }
        });
    </script>
</body>
</html>
