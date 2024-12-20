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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Carte Jours de congés -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Jours de congé</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="text-center">
                                <span class="text-3xl font-bold text-green-600" id="conges-count">0</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span id="conges-en-attente">(0 en attente)</span>
                                </div>
                            </div>
                            <i class="fas fa-calendar ml-4 text-green-400"></i>
                        </div>
                        <button onclick="openVacationModal()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm">
                            <i class="fas fa-plus mr-1"></i> Demande
                        </button>
                    </div>
                </div>

                <!-- Carte Demandes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Demandes</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="text-center">
                                <span class="text-3xl font-bold text-blue-600" id="demandes-count">0</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span id="demandes-details">En attente</span>
                                </div>
                            </div>
                            <i class="fas fa-file-alt ml-4 text-blue-400"></i>
                        </div>
                        <div class="text-sm">
                            <div class="text-green-600"><i class="fas fa-check"></i> <span id="demandes-approuvees">0</span></div>
                            <div class="text-red-600"><i class="fas fa-times"></i> <span id="demandes-rejetees">0</span></div>
                        </div>
                    </div>
                </div>

                <!-- Carte Absences & Entretiens -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Absences & Entretiens</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="text-center">
                                <span class="text-3xl font-bold text-red-600" id="absences-count">0</span>
                                <div class="text-xs text-gray-500 mt-1">absences</div>
                            </div>
                            <i class="fas fa-user-clock ml-4 text-red-400"></i>
                        </div>
                        <div class="border-l pl-4">
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
            </div>

            <!-- Troisième ligne - Tableaux -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <!-- Mes Prochains Entretiens -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Mes Prochains Entretiens</h3>
                        <button onclick="openEntretiensStats()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="entretiens-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avec</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Les données seront insérées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mes Absences -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Absences</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="absences-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jours passés</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Signalé par</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Les données seront insérées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quatrième ligne - Tableau des demandes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Mes Demandes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="demandes-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date demande</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nb jours</th>
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
                        <button type="button" onclick="closeVacationModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg text-sm">
                            Annuler
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de détails de la demande -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Détails de la demande</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4" id="detailsContent">
                    <!-- Le contenu sera inséré dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Statistiques Entretiens -->
    <div id="entretiensStatsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Statistiques des Entretiens</h2>
                <button onclick="closeEntretiensStats()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mb-4">
                <label for="statsYear" class="block text-sm font-medium text-gray-700">Année</label>
                <select id="statsYear" onchange="loadEntretiensStats()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <!-- Les années seront ajoutées dynamiquement -->
                </select>
            </div>

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg flex-1 min-h-0">
                <div class="overflow-y-auto max-h-[50vh]">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 bg-gray-50">Type d'entretien</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">Nombre</th>
                            </tr>
                        </thead>
                        <tbody id="entretiensStatsBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Les statistiques seront ajoutées dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="/JS/SPIB/public/js/dashboard.js"></script>
    <script>
        // Fonction pour charger les entretiens
        async function loadEntretiens() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/actions.php');
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.querySelector('#entretiens-table tbody');
                    tbody.innerHTML = '';
                    
                    data.actions.forEach(action => {
                        const tr = document.createElement('tr');
                        const statusClass = action.type === 'absence' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
                        
                        tr.innerHTML = `
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${new Date(action.date).toLocaleDateString('fr-FR')}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                    ${action.type_action}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${action.manager_name}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">${action.commentaire || '-'}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction pour afficher le commentaire
        function showComment(comment) {
            alert(comment);
        }

        // Charger les entretiens au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadEntretiens();
            loadConges();
        });

        function openVacationModal() {
            document.getElementById('vacationModal').classList.remove('hidden');
        }

        function closeVacationModal() {
            document.getElementById('vacationModal').classList.add('hidden');
        }

        function openDetailsModal() {
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function getStatusBadgeClass(statut) {
            switch(statut) {
                case 'en_attente':
                    return 'bg-yellow-100 text-yellow-800';
                case 'approuve':
                    return 'bg-green-100 text-green-800';
                case 'refuse':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusText(statut) {
            switch(statut) {
                case 'en_attente':
                    return 'En attente';
                case 'approuve':
                    return 'Approuvé';
                case 'refuse':
                    return 'Refusé';
                default:
                    return statut;
            }
        }

        function showDetails(demande) {
            const detailsContent = document.getElementById('detailsContent');
            detailsContent.innerHTML = `
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    <div>
                        <span class="font-semibold">Date de la demande:</span>
                        <span class="ml-2">${formatDate(demande.date_demande)}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Période:</span>
                        <span class="ml-2">Du ${formatDate(demande.date_debut)} au ${formatDate(demande.date_fin)}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Nombre de jours:</span>
                        <span class="ml-2">${demande.nb_jours} jour${demande.nb_jours > 1 ? 's' : ''}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Statut:</span>
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(demande.statut)}">
                            ${getStatusText(demande.statut)}
                        </span>
                    </div>
                    <div>
                        <span class="font-semibold">Commentaire:</span>
                        <p class="mt-1 text-sm text-gray-600">${demande.commentaire || 'Aucun commentaire'}</p>
                    </div>
                    ${demande.reponse_commentaire ? `
                    <div>
                        <span class="font-semibold">Réponse:</span>
                        <p class="mt-1 text-sm text-gray-600">${demande.reponse_commentaire}</p>
                    </div>
                    ` : ''}
                </div>
            `;
            openDetailsModal();
        }

        async function deleteRequest(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette demande de congés ?')) {
                return;
            }
            
            try {
                const response = await fetch('/JS/SPIB/api/employee/supprimer_conge.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });

                const result = await response.json();
                
                if (result.success) {
                    loadConges(); // Recharger le tableau des demandes
                } else {
                    alert(result.error || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression de la demande');
            }
        }

        async function loadDemandesCount() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/demandes_count.php');
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour les compteurs
                    document.getElementById('demandes-count').textContent = data.demandes.total_demandes;
                    document.getElementById('demandes-approuvees').textContent = data.demandes.demandes_approuvees;
                    document.getElementById('demandes-rejetees').textContent = data.demandes.demandes_rejetees;
                    
                    // Ajouter une info-bulle pour plus de détails
                    const demandesElement = document.getElementById('demandes-count').parentElement;
                    demandesElement.title = `Total: ${data.demandes.total_demandes}\nEn attente: ${data.demandes.demandes_en_attente}\nApprouvées: ${data.demandes.demandes_approuvees}\nRejetées: ${data.demandes.demandes_rejetees}`;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function loadAbsencesStats() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/absences_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('absences-count').textContent = data.stats.absences;
                    document.getElementById('entretiens-pm').textContent = data.stats.entretiens.PM;
                    document.getElementById('entretiens-em').textContent = data.stats.entretiens.EM;
                    document.getElementById('entretiens-dm').textContent = data.stats.entretiens.DM;
                    
                    // Ajouter une info-bulle pour plus de détails
                    const absencesElement = document.getElementById('absences-count').parentElement;
                    absencesElement.title = `Absences cette année: ${data.stats.absences}\nEntretiens PM: ${data.stats.entretiens.PM}\nEntretiens EM: ${data.stats.entretiens.EM}\nEntretiens DM: ${data.stats.entretiens.DM}`;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function loadConges() {
            try {
                await Promise.all([
                    loadDemandesCount(),
                    loadAbsencesStats(),
                    loadEntretiens(),
                    loadMesAbsences(),
                    (async () => {
                        // Charger le solde de congés
                        const soldeResponse = await fetch('/JS/SPIB/api/employee/solde_conges.php');
                        const soldeData = await soldeResponse.json();
                        
                        if (soldeData.success) {
                            document.getElementById('conges-count').textContent = soldeData.conges.conges_restant;
                            document.getElementById('conges-en-attente').textContent = 
                                `(${soldeData.conges.jours_en_attente} en attente)`;
                            
                            const congesElement = document.getElementById('conges-count').parentElement;
                            congesElement.title = `Total: ${soldeData.conges.conges_total} jours\nPris: ${soldeData.conges.conges_pris} jours\nEn attente: ${soldeData.conges.jours_en_attente} jours\nRestant: ${soldeData.conges.conges_restant} jours`;
                        }

                        // Charger la liste des demandes
                        const response = await fetch('/JS/SPIB/api/conges/liste.php');
                        const data = await response.json();

                        if (data.success) {
                            const tbody = document.querySelector('#demandes-table tbody');
                            tbody.innerHTML = '';

                            data.demandes.forEach(demande => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${formatDate(demande.date_demande)}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${formatDate(demande.date_debut)}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${formatDate(demande.date_fin)}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${demande.nb_jours}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">Congés</td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(demande.statut)}">
                                            ${getStatusText(demande.statut)}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <button onclick='showDetails(${JSON.stringify(demande)})' class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            ${demande.statut === 'en_attente' ? `
                                                <button onclick='deleteRequest(${demande.id})' class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            });

                            // Mettre à jour le compteur de demandes
                            document.getElementById('demandes-count').textContent = data.demandes.length;
                        }
                    })()
                ]);
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        document.getElementById('vacationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            const data = {
                date_debut: formData.get('start_date'),
                date_fin: formData.get('end_date'),
                commentaire: formData.get('comment')
            };

            try {
                const response = await fetch('/JS/SPIB/api/conges/demander.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (response.ok) {
                    closeVacationModal();
                    loadConges(); // Recharger le tableau des demandes
                    this.reset(); // Réinitialiser le formulaire
                } else {
                    alert('Erreur : ' + (result.error || 'Erreur lors de l\'envoi de la demande'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi de la demande');
            }
        });

        // Fonction pour charger les absences
        async function loadMesAbsences() {
            try {
                const response = await fetch('/JS/SPIB/api/employee/mes_absences.php');
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.querySelector('#absences-table tbody');
                    tbody.innerHTML = '';
                    
                    data.absences.forEach(absence => {
                        const tr = document.createElement('tr');
                        const roleClass = getRoleClass(absence.role_signaleur);
                        
                        tr.innerHTML = `
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                ${formatDate(absence.date_absence)}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                ${Math.abs(absence.jours_passes)} jours
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleClass}">
                                    ${absence.signale_par}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                ${absence.motif || '-'}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        function getRoleClass(role) {
            switch (role) {
                case 'PM':
                    return 'bg-purple-100 text-purple-800';
                case 'EM':
                    return 'bg-blue-100 text-blue-800';
                case 'DM':
                    return 'bg-green-100 text-green-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        // Fonctions pour la modal des statistiques d'entretiens
        function openEntretiensStats() {
            document.getElementById('entretiensStatsModal').classList.remove('hidden');
            // Initialiser les années
            const currentYear = new Date().getFullYear();
            const select = document.getElementById('statsYear');
            select.innerHTML = '';
            for (let year = currentYear; year >= currentYear - 5; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                select.appendChild(option);
            }
            loadEntretiensStats();
        }

        function closeEntretiensStats() {
            document.getElementById('entretiensStatsModal').classList.add('hidden');
        }

        async function loadEntretiensStats() {
            try {
                const year = document.getElementById('statsYear').value;
                const response = await fetch(`/JS/SPIB/api/employee/entretiens_stats.php?year=${year}`);
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('entretiensStatsBody');
                    tbody.innerHTML = '';

                    data.stats.forEach(stat => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="py-4 pl-4 pr-3">
                                <div class="text-sm font-medium text-gray-900">${stat.type}</div>
                                <div class="text-sm text-gray-500">${stat.description || ''}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${stat.count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                    ${stat.count}
                                </span>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }
    </script>
</body>
</html>
