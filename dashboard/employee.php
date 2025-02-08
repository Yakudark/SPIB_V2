<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /JS/STIB/public/views/login.php');
    exit;
}
if ($_SESSION['role'] !== 'salarié') {
    header('Location: /JS/STIB/public/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STIB - Espace Salarié</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <!-- Barre latérale -->
    <div class="fixed left-0 top-0 h-full w-60 bg-white p-4 shadow-lg">
        <!-- En-tête avec photo -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold">Mon Profil</h2>
            </div>
        </div>

        <!-- Informations personnelles -->
        <div class="space-y-4 mb-8">
            <div>
                <div class="text-lg font-bold text-center"><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></div>
                <div class="text-gray-600 text-center"><?php echo htmlspecialchars($_SESSION['matricule']); ?></div>
                <div class="text-gray-600 text-center"><?php echo isset($_SESSION['pool']) ? htmlspecialchars($_SESSION['pool']) : 'Non assigné'; ?></div>
            </div>
            
            <div>
                <div class="text-gray-600 text-center">Manager</div>
                <div class="font-medium text-center"><?php echo isset($_SESSION['pm_nom'], $_SESSION['pm_prenom']) ? htmlspecialchars($_SESSION['pm_prenom'] . ' ' . $_SESSION['pm_nom']) : 'Non assigné'; ?></div>
            </div>

            <!-- Statistiques -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 text-center">Absences & Entretiens</div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-red-600 text-center" id="absences-count">0</div>
                        <div class="text-sm text-gray-500">absences</div>
                    </div>
                    <div class="text-sm">
                        <div class="mb-1">
                            <i class="fas fa-user text-purple-500"></i>
                            <span class="text-gray-600">PM:</span>
                            <span id="entretiens-pm" class="font-semibold">0</span>
                        </div>
                        <div class="mb-1">
                            <i class="fas fa-user text-blue-500"></i>
                            <span class="text-gray-600">EM:</span>
                            <span id="entretiens-em" class="font-semibold">0</span>
                        </div>
                        <div>
                            <i class="fas fa-user text-green-500"></i>
                            <span class="text-gray-600">DM:</span>
                            <span id="entretiens-dm" class="font-semibold">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons du bas -->
        <div class="space-y-2">
            
            <a href="/JS/STIB/api/auth/logout.php" class="block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 border border-red-600 rounded shadow text-center">
                Déconnexion
            </a>
           
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="ml-60">

        <!-- Contenu -->
        <div class="p-8 space-y-6">
            <!-- Système d'onglets pour les entretiens -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="border-b border-gray-200 mb-4">
                    <ul class="flex -mb-px">
                        <li class="mr-2">
                            <button onclick="switchTab('upcoming')" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active" id="upcoming-tab">
                                Mes Prochains Entretiens
                            </button>
                        </li>
                        <li class="mr-2">
                            <button onclick="switchTab('history')" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="history-tab">
                                Historique des Entretiens
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Contenu des onglets -->
                <div id="upcoming-content" class="tab-content">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="upcoming-interviews">
                                <!-- Les entretiens à venir seront injectés ici -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-content" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="history-interviews">
                                <!-- L'historique des entretiens sera injecté ici -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mes Absences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Mes Absences</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours passés</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signalé par</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="absences-table">
                            <!-- Les données seront insérées ici dynamiquement -->
                        </tbody>
                    </table>
                    <div class="flex justify-between items-center mt-4">
                        <button id="prev-page" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Précédent
                        </button>
                        <div id="pagination-info" class="text-sm text-gray-600"></div>
                        <div id="pagination-numbers" class="flex space-x-2"></div>
                        <button id="next-page" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            Suivant
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour charger les statistiques des absences
        async function loadAbsencesStats() {
            try {
                const response = await fetch('/JS/STIB/api/employee/absences_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('absences-count').textContent = data.absences_count;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les entretiens
        async function loadEntretiens() {
            try {
                const response = await fetch('/JS/STIB/api/employee/entretiens.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('entretiens-pm').textContent = data.pm_count;
                    document.getElementById('entretiens-em').textContent = data.em_count;
                    document.getElementById('entretiens-dm').textContent = data.dm_count;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour charger les absences
        async function loadAbsences(page = 1) {
            try {
                const response = await fetch(`/JS/STIB/api/employee/absences.php?page=${page}`);
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('absencesTableBody');
                    tbody.innerHTML = '';
                    
                    data.absences.forEach(absence => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${new Date(absence.date_debut).toLocaleDateString()}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${absence.date_fin ? new Date(absence.date_fin).toLocaleDateString() : 'Non définie'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${absence.commentaire || ''}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${absence.signale_par_nom || ''}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour switcher entre les onglets
        function switchTab(tab) {
            // Mettre à jour les classes des onglets
            document.getElementById('upcoming-tab').classList.toggle('text-blue-600', tab === 'upcoming');
            document.getElementById('upcoming-tab').classList.toggle('border-blue-600', tab === 'upcoming');
            document.getElementById('history-tab').classList.toggle('text-blue-600', tab === 'history');
            document.getElementById('history-tab').classList.toggle('border-blue-600', tab === 'history');
            
            // Afficher/masquer le contenu approprié
            document.getElementById('upcoming-content').classList.toggle('hidden', tab !== 'upcoming');
            document.getElementById('history-content').classList.toggle('hidden', tab !== 'history');
        }

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadAbsencesStats();
            loadEntretiens();
            loadAbsences();
        });
    </script>
</body>
</html>